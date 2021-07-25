<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PaymentGatewayCredentials extends Model
{
    protected $guarded = ['id'];
    protected $appends = ['show_pay'];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('company', function (Builder $builder) {
            if (company()) {
                $builder->where('company_id', company()->id);
            }
        });
    }

    public function company()
    {
        return $this->belongsTo(company::class);
    }

    public function getShowPayAttribute() {
        return $this->attributes['paypal_status'] == 'active' || $this->attributes['stripe_status'] == 'active' || $this->attributes['razorpay_status'] == 'active';
    }
}
