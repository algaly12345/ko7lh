<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class VendorPage extends Model
{
    protected $guarded = ['id'];
    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope('company', function (Builder $builder) {
            if (company()) {
                $builder->where('company_id', company()->id);
            }
        });
    }
    protected $appends =[ 'images', 'photos_without_default_image'];
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function getOgImageAttribute($og_image){
        if(is_null($og_image)){
            return $og_image;
        }
        return asset_url('vendor-page/'.$og_image);
    }

    public function getPhotosAttribute($value)
    {
        if (is_array(json_decode($value, true))) {
            return json_decode($value, true);
        }
        return $value;
    }
    public function getPhotosWithoutDefaultImageAttribute()
    {
        $photos = $this->photos;
        if($photos){
            return array_merge( array_diff( $photos, [$this->default_image] ));
        }
        return [];
    }
    public function getImagesAttribute()
    {
        $images = [];
        if ($this->photos) {
            foreach ($this->photos as $image) {
                $reqImage['name'] = $image;
                $reqImage['size'] = filesize(public_path('/user-uploads/vendor-page/'.$this->id.'/'.$image));
                $reqImage['type'] = mime_content_type(public_path('/user-uploads/vendor-page/'.$this->id.'/'.$image));
                $images[] = $reqImage;
            }
        }
        return  json_encode($images);
    }
}
