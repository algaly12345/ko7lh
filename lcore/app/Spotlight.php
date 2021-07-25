<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Spotlight extends Model
{
    protected $table = 'spotlight';

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function deal()
    {
        return $this->belongsTo(Deal::class);
    }

    public function scopeActiveCompany($query) {
        return $query->whereHas('company', function($q){
            $q->withoutGlobalScope('company')->active();
        });
    }
}
