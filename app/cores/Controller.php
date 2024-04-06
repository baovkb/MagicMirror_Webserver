<?php
namespace MM\cores;
use MM\cores\Http;

class Controller extends Http{
    function render($layout, $view) {
        $this->layout($layout);
        $this->view($view);
    }
    
    function view($view) {
        require_once('app/views/' .$view);
    }

    function layout($layout) {
        require_once('assets/layout/' .$layout .'.php');
    }

    function model($model) {
        require_once('app/models/' .$model .'.php');
        return new $model;
    }
}

?>