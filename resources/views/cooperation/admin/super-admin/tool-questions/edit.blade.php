@extends('cooperation.admin.layouts.app')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('cooperation/admin/super-admin/tool-questions.edit.header')
        </div>

        <div class="panel-body">
            <form action="{{route('cooperation.admin.super-admin.tool-questions.update', compact('toolQuestion'))}}" method="post">
                <div class="form-group">
                    <a href="{{route('cooperation.admin.super-admin.tool-questions.index')}}"
                       class="btn btn-default">
                        <i class="glyphicon glyphicon-chevron-left"></i>
                        @lang('woningdossier.cooperation.tool.back-to-overview')
                    </a>
                </div>
                @csrf
                @method('put')
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label for="tool-questions">@lang('cooperation/admin/super-admin/tool-questions.edit.form.name')</label>
                            @foreach($toolQuestion->getTranslations('name') as $locale => $translation)
                                <input class="form-control" type="text" name="tool_questions[name][{{$locale}}]" value="{{old("tool_questions.name.{$locale}", $translation)}}">
                            @endforeach
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label for="tool-questions">@lang('cooperation/admin/super-admin/tool-questions.edit.form.help-text')</label>
                            @foreach($toolQuestion->getTranslations('help_text') as $locale => $translation)
                                <textarea class="form-control"  name="tool_questions[help_text][{{$locale}}]">{{old("tool_questions.help_text.{$locale}", $translation)}}</textarea>
                            @endforeach
                        </div>
                    </div>

                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <button class="btn btn-primary">@lang('cooperation/admin/super-admin/tool-questions.edit.form.submit')</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
