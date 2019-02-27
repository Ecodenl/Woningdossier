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
            @lang('woningdossier.cooperation.admin.super-admin.translations.edit.header', ['step_name' =>  \App\Models\Step::where('slug', $stepSlug)->first() instanceOf \App\Models\Step ? \App\Models\Step::where('slug', $stepSlug)->first()->name : $stepSlug])
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <form action="{{route('cooperation.admin.super-admin.translations.update', ['step-slug' => $stepSlug])}}" method="post">
                        <div class="form-group">
                            <button type="submit" class="btn btn-block btn-primary">@lang('woningdossier.cooperation.admin.super-admin.translations.edit.save')</button>
                        </div>
                        {{csrf_field()}}
                        {{method_field('PUT')}}
                        @foreach($questions as $question)
                            <div class="translations panel panel-default">
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            @foreach($question->text as $locale => $text)
                                                <div class="form-group">
                                                    <label for="">@lang('woningdossier.cooperation.admin.super-admin.translations.edit.question', ['locale' => $locale])</label>
                                                    <input class="form-control question-input" name="language_lines[{{$locale}}][question][{{$question->id}}]" value="{{$text}}">
                                                    <label for="">key: {{$question->group}}.{{$question->key}}</label>
                                                </div>
                                            @endforeach
                                            @forelse($question->helpText->text as $locale => $text)
                                                <div class="form-group">
                                                    <label for="">@lang('woningdossier.cooperation.admin.super-admin.translations.edit.help', ['locale' => $locale])</label>
                                                    <textarea class="form-control" name="language_lines[{{$locale}}][help][{{$question->helpText->id}}]">{{$text}}</textarea>
                                                    <label for="">key: {{$question->helpText->group}}.{{$question->helpText->key}}</label>
                                                </div>
                                            @empty
                                            @endforelse
                                        </div>
                                    </div>
                                    @if($question->subQuestions->isNotEmpty())
                                        <a data-toggle="collapse" data-target="#sub-questions-{{$question->id}}"
                                                class="btn btn-primary">
                                            @lang('woningdossier.cooperation.admin.super-admin.translations.edit.sub-question')
                                        </a>
                                    @endif
                                </div>
                                <div class="panel-footer collapse" id="sub-questions-{{$question->id}}">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            @foreach($question->subQuestions as $subQuestion)
                                                <br>
                                                <a data-toggle="modal"
                                                   data-target="#sub-question-modal-{{$subQuestion->id}}"
                                                   class="label label-success">
                                                    {{$subQuestion->text['nl']}}
                                                </a>
                                                @include('cooperation.admin.super-admin.translations.sub-question-modal', [
                                                    'id' => 'sub-question-modal-'.$subQuestion->id,
                                                    'subQuestion' => $subQuestion
                                                ])
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        <div class="form-group">
                            <button type="submit" class="btn btn-block btn-primary">@lang('woningdossier.cooperation.admin.super-admin.translations.edit.save')</button>
                        </div>
                    </form>
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

        $(document).ready(function () {
            $('#search').on("keyup", function () {
                var value = removeDiacritics($(this).val()).toLowerCase();
                $('.question-input').filter(function () {

                    var translationsPanel = $(this).parent().parent().parent().parent().parent();
                    var cleanTranslation = removeDiacritics($(this).val());

                    translationsPanel.toggle(cleanTranslation.toLowerCase().indexOf(value) > -1)
                });
            });
        });
    </script>
@endpush