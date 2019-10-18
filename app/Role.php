<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    public static function getByName($roleName)
    {
        $role = self::all()->firstWhere('name', $roleName);
        return (($role == null) ? null : $role);
    }
}
