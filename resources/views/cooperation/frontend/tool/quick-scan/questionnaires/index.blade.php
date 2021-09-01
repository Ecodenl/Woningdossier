@extends('cooperation.frontend.layouts.tool')

@section('content')
    <div class="w-full">
        <div class="w-full flex flex-wrap justify-between">
            <h4 class="heading-4">
                {{ $questionnaire->name }}
            </h4>
        </div>

        @include('cooperation.frontend.layouts.parts.custom-questionnaire', [
            'questionnaire' => $questionnaire, 'isTab' => false
        ])
    </div>
@endsection