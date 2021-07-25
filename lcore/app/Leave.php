<?php

namespace App;

use Carbon\Carbon;
use DateTime;
use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    public function employee()
    {
        return $this->belongsTo(User::class);
    }
}
