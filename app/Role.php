<?php

namespace App;

use App\Observers\RoleObserver;
use Illuminate\Database\Eloquent\Builder;
use Laratrust\Models\LaratrustRole;

class Role extends LaratrustRole
{
    protected $guarded = ['id'];

    protected static function boot()
    {
        parent::boot();

        static::observe(RoleObserver::class);

        static::addGlobalScope('withoutCustomerRole', function (Builder $builder) {
            if (company()) {
                $builder->whereNotIn('name', ['customer', 'superadmin']);
            }
        });

        static::addGlobalScope('company', function (Builder $builder) {
            if (company()) {
                $builder->where('company_id', company()->id);
            }
        });
    }

    public function getMemberCountAttribute()
    {
        return $this->users->count();
    }
}
