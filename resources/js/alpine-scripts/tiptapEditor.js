export default (content) => {
    // https://tiptap.dev/docs/editor/installation/php#laravel-livewire
    // Note the different export syntax; the other syntax defines a shorthand return, here we return manually.
    // This is necessary so Tiptap and Alpine don't clash.
    let editor;

    return {
        content: content,
        livewire: false,

        init() {
            this.livewire = content && typeof content === 'object' && content._x_interceptor;

            // If we don't, Livewire won't have updated the value of the textarea yet, and then the initial
            // value is empty.
            setTimeout(() => {
                this.buildTiptap();
            });
        },
        buildTiptap() {
            let element = this.$refs['editor'];

            // Should always be present. If it's not, a console error will be thrown. Adequate target selector
            // should be given.
            let div = element.closest('.tiptap-parent');
            element.style.display = 'none';

            let context = this;
            editor = new Tiptap({
                element: div,
                editable: ! element.hasAttribute('disabled'),
                extensions: [
                    TiptapExt.StarterKit.configure({
                        // history: false,
                        // codeBlock: false,
                    }),
                    TiptapExt.Link.extend({
                        inclusive: false, // When a user continues typing (behind the link), it won't be part of the link.
                    }).configure({
                        openOnClick: false,
                        autolink: false,
                        linkOnPaste: true,

                        HTMLAttributes: {
                            draggable: 'false',
                            role: 'link',
                        }
                    }),
                    TiptapExt.Color.configure({}),
                    TiptapExt.BackColor.configure({}),
                    TiptapExt.TextStyle.configure({}),
                    TiptapExt.Underline.configure({}),
                    TiptapExt.FontSize.configure({}),
                    TiptapExt.Superscript.configure({}),
                    TiptapExt.Subscript.configure({}),
                ],
                editorProps: {
                    handlePaste(view, event, slice) {
                        // 1, because it's the second added extension!
                        // Only enable this logic if linkOnPaste is enabled.
                        if (editor?.options?.extensions?.[1]?.options?.linkOnPaste) {
                            let html = event.clipboardData.getData('text/html');
                            if (html === '') {
                                let rtf = event.clipboardData.getData('text/rtf');
                                let matches = rtf.matchAll(/{\\field{\\\*\\fldinst{HYPERLINK\s"([^"]*)"}}{\\fldrslt\s(.*?)}}/gms);

                                let hyperlinks = [];
                                // RTF seems to always start with some context ending with this. We can split to make
                                // searching easier. Might exist more than once though.
                                let rtfSplit = rtf.split('partightenfactor0');
                                let rtfToMatch = rtfSplit.shift();
                                if (rtfSplit.length > 0) {
                                    rtfToMatch = rtfSplit.join('partightenfactor0');
                                }

                                for (const match of matches) {
                                    let prepared = match[2].replaceAll(/(?<!\\)\\\w[\w\\]+?\s/g, '') // Replace all RTF styling tags, just not escaped tags
                                        .replaceAll(/\\([^\\ ])/g, '$1') // Replace escaped chars with just the chars (note: \\ul > \ul!)
                                        .replaceAll(/\\{2}/g, '\\') // Replace double slashes with single slashes
                                        .trim();

                                    let hyperlink = match[1];

                                    // Might exist more than once. In that case, we want to split on the nth occurrence.
                                    let split = 0;
                                    hyperlinks.forEach(data => {
                                        if (data.text === prepared && data.hyperlink === hyperlink) {
                                            ++split;
                                        }
                                    });

                                    // Get all text before the match.
                                    let matchSplits = rtfToMatch.split(match[0]);
                                    let splitsToJoin = [];
                                    for (let i = 0; i <= split; i++) {
                                        splitsToJoin.push(matchSplits[i]);
                                    }
                                    let textBefore = splitsToJoin.join(match[0]);

                                    // Remove RTF tags so we can check the occurrence.
                                    textBefore = textBefore.replaceAll(/{\\field{\\\*\\fldinst{HYPERLINK\s"([^"]*)"}}{\\fldrslt\s(.*?)}}/gms, '$2')
                                        .replaceAll(/(?<!\\)\\\w[\w\\]+?\s/g, '');

                                    // Find the amount of times the value exists in the text.
                                    let regExp = new RegExp(`${prepared}`, 'g');
                                    let occurrence = (textBefore.match(regExp) || []).length;

                                    hyperlinks.push({
                                        hyperlink: hyperlink,
                                        text: prepared,
                                        occurrence: occurrence,
                                    });
                                }

                                if (hyperlinks.length > 0) {
                                    let content = slice.content;
                                    let text = content.textBetween(0, content.size);

                                    // Loop through the hyperlinks and add them to the text.
                                    // We will go in reverse, since the occurrence is counted before adding the hyperlinks,
                                    // and the hyperlinks might add context that matches literally.
                                    hyperlinks.reverse().forEach(data => {
                                        let index = text.indexOfAfter(data.text, data.occurrence);
                                        if (index !== -1) {
                                            text = text.substring(0, index)
                                                + `<a href=${data.hyperlink}>` + data.text + '</a>'
                                                + text.substring(index + data.text.length);
                                        }
                                    });

                                    // Don't pass event, because if we do it will run this callback again, causing in
                                    // an infinite recursion error. By not passing an event, it seems to instantiate
                                    // a new event, which doesn't have the RTF clipboard data.
                                    view.pasteHTML(text);
                                    return true;
                                }
                            }
                        }

                        return false;
                    },
                    // transformPastedText(text, plain) {
                    //     return text;
                    // },
                    // transformPastedHTML(html) {
                    //     return String(html).stripTags('<a>');
                    // },
                },
                content: element.value,
                onUpdate: (event) => {
                    element.value = event.editor.getHTML();
                    context.content = event.editor.getHTML();
                },
                onSelectionUpdate: (event) => {
                    setTimeout(() => context.setTags(div, event.editor));
                }
            });

            // Tiptap creates the editor at the end of the parent container, so we manually append the footer.
            let footer = document.createElement('div');
            footer.classList.add('tiptap-footer');

            let tagList = document.createElement('div');
            tagList.classList.add('tiptap-current-tag')

            footer.appendChild(tagList);
            div.appendChild(footer);

            this.$watch('content', content => {
                if (content === editor.getHTML()) {
                    return;
                }

                // Sync content if backend has changed.
                editor.commands.setContent(content, false);
            });

            // Ensure we change disabled state based on the textarea attribute.
            let attributeObserver = new MutationObserver(function (mutations) {
                editor.setOptions({editable: ! element.hasAttribute('disabled')})
            });

            attributeObserver.observe(element, { attributeFilter: ['disabled'] });
        },
        destroy() {
            editor.destroy();
        },
        setTags(editorContainer, e) {
            editorContainer.querySelector('.tiptap-footer .tiptap-current-tag').textContent = this.getSelectionTagList(e).join(' > ');
        },
        getSelectionTagList(e) {
            // AnchorNode can be _any_ node type, but we only want the element type. If the current selection is text,
            // we will get a text node, however if there's no text (e.g. empty P tag), we directly get that element.
            let currentTag = window.getSelection().anchorNode;

            let tags = [];
            if (currentTag) {
                if (currentTag.nodeType !== Node.ELEMENT_NODE) {
                    currentTag = currentTag.parentElement;
                }

                // Result when the Tiptap update hasn't quite passed to the window. This means in some cases you might not
                // see a tag. No need to spend hours on this though.
                if (currentTag.classList.contains('tiptap')) {
                    return [];
                }

                tags = [currentTag.tagName];

                do {
                    currentTag = currentTag.parentElement;

                    if (currentTag && ! currentTag.classList.contains('tiptap')) {
                        tags.unshift(currentTag.tagName);
                    }
                } while (currentTag && ! currentTag.classList.contains('tiptap'));
            }
            return tags;
        }
    }
}
