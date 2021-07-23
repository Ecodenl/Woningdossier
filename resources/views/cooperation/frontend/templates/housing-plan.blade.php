@extends('cooperation.frontend.layouts.tool')

@section('content')
    <div class="w-full">
        <div class="w-full flex flex-wrap justify-between mb-5">
            <h4 class="heading-4">
                Uw geadviseerde Woonplan
            </h4>
            <p>Wilt u iets aanpassen? Sleep dan de maatregelen naar de gewenste kolom</p>
        </div>

        @livewire('frontend.housing-plan')
    </div>
@endsection