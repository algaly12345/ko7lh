<?php

namespace App;

use App\Observers\GatewayAccountDetailObserver;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class GatewayAccountDetail extends Model
{
    protected $guarded = ['id'];

    protected $dates = [
        'link_expire_at'
    ];

    protected static function boot()
    {
        parent::boot();

        static::observe(GatewayAccountDetailObserver::class);

        static::addGlobalScope('company', function (Builder $builder) {
            if (company()) {
                $builder->where('gateway_account_details.company_id', company()->id);
            }
        });
    }

    public function company()
    {
        $this->belongsTo(Company::class);
    }

    public function scopeActiveConnectedOfGateway($query, $type)
    {
        return $query->whereAccountStatus('active')->whereConnectionStatus('connected')->whereGateway($type);
    }

    public function scopeOfStatus($query, $type)
    {
        return $query->whereAccountStatus($type);
    }

    public function scopeOfConnectionType($query, $type)
    {
        return $query->whereConnectionStatus($type);
    }

    public function scopeOfGateway($query, $type)
    {
        return $query->whereGateway($type);
    }
}


