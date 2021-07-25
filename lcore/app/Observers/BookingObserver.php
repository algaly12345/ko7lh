<?php

namespace App\Observers;

use App\Booking;
use App\Company;
use Carbon\Carbon;
use App\BookingTime;
use App\Services\Google;
use App\BookingNotifaction;
use Google_Service_Calendar_Event;
use App\Notifications\BookingStatusChange;

class BookingObserver
{
    public function creating(Booking $booking)
    {
        if (company()) {
            $booking->company_id = company()->id;
        }
        $booking->event_id = $this->googleCalendarEvent($booking);
    }

    public function updating(Booking $booking)
    {
        $booking->event_id = $this->googleCalendarEvent($booking);
    }

    public function updated(Booking $booking)
    {
        if ($booking->isDirty('status')) {
            $booking->user->notify(new BookingStatusChange($booking));
        }

        $booking = Booking::with([
            'user' => function ($q) {
                $q->withoutGlobalScope('company');
            }
        ])
            ->find($booking->id);
    }

    /**
     * Handle the currency "deleting" event.
     *
     * @param  \App\Booking  $booking
     * @return void
     */
    public function deleting(Booking $booking)
    {
        $google = new Google();
        $company = $booking->company;
        $googleAccount = $company->googleAccount;
        if ((global_setting()->google_calendar == 'active') && $googleAccount) {
            // Create event
            $google->connectUsing($googleAccount->token);
            try {
                if ($booking->event_id) {
                    $google->service('Calendar')->events->delete('primary', $booking->event_id);
                }
            } catch (\Google\Service\Exception $th) {
                $googleAccount->delete();
                $google->revokeToken($googleAccount->token);
            }
        }
    }

    protected function googleCalendarEvent($booking)
    {
        $google = new Google();
        $company = $booking->company;
        $googleAccount = $company->googleAccount;

        if ((global_setting()->google_calendar == 'active') && $googleAccount) {

            $currency_symbol = $company->currency->currency_symbol;
            $vendorPage = $company->vendor_page;

            $location = ($vendorPage && ($vendorPage->map_option  == 'active' ) && (global_setting()->map_option  == 'active' ) && $vendorPage->latitude && $vendorPage->longitude) ? $vendorPage->latitude . ',' . $vendorPage->longitude : '';

            $description = __('app.booking').' '.__('app.id').':- #' . $booking->id . ', ';
            $description = $booking->order_id ? $description . __('app.payment').' '.__('app.id').':- ' . $booking->order_id . ', ' : $description;
            $description = $description .  __('app.subTotal').':- ' . currency_formatter($booking->original_amount, $currency_symbol) . ', ' . __('app.discount').':- ' . currency_formatter($booking->discount, $currency_symbol) . ', ' . __('app.tax').':- ' . currency_formatter($booking->tax_amount, $currency_symbol) . ', ' . __('app.total').':- ' . currency_formatter($booking->amount_to_pay, $currency_symbol) . ' ';

            $bookingTime = BookingTime::where('company_id', $booking->company_id)->where('day', strtolower($booking->date_time->format('l')))->first();

            // for more colors check this url https://lukeboyle.com/blog-posts/2016/04/google-calendar-api---color-id
            $color = 0;
            if ($booking->status == 'pending') {
                $color = 5;
            }elseif($booking->status == 'approved'){
                $color = 7;
            }elseif($booking->status == 'in progress'){
                $color = 9;
            }elseif($booking->status == 'completed'){
                $color = 2;
            }elseif($booking->status == 'canceled'){
                $color = 11;
            }
            // Create event
            $google->connectUsing($googleAccount->token);


            $bookingNotifactions = BookingNotifaction::where('company_id',$company->id)->get();
            $reminders=[];
            foreach ($bookingNotifactions as $key => $bookingNotifaction) {

                $duration = convertToMinutes($bookingNotifaction->duration,$bookingNotifaction->duration_type);

                $reminders[] = array('method' => 'email', 'minutes' => $duration);
                $reminders[] = array('method' => 'popup', 'minutes' => $duration);
            }
            $event = new Google_Service_Calendar_Event(array(
                'summary' => $booking->user->name . ' (' . __('app.' . $booking->status) . ')',
                'location' => $location,
                'description' =>  $description,
                'colorId'=>$color,
                'start' => array(
                    'dateTime' => $booking->date_time,
                    'timeZone' => $company->timezone,
                ),
                'end' => array(
                    'dateTime' => $booking->date_time->addMinutes($bookingTime->slot_duration),
                    'timeZone' => $company->timezone,
                ),
                'reminders' => array(
                    'useDefault' => FALSE,
                    'overrides' => $reminders,
                ),
            ));

            try {
                if ($booking->event_id) {
                    $results =  $google->service('Calendar')->events->patch('primary', $booking->event_id, $event);
                } else {
                    $results =  $google->service('Calendar')->events->insert('primary', $event);
                }

                return $results->id;
            } catch (\Google\Service\Exception $th) {
                $googleAccount->delete();
                $google->revokeToken($googleAccount->token);
            }
        }
        return $booking->event_id;
    }
}
