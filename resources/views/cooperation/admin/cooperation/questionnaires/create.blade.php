@extends('cooperation.layouts.app')
@section('content')
    <section class="section">
        <div class="container">
            <form action="{{ route('cooperation.admin.cooperation.questionnaires.store') }}" method="post">
                {{csrf_field()}}
                <div class="row">
                    <div class="col-sm-6">
                        <a id="leave-creation-tool" href="{{route('cooperation.admin.cooperation.questionnaires.index')}}" class="btn btn-warning">
                            @lang('cooperation/admin/cooperation/questionnaires.shared.leave-creation-tool')
                        </a>
                    </div>
                    <div class="col-sm-6">
                        <button type="submit"  class="btn btn-primary pull-right">
                            @lang('cooperation/admin/cooperation/questionnaires.create.submit')
                        </button>
                    </div>
                </div>
            <div class="row alert-top-space">
                <div class="col-md-12">
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
                                                       placeholder="@lang('cooperation/admin/cooperation/questionnaires.shared.column-translations.name.placeholder')">
                                            </div>
                                        </div>
                                    @endforeach
                                    <div class="form-group {{ $errors->has('questionnaire.step_id') ? ' has-error' : '' }}">
                                        <label for="step_id">@lang('cooperation/admin/cooperation/questionnaires.shared.column-translations.after-step.label')</label>
                                        <select id="step_id" name="questionnaire[step_id]" class="form-control">
                                            @foreach($steps as $i => $step)
                                                <option value="{{ $step->id }}">
                                                    {{ $i+1 }}: {{ $step->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </form>
        </div>
    </section>
@endsection