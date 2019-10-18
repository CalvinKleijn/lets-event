@extends('layouts.app')

@section('bodyScripts')
<script src="{{ asset('js/charts/homeChart.js') }}"></script>
@endsection

@section('content')
<div class="container">
    <h1 class="text-center font-weight-bolder mb-4">Dashboard</h1>
    <div class="container">
        <div class="row">
            <div class="col-sm-6">
                <div class="card">
                    <h5 class="card-header"><b>Mijn Toernooien</b></h5>
                    <div class="card-body">
                        <table class="table table-borderless table-striped">
                            <tbody>
                                @foreach($tournaments as $tournament)
                                <tr>
                                    <th>{{ $tournament->name }}</th>
                                    <th>{{ $tournament->start_date_time }}</th>
                                    <td class="text-danger float-right">
                                        <a href="{{ route('tournament.leave', [$tournament->id, $tournament->start_date_time])}}"
                                            class="btn-link text-danger">Verlaat
                                            toernooi</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <a href="{{ route('tournament.index')}}" class="btn btn-primary">Ga naar toernooien</a>
                    </div>
                </div>
            </div>


            <!-- only display tourneys that the user didnt join yet -->


            <!-- filtering -->

            <!-- <div class="col-sm-6">
                <div class="card">
                    <h5 class="card-header"><b>Lopende Uitnodigingen</b></h5>
                    <div class="card-body">
                        <table class="table table-borderless table-striped">
                            <tbody>
                                @foreach($tournamentsWhereUserIsNotParticipant as $tournament)
                                <tr>
                                    <th>{{ $tournament->name }}</th>
                                    <th>{{ $tournament->start_date_time }}</th>
                                    <th class="text-danger float-right">
                                        <a route:="dashboard/{{ $tournament->id }}/join"
                                            class="btn-link text-success">Doe mee!</a>
                                    </th>
                                    <td class="text-danger float-right">
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <a href="{{ route('tournament.index')}}" class="btn btn-primary">Ga naar toernooien</a>
                    </div>
                </div>
            </div> -->

            <div class="col-sm-6">
                <div class="card">
                    <h5 class="card-header"><b>Account Instellingen</b> </h5>
                    <div class="card-body">
                        <ul class="noListStyling">
                            <h4>
                                <li>Naam: {{ $user->name }}</li>
                                <li>Email: {{ $user->email }}</li>
                                <li>Telefoon: {{ $user->phone_number }}</li>
                            </h4>
                        </ul>
                        <a href="{{ route('user.index', ['id' => auth()->user()->id]) }}" class="btn btn-primary">Ga
                            naar
                            Account Instellingen</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-5">
            <div class="col-sm-6">
                <div class="card">
                    <h5 class="card-header"><b>Statistieken voor Nerds</b> </h5>
                    <div class="card-body">
                        <canvas id="miniChart"></canvas>
                        <a href="{{ url('stats') }}" class="btn btn-primary">Ga naar Statistieken voor Nerds</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- <div class="row mt-5">
    
            <div class="col-sm-6">
                <div class="card">
                    <h5 class="card-header"><b>Live Time</b> </h5>
                    <div class="card-body">
                        <p class="card-text">Lorem ipsum dolor sit amet consectetur adipisicing elit. Amet iusto enim
                            est sa
                        </p>
                        <a href="#" class="btn btn-primary">Ga naar live Time</a>
                    </div>
                </div>
            </div>
        </div> -->
    </div>
    @endsection
