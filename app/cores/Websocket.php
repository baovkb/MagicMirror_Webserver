<?php
namespace MM\cores\ChatWebSocket;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

/*
{
    action: string [join|update|request|accept|refresh]

    /*
        join chua du lieu neu sender la mm hoac devices
        update la yeu cau tu cap nhat, khong chua du lieu
        request la yeu cau, chua du lieu yeu cau
        accept la chap nhan yeu cau, chua du lieu
        refresh la phan hoi tu dong lam moi du lieu, chua du lieu
    */
/*
    requestType: string [update modules|update devices]
    sender: string [apps|mm|devices]
    data: [

    ]
}
*/

/*
json data field from devices
{
    device_id: bulb_01, bulb_02, door_01, door_02... 
    name: string
    active: bool
}

*/

class Chat implements MessageComponentInterface {
    public static $modulesData = [];
    public static $devicesData = [];

    protected $appsList;
    protected $mm;
    protected $devicesList;
    
    public function __construct() {
        $this->appsList = new \SplObjectStorage;
        $this->mm = new \SplObjectStorage;
        $this->devicesList = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn) {
        echo "New guest has connected\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $raw_msg = $msg;
        $msg = json_decode($msg, true);
        
        if (!is_array($msg)) {
            return;
        }

        $action = array_key_exists('action', $msg) ? $msg['action'] : null;
        $data = array_key_exists('data', $msg) ? $msg['data'] : [];
        $sender = array_key_exists('sender', $msg) ? $msg['sender'] : null;

        //join action
        if (isset($action) && $action == "join") {
            var_dump($raw_msg);

            if (isset($sender) && $sender == "apps" ) {
                $this->appsList->attach($from);
                echo "New app has joined\n";

                $payload = self::payload(true, "accept");
                self::sendData($from, $payload);
            } else if (isset($sender) && $sender == "mm") {
                $this->mm->attach($from);
                echo "MagicMirror has joined\n";

                //get data from mm
                self::$modulesData = $data;

                //send data to mm
                $payload = self::payload(true, "accept");
                self::sendData($from, $payload);
                
                //send data to apps
                foreach ($this->appsList as $app) {
                    self::sendData($app, $payload);
                }
            } else if (isset($sender) && $sender == "devices") {
                $this->devicesList->attach($from);
                echo "Device has joined\n";

                //get data from devices
                self::$devicesData = $data;

                //send data to mm
                $payload = self::payload(true, "accept");
                foreach ($this->mm as $m) {
                    self::sendData($m, $payload);
                }
                
                //send data to apps
                foreach ($this->appsList as $app) {
                    self::sendData($app, $payload);
                }
            }else return;

        } else {
            $fromMM = $sender == "mm" && $this->mm->contains($from);
            $fromApps = $sender == "apps" && $this->appsList->contains($from);
            $fromDevices = $sender == "devices" && $this->devicesList->contains($from);

            //xu ly phan hoi cho yeu cau
            if ($action == "accept") {
                echo "Server received an accept from {$sender}\n";
                var_dump($raw_msg);

                //magic mirror chi co the xu ly phan hoi modules
                //devices chi co the xu ly phan hoi dieu khien thiet bi
                if ($fromMM) {
                    self::$modulesData = $data;
                } else if ($fromDevices) {
                    self::$devicesData = $data;
                } else return;
    
                //send data to mm
                $payload = self::payload(true, "accept");
                foreach ($this->mm as $m) {
                    self::sendData($m, $payload);
                }
                
                //send data to apps
                foreach ($this->appsList as $app) {
                    self::sendData($app, $payload);
                }
            } 
            //xu ly yeu cau
            else if ($action == "request") {
                echo "Server received a request from {$sender}\n";
                var_dump($raw_msg);

                $requestType = array_key_exists('requestType', $msg) ? $msg['requestType'] : null;
                $payload = self::payload(false, "request", $data);

                //request from app
                if ($fromApps || $fromMM) {
                    if ($requestType == "update modules") {
                        foreach ($this->mm as $m) {
                            self::sendData($m, $payload);
                        }
                    } else if ($requestType == "update devices") {
                        foreach ($this->devicesList as $device) {
                            self::sendData($device, $payload);
                        }
                    }
                }
            } else if ($action == "refresh") {
                echo "Server received a refresh from {$sender}\n";
                var_dump($raw_msg);
                if ($fromMM) {
                    self::$modulesData = $data;
                } else if ($fromDevices) {
                    self::$devicesData = $data;
                } else return;
    
                //send data to mm
                $payload = self::payload(true, "accept");
                foreach ($this->mm as $m) {
                    self::sendData($m, $payload);
                }
                
                //send data to apps
                foreach ($this->appsList as $app) {
                    self::sendData($app, $payload);
                }
            } else {
                return;
            }
        }
    }

    public function onClose(ConnectionInterface $conn) {
        if ($this->appsList->contains($conn)) {
            $this->appsList->detach($conn);
            echo "App has disconnected\n";
        } else if ($this->mm->contains($conn)){
            $this->mm->detach($conn);
            echo "MagicMirror has disconnected\n";

            self::$modulesData = [];
            $payload = self::payload(true, "accept");
            foreach ($this->appsList as $app) {
                self::sendData($app, $payload);
            }
        } else if ($this->devicesList->contains($conn)) {
            echo "Devices has disconnected\n";
            $this->devicesList->detach($conn);

            self::$devicesData = [];
            $payload = self::payload(true, "accept");
            foreach ($this->appsList as $app) {
                self::sendData($app, $payload);
            }
            foreach ($this->mm as $m) {
                self::sendData($m, $payload);
            }
        } else {
            echo "Guest has disconnected\n";
        }
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }

    static function payload($all = true, $action="update", $data = []) {
        if ($all) {
            $data = [
                "modules" => self::$modulesData,
                "devices" => self::$devicesData,
            ];
        } 

        $payload = [
            "action" => $action,
            "data" => $data,
            "sender" => "server",
        ];

        return json_encode($payload);
    }

    static function sendData($conn, $payload) {
        $conn->send($payload);
    }
}

?>