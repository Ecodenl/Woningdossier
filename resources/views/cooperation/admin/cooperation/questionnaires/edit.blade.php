@extends('cooperation.admin.layouts.app', [
    'panelTitle' => __('cooperation/admin/cooperation/cooperation-admin/questionnaires.edit.header'),
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

        <div x-data="tabs()">
            <nav class="nav-tabs">
                <a x-bind="tab" data-tab="edit-questionnaire" x-ref="main-tab">
                    @lang('cooperation/admin/cooperation/cooperation-admin/questionnaires.edit.tabs.edit-questionnaire')
                </a>
                <a x-bind="tab" data-tab="edit-questions">
                    @lang('cooperation/admin/cooperation/cooperation-admin/questionnaires.edit.tabs.edit-questions')
                </a>
            </nav>

            <div class="border border-t-0 border-blue/50 rounded-b-lg p-4">
                <div id="edit-questionnaire" x-bind="container" data-tab="edit-questionnaire">
                    <form class="w-full flex flex-wrap"
                          action="{{ route('cooperation.admin.cooperation.questionnaires.update', compact('questionnaire')) }}"
                          method="POST">
                        @csrf
                        @method('PUT')

                        @foreach(Hoomdossier::getSupportedLocales() as $locale)
                            @component('cooperation.frontend.layouts.components.form-group', [
                                'withInputSource' => false,
                                'label' => __('cooperation/admin/cooperation/cooperation-admin/questionnaires.form.name.label'),
                                'class' => 'w-full -mt-5 lg:w-1/2 lg:pr-3',
                                'inputName' => "questionnaires.name.{$locale}",
                            ])
                                <div class="input-group-prepend">
                                    {{ $locale }}
                                </div>
                                <input class="form-input" type="text" name="questionnaires[name][{{$locale}}]"
                                       placeholder="@lang('cooperation/admin/cooperation/cooperation-admin/questionnaires.form.name.placeholder')"
                                       value="{{ old("questionnaires.name.{$locale}", $questionnaire->getTranslation('name', $locale)) }}">
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
                            @php $questionnaireSteps = $questionnaire->steps()->pluck('steps.id')->toArray(); @endphp
                            @component('cooperation.frontend.layouts.components.alpine-select', ['withSearch' => true])
                                <select multiple id="step-select" class="form-input hidden" name="questionnaires[steps][]">
                                    <option selected disabled>
                                        @lang('cooperation/admin/cooperation/cooperation-admin/questionnaires.form.step.placeholder')
                                    </option>
                                    @foreach($scans as $scan)
                                        <optgroup label="{{ $scan->name }}">
                                            @foreach($scan->steps as $step)
                                                <option value="{{ $step->id }}"
                                                        @if(in_array($step->id, old('questionnaires.steps', $questionnaireSteps))) selected @endif>
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
                                @lang('default.buttons.update')
                            </button>
                        </div>
                    </form>
                </div>

                <div id="edit-questions" x-bind="container" data-tab="edit-questions">
                    <livewire:cooperation.admin.cooperation.cooperation-admin.questionnaires.form-builder :questionnaire="$questionnaire"/>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script type="module">
        document.getElementById('leave-creation-tool').addEventListener('click', function (event) {
            if (confirm('@lang('cooperation/admin/cooperation/cooperation-admin/questionnaires.edit.leave-warning')')) {

            } else {
                event.preventDefault();
                return false;
            }
        });
    </script>
@endpush