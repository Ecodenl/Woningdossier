@php
    // Either 'scan' or 'admin'
    $mode ??= 'admin';

    $colors = [
        '' => 'Geen kleur',
        '#000000' => 'Zwart',
        '#FFFFFF' => 'Wit',
    ];

    $sizes = [
    //    '' => 'Reset font size',
    //    '3rem' => 'H1',
    //    '1rem' => 'Paragraph (1rem)',
    ];

    $linkStyles = [
        //'' => 'Standaard link',
        //'btn btn-blue' => 'Blauwe knop',
    ];

    $disableToolbar ??= false;
@endphp

<div class="tiptap-parent" id="{{ Str::random() }}">
    <div class="tiptap-menu">
        <button type="button" data-tiptap="undo" title="Undo" @if($disableToolbar) disabled @endif>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
            </svg>
        </button>
        <button type="button" data-tiptap="redo" title="Redo" @if($disableToolbar) disabled @endif>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m15 15 6-6m0 0-6-6m6 6H9a6 6 0 0 0 0 12h3" />
            </svg>
        </button>

        <div class="spacer"></div>

{{--        @component('admin.shared.components.dropdown', [--}}
{{--            'dropdownTitle' => 'Text style',--}}
{{--            'actionMenu' => true,--}}
{{--        ])--}}
{{--            @slot('label')--}}
{{--                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">--}}
{{--                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25H12" />--}}
{{--                </svg>--}}
{{--            @endslot--}}

{{--            <ul>--}}
{{--                @for($i = 1; $i < 5; $i++)--}}
{{--                    <li>--}}
{{--                        <button type="button" data-tiptap="toggleHeading,{{$i}}"--}}
{{--                                title="Header {{$i}}">--}}
{{--                            {{ "Header {$i}" }}--}}
{{--                        </button>--}}
{{--                    </li>--}}
{{--                @endfor--}}
{{--            </ul>--}}
{{--        @endcomponent--}}

{{--        <div class="spacer"></div>--}}

        @if(! empty($sizes))
            @component('cooperation.layouts.components.dropdown', [
                'dropdownTitle' => 'Font size',
                'actionMenu' => true,
            ])
                @slot('label')
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 8.25h15m-16.5 7.5h15m-1.8-13.5-3.9 19.5m-2.1-19.5-3.9 19.5" />
                    </svg>
                @endslot

                <ul>
                    @foreach($sizes as $size => $label)
                        <li>
                            <button type="button" data-tiptap="{{ empty($size) ? 'unsetFontSize' : "setFontSize,{$size}" }}"
                                    title="{{ $label }}" @if($disableToolbar) disabled @endif>
                                {{ $label }}
                            </button>
                        </li>
                    @endforeach
                </ul>
            @endcomponent

            <div class="spacer"></div>
        @endif

        <button type="button" data-tiptap="toggleBold" title="Bold" @if($disableToolbar) disabled @endif>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linejoin="round" d="M6.75 3.744h-.753v8.25h7.125a4.125 4.125 0 0 0 0-8.25H6.75Zm0 0v.38m0 16.122h6.747a4.5 4.5 0 0 0 0-9.001h-7.5v9h.753Zm0 0v-.37m0-15.751h6a3.75 3.75 0 1 1 0 7.5h-6m0-7.5v7.5m0 0v8.25m0-8.25h6.375a4.125 4.125 0 0 1 0 8.25H6.75m.747-15.38h4.875a3.375 3.375 0 0 1 0 6.75H7.497v-6.75Zm0 7.5h5.25a3.75 3.75 0 0 1 0 7.5h-5.25v-7.5Z" />
            </svg>
        </button>
        <button type="button" data-tiptap="toggleItalic" title="Italic" @if($disableToolbar) disabled @endif>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5.248 20.246H9.05m0 0h3.696m-3.696 0 5.893-16.502m0 0h-3.697m3.697 0h3.803" />
            </svg>
        </button>
        <button type="button" data-tiptap="toggleUnderline" title="Underline" @if($disableToolbar) disabled @endif>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17.995 3.744v7.5a6 6 0 1 1-12 0v-7.5m-2.25 16.502h16.5" />
            </svg>
        </button>
        <button type="button" data-tiptap="toggleStrike" title="Strikethrough" @if($disableToolbar) disabled @endif>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 12a8.912 8.912 0 0 1-.318-.079c-1.585-.424-2.904-1.247-3.76-2.236-.873-1.009-1.265-2.19-.968-3.301.59-2.2 3.663-3.29 6.863-2.432A8.186 8.186 0 0 1 16.5 5.21M6.42 17.81c.857.99 2.176 1.812 3.761 2.237 3.2.858 6.274-.23 6.863-2.431.233-.868.044-1.779-.465-2.617M3.75 12h16.5" />
            </svg>
        </button>

{{--        <div class="spacer"></div>--}}

{{--        @component('admin.shared.components.dropdown', [--}}
{{--            'dropdownTitle' => 'Blocks',--}}
{{--            'actionMenu' => true,--}}
{{--        ])--}}
{{--            @slot('label')--}}
{{--                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">--}}
{{--                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.25 9.75 16.5 12l-2.25 2.25m-4.5 0L7.5 12l2.25-2.25M6 20.25h12A2.25 2.25 0 0 0 20.25 18V6A2.25 2.25 0 0 0 18 3.75H6A2.25 2.25 0 0 0 3.75 6v12A2.25 2.25 0 0 0 6 20.25Z" />--}}
{{--                </svg>--}}
{{--            @endslot--}}

{{--            <ul>--}}
{{--                <li>--}}
{{--                    <button type="button" data-tiptap="toggleBlockquote"--}}
{{--                            title="Blockquote">--}}
{{--                        Blockquote--}}
{{--                    </button>--}}
{{--                </li>--}}
{{--                <li>--}}
{{--                    <button type="button" data-tiptap="toggleCodeBlock"--}}
{{--                            title="Code">--}}
{{--                        Code--}}
{{--                    </button>--}}
{{--                </li>--}}
{{--            </ul>--}}
{{--        @endcomponent--}}

        <div class="spacer"></div>

        <button type="button" data-tiptap="toggleLink" @if($disableToolbar) disabled @endif
                title="Link">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-.622 1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 4.5a4.5 4.5 0 0 0 1.242 7.244" />
            </svg>
        </button>
        <!-- Hidden, clicked programmatically -->
        <button type="button" data-tiptap="setLink" class="hidden"
                title="Link">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-.622 1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 4.5a4.5 4.5 0 0 0 1.242 7.244" />
            </svg>
        </button>

        @if($mode === 'scan')
            <div class="spacer"></div>

            <button type="button" data-tiptap="toggleBulletList" @if($disableToolbar) disabled @endif
                    title="Bullet list">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0ZM3.75 12h.007v.008H3.75V12Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm-.375 5.25h.007v.008H3.75v-.008Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                </svg>
            </button>
            <button type="button" data-tiptap="toggleOrderedList" @if($disableToolbar) disabled @endif
                    title="Numbered list">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.242 5.992h12m-12 6.003H20.24m-12 5.999h12M4.117 7.495v-3.75H2.99m1.125 3.75H2.99m1.125 0H5.24m-1.92 2.577a1.125 1.125 0 1 1 1.591 1.59l-1.83 1.83h2.16M2.99 15.745h1.125a1.125 1.125 0 0 1 0 2.25H3.74m0-.002h.375a1.125 1.125 0 0 1 0 2.25H2.99" />
                </svg>
            </button>
        @endif

{{--        <div class="spacer"></div>--}}

{{--        @component('admin.shared.components.dropdown', [--}}
{{--            'dropdownTitle' => 'Font color',--}}
{{--            'actionMenu' => true,--}}
{{--        ])--}}
{{--            @slot('label')--}}
{{--                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">--}}
{{--                    <path stroke-linecap="round" stroke-linejoin="round" d="m15 11.25 1.5 1.5.75-.75V8.758l2.276-.61a3 3 0 1 0-3.675-3.675l-.61 2.277H12l-.75.75 1.5 1.5M15 11.25l-8.47 8.47c-.34.34-.8.53-1.28.53s-.94.19-1.28.53l-.97.97-.75-.75.97-.97c.34-.34.53-.8.53-1.28s.19-.94.53-1.28L12.75 9M15 11.25 12.75 9" />--}}
{{--                </svg>--}}
{{--            @endslot--}}

{{--            <ul>--}}
{{--                @foreach($colors as $color => $trans)--}}
{{--                    <li>--}}
{{--                        <button type="button" data-tiptap="{{ empty($color) ? 'unsetColor' : "setColor,{$color}" }}"--}}
{{--                                title="{{ $trans }}" class="border border-solid border-blue-500 border-opacity-50 rounded-lg m-2 h-8"--}}
{{--                               style="background-color: {{empty($color) ? 'white' : $color}};">--}}
{{--                            @empty($color)--}}
{{--                                <svg class="text-red-500">--}}
{{--                                    <path stroke="currentColor" stroke-width="2" d="M21 3L3 21" fill-rule="evenodd"></path>--}}
{{--                                </svg>--}}
{{--                            @endempty--}}
{{--                        </button>--}}
{{--                    </li>--}}
{{--                @endforeach--}}
{{--            </ul>--}}
{{--        @endcomponent--}}

{{--        @component('admin.shared.components.dropdown', [--}}
{{--            'dropdownTitle' => 'Background color',--}}
{{--            'actionMenu' => true,--}}
{{--        ])--}}
{{--            @slot('label')--}}
{{--                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">--}}
{{--                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.53 16.122a3 3 0 0 0-5.78 1.128 2.25 2.25 0 0 1-2.4 2.245 4.5 4.5 0 0 0 8.4-2.245c0-.399-.078-.78-.22-1.128Zm0 0a15.998 15.998 0 0 0 3.388-1.62m-5.043-.025a15.994 15.994 0 0 1 1.622-3.395m3.42 3.42a15.995 15.995 0 0 0 4.764-4.648l3.876-5.814a1.151 1.151 0 0 0-1.597-1.597L14.146 6.32a15.996 15.996 0 0 0-4.649 4.763m3.42 3.42a6.776 6.776 0 0 0-3.42-3.42" />--}}
{{--                </svg>--}}
{{--            @endslot--}}

{{--            <ul>--}}
{{--                @foreach($colors as $color => $trans)--}}
{{--                    <li>--}}
{{--                        <button type="button" data-tiptap="{{ empty($color) ? 'unsetBackColor' : "setBackColor,{$color}" }}"--}}
{{--                                title="{{ $trans }}" class="border border-solid border-blue-500 border-opacity-50 rounded-lg m-2 h-8"--}}
{{--                               style="background-color: {{empty($color) ? 'white' : $color}};">--}}
{{--                            @empty($color)--}}
{{--                                <svg class="text-red-500">--}}
{{--                                    <path stroke="currentColor" stroke-width="2" d="M21 3L3 21" fill-rule="evenodd"></path>--}}
{{--                                </svg>--}}
{{--                            @endempty--}}
{{--                        </button>--}}
{{--                    </li>--}}
{{--                @endforeach--}}
{{--            </ul>--}}
{{--        @endcomponent--}}

        @if(! empty($menuSlot))
            <div class="spacer"></div>
            {{ $menuSlot }}
        @endif
    </div>

    {{-- The textarea --}}
    {{ $slot }}

    {{--
        Any extra elements that need to be initiated _after_ the textarea should be initialized via Tiptap, since
        Tiptap initializes at the end of the parent container.
    --}}
</div>

@pushonce('js')
    @component('cooperation.frontend.layouts.components.modal', [
        'header' => 'Link toevoegen',
        'id' => 'tiptap-link-modal',
        'attr' => 'x-data="modal()"',
    ])
        <form id="tiptap-link-form">
            @component('cooperation.frontend.layouts.components.form-group', [
                'withInputSource' => false,
                'label' => "Geselecteerde tekst",
                'inputName' => 'tiptap.link.content',
                'id' => 'tiptap-link-content',
            ])
                <input id="tiptap-link-content" name="tiptap[link][content]" class="form-input" type="text"
                       readonly>
            @endcomponent
            @component('cooperation.frontend.layouts.components.form-group', [
                'withInputSource' => false,
                'inputName' => 'tiptap.link.href',
                'id' => 'tiptap-link-href',
            ])
                @slot('label')
                    URL
                    <br>
                    <small>
                        Interne link: /interne-link<br>
                        Externe link: https://hoomdossier.nl/externe-link<br>
                        Laat het URL veld leeg om de link te verwijderen
                    </small>
                @endslot
                <input id="tiptap-link-href" name="tiptap[link][href]" class="form-input" type="text">
            @endcomponent
            @if(! empty($linkStyles))
                @component('cooperation.frontend.layouts.components.form-group', [
                    'withInputSource' => false,
                    'label' => 'Style',
                    'inputName' => 'tiptap.link.style',
                    'id' => 'tiptap-link-style',
                ])
                    @foreach($linkStyles as $classes => $trans)
                        <div class="block">
                            <input id="tiptap-link-style-{{$loop->iteration}}"
                                   class="inline-block cursor-pointer tiptap-link-style"
                                   name="tiptap[link][style]"
                                   type="radio" value="{{ $classes }}">
                            <label for="tiptap-link-style-{{$loop->iteration}}" class="inline-block font-normal cursor-pointer {{$classes}}">
                                {{ $trans }}
                            </label>
                        </div>
                    @endforeach
                @endcomponent
            @endif

            <input id="tiptap-id" type="hidden">
        </form>

        <button id="tiptap-modal-ok" type="button" class="btn btn-green w-full mx-0 mt-3 md:mt-0">
            @lang('default.buttons.save')
        </button>
    @endcomponent

    <script type="module" nonce="{{ $cspNonce }}">
        document.getElementById('tiptap-modal-ok').addEventListener('click', () => {
            let id = document.getElementById('tiptap-id').value;

            document.getElementById(id).querySelector('[data-tiptap="setLink"]').triggerEvent('click');

            document.getElementById('tiptap-link-modal').triggerEvent('close-modal');
        });

        document.addEventListener('click', (event) => {
            let target = event.target.closest('[data-tiptap]') ?? event.target;
            let linkTarget = event.target.tagName === 'A' ? event.target : event.target.closest('.tiptap a');

            if (target.hasAttribute('data-tiptap')) {
                let parent = target.closest('.tiptap-parent');
                let editor = parent.querySelector('.tiptap').editor;
                let state = editor.state;
                let selection = state.selection;
                let currentText = state.doc.textBetween(selection.from, selection.to);
                let command = target.getAttribute('data-tiptap');

                callTiptap(parent, editor, currentText, command);
            } else if (linkTarget?.tagName === 'A') {
                let editorContainer = linkTarget.closest('.tiptap');

                // This means we clicked a link _inside_ the editor
                if (editorContainer) {
                    event.preventDefault();

                    let classes = Array.from(linkTarget.classList).join(' ');

                    let editor = editorContainer.editor;
                    document.getElementById('tiptap-id').value = editorContainer.closest('.tiptap-parent').getAttribute('id');
                    // Because seemingly we cannot get the current node from this editor??
                    document.getElementById('tiptap-link-content').value = linkTarget.textContent;
                    // Because seemingly when the cursor is at the end or start of the element it doesn't know that
                    // we're "in" the link
                    document.getElementById('tiptap-link-href').value = linkTarget.getAttribute('href'); //editor.getAttributes('link').href;
                    @if(! empty($linkStyles))
                    document.querySelector(`.tiptap-link-style[value="${classes}"]`).checked = true;
                    @endif

                    document.getElementById('tiptap-link-modal').triggerEvent('open-modal');
                }
            }
        });

        function callTiptap(parent, editor, currentText, command) {
            let commandParam = null;
            let params = [];

            if (command.includes(',')) {
                let parts = command.split(',');
                command = parts[0];
                commandParam = parts[1];
            }

            switch (command) {
                case 'toggleLink':
                    if (currentText) {
                        document.getElementById('tiptap-id').value = parent.getAttribute('id');
                        document.getElementById('tiptap-link-content').value = currentText;
                        document.getElementById('tiptap-link-href').value = '';
                        @if(! empty($linkStyles))
                        document.querySelector('.tiptap-link-style[value=""]').checked = true;
                        @endif

                        document.getElementById('tiptap-link-modal').modal.open();
                    } else {
                        alert('Selecteer tekst om een link te geven!');
                    }
                    return;

                case 'setLink':
                    let href = document.getElementById('tiptap-link-href').value;
                    // Query selector will be undefined if no linkStyles are present, but to not break the JS,
                    // we will simply fall back to nothing (which is similar to just having the no style selected).
                    let classes = document.querySelector('.tiptap-link-style:checked')?.value ?? '';

                    if (! href) {
                        editor.chain()
                            .focus()
                            .extendMarkRange('link')
                            // .updateAttributes('link', {class: ''}) // Remove link styling
                            .unsetLink()
                            .run();
                        return;
                    }
                    params.push({href: href});

                    editor.chain()
                        .focus()
                        .extendMarkRange('link')
                        [command](...params) // We can use bracket notation to call the command
                        .updateAttributes('link', {class: classes}) // Update link styling
                        .run();
                    return;

                case 'toggleHeading':
                    // MUST be int!
                    params.push({ level: parseInt(commandParam) });
                    break;

                default:
                    if (commandParam) {
                        params.push(commandParam);
                    }

                    // If needed we can maybe do some callback?
                    // if (typeof window[command] === 'function') {
                    //     window[command](parent, editor, range, currentText, command);
                    // }

                    break;
            }

            editor.chain()
                .focus()
                [command](...params) // We can use bracket notation to call the command
                .run();
        }
    </script>
@endpushonce
