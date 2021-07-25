<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ModuleSetting extends Model
{
    protected static function boot() {
        parent::boot();

        static::addGlobalScope('company', function (Builder $builder) {
            if (company()) {
                $builder->where('company_id', company()->id);
            }
        });
    }


    protected $fillable = [ 'company_id', 'module_name', 'status', 'type'];

} /* end of class */
