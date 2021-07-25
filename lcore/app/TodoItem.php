<?php

namespace App;

use App\Observers\TodoItemObserver;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class TodoItem extends Model
{
    protected static function boot()
    {
        parent::boot();

        static::observe(TodoItemObserver::class);

        static::addGlobalScope('company', function (Builder $builder) {
            if (company()) {
                $builder->where('company_id', company()->id);
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}
