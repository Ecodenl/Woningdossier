@extends('cooperation.admin.layouts.app', [
    'panelTitle' => '<strong>' . __('cooperation/admin/super-admin/translations.edit.header', ['step_name' =>  $group]) . '</strong>'
])

@section('content')
    @component('cooperation.frontend.layouts.components.form-group', [
       'withInputSource' => false,
       'id' => "search",
       'class' => 'w-full -mt-5',
       'inputName' => "search",
    ])
        <input type="text" class="form-input" id="search" autocomplete="off"
               placeholder="@lang('cooperation/admin/super-admin/translations.edit.search.placeholder')">
    @endcomponent

    <hr class="w-full mb-0">

    <form class="w-full flex flex-wrap space-y-5"
          action="{{route('cooperation.admin.super-admin.translations.update', ['group' => str_replace('/', '_', $group)])}}"
          method="POST" autocomplete="off">
        @csrf
        @method('PUT')

        <div class="w-full mt-5">
            <button type="submit" class="btn btn-green float-right">
                @lang('cooperation/admin/super-admin/translations.edit.save')
            </button>
        </div>

        @foreach($translations as $translation)
            {{-- Since we don't want the helptexts to show right here. --}}
            @if($translation->isNotHelpText())
                @php
                    $noWysiwyg = Str::contains($group, 'mail');

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
                           'label' => __('cooperation/admin/super-admin/translations.edit.question', compact('locale')),
                           'id' => "translation-{$translation->id}",
                           'class' => 'w-full -mt-5',
                           'inputName' => "language_lines.{$locale}.question.{$translation->id}",
                        ])
                            @if(in_array($translation->key, $keysToTreatAsHelpText) || in_array($translation->group, $groupsToTreatAllTextsAsHelpText))
                                @include('cooperation.admin.super-admin.translations.parts.textarea', [
                                    'id' => "translation-{$translation->id}",
                                    'noWysiwyg' => $noWysiwyg,
                                    'content' => $text,
                                    'htmlName' => "language_lines[{$locale}][question][{$translation->id}]",
                                ])
                            @else
                                <input id="{{ "translation-{$translation->id}" }}" class="form-input question-input mb-0"
                                       name="language_lines[{{$locale}}][question][{{$translation->id}}]"
                                       value="{{$text}}">
                            @endif
                            <label class="w-full text-blue text-sm font-bold key-label">
                                {{"Key: {$translation->group}.{$translation->key}"}}
                            </label>
                        @endcomponent

                        @if($translation->helpText instanceof \Spatie\TranslationLoader\LanguageLine)
                            @foreach($translation->helpText->text as $helpTextLocale => $helpText)
                                @component('cooperation.frontend.layouts.components.form-group', [
                                   'withInputSource' => false,
                                   'label' => __('cooperation/admin/super-admin/translations.edit.help', ['locale' => $helpTextLocale]),
                                   'id' => "help-text-translation-{$translation->helpText->id}",
                                   'class' => 'w-full',
                                   'inputName' => "language_lines.{$helpTextLocale}.help.{$translation->helpText->id}",
                                ])
                                    @include('cooperation.admin.super-admin.translations.parts.textarea', [
                                        'id' => "help-text-translation-{$translation->helpText->id}",
                                        'noWysiwyg' => $noWysiwyg,
                                        'content' => $helpText,
                                        'htmlName' => "language_lines[{$helpTextLocale}][help][{$translation->helpText->id}]",
                                    ])
                                    <label class="w-full text-blue text-sm font-bold key-label">
                                        {{"Key: {$translation->helpText->group}.{$translation->helpText->key}"}}
                                    </label>
                                @endcomponent
                            @endforeach
                        @endif

                        @if($translation->subQuestions->isNotEmpty())
                            <div class="w-full rounded-lg border border-blue-500/50 mt-5 bg-gray/25 p-4">
                                <h5 class="w-full heading-5">
                                    @lang('cooperation/admin/super-admin/translations.edit.sub-questions')
                                </h5>
                                @foreach($translation->subQuestions as $subQuestion)
                                    @foreach($subQuestion->text as $locale => $text)
                                        @component('cooperation.frontend.layouts.components.form-group', [
                                           'withInputSource' => false,
                                           'label' => __('cooperation/admin/super-admin/translations.edit.question', compact('locale')),
                                           'id' => "sub-question-translation-{$subQuestion->id}",
                                           'class' => 'w-full',
                                           'inputName' => "language_lines.{$locale}.question.{$subQuestion->id}",
                                        ])
                                            <input id="{{ "sub-question-translation-{$subQuestion->id}" }}"
                                                   class="form-input question-input mb-0"
                                                   name="language_lines[{{$locale}}][question][{{$subQuestion->id}}]"
                                                   value="{{$text}}">
                                            <label class="w-full text-blue text-sm font-bold key-label">
                                                {{"Key: {$subQuestion->group}.{$subQuestion->key}"}}
                                            </label>
                                        @endcomponent
                                    @endforeach

                                    @if($subQuestion->helpText instanceof \Spatie\TranslationLoader\LanguageLine)
                                        @foreach($subQuestion->helpText->text as $locale => $text)
                                            @component('cooperation.frontend.layouts.components.form-group', [
                                               'withInputSource' => false,
                                               'label' => __('cooperation/admin/super-admin/translations.edit.help', compact('locale')),
                                               'id' => "sub-question-help-text-translation-{$subQuestion->helpText->id}",
                                               'class' => 'w-full',
                                               'inputName' => "language_lines.{$locale}.help.{$subQuestion->helpText->id}",
                                            ])
                                                @include('cooperation.admin.super-admin.translations.parts.textarea', [
                                                    'id' => "sub-question-help-text-translation-{$subQuestion->helpText->id}",
                                                    'noWysiwyg' => $noWysiwyg,
                                                    'content' => $text,
                                                    'htmlName' => "language_lines[{$locale}][help][{$subQuestion->helpText->id}]",
                                                ])
                                                <label class="w-full text-blue text-sm font-bold key-label">
                                                    {{"Key: {$subQuestion->helpText->group}.{$subQuestion->helpText->key}"}}
                                                </label>
                                            @endcomponent
                                        @endforeach
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            @endif
        @endforeach

        <div class="w-full mt-5">
            <button type="submit" class="btn btn-green float-right">
                @lang('cooperation/admin/super-admin/translations.edit.save')
            </button>
        </div>
    </form>
@endsection

@push('js')
    <script type="module" nonce="{{ $cspNonce }}">
        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('search').addEventListener('input', function () {
                const search = this.value;

                document.querySelectorAll('.question-input').forEach((input) => {
                    const formGroup = input.closest('.form-group');

                    if (! search?.trim()) {
                        formGroup.style.display = null;
                    } else {
                        const visible = searchValue(input.value, search)
                            || searchValue(formGroup.querySelector('label.form-label').textContent, search)
                            || searchValue(formGroup.querySelector('label.key-label').textContent, search);

                        formGroup.style.display = visible ? null : 'none';
                    }
                });
            });
        });
    </script>
@endpush