<?php
namespace MM\cores;

class Route {
    protected $routes = [];

    function get($path, $callback = []) {
        $this->routes['GET'][$path] = $callback;
    }

    function post($path, $callback = []) {
        $this->routes['POST'][$path] = $callback;
    }

    function run() {
        $method = $_SERVER['REQUEST_METHOD'];
        if (empty($_GET['url'])) {
            $controller = '/';
            $action = '';
            $id = '';
        } else {
            $controller = '/' .$_GET['url'];
            $action = empty($_GET['action']) ? '' : '/' .$_GET['action'] ;
            $id = empty($_GET['id']) ? '' : '/' .$_GET['id'];

        }
        
        $path = trim("$controller$action$id"); 
        /*
        $path = /category/edit/1 
        $path = /cart/view 
        */
        $params = $this->routes[$method][$path] ?? false;
        // echo '<pre>';
        // var_dump($this->routes[$method]);
        // echo '</pre>';
        if ($params) {
            $params = $this->routes[$method][$path];
            if (is_array($params)) {
                $controller = new $params[0];
                $action = $params[1];
                $controller->$action();
            } else if (is_object($params)) {
                $params();
            } else {
                echo 'error';
            }
        } else {
            echo '404';
        }
    }     
}


?>