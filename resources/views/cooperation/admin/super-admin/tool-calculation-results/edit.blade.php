@extends('cooperation.admin.layouts.app', [
    'panelTitle' => __('cooperation/admin/super-admin/tool-calculation-results.edit.header')
])

@section('content')
    <form class="flex flex-wrap w-full"
          action="{{route('cooperation.admin.super-admin.tool-calculation-results.update', compact('toolCalculationResult'))}}"
          method="POST">
        @csrf
        @method('PUT')

        @foreach($toolCalculationResult->getTranslations('name') as $locale => $translation)
            @component('cooperation.frontend.layouts.components.form-group', [
               'withInputSource' => false,
               'id' => "tool-calculation-results-name",
               'label' => $loop->first ? __('cooperation/admin/super-admin/tool-calculation-results.edit.form.name') : '',
               'class' => 'w-full -mt-5',
               'inputName' => 'tool_calculation_results.name',
            ])
                <div class="input-group-prepend">
                    {{ $locale }}
                </div>
                <input class="form-input" type="text" name="tool_calculation_results[name][{{$locale}}]"
                       value="{{old("tool_calculation_results.name.{$locale}", $translation)}}">
            @endcomponent
        @endforeach

        @php
            $helpTexts = array_filter($toolCalculationResult->getTranslations('help_text'));
            $helpTexts = empty($helpTexts) ? ['nl' => ''] : $helpTexts;
        @endphp
        @foreach($helpTexts as $locale => $translation)
            @component('cooperation.frontend.layouts.components.form-group', [
               'withInputSource' => false,
               'id' => "tool-calculation-results-help-text",
               'label' => $loop->first ? __('cooperation/admin/super-admin/tool-calculation-results.edit.form.help-text') : '',
               'class' => 'w-full ' . (! $loop->first ? '-mt-5' : ''),
               'inputName' => 'tool_calculation_results.help_text',
            ])
                <div class="input-group-prepend">
                    {{ $locale }}
                </div>
                <textarea class="form-input" name="tool_calculation_results[help_text][{{$locale}}]"
                >{{old("tool_calculation_results.help_text.{$locale}", $translation)}}</textarea>
            @endcomponent
        @endforeach

        <div class="w-full mt-5">
            <button class="btn btn-green">
                @lang('cooperation/admin/super-admin/tool-calculation-results.edit.form.submit')
            </button>
        </div>
    </form>
@endsection
