<?php

namespace App\Http\Controllers;

use App\Enums\RoleEnum;
use App\Role;
use App\Tournament;
use App\TournamentUserRole;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('welcome');
    }

    public function dashboard()
    {
        $userTournaments = null;

        if (Auth::check()) {
            $participantRoleId = Role::where('name', RoleEnum::PARTICIPANT)->first()->id;

            // TODO: Check inbouwen om te kijken of id wel bestaat.
            // Ga na aan welke toernooien de gebruiker deelneemt.
            $tournamentIds = TournamentUserRole::where([
                'user_id' => Auth::id(),
                'role_id' => $participantRoleId
            ])->pluck('tournament_id')->toArray();

            $userTournaments = Tournament::all()->whereIn('id', $tournamentIds);
        }

        return view('home')->with('tournaments', $userTournaments);
    }
}
