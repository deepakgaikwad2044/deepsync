<?php
//bootstrap/config.php


//load to autoload file 
require APP_ROOT ."/vendor/autoload.php";

$config = require APP_ROOT . '/config/realtime.php';

return [
    'host' => $config['host'],
    'port' => $config['port']
];
