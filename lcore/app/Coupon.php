<?php

namespace App;

use App\Observers\CouponObserver;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'title',
        'code',
        'start_date_time',
        'uses_limit',
        'amount',
        'discount_type',
        'minimum_purchase_amount',
        'days',
        'description',
        'status',
        'end_date_time'
    ];
    protected static function boot()
    {
        parent::boot();
        static::observe(CouponObserver::class);
    }

    protected $dates = ['start_date_time', 'end_date_time', 'created_at'];

    public function customers()
    {
        return $this->hasMany(CouponUser::class, 'coupon_id');
    }


    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
