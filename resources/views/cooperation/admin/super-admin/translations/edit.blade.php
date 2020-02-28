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
{{--            @lang('woningdossier.cooperation.admin.super-admin.translations.edit.header', ['step_name' =>  \App\Models\Step::where('short', $group)->first() instanceOf \App\Models\Step ? \App\Models\Step::where('group', $group)->first()->name : $stepSlug])--}}
            @lang('woningdossier.cooperation.admin.super-admin.translations.edit.header', ['step_name' =>  $group])
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <form action="{{route('cooperation.admin.super-admin.translations.update', ['group' => str_replace('/', '_', $group)])}}" method="post" autocomplete="off">
                        <div class="form-group">
                            <a href="{{route('cooperation.admin.super-admin.translations.index')}}"
                               class="btn btn-default"><i
                                        class="glyphicon glyphicon-chevron-left"></i> @lang('woningdossier.cooperation.tool.back-to-overview')
                            </a>
                            <button type="submit"
                                    class="btn btn-primary pull-right">@lang('woningdossier.cooperation.admin.super-admin.translations.edit.save')</button>
                        </div>
                        {{csrf_field()}}
                        {{method_field('PUT')}}
                        @foreach($translations as $translation)
                            <?php // since we dont want the helptexts to show right here.
                            ?>
                            @if($translation->isNotHelpText())
                                <div class="translations panel panel-default">
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <?php

                                                    $groupsToTreatAllTextsAsHelpText = ['home', 'heat-pump', 'pdf/user-report'];
                                                    $keysToTreatAsHelpText = ['index.indication-for-costs-other.text']
                                                ?>

                                                @foreach($translation->text as $locale => $text)
                                                    <div class="form-group">
                                                        <label for="">@lang('woningdossier.cooperation.admin.super-admin.translations.edit.question', ['locale' => $locale])</label>
                                                        @if(in_array($translation->key, $keysToTreatAsHelpText) || in_array($translation->group, $groupsToTreatAllTextsAsHelpText))
                                                            <textarea class="form-control question-input" name="language_lines[{{$locale}}][question][{{$translation->id}}]">{{$text}}</textarea>
                                                        @else
                                                            <input class="form-control question-input" name="language_lines[{{$locale}}][question][{{$translation->id}}]" value="{{$text}}">
                                                        @endif
                                                        <label for="">key: {{$translation->group}}.{{$translation->key}}</label>
                                                    </div>
                                                @endforeach
                                                @if($translation->helpText instanceof \Spatie\TranslationLoader\LanguageLine)
                                                    @foreach($translation->helpText->text as $locale => $text)
                                                        <div class="form-group">
                                                            <label for="">@lang('woningdossier.cooperation.admin.super-admin.translations.edit.help', ['locale' => $locale])</label>
                                                            <textarea class="form-control" name="language_lines[{{$locale}}][help][{{$translation->helpText->id}}]">{{$text}}</textarea>
                                                            <label for="">key: {{$translation->helpText->group}}.{{$translation->helpText->key}}</label>
                                                            <input type="hidden" class="original-help-text" disabled="disabled" value="{{$text}}">
                                                        </div>
                                                    @endforeach
                                                @endif
                                            </div>
                                        </div>
                                        @if($translation->subQuestions->isNotEmpty())
                                            <a data-toggle="collapse" data-target="#sub-questions-{{$translation->id}}"
                                               class="btn btn-primary">
                                                @lang('woningdossier.cooperation.admin.super-admin.translations.edit.sub-question')
                                            </a>
                                        @endif
                                    </div>
                                    <div class="panel-footer collapse" id="sub-questions-{{$translation->id}}">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                @foreach($translation->subQuestions as $subQuestion)
                                                    <br>
                                                    <a data-toggle="modal" data-target="#sub-question-modal-{{$subQuestion->id}}" class="label label-success">
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
                            @endif
                        @endforeach
                        <div class="form-group">
                            <a href="{{route('cooperation.admin.super-admin.translations.index')}}"
                               class="btn btn-default"><i
                                        class="glyphicon glyphicon-chevron-left"></i> @lang('woningdossier.cooperation.tool.back-to-overview')
                            </a>
                            <button type="submit"
                                    class="btn btn-primary pull-right">@lang('woningdossier.cooperation.admin.super-admin.translations.edit.save')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>

        {{--
         https://www.tiny.cloud/docs/configure/content-filtering/#forced_root_block
         --}}
        tinymce.init({
            selector: 'textarea',
            @if($group == 'pdf/user-report')
            forced_root_block: "",
            @endif
            menubar: 'edit format',
            plugins: 'code link',
            toolbar: 'code link unlink bold italic underline strikethrough cut copy paste undo redo restoreOriginalText ',
            language: 'nl',
            setup: function (editor) {
                editor.ui.registry.addButton('restoreOriginalText', {
                    text: 'Herstel tekst',
                    onAction: function (buttonApi) {
                        if (confirm('Orginele helptext herstellen ? Dit verwijderd de huidige helptext en vervangt deze met de orginele.')) {
                            var originalHelpText = $(editor.targetElm).parent().find('.original-help-text').val();
                            editor.setContent(originalHelpText);
                        }
                    }
                });
            }
        });

        $(document).on('focusin', function(e) {
            var target = $(e.target);
            if (target.closest(".mce-window").length || target.closest(".tox-dialog").length) {
                e.stopImmediatePropagation();
                target = null;
            }
        });


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