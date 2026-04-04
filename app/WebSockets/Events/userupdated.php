<?php //demo
namespace App\Events;

use App\Core\Helpers\Realtime;

class userupdated
{
    public static function dispatch(
        int $inactive_users,
        int $total_user
    ): void {

        $realtime = new Realtime();

        $realtime->UserChannel(
            "userupdated",
            [
                "inactive_users" => $inactive_users,
                "total_user"  => $total_user
            ]
        );
    }
}
