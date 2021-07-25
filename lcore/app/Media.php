<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Observers\frontSliderObserver;

class Media extends Model
{

    protected static function boot()
    {
        parent::boot();

        static::observe(frontSliderObserver::class);
    }

    protected $appends = [
        'image_url'
    ];

    public function getImageUrlAttribute()
    {
        if (is_null($this->image)) {
            return asset('img/default-avatar-user.png');
        }
        return asset_url('sliders/' . $this->image);
    }
}
