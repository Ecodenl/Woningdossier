@extends('cooperation.admin.layouts.app', [
    'panelTitle' => __('cooperation/admin/super-admin/questionnaires.edit.header')
])

@section('content')
    <form class="w-full flex flex-wrap"
          action="{{route('cooperation.admin.super-admin.questionnaire.copy')}}"
          method="POST">
        @csrf

        @component('cooperation.frontend.layouts.components.form-group', [
            'withInputSource' => false,
            'label' => __('cooperation/admin/super-admin/questionnaires.edit.form.questionnaire'),
            'id' => "questionnaires",
            'class' => 'w-full -mt-5 lg:w-1/2 lg:pr-3',
            'inputName' => "questionnaires.id",
        ])
            @component('cooperation.frontend.layouts.components.alpine-select', ['withSearch' => true])
                <select id="questionnaires" name="questionnaires[id]" id="questionnaires" class="form-input hidden">
                    @foreach($questionnaires as $questionnaire)
                        <option value="{{$questionnaire->id}}"
                                @if($selectedQuestionnaire->id == $questionnaire->id) selected @endif>
                            {{$questionnaire->name}}
                        </option>
                    @endforeach
                </select>
            @endcomponent
        @endcomponent
        @component('cooperation.frontend.layouts.components.form-group', [
            'withInputSource' => false,
            'label' => __('cooperation/admin/super-admin/questionnaires.edit.form.cooperations'),
            'id' => "cooperations",
            'class' => 'w-full -mt-5 lg:w-1/2 lg:pl-3',
            'inputName' => "cooperations.id",
        ])
            @component('cooperation.frontend.layouts.components.alpine-select', ['withSearch' => true])
                <select name="cooperations[id][]" id="cooperations" class="form-input hidden" multiple>
                    @foreach($cooperations as $cooperation)
                        <option value="{{$cooperation->id}}">
                            {{$cooperation->name}}
                        </option>
                    @endforeach
                </select>
            @endcomponent
        @endcomponent

        <div class="w-full mt-5">
            <button class="btn btn-green">
                @lang('cooperation/admin/super-admin/questionnaires.edit.form.submit')
            </button>
        </div>
    </form>
@endsection

