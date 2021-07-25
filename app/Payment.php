<?php

namespace App;

use App\Observers\PaymentObserver;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $dates = ['paid_on'];

    protected static function boot()
    {
        parent::boot();

        static::observe(PaymentObserver::class);

        static::addGlobalScope('company', function (Builder $builder) {
            if (company()) {
                $builder->where('company_id', company()->id);
            }
        });
    }

    public function booking() {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
}
