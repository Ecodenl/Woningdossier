
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

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

var baseUrl = window.location.origin;
var fillAddressUrl = baseUrl + "/fill-address";

$(document).ready(function () {

    $('.input-source-group').on('click', 'li.change-input-value', function (event) {

        // so it will not jump to the top of the page.
        event.preventDefault();

        var dataInputValue = $(this).data('input-value');

        // find the selected option
        var inputSourceGroup = $(this).parent().parent().parent();
        //var inputSourceGroup = $(this).closest(".input-group.input-source-group");
        var inputType = inputSourceGroup.find('input').attr('type');

        if (inputType === undefined){
            // check if it's a select
            inputType = inputSourceGroup.find('select').length === 1 ? 'select' : undefined;
        }

        // check if the input is a "input" and not a select
        if (typeof inputType !== undefined) {
            switch (inputType) {
                case "text":
                    inputSourceGroup.find('input[type=text]').val(dataInputValue);
                    break;
                case "radio":
                    inputSourceGroup.find('input[type=radio]:checked').removeProp('checked');
                    inputSourceGroup.find('input[type=radio][value='+dataInputValue+']').prop('checked', true);
                    break;
                case "checkbox":
                    inputSourceGroup.find('input[type=checkbox]:checked').removeProp('selected');
                    inputSourceGroup.find('input[value='+dataInputValue+']').prop('selected', true);
                    break;
                case "select":
                    inputSourceGroup.find('select').val(dataInputValue);
                    break;
                default:
                    //inputSourceGroup.find('select option:selected').removeAttr('selected');
                    //inputSourceGroup.find('select option[value='+dataInputValue+']').attr('selected', true);
                    break;
            }

            $('form').find('*').filter(':input:visible:first').trigger('change');
        }
    });


});

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
            },
            error: function (request, status, error) {
                var helpBlock = '<span class="help-block"></span>';
                var errorMessage = $.parseJSON(request.responseText);

                $.each(errorMessage.errors, function(fieldName, message) {
                    $('#'+fieldName).parent().append($(helpBlock).append('<strong>'+message+'</strong>'));
                });
            },
            dataType: 'json'
        });
    }
);