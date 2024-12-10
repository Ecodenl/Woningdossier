<div>
    <div id="tool-box" class="w-full flex flex-wrap items-center">
        @foreach(__('cooperation/admin/cooperation/cooperation-admin/questionnaires.edit.types') as $type => $trans)
            <button class="btn btn-outline-blue mr-2 mb-2" wire:click="addQuestion('{{$type}}')" wire:key="add-{{$type}}">
                {{ $trans }}
            </button>
        @endforeach
    </div>
    <hr class="w-full">
    <div id="questions" class="w-full flex flex-wrap">
        @foreach($questions as $order => $question)
            <div class="w-full border border-blue/25 rounded-lg mb-4" wire:key="{{ $question['key'] }}">
                <div class="w-full border-b border-blue/25 px-4 py-2">
                    <h4 class="heading-6">
                        @lang('cooperation/admin/cooperation/cooperation-admin/questionnaires.edit.types.' . $question['type'])
                    </h4>
                </div>
                <div class="w-full p-4">
                    @foreach(Hoomdossier::getSupportedLocales() as $locale)
                        @component('cooperation.frontend.layouts.components.form-group', [
                            'withInputSource' => false,
                            'label' => __('cooperation/admin/cooperation/cooperation-admin/questionnaires.form.question.label'),
                            'class' => 'w-full -mt-5 lg:w-1/2 lg:pr-3',
                            'inputName' => "questions.{$order}.name.{$locale}",
                        ])
                            <div class="input-group-prepend">
                                {{ $locale }}
                            </div>
                            <input class="form-input" type="text" wire:model="{{ "questions.{$order}.name.{$locale}" }}">
                        @endcomponent
                    @endforeach
                </div>
                <div class="flex w-full justify-between items-center border-t border-blue/25 px-4 py-2">
                    <i class="icon-md icon-trash-can-red clickable" wire:click="removeQuestion('{{ $question['key'] }}')"></i>

                    <div class="checkbox-wrapper mb-0">
                        <input type="checkbox" wire:model="{{ "questions.{$order}.required" }}"
                               id="question-{{$order}}-required">
                        <label for="question-{{$order}}-required">
                            <span class="checkmark"></span>
                            <span>
                                @lang('cooperation/admin/cooperation/cooperation-admin/questionnaires.form.required.label')
                            </span>
                        </label>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
