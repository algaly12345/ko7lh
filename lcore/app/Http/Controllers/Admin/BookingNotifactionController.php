<?php

namespace App\Http\Controllers\Admin;

use App\BookingNotifaction;
use App\BookingTime;
use Illuminate\Http\Request;
use Froiden\Envato\Helpers\Reply;
use App\Http\Controllers\AdminBaseController;
use App\Http\Requests\Admin\BookingNotifaction\Store;

class BookingNotifactionController extends AdminBaseController
{
    public function store(Store $request)
    {
        $company = company();
        BookingNotifaction::where('company_id', $company->id)->delete();
        foreach ($request->duration as $key => $duration) {
            $booking = new BookingNotifaction();
            $booking->company_id = $company->id;
            $booking->duration = $duration;
            $booking->duration_type = $request->duration_type[$key];
            $booking->save();
        }

        return Reply::success(__('messages.googleCalendarNotifactionSaved'));
    }
    public function destroy($id)
    {
        $bookingNotifaction = BookingNotifaction::findOrFail($id);
        $bookingNotifaction->delete();
        return Reply::success(__('messages.googleCalendarNotifactionDeleted'));
    }
}
