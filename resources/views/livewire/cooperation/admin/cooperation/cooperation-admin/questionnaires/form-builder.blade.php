<div>
    <div id="tool-box" class="w-full flex flex-wrap items-center">
        @foreach(__('cooperation/admin/cooperation/cooperation-admin/questionnaires.edit.types') as $type => $trans)
            <button class="btn btn-outline-blue mr-2 mb-2" wire:click="addQuestion('{{$type}}')" wire:key="add-{{$type}}">
                {{ $trans }}
            </button>
        @endforeach
    </div>
    <hr class="w-full">
    <div class="w-full flex justify-end mb-4">
        <button class="btn btn-green" wire:click="save()">
            @lang('default.buttons.save')
        </button>
    </div>
    <div id="questions" class="w-full flex flex-wrap">
        @php $lastOrder = array_key_last($questions); @endphp
        @foreach($questions as $order => $question)
            <div class="w-full border border-blue/25 rounded-lg mb-4" wire:key="{{ "{$order}-{$question['key']}" }}">
                {{-- Type title --}}
                <div class="w-full flex justify-between border-b border-blue/25 px-4 py-2">
                    <h4 class="heading-6">
                        @lang('cooperation/admin/cooperation/cooperation-admin/questionnaires.edit.types.' . $question['type'])
                    </h4>
                    <div class="flex flex-col items-center">
                        <i class="icon-sm icon-arrow-up clickable @if($order === 0) hidden @endif"
                           wire:click="moveUp('{{ $question['key'] }}')"></i>
                        <i class="icon-sm icon-arrow-up clickable transform rotate-180 @if($order === $lastOrder) hidden @endif"
                           wire:click="moveDown('{{ $question['key'] }}')"></i>
                    </div>
                </div>
                {{-- Question "name" --}}
                <div class="w-full p-4">
                    @foreach(Hoomdossier::getSupportedLocales() as $locale)
                        @component('cooperation.frontend.layouts.components.form-group', [
                            'withInputSource' => false,
                            'label' => __('cooperation/admin/cooperation/cooperation-admin/questionnaires.form.question.label'),
                            'class' => 'w-full -mt-5 lg:w-1/2 lg:pr-3',
                            'inputName' => "questions.{$order}.name", //.{$locale}
                        ])
                            <div class="input-group-prepend">
                                {{ $locale }}
                            </div>
                            <input class="form-input" type="text" wire:model="{{ "questions.{$order}.name.{$locale}" }}">
                        @endcomponent
                    @endforeach
                </div>
                {{-- Options --}}
                @if(in_array($question['type'], ['select', 'radio', 'checkbox']))
                    <div class="w-full flex flex-wrap p-4 -mt-4">
                        <label class="w-full text-blue-500 text-sm font-bold max-w-15/20">
                            @lang('cooperation/admin/cooperation/cooperation-admin/questionnaires.form.option.label')
                        </label>
                        @error("questions.{$order}.options")
                            <p class="text-red text-sm -mt-3 w-full">
                                {{ $message }}
                            </p>
                        @enderror
                        @php $totalOptions = count($question['options']); @endphp
                        @foreach($question['options'] as $optionOrder => $option)
                            @foreach(Hoomdossier::getSupportedLocales() as $locale)
                                @component('cooperation.frontend.layouts.components.form-group', [
                                    'withInputSource' => false,
                                    'class' => 'w-full -mt-5 lg:w-1/2 lg:pr-3',
                                    'inputName' => "questions.{$order}.options.{$optionOrder}.name", //.{$locale}
                                    'attr' => "wire:key=\"{$order}-{$question['key']}-option-{$option['key']}\"",
                                ])
                                    <div class="input-group-prepend">
                                        {{ $locale }}
                                    </div>
                                    <input class="form-input with-append" type="text"
                                           placeholder="@lang('cooperation/admin/cooperation/cooperation-admin/questionnaires.form.option.placeholder')"
                                           wire:model="{{ "questions.{$order}.options.{$optionOrder}.name.{$locale}" }}">
                                    <div class="input-group-append">
                                        <i class="icon-sm icon-trash-can-red clickable @if($totalOptions <= 1) hidden @endif" wire:click="removeOption('{{ $question['key'] }}', {{ $optionOrder }})"></i>
                                    </div>
                                @endcomponent
                            @endforeach
                        @endforeach

                        <div class="w-full mt-4">
                            <i class="icon-md icon-plus-circle clickable"wire:click="addOption('{{ $question['key'] }}')"></i>
                        </div>
                    </div>
                @endif
                {{-- Validation row --}}
                @if(in_array($question['type'], ['text', 'textarea']))
                    <div class="w-full flex flex-wrap p-4 -mt-4">
                        @if(! empty($question['validation']))
                            {{--
                                TODO: As of right now, no one has more than a single validation line, so this
                                 wasn't made with multiple in mind, even though it was (sorta, buggy).
                                 ADDENDUM: It was in the frontend, not so much in the backend.
                            --}}
                            @component('cooperation.frontend.layouts.components.form-group', [
                                'withInputSource' => false,
                                'class' => 'w-full -mt-5 lg:w-1/3 lg:pr-3',
                                'inputName' => "questions.{$order}.validation.type",
                            ])
                                <select wire:model.live="{{ "questions.{$order}.validation.type" }}" class="form-input">
                                    @foreach(__('cooperation/admin/cooperation/cooperation-admin/questionnaires.form-builder.rules') as $type => $translation)
                                        <option value="{{$type}}">
                                            {{$translation}}
                                        </option>
                                    @endforeach
                                </select>
                            @endcomponent
                            @component('cooperation.frontend.layouts.components.form-group', [
                                'withInputSource' => false,
                                'class' => 'w-full -mt-5 lg:w-1/3 lg:pr-3',
                                'inputName' => "questions.{$order}.validation.rule",
                            ])
                                <select wire:model.live="{{ "questions.{$order}.validation.rule" }}" class="form-input">
                                    @foreach(__("cooperation/admin/cooperation/cooperation-admin/questionnaires.form-builder.optional-rules.{$question['validation']['type']}") as $rule => $translation)
                                        <option value="{{$rule}}">
                                            {{$translation}}
                                        </option>
                                    @endforeach
                                </select>
                            @endcomponent
                            @php
                                $currenType = $question['validation']['type'];
                                $currentRule = $question['validation']['rule'];
                                // We show min if it's numeric.
                                $showMin = $currenType === 'numeric';
                                // We show max if it's numeric + between or string + max
                                $showMax = ($currenType === 'numeric' && $currentRule === 'between') || ($currenType === 'string' && $currentRule === 'max');
                            @endphp
                            @component('cooperation.frontend.layouts.components.form-group', [
                                'withInputSource' => false,
                                'class' => 'w-full -mt-5 lg:w-1/6 lg:pr-3 ' . ($showMin ? 'block' : 'hidden'),
                                'inputName' => "questions.{$order}.validation.min",
                            ])
                                <input wire:model="{{ "questions.{$order}.validation.min" }}" class="form-input"
                                       placeholder="@lang("cooperation/admin/cooperation/cooperation-admin/questionnaires.form-builder.extra-fields.min.placeholder")">
                            @endcomponent
                            @component('cooperation.frontend.layouts.components.form-group', [
                                'withInputSource' => false,
                                'class' => 'w-full -mt-5 lg:w-1/6 lg:pr-3 ' . ($showMax ? 'block' : 'hidden'),
                                'inputName' => "questions.{$order}.validation.max",
                            ])
                                <input wire:model="{{ "questions.{$order}.validation.max" }}" class="form-input"
                                       placeholder="@lang("cooperation/admin/cooperation/cooperation-admin/questionnaires.form-builder.extra-fields.max.placeholder")">
                            @endcomponent

                            <div class="w-full">
                                <button class="btn btn-outline-red" wire:click="removeValidation('{{ $question['key'] }}')">
                                    @lang('cooperation/admin/cooperation/cooperation-admin/questionnaires.edit.remove-validation')
                                </button>
                            </div>
                        @else
                            <button class="btn btn-green" wire:click="addValidation('{{ $question['key'] }}')">
                                @lang('cooperation/admin/cooperation/cooperation-admin/questionnaires.edit.add-validation')
                            </button>
                        @endif
                    </div>
                @endif
                {{-- Footer --}}
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
