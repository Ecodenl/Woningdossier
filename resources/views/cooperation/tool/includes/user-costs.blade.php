<div class="user-costs">
    @foreach($userCosts as $measureId => $questions)
        @php $measureApplication = \App\Models\MeasureApplication::find($measureId); @endphp
        <div class="flex flex-row flex-wrap w-full mt-2" id="user-cost-{{$measureId}}">
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

                        if (Str::startsWith($short, 'execute')) {
                            $isDropdown = true;
                            preg_match('/execute-(.*)-how/', $short, $matches);
                            $name = "execute.{$matches[1]}.how";
                        } else {
                            $isDropdown = false;
                            $name = \App\Helpers\Models\UserCostHelper::resolveNameFromShort($short);
                        }
                    @endphp
                    <div class="w-full sm:w-1/3">
                        @component('cooperation.frontend.layouts.components.form-group', [
                            'inputName' => $name,
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
                                    <select name="{{ \App\Helpers\Str::dotToHtml($name) }}" class="form-input">
                                        <option value="">
                                            @lang('default.form.dropdown.choose')
                                        </option>
                                        @foreach($toolQuestion->toolQuestionCustomValues as $toolQuestionCustomValue)
                                            <option value="{{ $toolQuestionCustomValue->short }}"
                                                    @if(old($name, $answer) == $toolQuestionCustomValue->short) selected @endif>
                                                {{ $toolQuestionCustomValue->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                @endcomponent
                            @else
                                <input name="{{ \App\Helpers\Str::dotToHtml($name) }}" value="{{ old($name, $answer) }}" class="form-input"
                                       id="{{ $short }}">
                            @endif
                        @endcomponent
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
</div>