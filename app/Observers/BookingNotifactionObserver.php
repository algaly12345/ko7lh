<?php

namespace App\Observers;

use App\BookingNotifaction;

class BookingNotifactionObserver
{
    /**
     * Handle the booking notifaction "created" event.
     *
     * @param  \App\BookingNotifaction  $bookingNotifaction
     * @return void
     */
    public function created(BookingNotifaction $bookingNotifaction)
    {
        //
    }

    /**
     * Handle the booking notifaction "updated" event.
     *
     * @param  \App\BookingNotifaction  $bookingNotifaction
     * @return void
     */
    public function updated(BookingNotifaction $bookingNotifaction)
    {
        //
    }

    /**
     * Handle the booking notifaction "deleted" event.
     *
     * @param  \App\BookingNotifaction  $bookingNotifaction
     * @return void
     */
    public function deleted(BookingNotifaction $bookingNotifaction)
    {
        //
    }

    /**
     * Handle the booking notifaction "restored" event.
     *
     * @param  \App\BookingNotifaction  $bookingNotifaction
     * @return void
     */
    public function restored(BookingNotifaction $bookingNotifaction)
    {
        //
    }

    /**
     * Handle the booking notifaction "force deleted" event.
     *
     * @param  \App\BookingNotifaction  $bookingNotifaction
     * @return void
     */
    public function forceDeleted(BookingNotifaction $bookingNotifaction)
    {
        //
    }
}
