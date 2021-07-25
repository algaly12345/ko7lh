<?php

namespace App\Notifications;

use App\Booking;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\NexmoMessage;
use Illuminate\Support\HtmlString;

class BookingCancel extends BaseNotification
{
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    private $booking;
    private $role;
    public function __construct(Booking $booking, $role)
    {
        parent::__construct();

        $this->booking = $booking;
        $this->role = $role;
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

        $role = $this->role;
        return (new MailMessage)
            ->subject(__('email.bookingCancel.subject').' '.config('app.name').'!')
            ->greeting(__('email.hello').' '.ucwords($notifiable->name).'!')
            ->line(__('email.bookingCancel.text', ['user' => $role]))
            ->line(__('app.booking').' #'.$this->booking->id)
            ->line(__('app.booking').' '.__('app.date').' - '.$this->booking->date_time->isoFormat('DD MMMM, YYYY'))
            ->line(__('app.booking').' '.__('app.time').' - '.$this->booking->date_time->isoFormat('hh:mm A'))
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
                    ->content(
                        __('email.bookingCancel.text')."\n".
                        __('app.booking')." #".$this->booking->id."\n".
                        __('app.booking')." ".__('app.date')." - ".$this->booking->date_time->isoFormat('DD MMMM, YYYY - hh:mm A')
                    )->unicode();
    }
}
