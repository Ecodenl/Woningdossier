@extends('cooperation.admin.layouts.app', [
    'panelTitle' => __('cooperation/admin/super-admin/tool-questions.edit.header')
])

@section('content')
    <form class="flex flex-wrap w-full"
          action="{{route('cooperation.admin.super-admin.tool-questions.update', compact('toolQuestion'))}}"
          method="POST">
        @csrf
        @method('PUT')

        @foreach($toolQuestion->getTranslations('name') as $locale => $translation)
            @component('cooperation.frontend.layouts.components.form-group', [
               'withInputSource' => false,
               'id' => "tool-questions-name",
               'label' => $loop->first ? __('cooperation/admin/super-admin/tool-questions.edit.form.name') : '',
               'class' => 'w-full -mt-5',
               'inputName' => 'tool_questions.name',
            ])
                <div class="input-group-prepend">
                    {{ $locale }}
                </div>
                <input class="form-input" type="text" name="tool_questions[name][{{$locale}}]"
                       value="{{old("tool_questions.name.{$locale}", $translation)}}">
            @endcomponent
        @endforeach

        @foreach($toolQuestion->getTranslations('help_text') as $locale => $translation)
            @component('cooperation.frontend.layouts.components.form-group', [
               'withInputSource' => false,
               'id' => "tool-questions-help-text",
               'label' => $loop->first ? __('cooperation/admin/super-admin/tool-questions.edit.form.help-text') : '',
               'class' => 'w-full ' . (! $loop->first ? '-mt-5' : ''),
               'inputName' => 'tool_questions.help_text',
            ])
                <div class="input-group-prepend">
                    {{ $locale }}
                </div>
                <textarea class="form-input" name="tool_questions[help_text][{{$locale}}]"
                >{{old("tool_questions.help_text.{$locale}", $translation)}}</textarea>
            @endcomponent
        @endforeach

        <div class="w-full mt-5">
            <button class="btn btn-green">
                @lang('cooperation/admin/super-admin/tool-questions.edit.form.submit')
            </button>
        </div>
    </form>
@endsection
