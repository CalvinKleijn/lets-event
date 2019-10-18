<?php

namespace App\Http\Controllers;

use App\Admin;
use App\User;
use App\Role;
use App\Tournament;
use App\TournamentUserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;

class AdminPanelController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }
    
    public function index($tournament_id, Tournament $tournament)
    {
        
        $organizerRoleId = Role::all()->firstWhere('name', '=', 'organizer')->id;
        $participantRoleId = Role::all()->firstWhere('name', '=', 'participant')->id;
        $tournamentOrganizer = TournamentUserRole::where([
            'tournament_id' => $tournament_id,
            'user_id' => Auth::id(),
            'role_id' => $organizerRoleId
            ])->get();
            
            if (!$tournamentOrganizer->count()) {
                return redirect()->route('tournament.index')
                    ->withErrors(array('TournamentAdminAuthorizationFail' => 'Je bent geen beheerder van dit toernooi, je mag niet in de beheerder instellingen.'));
            }
            $tournamentParticipant = TournamentUserRole::where([
                'tournament_id' => $tournament_id,
                'role_id' => $participantRoleId
            ])->get();
            $users = User::all();
    
        return View('admin.index', compact('users', 'tournamentParticipant' , 'tournament_id'));
    }
    public function show($tournament_id, $user_id)
    {
        $user = User::find($user_id);
        return View('admin.edit', compact('tournament_id', 'user'));

    }
    public function create($tournament_id, $user_id)
    {

        $organizerRoleId = Role::all()->firstWhere('name' , '=', 'organizer')->id;

            TournamentUserRole::firstOrCreate([
                'tournament_id' => $tournament_id,
                'user_id' => $user_id,
                'role_id' => $organizerRoleId
            ]);
        
    

        $participantRoleId = Role::all()->firstWhere('name', '=', 'participant')->id;
        $tournamentParticipant = TournamentUserRole::where([
            'tournament_id' => $tournament_id,
            'role_id' => $participantRoleId
        ])->get();
            $users = User::all();

        return redirect('../../../tournament/')->with('message', 'Speler is mede-beheerder gemaakt!!');;        
    }
    public function edit($tournament_id, $user_id , Request $request)
    {
            
            $user = User::find($user_id);
            $user->name = $request->get('name');
            $user->email = $request->get('email');
            $user->phone_number = $request->get('phone_number');
            $user->save();

            return redirect('admin/{$tournament_id}')->with('message', 'Contact updated!');
    }

    public function destroy($tournament_id, $user_id)
    {
        // delete from tourney
        $tourneyPlayer = TournamentUserRole::where([
            'tournament_id' => $tournament_id,
            'user_id' => $user_id
        ])->delete();

        $users = User::all();

        $participantRoleId = Role::all()->firstWhere('name', '=', 'participant')->id;
        $tournamentParticipant = TournamentUserRole::where([
            'tournament_id' => $tournament_id,
            'role_id' => $participantRoleId
        ])->get();
    
        return redirect('../../../tournament/')->with('message', 'Speler is verwijderd van het toernooi!');;        
            
    }
}
