<?php

namespace App\Http\Controllers;

use App\Role;
use App\Tournament;
use App\TournamentUserRole;
use App\User;
use Carbon\Carbon;
use DateTimeZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TournamentController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     *
     *
     */
    public function index()
    {
        $currentUser = User::find(Auth::id());

        $tournaments = Tournament::all()->map(function ($tournament) use ($currentUser) {
            $tournament->isOrganizer = ($currentUser->hasRoleInTournament($tournament->id, 'organizer')->count() > 0)
                ? true : false;
            $tournament->isParticipant = ($currentUser->hasRoleInTournament($tournament->id, 'participant')->count() > 0)
                ? true : false;
            return $tournament;
        });

        return view('tournament.index')
            ->with('currentUser', $currentUser)
            ->with('tournaments', $tournaments);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('tournament.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50',
            'description' => 'required|string|max:255',
            'start-date-time' => 'required|date_format:Y-m-d\TH:i'
        ]);

        if ($request->input('start-date-time') < now()) {
            return redirect()->back()
                ->withErrors(['De ingevulde startdatum ligt in het verleden.'])
                ->withInput($request->all());
        }

        $createdTournament = Tournament::create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'start_date_time' => $request->input('start-date-time')
        ]);

        $organizerRoleId = Role::getByName('organizer')->id;

        TournamentUserRole::create([
            'tournament_id' => $createdTournament->id,
            'user_id' => Auth::id(),
            'role_id' => $organizerRoleId
        ]);

        return redirect()->route('tournament.index')->with('message', 'Je hebt met succes een toernooi aangemaakt!');
    }

    /**
     * Display the specified resource.
     *
     * @param Tournament $tournament
     * @return \Illuminate\Http\Response
     */
    public function show(Tournament $tournament)
    {
//        $participantRoleId = Role::getByName('participant');
        return view('tournament.show', compact('tournament'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Tournament $tournament
     * @return \Illuminate\Http\Response
     */
    public function edit(Tournament $tournament)
    {
        $organizerRole = Role::getByName('organizer');

        if (!$organizerRole) {
            return redirect()->route('tournament.index')
                ->withErrors(['Er bestaat geen toernooi administrator rol.']);
        }

        // Check if logged in user is an organiser for the tournament
        $organizerRoleId = Role::all()->firstWhere('name', '=', 'organizer')->id;

        $tournamentOrganizer = TournamentUserRole::where([
            'tournament_id' => $tournament->id,
            'user_id' => Auth::id(),
            'role_id' => $organizerRoleId
        ])->get();

        if (!$tournamentOrganizer->count()) {
            return redirect()->route('tournament.index')
                ->withErrors(['Je bent geen beheerder van dit toernooi, je mag dit toernooi niet editten.']);
        }

        return view('tournament.edit')->with('tournament', $tournament);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Tournament $tournament
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Tournament $tournament)
    {
        $request->validate([
            'name' => 'required|string|max:50',
            'description' => 'required|string|max:255',
            'start-date-time' => 'required|date_format:Y-m-d\TH:i'
        ]);

        if ($request->input('start-date-time') < now()) {
            return redirect()->back()->withErrors(['De ingevulde startdatum ligt in het verleden.']);
        }

        $tournament->update([
            'name' => $request->get('name'),
            'description' => $request->get('description'),
            'start_date_time' => $request->get('start-date-time')
        ]);

        return redirect()->route('tournament.index')->with('message', 'Je hebt met succes een toernooi gewijzigd!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Tournament $tournament
     * @return \Illuminate\Http\Response
     */
    public function destroy(Tournament $tournament)
    {
        if (!$tournament) {
            return redirect()->back()
                ->withErrors(['Het opgevraagde toernooi is al verlopen of niet meer beschikbaar.'
            ]);
        }

        $organizerRole = Role::getByName('organizer');

        if (!$organizerRole) {
            return redirect()->back()
                ->withErrors(['Er bestaat geen toernooi administrator rol.']);
        }

        // Check if logged in user is an organiser for the tournament
        $organizerRoleId = Role::all()->firstWhere('name', '=', 'organizer')->id;

        $tournamentOrganizer = TournamentUserRole::where([
            'tournament_id' => $tournament->id,
            'user_id' => Auth::id(),
            'role_id' => $organizerRoleId
        ])->get();

        if (!$tournamentOrganizer->count()) {
            return redirect()->back()
                ->withErrors(['Je bent geen beheerder van dit toernooi, je mag dit toernooi dus niet verwijderen.']);
        }

        if (!Tournament::destroy($tournament->id)) {
            return redirect()->back()
                ->withErrors(['Er is iets fout gegaan bij het verwijderen van het toernooi.']);
        };

        // Delete alleen de relaties als het destroyen van het toernooi goed is gegaan.
        if (!TournamentUserRole::where('tournament_id', $tournament->id)->delete()) {
            return redirect()->back()
                ->withErrors(['Er is iets fout gegaan bij het verwijderen van de relaties binnen het toernooi.']);
        }

        return redirect()->back()->with('message', 'Je hebt het toernooi met success verwijderd.');
    }

    /**
     * Deze methode zorgt ervoor dat er een record aangemaakt kan worden in de TournamentUserRole tabel.
     * @param $tournamentId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function join($tournamentId)
    {
        // Vraag het id van de rol op van een participant (deelnemer).
        $participantRoleId = Role::all()->firstWhere('name', '=', 'participant')->id;

        $existingRecord = TournamentUserRole::where([
            'tournament_id' => $tournamentId,
            'user_id' => Auth::id(),
            'role_id' => $participantRoleId
        ]);

        if ($existingRecord->count()) {
            return redirect()
                ->route('tournament.index')
                ->withErrors(array('joinParticipantError' => 'Je neemt al deel aan dit toernooi!'));
        }

        // Maak een nieuwe record aan in de TournamentUserRole table.
        TournamentUserRole::create([
            'tournament_id' => $tournamentId,
            'user_id' => Auth::id(),
            'role_id' => $participantRoleId
        ]);

        // Redirect terug naar de vorige pagina.
        return redirect()->route('dashboard');
    }

    public function leave($tournamentId, $tournamentStartDateTime)
    {
        $participantRoleId = Role::all()->firstWhere('name', 'participant')->id;
        $time = Carbon::now(new DateTimeZone('Europe/Amsterdam'));
        $mytime = $time->toDateTimeString();

        //kijk of de current time kleiner is dan de tijd waarop het toernooi start
        //als dit zo is dan wordt de persoon verwijderd
        //als dit niet zo is wordt hij redirect terug naar de pagina met een message
        if ($mytime < $tournamentStartDateTime) {
            TournamentUserRole::where([
                'tournament_id' => $tournamentId,
                'user_id' => Auth::id(),
                'role_id' => $participantRoleId
            ])->delete();

            return redirect()->back()
                ->with('message', 'Je hebt het toernooi verlaten.');
        }

        return redirect()->back()
            ->withErrors(['Je kan het toernooi niet verlaten omdat het al begonnen is.']);
    }
}
