<?php

namespace App;

use App\Observers\BookingItemObserver;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class BookingItem extends Model
{
    protected $guarded = ['id'];

    protected static function boot()
    {
        parent::boot();

        static::observe(BookingItemObserver::class);

        static::addGlobalScope('company', function (Builder $builder) {
            if (company()) {
                $builder->where('company_id', company()->id);
            }
        });
    }

    public function businessService(){
        return $this->belongsTo(BusinessService::class);
    }

    public function deal(){
        return $this->belongsTo(Deal::class);
    }

    public function product(){
        return $this->belongsTo(Product::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function getConvertedUnitPriceAttribute(){
        return  currency_converted_price($this->company_id,$this->unit_price);
    }

    public function getFormatedUnitPriceAttribute(){
        return  currency_formatter($this->converted_unit_price);
    }
}
