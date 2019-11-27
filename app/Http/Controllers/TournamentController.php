<?php

namespace App\Http\Controllers;

use App\Enums\RoleEnum;
use App\RefereeRequest;
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

    public function index(Request $request)
    {
        $pageNumber = 1;
        $requestPageNumber = $request->get('pageNumber');

        if ($requestPageNumber && is_numeric($requestPageNumber) && $requestPageNumber > 0) {
            $pageNumber = $requestPageNumber;
        }

        $orderToSortBy = 'desc';
        $requestOrderToSortBy = $request->get('orderToSortBy');

        if ($requestOrderToSortBy && ($requestOrderToSortBy === 'asc' || $requestOrderToSortBy === 'desc')) {
            $orderToSortBy = $requestOrderToSortBy;
        }

        $columnToSortBy = 'start_date_time';
        $requestColumnToSortBy = $request->get('columnToSortBy');
        if ($requestColumnToSortBy && (array_search($requestColumnToSortBy,
                    ['id', 'name', 'description', 'start_date_time','status']) >= 0)) {
            $columnToSortBy = $requestColumnToSortBy;
        }

        $orderedTournaments = Tournament::orderBy($columnToSortBy, $orderToSortBy)->get();
        $pagedTournaments = $orderedTournaments->forPage($pageNumber, 10);

        $authUser = User::findOrFail(Auth::id());

        $mappedTournaments = $pagedTournaments->map(function ($tournament) use ($authUser) {
            $userHasOrganizerRoleForTournament = $authUser
                ->hasRoleInTournament(RoleEnum::ORGANIZER, $tournament->id);
            $tournament['isOrganizer'] = !!$userHasOrganizerRoleForTournament;

            $userHasParticipantRoleForTournament = $authUser
                ->hasRoleInTournament(RoleEnum::PARTICIPANT, $tournament->id);
            $tournament['isParticipant'] = !!$userHasParticipantRoleForTournament;

            $userHasRefereeRoleForTournament = $authUser
                ->hasRoleInTournament(RoleEnum::REFEREE, $tournament->id);
            $tournament['isReferee'] = !!$userHasRefereeRoleForTournament;

            return $tournament;
        });

        $lastPageNumber = ceil($orderedTournaments->count() / 10);

        if ($pageNumber > $lastPageNumber) {
            $pageNumber = $lastPageNumber;
        }

        $oppositeOrderToSortBy = ($orderToSortBy == 'asc') ? 'desc' : 'asc';

        return view('tournament.index')
            ->with('tournaments', $mappedTournaments)
            ->with('pageNumber', $pageNumber)
            ->with('lastPageNumber', $lastPageNumber)
            ->with('columnToSortBy', $columnToSortBy)
            ->with('orderToSortBy', $orderToSortBy)
            ->with('newOrderToSortBy', $oppositeOrderToSortBy);
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
            'start-date-time' => 'required|date_format:Y-m-d\TH:i',
            'status' => 'string|max:50',
        ]);

        if ($request->input('start-date-time') < now()) {
            return redirect()->back()
                ->withErrors([__('app_messages.tournament.error_start_date_in_past')])
                ->withInput($request->all());
        }

        $createdTournament = Tournament::create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'start_date_time' => $request->input('start-date-time'),
            'status' => 'Upcoming'
        ]);

        $organizerRoleId = Role::getByName(RoleEnum::ORGANIZER)->id;

        TournamentUserRole::create([
            'tournament_id' => $createdTournament->id,
            'user_id' => Auth::id(),
            'role_id' => $organizerRoleId
        ]);

        return redirect()->route('tournament.index')->with('message', __('app_messages.tournament.store.success'));
    }

    /**
     * Display the specified resource.
     *
     * @param Tournament $tournament
     * @return \Illuminate\Http\Response
     */
    public function show(Tournament $tournament)
    {
//        $participantRoleId = RoleEnum::getByName('participant');
        return view('tournament.show')
            ->with('tournament', $tournament);
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
            return redirect()->route('tournament.index');
        }

        // Check if logged in user is an organiser for the tournament
        $organizerRoleId = Role::where('name', RoleEnum::ORGANIZER)->first()->id;

        $tournamentOrganizer = TournamentUserRole::where([
            'tournament_id' => $tournament->id,
            'user_id' => Auth::id(),
            'role_id' => $organizerRoleId
        ])->get();

        if (!$tournamentOrganizer->count()) {
            return redirect()->route('tournament.index')
                ->withErrors([__('app_messages.tournament.not_authorized')]);
        }

        return view('tournament.edit')
            ->with('tournament', $tournament);
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
            'start-date-time' => 'required|date_format:Y-m-d\TH:i',
            'status' => 'string|max:50'
        ]);

        if ($request->input('start-date-time') < now()) {
            return redirect()->back()->withErrors([__('app_messages.tournament.error_start_date_in_past')]);
        }

        $tournament->update([
            'name' => $request->get('name'),
            'description' => $request->get('description'),
            'start_date_time' => $request->get('start-date-time'),
            'status' => 'Upcoming'
        ]);

        return redirect()->route('tournament.index')
            ->with('message', __('app_messages.tournament.update.success'));
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
                ->withErrors([__('app_messages.tournament.destroy.tournament_not_available')]);
        }

        $organizerRole = Role::getByName(RoleEnum::ORGANIZER);

        if (!$organizerRole) {
            return redirect()->back();
        }

        $tournamentOrganizer = TournamentUserRole::where([
            'tournament_id' => $tournament->id,
            'user_id' => Auth::id(),
            'role_id' => $organizerRole->id
        ])->get();

        if (!$tournamentOrganizer->count()) {
            return redirect()->back()
                ->withErrors([__('app_messages.tournament.not_authorized')]);
        }

        if (!Tournament::destroy($tournament->id)) {
            return redirect()->back()
                ->withErrors([__('app_messages.tournament.destroy.error_on_destroy')]);
        };

        // Delete alleen de relaties als het destroyen van het toernooi goed is gegaan.
        if (!TournamentUserRole::where('tournament_id', $tournament->id)->delete()) {
            return redirect()->back()
                ->withErrors([__('app_messages.tournament.destroy.error_on_destroy')]);
        }

        return redirect()->back()->with('message', __('app_messages.tournament.destroy.success'));
    }

    /**
     * Deze methode zorgt ervoor dat er een record aangemaakt kan worden in de TournamentUserRole tabel.
     * @param $tournamentId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function join($tournamentId)
    {
        // Vraag het id van de rol op van een participant (deelnemer).
        $participantRoleId = Role::where('name', RoleEnum::PARTICIPANT)->first()->id;

        $existingRecord = TournamentUserRole::where([
            'tournament_id' => $tournamentId,
            'user_id' => Auth::id(),
            'role_id' => $participantRoleId
        ]);

        if ($existingRecord->count()) {
            return redirect()
                ->route('tournament.index')
                ->withErrors(array('joinParticipantError' => __('app_messages.tournament.join.error_already_participant')));
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

    /**
     * //TODO: Dubbele code? Zie HomeController@leave.
     * @param $tournamentId
     * @param $tournamentStartDateTime
     * @return \Illuminate\Http\RedirectResponse
     */
    public function leave($tournamentId, $tournamentStartDateTime)
    {
        $participantRoleId = Role::where('name', RoleEnum::PARTICIPANT)->first()->id;
        $time = Carbon::now(new DateTimeZone('Europe/Amsterdam'));
        $myTime = $time->toDateTimeString();

        //kijk of de current time kleiner is dan de tijd waarop het toernooi start
        //als dit zo is dan wordt de persoon verwijderd
        //als dit niet zo is wordt hij redirect terug naar de pagina met een message
        if ($myTime < $tournamentStartDateTime) {
            TournamentUserRole::where([
                'tournament_id' => $tournamentId,
                'user_id' => Auth::id(),
                'role_id' => $participantRoleId
            ])->delete();

            return redirect()->back()
                ->with('message', __('app_messages.tournament.leave.success'));
        }

        return redirect()->back()
            ->withErrors([__('app_messages.tournament.leave.already_started')]);
    }

    /**
     * @param $tournamentId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function requestReferee($tournamentId)
    {
        //kijk of deze user al eens heeft gevraagd om scheids te worden, als dat zo is stuur dan een foutcode
        $existingRecord = RefereeRequest::where([
            'tournament_id' => $tournamentId,
            'user_id' => Auth::id()
        ]);

        if ($existingRecord->count()) {
            return redirect()
                ->route('tournament.index')
                ->withErrors(array('joinRefereeError' => __('app_messages.tournament.request_referee.already_requested')));
        }

        // Maak een request aan in de referee request table
        RefereeRequest::create([
            'tournament_id' => $tournamentId,
            'user_id' => Auth::id(),
            'status' => 'pending'
        ]);

        // Redirect terug naar de vorige pagina.
        return redirect()->route('tournament.index');
    }

    /**
     * @param $tournamentId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteReferee($tournamentId)
    {
        //zoek het id van de referee
        $refereeRoleId = Role::where('name', '=', 'referee')->first()->id;

        //kijk of deze user al een scheids is, als dat zo is stuur dan een foutcode
        $existingRecord = TournamentUserRole::where([
            'tournament_id' => $tournamentId,
            'user_id' => Auth::id(),
            'role_id' => $refereeRoleId
        ]);

        if (!$existingRecord->count()) {
            return redirect()
                ->route('tournament.index');
        }
        TournamentUserRole::where([
            'tournament_id' => $tournamentId,
            'user_id' => Auth::id(),
            'role_id' => $refereeRoleId
        ])->delete();

        RefereeRequest::where([
            'tournament_id' => $tournamentId,
            'user_id' => Auth::id(),
        ])->delete();
        return redirect()->route('tournament.index');

    }
}
