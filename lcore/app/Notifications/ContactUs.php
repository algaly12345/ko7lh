<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class ContactUs extends BaseNotification
{
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($globalSetting)
    {
        parent::__construct();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
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
            ->subject(__('email.newContact.subject'))
            ->line(__('email.contactUsEmail'))
            ->line(__('email.name').' '.request('name'))
            ->line(__('email.email').' '.request('email'))
            ->line(__('email.details').' '.request('details'))
            ->salutation(new HtmlString(__('email.regards').',<br>'.request('name')));
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
}
