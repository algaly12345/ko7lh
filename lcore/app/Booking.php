<?php

namespace App;

use App\Observers\BookingObserver;
use Carbon\Carbon;
use DateTime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $dates = ['date_time'];
    protected $guarded = ['id'];
    protected static function boot()
    {
        parent::boot();

        static::observe(BookingObserver::class);

        static::addGlobalScope('company', function (Builder $builder) {
            if (company()) {
                $builder->where('bookings.company_id', company()->id);
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withoutGlobalScope('company');
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function deal()
    {
        return $this->belongsTo(Deal::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function employees()
    {
        return $this->belongsToMany(User::class, 'employee_id');
    }

    public function completedPayment()
    {
        return $this->hasOne(Payment::class)->where('status', 'completed')->whereNotNull('paid_on');
    }

    public function items()
    {
        return $this->hasMany(BookingItem::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class)->where('status', 'completed')->whereNotNull('paid_on');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function setDateTimeAttribute($value)
    {
        $this->attributes['date_time'] = Carbon::parse($value, Company::first()->timezone)->setTimezone('UTC');
    }

    public function getDateTimeAttribute($value)
    {
        if ($this->validateDate($value)) {
            return Carbon::createFromFormat('Y-m-d H:i:s', $value)->setTimezone(Company::first()->timezone);
        }
        return '';
    }

    public function getUtcDateTimeAttribute()
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $this->attributes['date_time']);
    }

    //----------------------------------- Validations -------------------

    function validateDate($date, $format = 'Y-m-d H:i:s')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }
    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }



    public function getConvertedOriginalAmountAttribute(){
        return  currency_converted_price($this->company_id,$this->original_amount);
    }

    public function getConvertedProductAmountAttribute(){
        return  currency_converted_price($this->company_id,$this->product_amount);
    }

    public function getConvertedDiscountAttribute(){
        return  currency_converted_price($this->company_id,$this->discount);
    }

    public function getConvertedCouponDiscountAttribute(){
        return  currency_converted_price($this->company_id,$this->coupon_discount);
    }

    public function getConvertedTaxAmountAttribute(){
        return  currency_converted_price($this->company_id,$this->tax_amount);
    }

    public function getConvertedAmountToPayAttribute(){
        return  currency_converted_price($this->company_id,$this->amount_to_pay);
    }


    public function getFormatedOriginalAmountAttribute(){
        return  currency_formatter($this->converted_original_amount);
    }

    public function getFormatedProductAmountAttribute(){
        return  currency_formatter($this->converted_product_amount);
    }

    public function getFormatedDiscountAttribute(){
        return  currency_formatter($this->converted_discount);
    }

    public function getFormatedCouponDiscountAttribute(){
        return  currency_formatter($this->converted_coupon_discount);
    }

    public function getFormatedTaxAmountAttribute(){
        return  currency_formatter($this->converted_tax_amount);
    }

    public function getFormatedAmountToPayAttribute(){
        return  currency_formatter($this->converted_amount_to_pay);
    }
} /* end of class */
