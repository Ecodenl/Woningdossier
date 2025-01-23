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
 * Tiptap
 */

import { Editor } from '@tiptap/core';
import StarterKit from '@tiptap/starter-kit';
import Link from '@tiptap/extension-link';
import Color from '@tiptap/extension-color';
import TextStyle from '@tiptap/extension-text-style';
import Underline from '@tiptap/extension-underline';
import Superscript from '@tiptap/extension-superscript';
import Subscript from '@tiptap/extension-subscript';
// Custom extensions
import { FontSize } from './tiptap-extensions/font-size.ts';
import { BackColor } from './tiptap-extensions/back-color.ts';

window.Tiptap = Editor;
window.TiptapExt = {
    StarterKit: StarterKit,
    Link: Link,
    Color: Color,
    BackColor: BackColor,
    TextStyle: TextStyle,
    Underline: Underline,
    FontSize: FontSize,
    Superscript: Superscript,
    Subscript: Subscript,
};

/**
 * Define functions that will be used throughout the whole application, that
 * are also required by Alpine.
 */

import './functions.js'

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
import TiptapEditor from './alpine-scripts/tiptapEditor';

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
Alpine.data('tiptapEditor', TiptapEditor);

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
    forceApply: true
});
