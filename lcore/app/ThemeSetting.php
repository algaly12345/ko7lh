<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ThemeSetting extends Model
{
    protected $guarded = ['id'];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('company', function (Builder $builder) {
            if (company()) {
                $builder->where('theme_settings.company_id', company()->id);
            }
        });
    }

    public function scopeOfSuperAdminRole($query)
    {
        return $query->whereRole('superadmin');
    }

    public function scopeOfAdminRole($query)
    {
        return $query->whereRole('administrator');
    }

    public function scopeOfCustomerRole($query)
    {
        return $query->whereRole('customer');
    }
}
