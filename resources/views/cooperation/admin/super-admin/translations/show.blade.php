@extends('cooperation.admin.layouts.app')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                <input type="text" class="form-control" id="search" placeholder="@lang('woningdossier.cooperation.admin.super-admin.translations.edit.search.placeholder')">
            </div>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('woningdossier.cooperation.admin.super-admin.translations.edit.header')
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    @foreach($questions as $question)
                        <div class="translations panel panel-default">
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-sm-12">
                                        @foreach($question->text as $locale => $text)
                                            <div class="form-group">
                                                <label for="">@lang('woningdossier.cooperation.admin.super-admin.translations.edit.question', ['locale' => $locale])</label>
                                                <textarea class="form-control"
                                                          name="translation_line[{{$question->id}}]">{{$text}}</textarea>
                                                <label for="">key: {{$question->group}}.{{$question->key}}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                @if($question->subQuestions->isNotEmpty())
                                    <button data-toggle="collapse" data-target="#sub-questions-{{$question->id}}" class="btn btn-primary">
                                        @lang('woningdossier.cooperation.admin.super-admin.translations.edit.sub-question')
                                    </button>
                                @endif
                            </div>
                            <div class="panel-footer collapse" id="sub-questions-{{$question->id}}">
                                <div class="row">
                                    <div class="col-sm-12">
                                        @foreach($question->subQuestions as $subQuestion)
                                            <br>
                                            <a class="label label-success" href="{{$subQuestion->id}}">
                                                {{$subQuestion->text['nl']}}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        /**
         * Remove the ï ê etc from a string.
         *
         * @param str
         * @returns {string|void|never}
         */
        function removeDiacritics(str) {
            return str.replace(/[^\u0000-\u007E]/g, function (string) {
                return diacriticsMap[string] || string;
            });
        }

        $(document).ready(function(){
            $('#search').on("keyup", function() {
                var value = removeDiacritics($(this).val()).toLowerCase();
                $('textarea').filter(function() {

                    var translationsPanel = $(this).parent().parent().parent().parent().parent();
                    var cleanTranslation = removeDiacritics($(this).text());

                    translationsPanel.toggle(cleanTranslation.toLowerCase().indexOf(value) > -1)
                });
            });
        });
    </script>
@endpush