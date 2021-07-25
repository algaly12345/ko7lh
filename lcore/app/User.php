<?php

namespace App;

use App\Observers\UserObserver;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laratrust\Traits\LaratrustUserTrait;
use Laravel\Cashier\Billable;

class User extends Authenticatable
{
    use LaratrustUserTrait, Notifiable;

    protected static function boot()
    {
        parent::boot();

        static::observe(UserObserver::class);

        $company = company();

        $role = Role::withoutGlobalScopes()->select('name')->get();
        foreach($role as $roles){
            if($roles->name != 'customer') {
                static::addGlobalScope('company', function (Builder $builder) use ($company){
                    if ($company) {
                        $builder->where('company_id', $company->id);
                    }
                });
            }
        }
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'company_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $appends = [
        'user_image_url', 'mobile_with_code', 'formatted_mobile'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    public function getUserImageUrlAttribute()
    {
        if (is_null($this->image)) {
            return asset('img/default-avatar-user.png');
        }
        return asset_url('avatar/' . $this->image);
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }

    public function completedBookings()
    {
        return $this->hasMany(Booking::class, 'user_id')->where('bookings.status', 'completed');
    }

    public function employeeGroup() {
        return $this->belongsTo(EmployeeGroup::class, 'group_id');
    }

    public function company() {
        return $this->belongsTo(Company::class);
    }


    public function leave()
    {
        return $this->hasMany('App\Leave', 'employee_id', 'id');
    }

    public function todoItems()
    {
        return $this->hasMany(TodoItem::class);
    }

    public function getRoleAttribute()
    {
        return $this->roles->first();
    }

    public function getMobileWithCodeAttribute()
    {
        return substr($this->calling_code, 1).$this->mobile;
    }

    public function getFormattedMobileAttribute()
    {
        if (!$this->calling_code) {
            return $this->mobile;
        }
        return $this->calling_code.'-'.$this->mobile;
    }

    public function routeNotificationForNexmo($notification)
    {
        return $this->mobile_with_code;
    }

    public function googleAccount()
    {
        return $this->hasOne(GoogleAccount::class);
    }

    public function getIsSuperadminAttribute()
    {
        return $this->hasRole('superadmin');
    }

    public function getIsAdminAttribute()
    {
        return $this->hasRole('administrator');
    }

    public function getIsEmployeeAttribute()
    {
        return $this->hasRole('employee');
    }

    public function getIsCustomerAttribute()
    {
        if ($this->roles()->withoutGlobalScopes()->where('roles.name', 'customer')->count() > 0) {
            return true;
        }
        return false;
    }

    public function scopeAllAdministrators()
    {
        return $this->whereHas('roles', function ($query) {
            $query->where('name', 'administrator');
        });
    }

    public function scopeAllCustomers()
    {
        return $this->whereHas('roles', function ($query) {
            $query->where('name', 'customer')->withoutGlobalScopes();
        });
    }

    public function scopeOtherThanCustomers()
    {
        return $this->whereHas('roles', function ($query) {
            $query->whereNotIn('name', ['superadmin', 'customer']);
        });
    }

    public function scopeAllEmployees()
    {
        return $this->whereHas('roles', function ($query) {
            $query->where('name', 'employee');
        });
    }

    public function bookings() {
        return $this->belongsToMany(Booking::class);
    }

    public function customerBookings() {
        return $this->hasMany(Booking::class);
    }

    public function services() {
        return $this->belongsToMany(BusinessService::class);
    }

    public function userBookingCount($date) {
        return Booking::where('user_id', $this->id)->whereDate('created_at', $date)->get()->count();
    }

    public function getModulesAttribute()
    {
        return ModuleSetting::select('module_name')->where(['status'=>'active', 'type'=>$this->role->name])->get()->map(function ($item, $key) { return $item->module_name; })->toArray();
    }

}
