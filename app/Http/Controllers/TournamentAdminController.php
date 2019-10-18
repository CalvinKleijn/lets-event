<?php


namespace App\Http\Controllers;

use App\Enums\RoleEnum;
use App\Role;
use App\Tournament;
use App\TournamentUserRole;
use App\User;

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

            return $tournamentUser;
        });

        return view('tournament.admin.show')->with([
            'tournament' => $tournament,
            'tournamentUsers' => $tournamentUsers
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
}
