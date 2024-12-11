/**
 * First we will load all of this project's JavaScript.
 */

import './bootstrap';

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});