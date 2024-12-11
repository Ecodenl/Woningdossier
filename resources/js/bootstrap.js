import _ from 'lodash';
window._ = _;

/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

import jQuery from 'jquery';

try {
    Object.assign(window, { $: jQuery, jQuery });
} catch (e) {}

/**
 * Define functions that will be used throughout the whole application, that
 * are also required by Alpine.
 */

import './functions.js'

window.initTinyMCE = function (options = {}) {
    let defaults = {
        selector: '.tiny-editor textarea',
        // menubar: 'edit format',
        menubar: false, // Bar above the toolbar with advanced options
        statusbar: true, // Bar that shows the current HTML tag, word count, etc. at the bottom of the editor
        plugins: [
            'link', // https://www.tiny.cloud/docs/tinymce/6/link/
            'wordcount', // https://www.tiny.cloud/docs/tinymce/6/wordcount/
            'lists', // https://www.tiny.cloud/docs/tinymce/6/lists/
            'advlist', // https://www.tiny.cloud/docs/tinymce/6/advlist/ < Without this, the lists plugin does not work
        ],
        // Link plugin settings start
        link_default_target: '_blank',
        link_target_list: false,
        link_title: false,
        // Link plugin settings end
        toolbar: 'link bold italic underline strikethrough fontsize | bullist numlist',
        contextmenu: false, //'link',
        paste_as_text: true,
        // font_size_formats: 'Extra-Small=10px Small=14px Normal=18px Medium=24px Large=32px Extra-Large=36px Extra-Extra Large=48px',
        font_size_formats: 'Normaal=14px',
        promotion: false,
        language: 'nl',
        resize: false,
        height: 200,
        lists_indent_on_tab: true,
        advlist_bullet_styles: 'disc,circle,square',
        advlist_number_styles: 'decimal,upper-alpha,upper-roman',
    };

    let defaultSetup = (editor) => {
        // When a command is executed
        editor.on('ExecCommand', function (e) {
            // Check if it's a list style command without a list style to replace it with a supported style
            if (['InsertUnorderedList', 'InsertOrderedList'].includes(e.command)) {
                let regex = e.command === 'InsertUnorderedList' ? /<ul>/ig : /<ol>/ig;
                let replace = e.command === 'InsertUnorderedList' ? '<ul style="list-style-type:disc;">' : '<ol style="list-style-type:decimal;">';

                // Save the current cursor position
                let bookmark = editor.selection.getBookmark(2, true);

                let content = editor.getContent();
                if (regex.test(content)) {
                    editor.setContent(content.replace(regex, replace));
                    // Restore the cursor position
                    editor.selection.moveToBookmark(bookmark);
                }
            }
        });

        // Since this config triggers on all tiny editors at once, we manually check on tiny init.
        editor.on('init', (event) => {
            if (editor.targetElm.hasAttribute('disabled')) {
                // Enable readonly to the editor if the textarea is disabled
                editor.mode.set('readonly');
            }
        });
        editor.on('change', (event) => {
            // Save editor (to textarea), then trigger change (to trigger updates for e.g. Livewire).
            editor.save();
            window.triggerEvent(editor.targetElm, 'change');
        });
        // Reset tiny if related textarea is reset
        document.addEventListener('reset-question', (event) => {
            if (editor.id.includes(event.detail.short)) {
                editor.setContent(editor.targetElm.value);
            }
        });
    }

    let setup = (editor) => {
        defaultSetup(editor);
    };
    if (typeof options.setup === 'function') {
        setup = (editor) => {
            defaultSetup(editor);
            options.setup(editor);
        };
    }

    // For now, this is fine. In the future, we might want to make some more fancy merging.
    let config = {
        ...defaults,
        ...options,
        setup: setup,
    };

    tinymce.init(config);
}

/**
 * Set up Alpine JS with extra data functions that can be used throughout
 * the whole application.
 */
import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm';
import AlpineSelect from './alpine-scripts/alpineSelect.js';
import SourceSelect from './alpine-scripts/sourceSelect.js';
import Modal from './alpine-scripts/modal.js';
import RatingSlider from './alpine-scripts/ratingSlider.js';
import Slider from './alpine-scripts/slider.js';
import Register from './alpine-scripts/register.js';
import CheckAddress from './alpine-scripts/checkAddress.js';
import Draggables from './alpine-scripts/draggables.js';
import Dropdown from './alpine-scripts/dropdown.js';
import Tabs from './alpine-scripts/tabs.js';
import AdaptiveInputs from './alpine-scripts/adaptiveInput.js';
import Popover from './alpine-scripts/popover.js';
import Datepicker from './alpine-scripts/datepicker.js';

Alpine.data('alpineSelect', AlpineSelect);
Alpine.data('sourceSelect', SourceSelect);
Alpine.data('modal', Modal);
Alpine.data('ratingSlider', RatingSlider);
Alpine.data('slider', Slider);
Alpine.data('register', Register);
Alpine.data('checkAddress', CheckAddress);
Alpine.data('draggables', Draggables);
Alpine.data('dropdown', Dropdown);
Alpine.data('tabs', Tabs);
Alpine.data('adaptiveInputs', AdaptiveInputs);
Alpine.data('popover', Popover);
Alpine.data('datepicker', Datepicker);

// Define AlpineJS Magic methods (below example defines "$nuke", e.g. x-on:click="$nuke")

// Alpine.magic('nuke', () => {
//     document.body.children.remove();
//     document.body.style.background = "linear-gradient(180deg, rgba(242,43,21,1) 0%, rgba(255,132,0,1) 100%)";
//     document.body.style.height = '100vh';
//     document.body.style.width = '100vw';
// });

Livewire.start();

/**
 * Set up mobile-drag-drop to allow touch events on native HTML 5 desktop drag events.
 */
import {polyfill} from "mobile-drag-drop";

// Init & Settings
polyfill({

});
