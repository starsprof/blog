<?php

require_once "../../vendor/autoload.php";

session_start();
// set
$path =  dirname(__DIR__);
putenv("ROOT=$path");

$bootstrap = new \App\Core\Bootstrap();
$bootstrap->run();