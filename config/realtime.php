<?php
//config/realtime.php

return [
    'host' => '0.0.0.0',
    'port' => 8080,
    'redis_host' => env('REDIS_HOST'),
    'redis_port' => env('REDIS_PORT'),
];