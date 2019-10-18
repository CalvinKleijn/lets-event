<?php

namespace App\Http\Controllers;

use App\User;
use App\Role;
use Carbon\Carbon;
use App\Tournament;
use App\TournamentUserRole;
use DateTimeZone;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index() {
        
        return view('welcome');
    }

    public function dashboard()
    {
        $user = Auth::user();
        
        $userTournaments = null;
        $tournamentsWhereUserIsNotParticipant = null;

        if (Auth::check()) {
            $participantRoleId = Role::all()->firstWhere('name', '=', 'participant')->id;
            $organizerRoleId = Role::all()->firstWhere('name', '=', 'organizer')->id;

            // TODO: Check inbouwen om te kijken of id wel bestaat.
            // Ga na aan welke toernooien de gebruiker deelneemt.
            $tournamentIds = TournamentUserRole::where([
                'user_id' => Auth::id(),
                'role_id' => $participantRoleId
            ])->pluck('tournament_id')->toArray();

            $userTournaments = Tournament::all()->whereIn('id', $tournamentIds)->map(function($tournament) {
                $parsedStartDateTime = Carbon::parse($tournament->start_date_time)->format('Y-m-d H:i');
                $tournament->start_date_time = $parsedStartDateTime;
                return $tournament;
            });



            $tournamentsWhereUserIsNotParticipant = TournamentUserRole::where('user_id', '!=', Auth::id())
            ->get()->unique()
            ->map(function($tournamentUserRole) {
                return Tournament::find($tournamentUserRole->tournament_id);
            });

        }


        // users doesnt get passed to view
        return view('home')->with([
            'tournaments' => $userTournaments,
            'tournamentsWhereUserIsNotParticipant' => $tournamentsWhereUserIsNotParticipant, 
            'user' => $user
        ]);
    }

    public function leave($id, $tourneyTime)
    {
        $participantRoleId = Role::all()->firstWhere('name', '=', 'participant')->id;
        $time = Carbon::now(new DateTimeZone('Europe/Amsterdam'));
        $mytime = $time->toDateTimeString();

            //kijk of de current time kleiner is dan de tijd waarop het toernooi start
            //als dit zo is dan wordt de persoon verwijderd 
            //als dit niet zo is wordt hij redirect terug naar de pagina met een message
            if ($mytime < $tourneyTime) {
        TournamentUserRole::where([
            'tournament_id' => $id,
            'user_id' => Auth::id(),
            'role_id' => $participantRoleId
        ])->delete();
        return redirect()->route('tournament.index')->with('message', 'Je hebt met succes het toernooi verlaten!');

        }
        else{
        return redirect()->route('dashboard')->with('message', 'Je kan het toernooi niet verlaten omdat het al begonnen is.');     
        }
    }

    public function Stats() {
        return view('statistics.index');
    }
}
