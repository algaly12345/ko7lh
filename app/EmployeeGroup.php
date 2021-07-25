<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class EmployeeGroup extends Model
{
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('company', function (Builder $builder) {
            if (company()) {
                $builder->where('company_id', company()->id);
            }
        });
    }


    //------------------------------------ Attributes ---------------------------
    protected $guarded = ['id'];
    protected $table = 'employee_groups';


    //------------------------------------ Relations ----------------------------

    public function services() {
        return $this->hasMany(EmployeeGroupService::class, 'employee_groups_id', 'id', 'employee_group_services');
    }
}
