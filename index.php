<?php
require_once("src/view/HTMLView.php");
require_once("src/controller/c_curl.php");
 
session_start();
//Views
$view = new \view\HTMLView();

//Controllers
$curl= new \controller\Curl();

$head  = '<link rel="stylesheet" type="text/css" href="css/main.css">';
$head .= '<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>';

$htmlBody = $curl->doControll(); 

$view->echoHTML("Laboration 1", $head, $htmlBody);

