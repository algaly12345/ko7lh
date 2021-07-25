<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;

class NewCompany extends BaseNotification
{
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
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
        ->subject(__('email.newCompany.subject', ['name' => $this->globalSetting->company_name]))
        ->view('emails.company_email_verification', ['user' => $notifiable, 'socialLinks' => $this->socialLinks, 'globalSetting' => $this->globalSetting]);
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
