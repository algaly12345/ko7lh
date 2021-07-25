<?php

namespace App;

use App\Observers\OfflineInvoicePaymentObserver;
use Illuminate\Database\Eloquent\Model;

class OfflineInvoicePayment extends Model
{
    protected static function boot()
    {
        parent::boot();
        static::observe(OfflineInvoicePaymentObserver::class);
    }
    
    public function payment_method()
    {
        return $this->belongsTo(OfflinePaymentMethod::class, 'payment_method_id');
    }

    public function getSlipAttribute()
    {
        return ($this->attributes['slip']) ? asset_url('offline-payment-files/' . $this->attributes['slip']) : asset('img/default-profile-3.png');
    }
}
