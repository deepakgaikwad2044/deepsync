<?php

namespace App\Core;

class Request
{
    public static function input()
    {
        $data = json_decode(file_get_contents("php://input"), true);
        return $data ?: $_POST;
    }
}