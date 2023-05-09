/**
 * First we will load all of this project's JavaScript.
 */

require('./bootstrap');

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
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

        if (inputType === undefined) {
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
                    inputSourceGroup.find('input[type=radio][value=' + dataInputValue + ']').prop('checked', true);
                    break;
                case "checkbox":
                    inputSourceGroup.find('input[type=checkbox]:checked').removeProp('checked');
                    inputSourceGroup.find('input[type=checkbox][value=' + dataInputValue + ']').prop('checked', true);
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