/**
 * First we will load all of this project's JavaScript.
 */

require('./bootstrap');

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

$("#postal_code, #number, #house_number_extension").focusout(function () {
    var postalCode = $(".has-address-data #postal_code");
    var number = $(".has-address-data #number");
    var houseNumberExtension = $(".has-address-data #house_number_extension");
    var street = $(".has-address-data #street");
    var city = $(".has-address-data #city");
    var addressId = $(".has-address-data #addressid");

    $.ajax({
        method: 'get',
        url: getAddressDataUrl,
        data: {postal_code: postalCode.val(), number: number.val(), house_number_extension: houseNumberExtension.val()},
        beforeSend: function () {
            street.addClass("loading");
            city.addClass("loading");
        },
        success: function (data) {

            removeError(city);
            removeError(postalCode);
            removeError(street);
            removeError(number);

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

            // this way the user can fill in the street and will only be forced with api data if it actually returns something
            if (address.bag_addressid !== "") {
                street.val(address.street);
                number.val(address.number);
                houseNumberExtension.val(address.house_number_extension);
                addressId.val(address.id);
                city.val(address.city);
            }
        },
        error: function (request, status, error) {


            removeError(city);
            removeError(postalCode);
            removeError(street);
            removeError(number);

            var helpBlock = '<span class="help-block"></span>';
            var errorMessage = $.parseJSON(request.responseText);

            $.each(errorMessage.errors, function (fieldName, message) {
                // on name because some input name fields will be scrambeled to prevent the browser from pefilling it.
                var inputWithError = $('input[id=' + fieldName + ']');
                inputWithError.parent().parent().addClass('has-error');
                inputWithError.parent().append($(helpBlock).append('<strong>' + message + '</strong>'));
            });
        },
        dataType: 'json'
    });
});

function removeError(input) {
    input.parents('.has-error').removeClass('has-error');
    input.next('.help-block').remove()
}