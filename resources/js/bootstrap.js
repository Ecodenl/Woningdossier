
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
 * Set up Alpine JS with extra data functions that can be used throughout
 * the whole application.
 */
import Alpine from 'alpinejs'
import alpineSelect from './alpine-scripts/alpine-select.js';
import sourceSelect from './alpine-scripts/source-select.js';
import modal from './alpine-scripts/modal.js';
import ratingSlider from './alpine-scripts/rating-slider.js';
import slider from './alpine-scripts/slider.js';
import register from './alpine-scripts/register.js';
import picoAddress from './alpine-scripts/picoAddress.js';
import draggables from './alpine-scripts/draggables.js';

Alpine.data('alpineSelect', alpineSelect)
Alpine.data('sourceSelect', sourceSelect)
Alpine.data('modal', modal)
Alpine.data('ratingSlider', ratingSlider)
Alpine.data('slider', slider)
Alpine.data('register', register)
Alpine.data('picoAddress', picoAddress)
Alpine.data('draggables', draggables)

window.Alpine = Alpine

Alpine.start()