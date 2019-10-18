<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    public static function getByName($roleName)
    {
        return self::where('name', $roleName)->first();
    }
}
