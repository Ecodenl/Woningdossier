@extends('cooperation.tool.layout')

@section('step_content')

    <div class="row">
        <div class="col-sm-10">
            <p>@lang('cooperation/tool/general-data/current-state.index.step-intro.title')</p>
        </div>
    </div>

    <form class="form-horizontal" method="POST" id="main-form" action="{{ route('cooperation.tool.general-data.current-state.store') }}" autocomplete="off">
        {{ csrf_field() }}

        <div class="elements">
            <h4>Isolatie</h4>
            @include('cooperation.tool.general-data.current-state.parts.elements')
            <div class="pl-65">
            @include('cooperation.tool.includes.comment', [
                'short' => 'element',
                'translation' => 'cooperation/tool/general-data/current-state.index.comment.element'
            ])
            </div>
        </div>

        <div class="services">
            <h4>Installaties</h4>
            @include('cooperation.tool.general-data.current-state.parts.services')

            <div class="pl-65">
            @include('cooperation.tool.includes.comment', [
                'short' => 'service',
                'translation' => 'cooperation/tool/general-data/current-state.index.comment.service'
            ])
            </div>
        </div>
    </form>
@endsection