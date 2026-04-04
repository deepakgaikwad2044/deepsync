<?php
namespace App\WebSockets\Realtime;

use App\WebSockets\Realtime\Realtime;

class DS
{
    public static function realtime(): Realtime
    {
        return new Realtime();
    }
}