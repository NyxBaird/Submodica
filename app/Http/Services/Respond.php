<?php

namespace App\Http\Services;

use App\Models\User;

/**
 * Class ResponseService
 * @package App\Http\Services
 */
class Respond
{
    /**
     * @param $msg
     * @param $data
     * @param $synchronousRoute
     * @return void
     */
    public static function success($msg, $data = [], $synchronousRoute = false) {
        if (!$synchronousRoute)
            self::respondAsynchronously(true, $msg, $data);
        else
            self::respond($synchronousRoute, true, $msg, $data);

    }

    /**
     * @param $msg
     * @param $data
     * @param $synchronousRoute
     * @return void
     */
    public static function error($msg, $data = [], $synchronousRoute = false) {
        if (!$synchronousRoute)
            self::respondAsynchronously(false, $msg, $data);
        else
            self::respond($synchronousRoute, false, $msg, $data);
    }

    /**
     * @param $route
     * @param $success
     * @param $msg
     * @param $data
     * @return void
     */
    private static function respond($route, $success, $msg, $data = []) {
        $data = [
            "route" => $route,
            "success" => $success,
            "message" => $msg,
            "data" => $data
        ];

        echo "Redirecting...";

        echo "<script>
            window.location.href = '/?" . urlencode(json_encode($data)) . "';
        </script>";
    }

    /**
     * @param $success
     * @param $msg
     * @param $data
     * @return void
     */
    private static function respondAsynchronously($success, $msg, $data = []) {
        die(json_encode([
            "success" => $success,
            "message" => $msg,
            "data" => $data
        ]));
    }
}
