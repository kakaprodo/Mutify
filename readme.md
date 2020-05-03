# Mutify 
A laravel package to send email,sms,pusher notification using ... notification service

## Version
Mutify 1.0.0

## Installation

``` shell
 $ composer require promessekayenga/mutify
```

*notice* if your Laravel version is beyond 5.5, package we'll auto register ServiceProvider to app.php, otherwise you will need to register this manually.

config/app.php
```php
'provider' => [
	// package ServiceProvider
	RWBuild\Mutify\MutifyServiceProvider::class,
]
```

```php
'alias' => [
	'Mutify' => RWBuild\Mutify\Facades\Mutify::class,
]
```

## Documentation
`Mutify` is one of easiest laravel packages to integrate. i Hope that you are ready and excited to see what comes next, Here we go!!

### Email notification

``` php
 Mutify::email()
    ->setHeader('My Header')
    ->setEmailAddress('peter@gmail.com')
    ->setSubject('A reminder to consult a doctor')
    ->setReceiverName('prodo')
    ->setContent('Just to remind you that today we are monday ... need to meet...')
    ->setFooter('My footer')
    ->send();

```

### Sms notification

``` php
 Mutify::sms()
    ->setMessage('Your verification code is : 123456')
    ->setPhoneNumber('+2507845564309')
    ->send();

```


### Pusher notification

1.  private channel

``` php
 Mutify::pusher()
    ->setChannelName('User.2')
    ->setChannelType('private')
    ->setEventName('user-notification')
    ->setPayload([
        'message' => 'You have received money',
        ...
    ])
    ->send();

```

2.  public channel

``` php
 Mutify::pusher()
    ->setChannelName('public.channel')
    ->setChannelType('public')
    ->setEventName('broadcast-updates')
    ->setPayload([
        'message' => 'A new release of our package is ready.',
        ...
    ])
    ->send();

```

### Integration with laravel notification

Laravel chips with notifications and you can send `email`, `pusher`, '`slack` ...(different channel) notification in two minutes by doing:

``` php
 $user = \App\User::first(1);

 $user->notify(new SendReceipt($receipt));
```

`Mutify` also provide a channel that will allow you to use it inside laravel notification.To do so, just watch:

Inside your notification class:


``` php
<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class EmailNotification extends Notification
{
    use Queueable;
    
    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [mutifyChannel()];
    }

    /**
     * payload for mutify email notifcation
     *
     * @return array
     */
    public function toMurugoEmail($notifiable)
    {
        return [
            'header' => 'Your header',
            'subject' => 'A reminder to consult a doctor',
            'message' => 'Just to remind you that today we are monday ... need to meet...',
            'footer' => 'Footer',
        ];
    }

    /**
     * payload for mutify sms notifcation
     *
     * @return array
     */
    public function toMurugoSms($notifiable)
    {
        return [
            'message' => 'Just to remind you that today we are monday ... need to meet...',
        ];
    }

    /**
     * payload for mutify pusher notifcation
     *
     * @return array
     */
    public function toMurugoPusher($notifiable)
    {
        return [
            'message' => 'Just to remind you that today we are monday ... need to meet...'
        ];
    }
}

```

Then you can send notification in this way :

``` php
 $user = \App\User::first(1);

 $user->notify(new EmailNotification($receipt));
```

`Note` : The header and footer mentioned on email notification are optional, by default `Mutify` will take `env('APP_NAME')` value at the place.

### Helper functions
In case you'd like to use functions instead of the facade used for sending notification, you can make it possible by doing this:


``` php
mutifyEmail()
    ->setHeader('My Header')
    ->setEmailAddress('peter@gmail.com')
    ->setSubject('A reminder to consult a doctor')
    ->setReceiverName('prodo')
    ->setContent('Just to remind you that today we are monday ... need to meet...')
    ->setFooter('My footer')
    ->send();
```


``` php
mutifySms()
    ->setMessage('Your verification code is : 123456')
    ->setPhoneNumber('+2507845564309')
    ->send();
```

``` php
mutifyPusher()
    ->setChannelName('public.channel')
    ->setChannelType('public')
    ->setEventName('broadcast-updates')
    ->setPayload([
        'message' => 'A new release of our package is ready.',
        ...
    ])
    ->send();
```