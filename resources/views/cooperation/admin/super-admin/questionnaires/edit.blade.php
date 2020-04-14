@extends('cooperation.admin.layouts.app')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('cooperation/admin/super-admin/questionnaires.edit.header')
        </div>

        <div class="panel-body">
            <form action="{{route('cooperation.admin.super-admin.questionnaire.copy')}}" method="post">
                {{csrf_field()}}
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="questionnaires">@lang('cooperation/admin/super-admin/questionnaires.edit.form.questionnaire')</label>
                            <select name="questionnaires[id]" id="questionnaires" class="form-control">
                                @foreach($questionnaires as $questionnaire)
                                    <option selected="selected" value="{{$questionnaire->id}}">
                                        {{$questionnaire->name}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="cooperations">@lang('cooperation/admin/super-admin/questionnaires.edit.form.cooperations')</label>
                            <select name="cooperations[id][]" id="cooperations" class="form-control" multiple="multiple">
                                @foreach($cooperations as $questionnaire)
                                    <option value="{{$questionnaire->id}}">
                                        {{$questionnaire->name}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <button class="btn btn-primary">@lang('cooperation/admin/super-admin/questionnaires.edit.form.submit')</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function () {
            $('#questionnaires').select2();
            $('#cooperations').select2();
        })
    </script>
@endpush

