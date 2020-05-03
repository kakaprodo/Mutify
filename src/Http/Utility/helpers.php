<?php

use RWBuild\Mutify\MurugoNotification;
use RWBuild\Mutify\Channels\MurugoChannel;

if (! function_exists('mutifyEmail')) {
    /**
     * Prepare murugo email notification object
     */
    function mutifyEmail() {
        return MurugoNotification::email();
    }
}


if (! function_exists('mutifySms')) {
    /**
     * Prepare murugo sms notification object
     */
    function mutifySms() {
        return MurugoNotification::sms();
    }
}


if (! function_exists('mutifyPusher')) {
    /**
     * Prepare murugo sms notification object
     */
    function mutifyPusher() {
        return MurugoNotification::pusher();
    }
}

if (! function_exists('mutifyChannel')) {
    /**
     * define a channel for laravel notifications
     */
    function mutifyChannel() {
        return MurugoChannel::class;
    }
}