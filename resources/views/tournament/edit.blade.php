@extends ('layouts.app')

@section ('content')

@if($errors->any())
<div class=" container alert alert-danger">
    @foreach ($errors->all() as $error)
        <b class="text-white">{{ $error }}</b>
    @endforeach
</div>
@endif

<div class="container">
    <h1 class="text-center">Edit <u><b>{{ $tournament->name }}</b></u></h1>
        <form method="POST" action="{{ action(TournamentController::class . '@update', $tournament) }}">
        {{ csrf_field() }}
        {{ method_field('PATCH') }}

        <div class="form-group">
            <label for="name">Naam</label>
            <input type="name" class="form-control" id="name" name="name" value="{{ $tournament->name }}">
        </div>

        <div class="form-group">
            <label for="description">Beschrijving</label>
            <input type="description" class="form-control" id="description" name="description" value="{{ $tournament->description }}">
        </div>

        <div class="form-group">
            <label for="start-date-time">Start datum en tijd</label>
            <input class="form-control" type="datetime-local" id="start-date-time" name="start-date-time" max="2100-12-31T00:00" value="{{ $tournament->start_date_time_formatted }}">
        </div>


        <button type="submit" class="btn btn-primary">Sla wijzigingen op</button>
</div>
@endsection
