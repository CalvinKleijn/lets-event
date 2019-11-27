@extends('layouts.app')

@section('content')

@if($errors->any())
<div class=" container alert alert-danger">
    @foreach ($errors->all() as $error)
    <b class="text-white">{{ $error }}</b>
    @endforeach
</div>
@endif

<div class="container">
    <table class="table ">
        <h1 class="text-left">Scheidrechters</h1>
        <tr>
            <th class="col">Naam</th>
            <th class="col">Status</th>
            <th class="col">E-mail</th>
            <th class="col">Telefoonummer</th>
            <th class="col" colspan="2">Instellingen</th>
        </tr>
        @foreach ($allReferees as $referee)
            @if ($referee->status == 'accepted')
                @php
                    $color = 'success';
                @endphp
            @elseif($referee->status == 'pending')
                @php
                    $color = 'warning';
                @endphp
            @elseif ($referee->status == 'denied')
                @php
                    $color = 'danger';
                    $accept = ''
                @endphp
            @endif
        <tr>
            <td class="text-{{ $color }}">{{ $referee->user->name }}</td>
            <td class="text-uppercase text-{{ $color }}">{{ $referee->status }}</td>
            <td class="text-{{ $color }}">{{ $referee->user->email }}</td>
            <td class="text-{{ $color }}">{{ $referee->user->phone_number }}</td>
            @if ($referee->status == 'accepted')
                <td><a class="text-danger" href="{{ action('TournamentAdminController@denyReferee', [
                        'tournamentId' => $tournamentId,
                        'userId' => $referee->user->id
                    ])}}">Wijs af</a></td>
            @else
                <td class="text-{{ $color }}"><a class="text-success" href="{{ action('TournamentAdminController@addReferee', [
                        'tournamentId' => $tournamentId,
                        'userId' => $referee->user->id
                    ])}}">Voeg toe</a>
                </td>
            @endif
        </tr>
@endforeach
    </table>


    <a href="{{ url('tournament') }}" class="btn btn-primary">Ga terug naar het overzicht</a>
</div>

@endsection
