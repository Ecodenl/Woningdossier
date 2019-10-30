@extends('cooperation.tool.layout')

@section('step_content')

    <div class="row">
        <div class="col-sm-10">
            <p>@lang('general-data/current-state.step-intro.title')</p>
        </div>
    </div>

    <form class="form-horizontal" method="POST" id="main-form" action="{{ route('cooperation.tool.general-data.current-state.store') }}" autocomplete="off">
        {{ csrf_field() }}

        <div class="elements">
            <h4>Isolatie</h4>
            @include('cooperation.tool.general-data.current-state.parts.elements')

            @include('cooperation.tool.includes.comment', [
                'columnName' => 'step_comments[comment]',
                'short' => 'element',
                'translation' => 'general-data/current-state.comment.element'
            ])
        </div>

        <div class="services">
            <h4>Installaties</h4>
            @include('cooperation.tool.general-data.current-state.parts.services')

            @include('cooperation.tool.includes.comment', [
                'columnName' => 'step_comments[comment]',
                'short' => 'service',
                'translation' => 'general-data/current-state.comment.service'
            ])
        </div>
    </form>
@endsection

@push('js')
@endpush