@extends ('layouts.app')

@section ('content')

<div class="container">
    <h1 class="text-center">{{ __('errors.errorPageTitle500') }}</h1>
    <h6 class="text-center">{{ __('errors.errorPageDescription500')}}</h6>
</div>

@endsection
