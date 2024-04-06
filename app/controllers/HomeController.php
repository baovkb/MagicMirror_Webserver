<?php
namespace MM\controllers;
use MM\cores\Controller;

class HomeController extends Controller{
    function index() {
        $this->render('main', 'home/index.php');
    }
}
?>