<?php


namespace App\Http\Controllers;

use App\Enums\RoleEnum;
use App\Role;
use App\Tournament;
use App\TournamentUserRole;
use App\User;
use App\RefereeRequest;
use Carbon\Carbon;
use DateTimeZone;

class TournamentAdminController extends Controller
{
    /**
     * Methode die de show view rendert voor het admin paneel,
     * hierin bevinden zich de gebruikers van het toernooi met hun bijbehorende rol(len).
     * @param $tournamentId
     * @return void
     */
    public function show($tournamentId)
    {
        $tournament = Tournament::findOrFail($tournamentId);
        // Alle tournamentUserRole records voor dit toernooi.
        $tournamentUserRoles = TournamentUserRole::where('tournament_id', $tournament->id)->get();
        // Alle gebruikers van het toernooi (inclusief beheerders, deelnemers etc).
        $tournamentUsers = $tournamentUserRoles->unique('user_id')->map(function ($tournamentUserRole) use ($tournamentUserRoles) {
            // Vind de gebruiker waarover geitereerd wordt.
            $tournamentUser = User::find($tournamentUserRole->user_id);
            // Vraag de rollen op voor deze gebruiker uit de niet unieke collectie.
            $userRoles = $tournamentUserRoles->where('user_id', $tournamentUserRole->user_id)
                ->map(function ($user) {
                    // Return de gevonden role voor deze gebruiker.
                    return Role::find($user->role_id)->name;
                })->toArray();

            // TODO: Hardcoded rol namen statisch maken.
            $tournamentUser->isOrganizer = array_search(RoleEnum::ORGANIZER, $userRoles) > -1
                ? true : false;
            $tournamentUser->isParticipant = array_search(RoleEnum::PARTICIPANT, $userRoles) > -1
                ? true : false;
            $tournamentUser->isReferee = array_search(RoleEnum::REFEREE, $userRoles) > -1
                ? true : false;

            
            return $tournamentUser;
        });

        //haal alle scheidsrechters op van dit toernament met een status van pending
        //zoek de user van elke scheids en plaats daar de data in van users zodat je zijn naam enzv kan gebruiken.
        $refereeRequests = RefereeRequest::where([
            'tournament_id' => $tournamentId,
            'status' => 'pending'
        ])->get()->map(function($refereeRequest) {
            $user = User::find($refereeRequest->user_id);
            $refereeRequest->user = $user;
            return $refereeRequest;   
        });;

        //stuur de gebruiker naar admin/show met het tournament id, de spelers en de aanvragen voor scheidsrechters.
        return view('tournament.admin.show')->with([
            'tournament' => $tournament,
            'tournamentUsers' => $tournamentUsers,
            'requests' => $refereeRequests
        ]);
    }

    /**
     * Verwijder een gebruiker van een toernooi.
     * @param $tournamentId
     * @param $userId
     * @param $roleName
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteUser($tournamentId, $userId, $roleName)
    {
        $role = Role::getByName($roleName);

        if (!$role) {
            return redirect()->back()
                ->withErrors([
                    'De opgegeven rol kon niet gevonden worden.'
                ]);
        }

        if ($roleName === 'organizer') {
            $totalTournamentOrganizerCount = TournamentUserRole::where([
                'tournament_id' => $tournamentId,
                'role_id' => $role->id
            ])->count();

            if ($totalTournamentOrganizerCount < 2) {
                return redirect()->back()
                    ->withErrors([
                        'De beheerder kan niet verwijderd worden omdat er altijd minimaal één beheerder moet zijn.'
                    ]);
            }
        }
        // Vind de record in de TournamentUserRole table.
        if (!TournamentUserRole::where([
            'tournament_id' => $tournamentId,
            'user_id' => $userId,
            'role_id' => $role->id
        ])->delete()) {
            // Return een error view.
            return redirect()->back()
                ->withErrors([
                    'De gebruiker kon niet als beheerder van het toernooi verwijdert worden.'
                ]);
        };

        return redirect()->back()
            ->with('successMessage', 'De gebruiker is met success verwijderd als beheerder van het toernooi.');
    }

    /**
     * Voeg een gebruiker toe aan een toernooi.
     * @param $tournamentId
     * @param $userId
     * @param $roleName
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeUser($tournamentId, $userId, $roleName)
    {
        $role = Role::getByName($roleName);

        if (!$role) {
            return redirect()->back()
                ->withErrors(['
                De opgegeven rol kon niet gevonden worden.'
                ]);
        }

        // Check of de gebruiker de opgegeven rol al bevat voor het toernooi.
        $userHasRoleInTournament = TournamentUserRole::where([
            'tournament_id' => $tournamentId,
            'user_id' => $userId,
            'role_id' => $role->id
        ])->count();

        // Als de gebruiker de rol al heeft, redirect.
        if ($userHasRoleInTournament > 0) {
            return redirect()->back()->
            withErrors([
                'De opgegeven gebruiker heeft deze rol al.'
            ]);
        }

        if (!TournamentUserRole::create([
            'tournament_id' => $tournamentId,
            'user_id' => $userId,
            'role_id' => $role->id
        ])) {
            return redirect()->back()
                ->withErrors([
                    'De opgegeven rol kan niet worden toegevoegd aan de opgegeven gebruiker.'
                ]);
        };

        return redirect()->back()
            ->with('successMessage', 'De rol is aan de gebruiker toegevoegd.');
    }
    public function addReferee($tournamentId, $userId)
    {
        //zoek het id van de referee
        $refereeRoleId = Role::all()->firstWhere('name', '=', 'referee')->id;

        //kijk of deze user al een scheids is, als dat zo is stuur dan een foutcode
        $existingRecord = TournamentUserRole::where([
            'tournament_id' => $tournamentId,
            'user_id' => $userId,
            'role_id' => $refereeRoleId
        ]);
        
        if ($existingRecord->count()) {
            return redirect()
                ->route('tournament.index')
                ->withErrors(array('joinRefereeError' => 'Deze persoon is al een scheidsrechter!'));
        }
        
        // Maak een scheidsrechter rol aan in tournamentUserRole
        TournamentUserRole::create([
            'tournament_id' => $tournamentId,
            'user_id' => $userId,
            'role_id' => $refereeRoleId
        ]);

        //Verander de status van de refereeRequest naar accepted
        RefereeRequest::where([
            'tournament_id' => $tournamentId,
            'user_id' => $userId
        ])->update([
            'status' => 'accepted'
        ]);
        
        // Redirect terug naar de vorige pagina.
        return redirect()->back()->with('message', 'Scheidsrechter is succesvol aangemaakt.');
    }
    public function denyReferee($tournamentId, $userId)
    {
        //kijk of deze user al eens is afgewezen, als dat zo is stuur dan een foutcode
        $existingRecord = RefereeRequest::where([
            'tournament_id' => $tournamentId,
            'user_id' => $userId,
            'status' => 'denied'
        ]);
                
        if ($existingRecord->count()) {
            return redirect()
                ->route('tournament.index')
                ->withErrors(array('joinRefereeError' => 'Deze persoon is al een keer afgewezen!'));
        }

        //Verander de status van de request naar DENIED
        RefereeRequest::where([
            'tournament_id' => $tournamentId,
            'user_id' => $userId
        ])->update([
            'status' => 'denied'
        ]);
        
        // Redirect terug naar de vorige pagina.
        return redirect()->back()->with('message', 'De aanvraag voor scheidsrechter is afgewezen.');
    }

    public function showReferee($tournamentId)
    {
        //pak all referees van dit toernooi
        //loop door alle data en zoek bij elke referee zijn User ID
        //zet alle data van de user in de $referee->user variable
         $allReferees = RefereeRequest::where([
             'tournament_id' => $tournamentId
         ])->get()->map(function($referee) {
             $user = User::find($referee->user_id);
             $referee->user = $user;
             return $referee;
         });
         
         //stuur naar referee index met het tournament id en alle referees van dit toernooi.
        return view('tournament.admin.referee.index')->with([
            'tournamentId' => $tournamentId,
            'allReferees' => $allReferees
        ]);
    }

     /**
      * 
      * door middel van deze functie kan een organiser een toernooi handmatig starten
      */
    public function adminStartTournament( $tournamentId){
        // check of de user een admin is

        $time = Carbon::now(new DateTimeZone('Europe/Amsterdam'));
        $myTime = $time->toDateTimeString();

        // $role = Role::getByName($roleName); 


        Tournament::WHERE([
            'id' => $tournamentId
            ])->update([
            'start_date_time' => $myTime,
            'status' => 'Gestart'
        ]);

         // Redirect terug naar de vorige pagina.
         return redirect('tournament')->with('message', 'Het toernooi is gestart.');
    }

}
