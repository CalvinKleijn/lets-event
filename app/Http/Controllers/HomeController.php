<?php

namespace App\Http\Controllers;

use App\Enums\RoleEnum;
use App\Role;
use Carbon\Carbon;
use App\Tournament;
use App\TournamentUserRole;
use DateTimeZone;
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

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function dashboard()
    {
        $user = Auth::user();

        $userTournaments = null;
        $tournamentsWhereUserIsNotParticipant = null;

        if (Auth::check()) {
            $participantRoleId = Role::where('name', RoleEnum::PARTICIPANT)->first()->id;
//            $organizerRoleId = Role::where('name', RoleEnum::ORGANIZER)->first()->id;

            // TODO: Check inbouwen om te kijken of id wel bestaat.
            // Ga na aan welke toernooien de gebruiker deelneemt.
            $tournamentIds = TournamentUserRole::where([
                'user_id' => Auth::id(),
                'role_id' => $participantRoleId
            ])->pluck('tournament_id')->toArray();

            $userTournaments = Tournament::whereIn('id', $tournamentIds)->get()->map(function ($tournament) {
                $parsedStartDateTime = Carbon::parse($tournament->start_date_time)->format('Y-m-d H:i');
                $tournament->start_date_time = $parsedStartDateTime;
                return $tournament;
            });

            $tournamentsWhereUserIsNotParticipant = TournamentUserRole::where('user_id', '!=', Auth::id())
                ->get()->unique()
                ->map(function ($tournamentUserRole) {
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

    /**
     * @param $id
     * @param $tourneyTime
     * @return \Illuminate\Http\RedirectResponse
     */
    public function leave($id, $tourneyTime)
    {
        $participantRoleId = Role::where('name', RoleEnum::PARTICIPANT)->first()->id;
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

            return redirect()->route('tournament.index')
                ->with('message', __('app_messages.tournament.leave.success'));
        }

        return redirect()->route('dashboard')
            ->with('message', __('app_messages.tournament.leave.already_started'));
    }

    public function Stats()
    {
        return view('statistics.index');
    }
}
