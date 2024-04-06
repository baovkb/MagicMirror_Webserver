var gateway = 'ws://' + window.location.hostname + ':9090';
var conn;

var modulesList = [];
var devicesList = [];

const $ = document.querySelector.bind(document);
const $$ = document.querySelectorAll.bind(document);

const moduleElements = $("#modules-list");
const deviceElements = $("#devices-list");

connectWS();
renderItems();
handleEvents();

function connectWS() {
    conn = new WebSocket(gateway);

    conn.onopen = () => {
        sendRequest("join");
    };

    conn.onclose = () => {
        console.log("mat ket noi");

        setTimeout(() => {
            connectWS();
        }, 2000);
    }

    conn.onmessage = (e) => {
        data = JSON.parse(e.data);
        console.log(data);

        if (data["sender"] == "server" && data["action"] == "accept") {
            modulesList = data["data"]["modules"];
            devicesList = data["data"]["devices"];

            renderItems();
            handleEvents();
        }
    }
}

function renderItems() {
    if (devicesList.length === 0) {
        deviceElements.innerHTML = `<img src="/assets/img/no_data.png" alt="No data" class="no_data_img">`;
    } else {
        let htmlDeviceList = devicesList.map((item, index) => {
            return `
            <div class="col l-6 m-6 c-6">
                <div class="devices-item ${item.active ? "devices-item--active" : ""}" id="${item.device_id}">
                    <div class="devices-item-icon">
                        ${item.device_id.includes("bulb") ? '<i class="fa-regular fa-lightbulb"></i>' : '<i class="fa-solid fa-fan"></i>'}
                    </div>
                    <span class="devices-item-header">${item.name}</span>
                </div>
            </div>
            `
        });

        deviceElements.innerHTML = htmlDeviceList.join('\n');
    }

    if (modulesList.length === 0) {
        moduleElements.innerHTML = `<img src="/assets/img/no_data.png" alt="No data" class="no_data_img">`;
    } else {
        let htmlModuleList = modulesList.map((item, index) => {
            return `
            <li class="control-item">
                <div class="toggle">
                    <label class="switch" for="${item.identifier}">
                        <input type="checkbox" id="${item.identifier}" ${item.hidden ? "" : "checked"} data-index="${index}">
                        <span class="slider round"></span>
                    </label>
                </div>
                <label class="control-content">
                    ${item.name}
                </label>
            </li>
            `
        });

        moduleElements.innerHTML = htmlModuleList.join('\n');
    }
}

function handleEvents() {
    checkboxElements = $$(".toggle");
    Array.from(checkboxElements).forEach(checkbox => {
        checkbox.onclick =  function(event) {
            event.preventDefault();
        };

        checkbox.addEventListener("click", (e) => {
            item = e.target;
            if (item.nodeName == "SPAN") {
                parentNode = item.parentNode;
                item = parentNode.getElementsByTagName("input")[0];
            }
            data = [
                {
                    "identifier": item.id,
                    "hidden": item.checked,
                }
            ];
            //send data
            sendRequest("request", "update modules", data);
        })
    });

    
}

function sendRequest(action, requestType="", data=[]) {
    js = payload(action, requestType, data);
    conn.send(js);
}

function payload(action, requestType, data) {
    json = {
        "sender": "apps",
        "data": data,
        "action": action,
    };

    if (action == "join") {
        json["data"] = [];
    } else if (action == "request") {
        json["requestType"] = requestType;
    } else return;

    return JSON.stringify(json);
}