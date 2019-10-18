<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Tournament extends Model
{
    protected $fillable = ['name', 'description','start_date_time'];

    public function getUsersByRole($roleId)
    {
        return TournamentUserRole::where([
            'user_id' => $this->id,
            'role_id' => $roleId
        ])->get();
    }

    public function getStartDateTimeFormattedAttribute()
    {
        return Carbon::parse($this->start_date_time)->format('Y-m-d\TH:i');
    }
}
