@extends('layouts.app')

@section('content')

    @if(session()->has('deleteOrganizerSuccess'))
        <div class="container alert alert-success">
            <b class="text-white">{{ session()->get('deleteOrganizerSuccess') }}</b>
        </div>
    @endif

    @if($errors->any())
        <div class=" container alert alert-danger">
            @foreach ($errors->all() as $error)
                <b class="text-white">{{ $error }}</b>
            @endforeach
        </div>
    @endif

    <div class="container">
        <table class="table ">
            <h1 class="text-left">Toernooi beheerders en gebruikers</h1>
            <tr>
                <th class="col">Naam</th>
                <th class="col">E-mail</th>
                <th class="col">Telefoonummer</th>
                <th class="col">Beheerder</th>
                <th class="col">Deelnemer</th>
                <th class="col">Scheidsrechter</th>
                <th class="col" colspan="2">Instellingen</th>
            </tr>
            @foreach ($tournamentUsers as $tournamentUser)
                <tr>
                    <td>{{ $tournamentUser->name }}</td>
                    <td>{{ $tournamentUser->email }}</td>
                    <td>{{ $tournamentUser->phone_number }}</td>
                    <td>{{ $tournamentUser->isOrganizer ? 'Ja' : 'Nee' }}</td>
                    <td>{{ $tournamentUser->isParticipant ? 'Ja' : 'Nee' }}</td>
                    <td>{{ $tournamentUser->isReferee ? 'Ja' : 'Nee' }}</td>
                    <td>
                        @if ($tournamentUser->isOrganizer)
                            <form action="{{ action('TournamentAdminController@deleteUser', [
                            'tournamentId' => $tournament->id,
                            'userId' => $tournamentUser->id,
                            'roleName' => 'organizer'
                        ]) }}" method="POST">
                                @method('DELETE')
                                @csrf()
                                <button type="submit" class="btn-custom text-danger">
                                    Verwijder beheerder
                                </button>
                            </form>
                        @else
                            <form action="{{ action('TournamentAdminController@storeUser', [
                            'tournamentId' => $tournament->id,
                            'userId' => $tournamentUser->id,
                            'roleName' => 'organizer'
                        ]) }}" method="POST">
                                @csrf()
                                <button type="submit" class="btn-custom text-success">
                                    Maak beheerder
                                </button>
                            </form>
                        @endif
                    </td>
                    <td>
                        @if ($tournamentUser->isParticipant)
                            <form action="{{ action('TournamentAdminController@deleteUser', [
                            'tournamentId' => $tournament->id,
                            'userId' => $tournamentUser->id,
                            'roleName' => 'participant'
                        ]) }}" method="POST">
                                @method('DELETE')
                                @csrf()
                                <button type="submit" class="btn-custom text-danger">
                                    Verwijder deelnemer
                                </button>
                            </form>
                        @else
                            <form action="{{ action('TournamentAdminController@storeUser', [
                            'tournamentId' => $tournament->id,
                            'userId' => $tournamentUser->id,
                            'roleName' => 'participant'
                        ]) }}" method="POST">
                                @csrf()
                                <button type="submit" class="btn-custom text-success">
                                    Maak deelnemer
                                </button>
                            </form>
                        @endif
                    </td>
                </tr>
            
        </table>
        <table class="table">
            <h2 class="text-left">Aanvragen voor scheidsrechter</h1>
            <tr>
                <th class="col">Naam</th>
                <th class="col">E-mail</th>
                <th class="col">Telefoonummer</th>
                <th class="col" colspan="2">Instellingen</th>
            </tr>
            @if ($tournament->isReferee)    
              
            <tr>
                <td>{{$tournamentUser->name}}</td>
                <td>test</td>
                <td></td>
            </tr>
            @endif
            @endforeach
            
            
        </table>
        <a href="{{ url('tournament') }}" class="btn btn-primary">Ga terug naar het overzicht</a>
    </div>

@endsection
