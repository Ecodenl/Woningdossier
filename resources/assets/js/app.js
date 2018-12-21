
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

var baseUrl = window.location.origin;
var fillAddressUrl = baseUrl + "/fill-address";

$("#register #street").focusin(
    function(){
        var postalCode = $("#register #postal_code");
        var number = $("#register #number");
        var houseNumberExtension = $("#register #house_number_extension");
        var street = $("#register #street");
        var city = $("#register #city");
        var addressId = $("#register #addressid");

        $.ajax({
            method: 'get',
            url: fillAddressUrl,
            data: { postal_code: postalCode.val(), number: number.val(), house_number_extension: houseNumberExtension.val() },
            beforeSend: function(){
                street.addClass("loading");
                city.addClass("loading");
            },
            success: function(data){
                street.removeClass("loading");
                city.removeClass("loading");
                var address = data;
                console.log(address);
                street.val(address.street);
                number.val(address.number);
                houseNumberExtension.val(address.house_number_extension);
                addressId.val(address.id);
                city.val(address.city);
            },
            fail: function (xhr, textStatus, errorThrown) {
                console.log(xhr, textStatus, errorThrown);
            },
            dataType: 'json'
        });
    }
);