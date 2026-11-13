{{--
    The questions about the dwelling SmartTwin does not answer, asked when registering in a cooperation
    with SmartTwin. See App\Services\SmartTwin\RegistrationQuestions.
--}}
@foreach(\App\Services\SmartTwin\RegistrationQuestions::questions()->values() as $index => $toolQuestion)
    @php $id = "tool-questions-{$toolQuestion->short}"; @endphp
    @component('cooperation.frontend.layouts.components.form-group', [
        'withInputSource' => false,
        'label' => __("auth.register.form.tool-questions.{$toolQuestion->short}"),
        'class' => 'w-full -mt-5 lg:w-1/2 ' . ($index % 2 === 0 ? 'lg:pr-3' : 'lg:pl-3'),
        'inputName' => "tool_questions.{$toolQuestion->short}",
        'id' => $id,
        'attr' => $attr ?? '',
    ])
        @component('cooperation.frontend.layouts.components.alpine-select')
            <select id="{{ $id }}" class="form-input hidden" name="tool_questions[{{ $toolQuestion->short }}]">
                <option value="" @if(is_null(old("tool_questions.{$toolQuestion->short}"))) selected @endif disabled>
                    @lang('auth.register.form.tool-questions.choose')
                </option>
                @foreach(\App\Services\SmartTwin\RegistrationQuestions::options($toolQuestion) as $value => $name)
                    <option value="{{ $value }}" @if(old("tool_questions.{$toolQuestion->short}") === (string) $value) selected @endif>
                        {{ $name }}
                    </option>
                @endforeach
            </select>
        @endcomponent
    @endcomponent
@endforeach
