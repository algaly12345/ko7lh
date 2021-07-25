<?php

namespace App\Console\Commands;

use App\Company;
use Carbon\Carbon;
use App\Traits\SmtpSettings;
use Illuminate\Bus\Queueable;
use Illuminate\Console\Command;
use App\Notifications\BookingReminder;

class BookingNotification extends Command
{
    use Queueable, SmtpSettings;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'booking:notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a notification before booking.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        // $this->setMailConfigs();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $companies = Company::active()->cronActive()->with('booking_not_notify')->get();
        foreach ($companies as $company) {
            $bookings = $company->booking_not_notify->whereIn('status', ['pending', 'approved'])->whereBetween('date_time', [Carbon::now()->timezone($company->timezone), Carbon::now()->timezone($company->timezone)->addMinutes(convertToMinutes($company->duration, $company->duration_type))]);
            foreach ($bookings as $booking) {
                $booking->user->notify(new BookingReminder($booking));
                $booking->update(['notify_at' => Carbon::now()]);
            }
        }
    }
}
