@extends('cooperation.frontend.layouts.tool')

@section('step_title', $step->name)

@section('content')

    <div x-data="{ active = 'current'} ">
        <h2 x-text="active"></h2>
        <div class="hidden sm:block">
            <nav class="flex space-x-4" aria-label="Tabs">
                <!-- Current: "bg-indigo-100 text-indigo-700", Default: "text-green-500 hover:text-green-700" -->
                <a x-on:click="active = 'current'" href="#" class="no-underline rounded-md p-4 bg-green text-white hover:bg-gray "> Huidige situatie</a>
                <a x-on:click="active = 'desired'" href="#" class="no-underline rounded-md p-4 bg-green text-white hover:bg-gray "> Gewenste situatie</a>
            </nav>
        </div>


    </div>

    @livewire('cooperation.frontend.tool.expert-scan.form', ['step' => $step])
@endsection

