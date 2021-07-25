<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OfflinePaymentMethod extends Model
{
    protected $table = 'offline_payment_methods';
    protected $dates = ['created_at'];

    protected $guarded = ['id'];

    protected static function boot()
    {
        parent::boot();
    }

    public static function activeMethod(){
       return OfflinePaymentMethod::where('status', 'yes')->get();
    }
}
