<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RefereeRequest extends Model
{
    protected $fillable = ['tournament_id', 'user_id', 'status'];
    
}
