@extends('cooperation.admin.layouts.app')

@section('content')
    @component('cooperation.frontend.layouts.components.form-group', [
       'withInputSource' => false,
       'id' => "search",
       'class' => 'w-full -mt-5',
       'inputName' => "search",
    ])
        <input type="text" class="form-input" id="search"
               placeholder="@lang('woningdossier.cooperation.admin.super-admin.translations.edit.search.placeholder')">
    @endcomponent

    <hr class="w-full">

    <h5 class="heading-5 w-full">
        @lang('woningdossier.cooperation.admin.super-admin.translations.edit.header', ['step_name' =>  $group])
    </h5>

    <form class="w-full flex flex-wrap space-y-5"
          action="{{route('cooperation.admin.super-admin.translations.update', ['group' => str_replace('/', '_', $group)])}}"
          method="POST" autocomplete="off">
        @csrf
        @method('PUT')

        <div class="w-full mt-5">
            <button type="submit" class="btn btn-green float-right">
                @lang('woningdossier.cooperation.admin.super-admin.translations.edit.save')
            </button>
        </div>

        @foreach($translations as $translation)
            {{-- Since we don't want the helptexts to show right here. --}}
            @if($translation->isNotHelpText())
                @php
                    $groupsToTreatAllTextsAsHelpText = [
                        'home',
                        'heat-pump',
                        'pdf/user-report',
                        'cooperation/mail/account-associated-with-cooperation',
                        'cooperation/mail/account-created',
                        'cooperation/mail/changed-email',
                        'cooperation/mail/confirm-account',
                        'cooperation/mail/reset-password',
                        'cooperation/mail/unread-message-count',
                    ];
                    $keysToTreatAsHelpText = [
                        'index.indication-for-costs-other.text',
                        'index.intro.decentrale-mechanische-ventilatie',
                        'index.intro.gebalanceerde-ventilatie',
                        'index.intro.mechanische-ventilatie',
                        'index.intro.natuurlijke-ventilatie',
                        'my-plan.calculations.description',
                    ];
                @endphp

                @foreach($translation->text as $locale => $text)
                    <div class="w-full rounded-lg border border-blue/50 p-4">
                        @component('cooperation.frontend.layouts.components.form-group', [
                           'withInputSource' => false,
                           'label' => __('woningdossier.cooperation.admin.super-admin.translations.edit.question', compact('locale')),
                           'id' => "translation-{$translation->id}",
                           'class' => 'w-full -mt-5',
                           'inputName' => "language_lines.{$locale}.question.{$translation->id}",
                        ])
                            @if(in_array($translation->key, $keysToTreatAsHelpText) || in_array($translation->group, $groupsToTreatAllTextsAsHelpText))
                                <textarea id="{{ "translation-{$translation->id}" }}" class="form-input question-input"
                                          name="language_lines[{{$locale}}][question][{{$translation->id}}]"
                                >{{$text}}</textarea>
                            @else
                                <input id="{{ "translation-{$translation->id}" }}" class="form-input question-input"
                                       name="language_lines[{{$locale}}][question][{{$translation->id}}]"
                                       value="{{$text}}">
                            @endif
                        @endcomponent
                        <label class="w-full text-blue-500 text-sm font-bold max-w-15/20">
                            {{"Key: {$translation->group}.{$translation->key}"}}
                        </label>

                        @if($translation->helpText instanceof \Spatie\TranslationLoader\LanguageLine)
                            @foreach($translation->helpText->text as $helpTextLocale => $helpText)
                                @component('cooperation.frontend.layouts.components.form-group', [
                                   'withInputSource' => false,
                                   'label' => __('woningdossier.cooperation.admin.super-admin.translations.edit.help', ['locale' => $helpTextLocale]),
                                   'id' => "help-text-translation-{$translation->helpText->id}",
                                   'class' => 'w-full',
                                   'inputName' => "language_lines.{$locale}.question.{$translation->id}",
                                ])
                                    <textarea id="{{ "help-text-translation-{$translation->helpText->id}" }}" class="form-input"
                                              name="language_lines[{{$helpTextLocale}}][help][{{$translation->helpText->id}}]"
                                    >{{$helpText}}</textarea>

                                    <input type="hidden" class="original-help-text"
                                           disabled="disabled" value="{{$helpText}}">
                                @endcomponent
                                <label class="w-full text-blue-500 text-sm font-bold max-w-15/20">
                                    {{"Key: {$translation->helpText->group}.{$translation->helpText->key}"}}
                                </label>
                            @endforeach
                        @endif

                        <div class="w-full rounded-lg border-blue-500/50">
                            @foreach($translation->subQuestions as $subQuestion)
{{--                                <br>--}}
{{--                                <a data-toggle="modal"--}}
{{--                                   data-target="#sub-question-modal-{{$subQuestion->id}}"--}}
{{--                                   class="label label-success">--}}
{{--                                    {{$subQuestion->text['nl']}}--}}
{{--                                </a>--}}
{{--                                @include('cooperation.admin.super-admin.translations.sub-question-modal', [--}}
{{--                                    'id' => 'sub-question-modal-'.$subQuestion->id,--}}
{{--                                    'subQuestion' => $subQuestion--}}
{{--                                ])--}}
                            @endforeach
                        </div>
                    </div>
                @endforeach
            @endif
        @endforeach

        <div class="w-full mt-5">
            <button type="submit" class="btn btn-green float-right">
                @lang('woningdossier.cooperation.admin.super-admin.translations.edit.save')
            </button>
        </div>
    </form>
@endsection

@push('js')
    <script type="module">

        {{--
         https://www.tiny.cloud/docs/configure/content-filtering/#forced_root_block
         --}}


        {{-- html mails are to sensitive to styling generated by the wysiwyg editor, so we restrict it when its mail. --}}
        @if(!\Illuminate\Support\Str::contains($group, 'mail'))
        tinymce.init({
            selector: 'textarea',
            @if($group == 'pdf/user-report')
            forced_root_block: "",
            @endif
            menubar: 'edit format',
            plugins: 'code link',
            toolbar: 'code link unlink bold italic underline strikethrough cut copy paste undo redo restoreOriginalText',
            promotion: false,
            language: 'nl',
            // Elements that should stay in the HTML upon submit
            extended_valid_elements: '#i[class|style]',
            skin: 'tinymce-5',
            height: 200,
            setup: function (editor) {
                editor.ui.registry.addButton('restoreOriginalText', {
                    text: 'Herstel tekst',
                    onAction: function (buttonApi) {
                        if (confirm('Orginele helptext herstellen? Dit verwijderd de huidige helptext en vervangt deze met de orginele.')) {
                            var originalHelpText = $(editor.targetElm).parent().find('.original-help-text').val();
                            editor.setContent(originalHelpText);
                        }
                    }
                });
            }
        });

        $(document).on('focusin', function (e) {
            var target = $(e.target);
            if (target.closest(".mce-window").length || target.closest(".tox-dialog").length) {
                e.stopImmediatePropagation();
                target = null;
            }
        });
        @endif

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

        document.addEventListener('DOMContentLoaded', function () {
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