@extends('cooperation.layouts.app')
@push('css')
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endpush
@section('content')
    <section class="section">
        <div class="container">
            <form action="{{ route('cooperation.admin.cooperation.questionnaires.update', compact('questionnaire')) }}" method="post">
                <input type="hidden" name="questionnaire[id]" value="{{$questionnaire->id}}">
                {{method_field('PUT')}}
                {{csrf_field()}}
                <div class="row">
                    <div class="col-sm-6">
                        <a id="leave-creation-tool" href="{{route('cooperation.admin.cooperation.questionnaires.index')}}" class="btn btn-warning">
                            @lang('cooperation/admin/cooperation/questionnaires.shared.leave-creation-tool')
                        </a>
                    </div>
                    <div class="col-sm-6">
                        <button type="submit" href="{{route('cooperation.admin.cooperation.questionnaires.index')}}" class="btn btn-primary pull-right">
                            @lang('default.save')
                        </button>
                    </div>
                </div>
                <div class="row alert-top-space">
                    <div class="col-md-3">
                        <div id="tool-box" class="list-group">
                            <a href="#" id="short-answer" data-type="text" class="list-group-item"><i class="glyphicon glyphicon-align-left"></i>
                                @lang('cooperation/admin/cooperation/questionnaires.shared.types.text.label')
                            </a>
                            <a href="#" id="long-answer" data-type="textarea" class="list-group-item"><i class="glyphicon glyphicon-align-justify"></i>
                                @lang('cooperation/admin/cooperation/questionnaires.shared.types.textarea.label')
                            </a>
                            <a href="#" id="radio-button" data-type="radio" class="list-group-item"><i class="glyphicon glyphicon-record"></i> 
                                @lang('cooperation/admin/cooperation/questionnaires.shared.types.radio.label')
                            </a>
                            <a href="#" id="checkbox" data-type="checkbox" class="list-group-item"><i class="glyphicon glyphicon-unchecked"></i> 
                                @lang('cooperation/admin/cooperation/questionnaires.shared.types.checkbox.label')
                            </a>
                            <a href="#" id="dropdown" data-type="select" class="list-group-item"><i class="glyphicon glyphicon-collapse-down"></i> 
                                @lang('cooperation/admin/cooperation/questionnaires.shared.types.select.label')
                            </a>
                            <a href="#" id="date" data-type="date" class="list-group-item"><i class="glyphicon glyphicon-calendar"></i>
                                @lang('cooperation/admin/cooperation/questionnaires.shared.types.date.label')
                            </a>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="panel">
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-xs-12">
                                        @foreach(config('hoomdossier.supported_locales') as $locale)
                                            <div class="form-group {{ $errors->has('questionnaire.name.*') ? ' has-error' : '' }}">
                                                <label for="name">@lang('cooperation/admin/cooperation/questionnaires.shared.column-translations.name.label')</label>
                                                <div class="input-group">
                                                    <span class="input-group-addon">{{$locale}}</span>
                                                    <input id="name" type="text" class="form-control" name="questionnaire[name][{{$locale}}]"
                                                           value="{{$questionnaire->getTranslation('name', $locale) instanceof \App\Models\Translation ? $questionnaire->getTranslation('name', $locale)->translation : "" }}"
                                                           placeholder="@lang('cooperation/admin/cooperation/questionnaires.shared.column-translations.name.placeholder')">
                                                </div>
                                            </div>
                                        @endforeach
                                        <div class="form-group">
                                            <label for="step_id">@lang('cooperation/admin/cooperation/questionnaires.shared.column-translations.after-step.label')</label>
                                            <select id="step_id" name="questionnaire[step_id]" class="form-control">
                                                @foreach($steps as $i => $step)
                                                    <option value="{{ $step->id }}" @if($questionnaire->step_id == $step->id) selected="selected" @endif >
                                                        {{ $i+1 }}: {{ $step->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel">
                            <div class="panel-body" >
                                <div id="sortable">
                                    @forelse($questionnaire->questions()->orderBy('order')->get() as $question)
                                        @component('cooperation.admin.cooperation.questionnaires.layouts.form-build-panel', ['question' => $question])

                                        @endcomponent
                                    @empty

                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection
@push('css')
    <link href="{{asset('css/bootstrap-toggle.min.css')}}" rel="stylesheet">
@endpush
@push('js')

    <script src="{{asset('js/bootstrap-toggle.min.js')}}"></script>
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

    @include('cooperation.admin.cooperation.questionnaires.parts.questionnaire-js')

@endpush