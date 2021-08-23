@extends('cooperation.frontend.layouts.tool')

@section('content')
    <div class="w-full">
        <div class="w-full flex flex-wrap justify-between mb-5">
            <h4 class="heading-4">
                @lang('cooperation/frontend/tool.my-plan.title')
            </h4>
            <p>
                @lang('cooperation/frontend/tool.my-plan.help')
            </p>
        </div>

        @livewire('cooperation.frontend.tool.quick-scan.my-plan.form')
    </div>
@endsection