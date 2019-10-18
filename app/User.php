<?php

namespace App;

use App\Enums\RoleEnum;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'phone_number', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function hasRoleInTournament($roleName, $tournamentId)
    {
        $role = Role::getByName($roleName);

        if (!$role) {
            return null;
        }

        return TournamentUserRole::where(['tournament_id' => $tournamentId, 'user_id' => $this->id, 'role_id' => $role->id])
            ->first();
    }
}
