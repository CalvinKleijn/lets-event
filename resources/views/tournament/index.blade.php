@extends ('layouts.app')

@section ('content')

    @if(session()->has('message'))
        <div class="container alert alert-success">
            <b class="text-white">{{ session()->get('message') }}</b>
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
        <h1 class="text-left">Overzicht van toernooien</h1>
        <table class="table sortable" data-request-url="{{ route('tournament.sort') }}">
            <thead>
            <tr>
                <th scope="col">
                    @if ($columnToSortBy == 'id')
                        <a href="{{ route('tournament.index') }}?pageNumber={{ $pageNumber }}&columnToSortBy=id&orderToSortBy={{ $newOrderToSortBy }}">Id</a>
                    @else
                        <a href="{{ route('tournament.index') }}?pageNumber={{ $pageNumber }}&columnToSortBy=id">Id</a>
                    @endif
                </th>
                <th scope="col">
                    @if ($columnToSortBy == 'name')
                        <a href="{{ route('tournament.index') }}?pageNumber={{ $pageNumber }}&columnToSortBy=name&orderToSortBy={{ $newOrderToSortBy }}">Naam</a>
                    @else
                        <a href="{{ route('tournament.index') }}?pageNumber={{ $pageNumber }}&columnToSortBy=name">Naam</a>
                    @endif
                </th>
                <th scope="col">
                    @if ($columnToSortBy == 'description')
                        <a href="{{ route('tournament.index') }}?pageNumber={{ $pageNumber }}&columnToSortBy=description&orderToSortBy={{ $newOrderToSortBy }}">Beschrijving</a>
                    @else
                        <a href="{{ route('tournament.index') }}?pageNumber={{ $pageNumber }}&columnToSortBy=description">Beschrijving</a>
                    @endif
                </th>
                <th scope="col">
                    @if ($columnToSortBy == 'start_date_time')
                        <a href="{{ route('tournament.index') }}?pageNumber={{ $pageNumber }}&columnToSortBy=start_date_time&orderToSortBy={{ $newOrderToSortBy }}">Start
                            datum en tijd</a>
                    @else
                        <a href="{{ route('tournament.index') }}?pageNumber={{ $pageNumber }}&columnToSortBy=start_date_time">Start
                            datum en tijd</a>
                    @endif
                </th>

                <th scope="col">Deelnemer</th>
                <th scope="col" colspan="3">Beheer</th>
            </tr>
            </thead>
            <tbody>
            @foreach($tournaments as $tournament)
                <tr>
                    <td>
                        {{ $tournament->id }}
                    </td>
                    <td>
                        <a href="{{ route('tournament.show', $tournament->id) }}">{{ $tournament->name }}</a>
                    </td>
                    <td>{{ $tournament->description }}</td>
                    <td>{{ $tournament->start_date_time }}</td>
                    @if($tournament->isParticipant)
                        <td>
                            <a href="{{ route('tournament.leave', [
                                    'tournamentId' => $tournament->id,
                                    'tournamentStartDateTime' => $tournament->start_date_time
                                ]) }}"
                               class="btn btn-link btn-custom text-danger">Verlaat toernooi</a>
                        </td>
                    @else
                        <td>
                            <a href="{{ route('tournament.join', $tournament->id) }}"
                               class="btn btn-link btn-custom text-success">Meedoen aan toernooi</a>
                        </td>
                    @endif
                    @if($tournament->isOrganizer)
                        <td>
                            <a href="{{ route('tournament.admin.show', $tournament->id) }}" class="text-success">Instellingen</a>
                        </td>
                        <td>
                            <a href="{{ route('tournament.edit', $tournament->id) }}" class="text-primary">Bewerken</a>
                        </td>
                        <td>
                            <form action="{{ action('TournamentController@destroy', $tournament->id) }}"
                                  method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-custom text-danger">Verwijderen</button>
                            </form>
                        </td>
                    @else
                        {{--  Todo: Betere manier bedenken  --}}
                        <td></td>
                        <td></td>
                        <td></td>
                    @endif
                </tr>
            @endforeach
            </tbody>
        </table>

        @include('layouts.partials.pagination')
        @yield('pagination')

        <a href="{{ route('tournament.create') }}" class="btn btn-primary">Maak een toernooi</a>

    </div>
@endsection
