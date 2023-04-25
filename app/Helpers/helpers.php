<?php

if (! function_exists('AppUrl')) {
    function AppUrl()
    {
        return env("APP_ENV") == "local" ? env("DEV_URL") : env("LIVE_URL");
    }
}
