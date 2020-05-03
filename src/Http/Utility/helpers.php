<?php

use RWBuild\Mutify\MurugoNotification;
use RWBuild\Mutify\Channels\MurugoChannel;

if (! function_exists('murugoEmail')) {
    /**
     * Prepare murugo email notification object
     */
    function murugoEmail() {
        return MurugoNotification::email();
    }
}


if (! function_exists('murugoSms')) {
    /**
     * Prepare murugo sms notification object
     */
    function murugoSms() {
        return MurugoNotification::sms();
    }
}


if (! function_exists('murugoPusher')) {
    /**
     * Prepare murugo sms notification object
     */
    function murugoPusher() {
        return MurugoNotification::pusher();
    }
}

if (! function_exists('murugoChannel')) {
    /**
     * define a channel for laravel notifications
     */
    function murugoChannel() {
        return MurugoChannel::class;
    }
}