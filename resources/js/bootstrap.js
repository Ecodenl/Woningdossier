
window._ = require('lodash');

/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

try {
    window.$ = window.jQuery = require('jquery');

    require('bootstrap-sass');
} catch (e) {}

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

/*
window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
*/
/**
 * Next we will register the CSRF Token as a common header with Axios so that
 * all outgoing HTTP requests automatically have it attached. This is just
 * a simple convenience so we don't have to attach every token manually.
 */

/*
let token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}
*/
/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo'

// window.Pusher = require('pusher-js');

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: 'your-pusher-key'
// });

/**
 * Define functions that will be used throughout the whole application, that
 * are also required by Alpine.
 */

/**
 * Trigger a default event
 *
 * @param element
 * @param eventName
 */
window.triggerEvent = function (element, eventName) {
    if (element && element.nodeType === Node.ELEMENT_NODE && eventName) {
        let event = new Event(eventName, {bubbles: true});
        element.dispatchEvent(event);
    }
}

/**
 * Trigger a custom event, with potential parameters.
 *
 * @param element
 * @param eventName
 * @param params
 */
window.triggerCustomEvent = function (element, eventName, params = {}) {
    if (typeof params !== 'object') {
        console.error('Params is not a valid object!');
        params = {};
    }

    if (element && element.nodeType === Node.ELEMENT_NODE && eventName) {
        let event = new CustomEvent(eventName, {bubbles: true, detail: params });
        element.dispatchEvent(event);
    }
}

/**
 * Expand HTML object functionality
 */

//--- HTMLCollection

/**
 * Remove all elements in the HTML collection
 */
Object.defineProperty(HTMLCollection.prototype, 'remove', {
    value: function() {
        Array.from(this).forEach((nodeElement) => {
            nodeElement.remove();
        });
    },
    enumerable: false,
    configurable: false,
});

//--- NodeList

/**
 * Remove all elements in the node list
 */
Object.defineProperty(NodeList.prototype, 'remove', {
    value: function() {
        Array.from(this).forEach((nodeElement) => {
            nodeElement.remove();
        });
    },
    enumerable: false,
    configurable: false,
});

/**
 * Set up Alpine JS with extra data functions that can be used throughout
 * the whole application.
 */
import Alpine from 'alpinejs';
import AlpineSelect from './alpine-scripts/alpine-select.js';
import SourceSelect from './alpine-scripts/source-select.js';
import Modal from './alpine-scripts/modal.js';
import RatingSlider from './alpine-scripts/rating-slider.js';
import Slider from './alpine-scripts/slider.js';
import Register from './alpine-scripts/register.js';
import PicoAddress from './alpine-scripts/picoAddress.js';
import Draggables from './alpine-scripts/draggables.js';
import Dropdown from './alpine-scripts/dropdown.js';
import Tabs from './alpine-scripts/tabs.js';
import AdaptiveInputs from './alpine-scripts/adaptive-input.js';

Alpine.data('alpineSelect', AlpineSelect);
Alpine.data('sourceSelect', SourceSelect);
Alpine.data('modal', Modal);
Alpine.data('ratingSlider', RatingSlider);
Alpine.data('slider', Slider);
Alpine.data('register', Register);
Alpine.data('picoAddress', PicoAddress);
Alpine.data('draggables', Draggables);
Alpine.data('dropdown', Dropdown);
Alpine.data('tabs', Tabs);
Alpine.data('adaptiveInputs', AdaptiveInputs);

window.Alpine = Alpine;

Alpine.start();

/**
 * Set up mobile-drag-drop to allow touch events on native HTML 5 desktop drag events.
 */
import {polyfill} from "mobile-drag-drop";

// Init & Settings
polyfill({

});

/**
 * Expand HTML object functionality
 */

/**
 * Fade out an element.
 *
 * @param time (in milliseconds)
 * @param callback
 */
Object.defineProperty(Object.prototype, 'fadeOut', {
    value: function (time = 1000, callback = null) {
        // Ensure time is a valid number
        if (isNaN(time) || time === null || time === '' || time <= 0) {
            time = 1000;
        }

        // Get current opacity, ensure that that is a valid number also
        let currentOpacity = getComputedStyle(this).opacity;
        if (isNaN(currentOpacity)) {
            currentOpacity = 1;
        }

        // Set opacity in the style, to ensure the value is set
        this.style.opacity = currentOpacity;
        // We use a default timeout of 5 ms
        let timeout = 5;
        // Calculate how much of the opacity we decrease per 5 ms;
        let steps = currentOpacity / (time / timeout);

        // Each timeout, remove the calculated step from the opacity
        let fade = setInterval(() => {
            if (this.style.opacity <= 0) {
                clearInterval(fade);
                if (callback) {
                    callback();
                }
            } else {
                this.style.opacity = parseFloat(this.style.opacity) - steps;
            }
        }, timeout);
    },
    enumerable: false,
    configurable: false,
});

/**
 * Fade in an element.
 *
 * @param time (in milliseconds)
 */
Object.defineProperty(Object.prototype, 'fadeIn', {
    value: function (time = 1000, callback = null) {
        // Ensure time is a valid number
        if (isNaN(time) || time === null || time === '' || time <= 0) {
            time = 1000;
        }

        // Get current opacity, ensure that that is a valid number also
        let currentOpacity = getComputedStyle(this).opacity;
        if (isNaN(currentOpacity)) {
            currentOpacity = 0;
        }

        // Set opacity in the style, to ensure the value is set
        this.style.opacity = currentOpacity;
        // We use a default timeout of 5 ms
        let timeout = 5;
        // Calculate how much of the opacity we increase per 5 ms
        let steps = (1 - currentOpacity) / (time / timeout);

        // Each timeout, add the calculated step to the opacity
        let fade = setInterval(() => {
            if (this.style.opacity >= 1) {
                clearInterval(fade);
                if (callback) {
                    callback();
                }
            } else {
                this.style.opacity = parseFloat(this.style.opacity) + steps;
            }
        }, timeout);
    },
    enumerable: false,
    configurable: false,
});