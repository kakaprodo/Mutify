<?php
namespace RWBuild\Mutify\Channels;

use RWBuild\Mutify\MurugoNotification;
use Illuminate\Notifications\Notification;

class MurugoChannel
{
    /**
     * MurugoChannel
     * ------------------------------------------------------------------------
     * This is a customer channel that will be integrated in laravel 
     * notification to handle murugo notification
     */

    

    /**
     * Send the given notification.
     *
     * @param  mixed  $user
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($user, Notification $notification)
    {
        $this->toMurugoSms($user, $notification);

        $this->toMurugoEmail($user, $notification);

        $this->toMurugoPusher($user, $notification);
    }

    /**
     * send to murugo pusher notification
     */
    private function toMurugoPusher($user, Notification $notification)
    {
        if (! method_exists($notification, 'toMurugoPusher')) return ;

        $payload = $notification->toMurugoPusher($user);

        $this->sendPusherNofication($payload, $user);

    }

    /**
     * send to murugo sms notification
     */
    private function toMurugoSms($user, Notification $notification)
    {
        if (! method_exists($notification, 'toMurugoSms')) return ;

        $payload = $notification->toMurugoSms($user);

        $this->sendSmsNotification($payload, $user);

    }

    /**
     * send to murugo sms notification
     */
    private function toMurugoEmail($user, Notification $notification)
    {
        if (! method_exists($notification, 'toMurugoEmail')) return ;

        $payload = $notification->toMurugoEmail($user);

        $this->sendEmailNotification($payload, $user);

    }

    /**
     * build murugo pusher notification then send
     */
    private function sendPusherNofication(array $payload, $user)
    {
        if ($payload == []) return ;

            MurugoNotification::pusher()
                ->setChannelName($payload['channel_name'])
                ->setChannelType($payload['channel_type'])
                ->setEventName($payload['event_name'])
                ->setPayload($payload)
                ->send();
    }

    /**
     * build murugo sms notification then send
     */
    private function sendSmsNotification(array $payload, $user)
    {
        if ($payload == []) return ;
        
        MurugoNotification::sms()
            ->setMessage($payload['message'])
            ->setPhoneNumber($user->phone)
            ->send();
    }

    /**
     * build murugo email notification then send
     */
    private function sendEmailNotification(array $payload, $user)
    {
        if ($payload == []) return ;

        MurugoNotification::email()
            ->onQueue('email')
            ->setHeader($payload['header'] ?? null)
            ->setEmailAddress($user->email)
            ->setSubject($payload['subject'])
            ->setReceiverName($user->name)
            ->setContent($payload['message'])
            ->setFooter($payload['footer'] ?? null)
            ->send();
    }
}