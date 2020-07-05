<?php

namespace App\Helpers;

class RequestHelper
{
    public static function getCountry()
    {
        if (isset($_SERVER["HTTP_CF_IPCOUNTRY"])) {
            return $_SERVER["HTTP_CF_IPCOUNTRY"];
        }
        return null;
    }

    public static function getRemoteIPAddress()
    {
        if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
            $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
        }

        return $_SERVER['REMOTE_ADDR'];
    }

    public static function getUserAgent()
    {
        return $_SERVER['HTTP_USER_AGENT'];
    }
}
