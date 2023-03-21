<div class="measure-related-questions">
    @foreach($measureRelatedAnswers as $measureId => $questions)
        @php $measureApplication = \App\Models\MeasureApplication::find($measureId); @endphp
        <div class="flex flex-row flex-wrap w-full mt-2" id="measure-related-question-{{$measureId}}">
            @if(($withHeader ?? false))
                <div class="section-title w-full">
                    <h4 class="heading-4">
                        {{ $measureApplication->name }}
                    </h4>
                </div>
            @endif

            <div class="flex flex-row flex-wrap w-full sm:pad-x-6">
                @foreach($questions as $short => $answer)
                    @php
                        $toolQuestion = \App\Models\ToolQuestion::findByShort($short);
                        $isDropdown = $toolQuestion->data_type === Caster::IDENTIFIER;
                    @endphp
                    <div class="w-full sm:w-1/3">
                        @component('cooperation.frontend.layouts.components.form-group', [
                            'inputName' => $short,
                            'label' => $toolQuestion->name,
                            'id' => $short,
                            'modalId' => $short . '-info',
                            'inputGroupClass' => $inputGroupClass ?? '',
                        ])
                            @slot('sourceSlot')
                                @include('cooperation.sub-step-templates.parts.source-slot-values', [
                                    'values' => $building->getAnswerForAllInputSources($toolQuestion),
                                    'toolQuestion' => $toolQuestion,
                                ])
                            @endslot

                            @slot('modalBodySlot')
                                {!! $toolQuestion->help_text !!}
                            @endslot

                            @if($isDropdown)
                                @component('cooperation.frontend.layouts.components.alpine-select')
                                    <select name="{{ $short }}" class="form-input">
                                        <option value="">
                                            @lang('default.form.dropdown.choose')
                                        </option>
                                        @foreach($toolQuestion->toolQuestionCustomValues as $toolQuestionCustomValue)
                                            <option value="{{ $toolQuestionCustomValue->short }}"
                                                    @if(old($short, $answer ?? $toolQuestion->options['value'] ?? null) == $toolQuestionCustomValue->short) selected @endif>
                                                {{ $toolQuestionCustomValue->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                @endcomponent
                            @else
                                <input name="{{ $short }}" value="{{ old($short, $answer) }}" class="form-input"
                                       id="{{ $short }}">
                            @endif
                        @endcomponent
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
</div>