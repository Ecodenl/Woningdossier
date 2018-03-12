
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

//window.Vue = require('vue');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

//Vue.component('example', require('./components/Example.vue'));

//const app = new Vue({
//    el: '#app'
//});

$("#register #number").focusout(function() {
    var postalCode = $("#register #postal_code");
    var number = $(this);
    var street = $("#register #street");
    var city = $("#register #city");
    var addressId = $("#register #addressid");

    $.ajax({
        method: 'get',
        url: 'fill-address',
        data: { postal_code: postalCode.val(), number: number.val() },
        beforeSend: function(){
            street.addClass("loading");
            city.addClass("loading");
        },
        success: function(data){
            street.removeClass("loading");
            city.removeClass("loading");
            if (data.length === 1){
                var address = data[0];
                console.log(address);
                street.val(address.street);
                number.val(address.number);
                addressId.val(address.id);
                city.val(address.city);
            }
            else {
                console.log("Multiple options");
            }
        },
        dataType: 'json'
    });

});