@extends('cooperation.frontend.layouts.tool')

@section('content')
    <div class="w-full">
        @include('cooperation.frontend.layouts.parts.custom-questionnaire', [
            'questionnaire' => $questionnaire, 'isTab' => false,
        ])
    </div>
@endsection