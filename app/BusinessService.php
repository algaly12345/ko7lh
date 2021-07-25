<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Observers\BusinessServiceObserver;

class BusinessService extends Model
{
    protected static function boot()
    {
        parent::boot();

        static::observe(BusinessServiceObserver::class);

        static::addGlobalScope('company', function (Builder $builder) {
            if (company()) {
                $builder->where('company_id', company()->id);
            }
        });
    }

    protected $appends =[
        'service_image_url',
        'service_detail_url',
        'converted_price',
        'converted_discounted_price',
        'formated_price',
        'formated_discounted_price',
        'discounted_price'
    ];

    public function getServiceImageUrlAttribute(){
        if(is_null($this->default_image)){
            return asset('img/no-image.jpg');
        }
        return asset_url('service/'.$this->id.'/'.$this->default_image);
    }

    public function getImageAttribute($value)
    {
        if (is_array(json_decode($value, true))) {
            return json_decode($value, true);
        }
        return $value;
    }

    public function getServiceDetailUrlAttribute() {
        return route('front.serviceDetail', ['categorySlug' => $this->category->slug, 'serviceSlug' => $this->slug]);
    }

    public function getDiscountedPriceAttribute(){
        if($this->discount > 0){
            if($this->discount_type == 'fixed'){
                return ($this->price - $this->discount);
            }
            elseif($this->discount_type == 'percent'){
                $discount = (($this->discount/100)*$this->price);
                return round(($this->price - $discount), 2);
            }
        }
        return $this->price;
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }


    public function scopeActiveCompany($query) {
        return $query->whereHas('company', function($q){
            $q->withoutGlobalScope('company')->active();
        });
    }

    public function category(){
        return $this->belongsTo(Category::class);
    }

    public function location(){
        return $this->belongsTo(Location::class);
    }

    public function bookingItems(){
        return $this->hasMany(BookingItem::class);
    }

    public function taxServices(){
        return $this->hasMany(ItemTax::class, 'service_id', 'id');
    }

    public function users() {
        return $this->belongsToMany(User::class);
    }


    public function company() {
        return $this->belongsTo(Company::class);
    }

    public function getConvertedPriceAttribute(){
        return  currency_converted_price($this->company_id,$this->price);
    }

    public function getConvertedDiscountedPriceAttribute(){
        return  currency_converted_price($this->company_id,$this->discounted_price);
    }

    public function getFormatedPriceAttribute(){
        return  currency_formatter($this->converted_price);
    }

    public function getFormatedDiscountedPriceAttribute(){
        return  currency_formatter($this->converted_discounted_price);
    }

    public function getTotalTaxPercentAttribute(){
        if (!$this->taxServices) {
            return 0;
        }
        $taxPercent =0;
        foreach ($this->taxServices as $key => $tax) {
            $taxPercent += $tax->tax->percent;
        }
        return $taxPercent;
    }
}
