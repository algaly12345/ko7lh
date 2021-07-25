<?php

namespace App\Notifications;

use App\SmsSetting;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\NexmoMessage;
use Illuminate\Support\HtmlString;

class NewUser extends BaseNotification
{
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    private $password;
    public function __construct($password)
    {
        parent::__construct();
        $this->password = $password;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        $via = ['mail'];

        if ($this->smsSetting->nexmo_status == 'active' && $notifiable->mobile_verified == 1) {
            array_push($via, 'nexmo');
        }

        return $via;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {

        return (new MailMessage)
            ->subject(__('email.newUser.subject').' '.config('app.name').'!')
            ->greeting(__('email.hello').' '.ucwords($notifiable->name).'!')
            ->line(__('email.newUser.text'))
            ->line(__('email.email').' '.$notifiable->email)
            ->line(__('email.password').' '.$this->password)
            ->action(__('email.loginAccount'), url('/login'))
            ->line(__('email.thankyouNote'))
            ->salutation(new HtmlString(__('email.regards').',<br>'.config('app.name')));

    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }

    /**
     * Get the Nexmo / SMS representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return NexmoMessage
     */
    public function toNexmo($notifiable)
    {
        return (new NexmoMessage)
                    ->content(__('email.newUser.text'))->unicode();
    }
}
