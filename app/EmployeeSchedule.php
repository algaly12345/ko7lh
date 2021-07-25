<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Observers\EmployeeScheduleObserver;
use DateTime;

class EmployeeSchedule extends Model
{
    protected static function boot()
    {
        parent::boot();

        static::observe(EmployeeScheduleObserver::class);

        $company = company();

        static::addGlobalScope('company', function (Builder $builder) {
            if (company()) {
                $builder->where('company_id', company()->id);
            }
        });
    }

    protected $dates = ['start_time', 'end_time'];

    public function employee()
    {
        return $this->belongsTo(User::class);
    }

    public function getStartTimeAttribute($value) {
        if($this->validateDate($value)){
            return Carbon::createFromFormat('H:i:s', $value)->setTimezone(Company::first()->timezone);
        }
        return '';
    }

    public function getEndTimeAttribute($value) {
        if($this->validateDate($value)){
            return Carbon::createFromFormat('H:i:s', $value)->setTimezone(Company::first()->timezone);
        }
        return '';
    }

    function validateDate($format = 'H:i:s') {
        $d = DateTime::createFromFormat('H:i:s' , $format);
        return $d && $d->format($format);
    }

}

