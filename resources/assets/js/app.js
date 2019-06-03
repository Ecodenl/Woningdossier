
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
var apiUrl = '/api';
var getAddressDataUrl = baseUrl + apiUrl + "/address-data";

$('i.glyphicon-info-sign').click(function () {
    $(this).parent().parent().find('.modal').modal();
});




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
            // try to find a select, if its not a select, its prob a textarea.
            inputType = inputSourceGroup.find('select').length === 1 ? 'select' : 'textarea';
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
                    inputSourceGroup.find('input[type=checkbox]:checked').removeProp('checked');
                    inputSourceGroup.find('input[type=checkbox][value='+dataInputValue+']').prop('checked', true);
                    break;
                case "select":
                    inputSourceGroup.find('select').val(dataInputValue);
                    break;
                case "date":
                    inputSourceGroup.find('input[type=date]').val(dataInputValue);
                    break;
                case "textarea":
                    inputSourceGroup.find('textarea').val(dataInputValue);
                    break;
                default:
                    //inputSourceGroup.find('select option:selected').removeAttr('selected');
                    //inputSourceGroup.find('select option[value='+dataInputValue+']').attr('selected', true);
                    break;
            }

            $('.panel-body form').find('*').filter(':input:visible:first').trigger('change');
            //$('for
            // m').find('*').filter(':input:visible:first').trigger('change');
        }
    });


});

$(".has-address-data #street").focusin(
    function(){
        var postalCode = $(".has-address-data #postal_code");
        var number = $(".has-address-data #number");
        var houseNumberExtension = $(".has-address-data #house_number_extension");
        var street = $(".has-address-data #street");
        var city = $(".has-address-data #city");
        var addressId = $(".has-address-data #addressid");

        $.ajax({
            method: 'get',
            url: getAddressDataUrl,
            data: { postal_code: postalCode.val(), number: number.val(), house_number_extension: houseNumberExtension.val() },
            beforeSend: function(){
                street.addClass("loading");
                city.addClass("loading");
            },
            success: function(data){

                removeErrorFields();

                street.removeClass("loading");
                city.removeClass("loading");

                var address = data;
                var possibleWrongPostalCode = $('#possible-wrong-postal-code');

                // if there is no postal code returned, then the given postal code is *probably* wrong.
                if (address.postal_code === "") {
                    possibleWrongPostalCode.show();
                } else {
                    possibleWrongPostalCode.hide();
                }

                street.val(address.street);
                if (address.street !== "") {
                    number.val(address.number);
                    houseNumberExtension.val(address.house_number_extension);
                }
                addressId.val(address.id);
                city.val(address.city);
            },
            error: function (request, status, error) {
                var helpBlock = '<span class="help-block"></span>';
                var errorMessage = $.parseJSON(request.responseText);



                $.each(errorMessage.errors, function(fieldName, message) {
                    var inputWithError = $('input[name='+fieldName+']');
                    inputWithError.parent().parent().addClass('has-error');
                    inputWithError.parent().append($(helpBlock).append('<strong>'+message+'</strong>'));
                });
            },
            dataType: 'json'
        });
    }
);


function removeErrorFields()
{
    $('.help-block').remove();
    $('.has-error').removeClass('has-error');
}
