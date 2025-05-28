@extends('cooperation.admin.layouts.app', [
    'panelTitle' => __('cooperation/admin/cooperation/cooperation-admin/questionnaires.create.header'),
    'menu' => false
])

@section('content')
    <div class="w-full">
        <div class="flex w-full">
            <a id="leave-creation-tool"
               href="{{route('cooperation.admin.cooperation.questionnaires.index')}}"
               class="btn btn-yellow">
                @lang('cooperation/admin/cooperation/cooperation-admin/questionnaires.create.leave-creation-tool')
            </a>
        </div>

        <hr class="w-full">

        <form class="w-full flex flex-wrap"
              action="{{ route('cooperation.admin.cooperation.questionnaires.store') }}" method="POST">
            @csrf

            @foreach(Hoomdossier::getSupportedLocales() as $locale)
                @component('cooperation.frontend.layouts.components.form-group', [
                    'withInputSource' => false,
                    'label' => __('cooperation/admin/cooperation/cooperation-admin/questionnaires.form.name.label'),
                    'id' => "name-{$locale}",
                    'class' => 'w-full -mt-5 lg:w-1/2 lg:pr-3',
                    'inputName' => "questionnaires.name.{$locale}",
                ])
                    <div class="input-group-prepend">
                        {{ $locale }}
                    </div>
                    <input id="{{ "name-{$locale}" }}" class="form-input" type="text" name="questionnaires[name][{{$locale}}]"
                           placeholder="@lang('cooperation/admin/cooperation/cooperation-admin/questionnaires.form.name.placeholder')"
                           value="{{ old("questionnaires.name.{$locale}") }}">
                @endcomponent
            @endforeach

            <div class="w-full"></div>

            @component('cooperation.frontend.layouts.components.form-group', [
                'class' => 'w-full lg:w-1/2 lg:pr-3',
                'label' => __('cooperation/admin/cooperation/cooperation-admin/questionnaires.form.step.label'),
                'id' => 'step-select',
                'inputName' => "questionnaires.steps",
                'withInputSource' => false,
            ])
                @component('cooperation.frontend.layouts.components.alpine-select', ['withSearch' => true])
                    <select multiple id="step-select" class="form-input hidden" name="questionnaires[steps][]">
                        <option selected disabled>
                            @lang('cooperation/admin/cooperation/cooperation-admin/questionnaires.form.step.placeholder')
                        </option>
                        @foreach($scans as $scan)
                            <optgroup label="{{ $scan->name }}">
                                @foreach($scan->steps as $step)
                                    <option value="{{ $step->id }}"
                                            @if(in_array($step->id, old('questionnaires.steps', []))) selected @endif>
                                        {{ $step->name }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                @endcomponent
            @endcomponent

            <div class="w-full mt-5">
                <button class="btn btn-green flex justify-center items-center" type="submit">
                    @lang('default.buttons.store')
                    <i class="w-3 h-3 icon-plus-purple ml-1"></i>
                </button>
            </div>
        </form>
    </div>
@endsection