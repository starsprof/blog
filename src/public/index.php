<?php

require_once "../../vendor/autoload.php";

// set
$path =  dirname(__DIR__);
putenv("ROOT=$path");

$bootstrap = new \App\Core\Bootstrap();
$bootstrap->run();