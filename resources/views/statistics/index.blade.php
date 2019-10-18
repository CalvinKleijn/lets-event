@extends('layouts.app')

@section('bodyScripts')
    <script src="{{ asset('js/charts/statsChart.js') }}"></script>
@endsection

@section('content')

<div class="container">
    <h1 class="text-center font-weight-bolder mb-4">Statistiek</h1>

    <h4 class="text-center font-weight-bolder mb-4">Totaal gespeelde toernooien: TEST</h4>

    <canvas id="myChart"></canvas>
</div>

@endsection
