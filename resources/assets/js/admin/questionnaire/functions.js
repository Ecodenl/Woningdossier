var formBuildPanel =
    '<div class="form-builder panel panel-default">' +
    '<div class="panel-body">' +
    '<div class="row">' +
    '<div class="col-sm-12 question">' +
    // '<div class="form-group">' +

    // '</div>' +
    '</div>' +
    '</div>' +
    '<div class="row validation-rules">' +

    '</div>' +
    '</div>' +
    '<div class="panel-footer">' +
    '<div class="row">' +
    '<div class="col-sm-12">' +
    '<div class="pull-left">' +
    '<a><i class="glyphicon glyphicon-trash"></i></a>' +
    '</div>' +
    '<div class="pull-right">' +

    '</div>' +
    '</div>' +
    '</div>' +
    '</div>' +
    '</div>';

var formBuildValidation =
    '<div class="row validation-inputs">' +
    '<div class="col-sm-4">' +
    '<div class="form-group">' +
    '<select class="validation form-control">' +
    '@foreach(__("woningdossier.cooperation.admin.custom-fields.index.rules") as $rule => $translation)' +
    '<option value="{{$rule}}">{{$translation}}</option>' +
    '@endforeach' +
    '</select>' +
    '</div>' +
    '</div>' +
    '<div class="col-sm-4">' +
    '<div class="form-group">' +
    '@foreach(__("woningdossier.cooperation.admin.custom-fields.index.rules") as $rule => $translation)' +
    '<select disabled="true" class="sub-rule form-control" data-sub-rule="{{$rule}}" style="display: none;">' +
    '@foreach(__("woningdossier.cooperation.admin.custom-fields.index.optional-rules.".$rule) as $optionalRule => $optionalRuleTranslation)' +
    '<option value="{{$optionalRule}}">{{$optionalRuleTranslation}}</option>' +
    '@endforeach' +
    '</select>' +
    '@endforeach' +
    '</div>' +
    '</div>' +
    '</div>';


// in the comments you may see "options"
// these will be the inputs that hold the name off the option that the user can choose from
// a question could have options, those options are the "option" inputs.

// preset variables we need in almost in every function
var sortable = $('#sortable');
var formGroupElement = '<div class="form-group"></div>';
var inputGroupElement = '<div class="input-group"></div>';
var localeInputGroupAddon = '<span class="input-group-addon"></span>';
var optionGroupElement = '<div class="option-group"></div>';

var requiredCheckboxLabel = $('<label>').addClass('control-label').text('Verplicht ');

var supportedLocales = [];

// creates two pushes, but it works.
@foreach(config('woningdossier.supported_locales') as $locale)
supportedLocales.push('{{$locale}}');
        @endforeach

// create guid
function createGuid()
{
    return "ss-s-s-s-sss".replace(/s/g, s4());
}
// some quick maths
function s4()
{
    return Math.floor((1 + Math.random()) * 0x10000)
        .toString(16)
        .substring(1);
}


/**
 * function to add a hidden input with the type the question should be
 */
function addHiddenInputWithInputType(question, guid, type)
{
    var hiddenInputWithInputTypeName = 'questions[new]['+guid+'][type]';
    var hiddenInputWithInputType = $('<input>').attr({
        name: hiddenInputWithInputTypeName,
        type: 'hidden',
        value: type
    });
    question.append(hiddenInputWithInputType);
}


/**
 * Add inputs where the user can add the options for a question
 * @param question
 * @param guid
 */
function addAdditionalQuestionOptions(question, guid)
{
    // we need to create this for every new option
    // so we can make a difference between the multiple options
    var additionalQuestionOptionGuid = createGuid();

    // we append a option-group for every new added option
    question.append(optionGroupElement);

    // we add "option" inputs for each supported language
    $(supportedLocales).each(function (index, locale) {
        var fullQuestionName = 'questions[new]['+guid+'][options]['+additionalQuestionOptionGuid+']['+locale+']';

        var formGroup = $($(formGroupElement).append(inputGroupElement));
        // raging because i dont know why $(optionGroup).append(formGroup) does not work.
        $(question).find('.option-group').last().append(formGroup);


        var additionalTextInput = $('<input>').addClass('form-control option-text').attr({
            placeholder: 'Optie toevoegen',
            name: fullQuestionName,
            type: 'text'
        });

        formGroup.find('.input-group').append($(localeInputGroupAddon).append(locale));
        formGroup.find('.input-group').append(additionalTextInput);
    });
}

/**
 * the input where the user can fill in the main question
 */
function addInputQuestion(question, guid, placeholder)
{
    $(supportedLocales).each(function (index, locale) {
        var fullQuestionName = 'questions[new]['+guid+'][question]['+locale+']';
        var formGroup = $($(formGroupElement).append(inputGroupElement)).appendTo(question);

        var textInput = $('<input>').addClass('form-control').attr({
            placeholder: placeholder,
            name: fullQuestionName,
            type: 'text'
        });

        formGroup.find('.input-group').append($(localeInputGroupAddon).append(locale));
        formGroup.find('.input-group').append(textInput);

    });
}

/**
 * function to add a required checkbox to the footer off the panel
 *
 * @param panelFooter
 * @param guid
 */
function addRequiredCheckbox(panelFooter, guid)
{

    var rbl = requiredCheckboxLabel.clone().attr({
        for: 'required-'+guid
    });
    var requiredCheckbox = $('<input>').addClass('control-label').attr({
        id: 'required-'+guid+'',
        type: 'checkbox',
        name: 'questions[new]['+guid+'][required]'
    });

    panelFooter.find('.pull-right').html(rbl);
    requiredCheckbox.appendTo(panelFooter.find('.pull-right > label'));
}



/**
 * function to add the validation inputs
 */
function addValidationInputs(question, guid)
{

    // add the validation options to the form
    question.append(formBuildValidation);
    // after that we add the name attribute
    question.find('.validation').attr('name', 'validation['+guid+'][main-rule]').trigger('change');
    question.find('.sub-rule').attr('name', 'validation['+guid+'][sub-rule]');

}


/**
 * Add a hidden input with a guid
 */
function addHiddenGuidInput(question, guid)
{
    var guidHiddenInput = $('<input>').attr({
        name: 'questions[new]['+guid+'][guid]',
        type: 'hidden',
        value: guid,
    }).addClass('guid');

    question.append(guidHiddenInput);
}


/**
 * Remove the rule inputs from a question
 */
function removeRuleInput(question)
{
    var validationInputRow = question.find('.validation-inputs');
    validationInputRow.find('.col-sm-2').remove();
}

/**
 * Add the sub-rule-check-value inputs, aka the inputs where the user can add a min, max, length etc
 */
function addSubRuleCheckValueInput(question, guid, placeholders)
{
    // remove the old rule inputs
    removeRuleInput(question);

    // get the validation input row
    var validationInputRow = question.find('.validation-inputs');

    $(placeholders).each(function (index, placeholder) {

        // create the min and max inputs
        var minInput = $('<div class="col-sm-2"></div>').append($('<input>').attr({
            name: 'validation['+guid+'][sub-rule-check-value][]',
            placeholder: placeholder,
            type: 'text'
        }).addClass('form-control'));

        validationInputRow.append(minInput);
    })
}

/**
 * Check if a question has a question id.
 * The question will have a question id if its a existing question
 * The question wont have it if its a new question, in that case it wil have a guid
 *
 */
function hasQuestionQuestionId(question)
{
    if (question.find('input.question_id').length > 0) {
        return true;
    }
    return false
}


/**
 * This returns the question id from a existing question or if it is a new question we return the guid.
 * @param question
 * @returns {*}
 */
function getQuestionId(question)
{
    if (hasQuestionQuestionId(question)) {
        return question.find('input.question_id').val();
    }

    return question.find('input.guid').val();
}