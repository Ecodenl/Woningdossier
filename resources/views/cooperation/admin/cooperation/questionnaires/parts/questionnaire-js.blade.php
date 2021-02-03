<script>
    // Config for the different types
    let config = {
        text: {
            validation: true,
            placeholder: '@lang('cooperation/admin/cooperation/questionnaires.shared.types.default-placeholder')',
            hasOption: false,
            header: '@lang('cooperation/admin/cooperation/questionnaires.shared.types.text.label')',
        },
        textarea: {
            validation: true,
            placeholder: '@lang('cooperation/admin/cooperation/questionnaires.shared.types.textarea.placeholder')',
            hasOption: false,
            header: '@lang('cooperation/admin/cooperation/questionnaires.shared.types.textarea.label')',
        },
        radio: {
            validation: false,
            placeholder: '@lang('cooperation/admin/cooperation/questionnaires.shared.types.default-placeholder')',
            hasOption: true,
            header: '@lang('cooperation/admin/cooperation/questionnaires.shared.types.radio.label')',
        },
        checkbox: {
            validation: false,
            placeholder: '@lang('cooperation/admin/cooperation/questionnaires.shared.types.default-placeholder')',
            hasOption: true,
            header: '@lang('cooperation/admin/cooperation/questionnaires.shared.types.checkbox.label')',
        },
        select: {
            validation: false,
            placeholder: '@lang('cooperation/admin/cooperation/questionnaires.shared.types.default-placeholder')',
            hasOption: true,
            header: '@lang('cooperation/admin/cooperation/questionnaires.shared.types.select.label')',
        },
        date: {
            validation: false,
            placeholder: '@lang('cooperation/admin/cooperation/questionnaires.shared.types.default-placeholder')',
            hasOption: false,
            header: '@lang('cooperation/admin/cooperation/questionnaires.shared.types.date.label')',
        },
    };

    // HTML components
    var formBuildPanel =
        `<div class="form-builder panel panel-default">
            <div class="panel-heading">
                :heading
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-12 question">
                        <input name="questions[:guid][type]" type="hidden" value=":type">
                        <input name="questions[:guid][guid]" type="hidden" value=":guid" class="guid">
                        :question
                        :option
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        :validationButton
                        :addOptionButton
                    </div>
                </div>
                <div class="row validation-rules">

                </div>
            </div>
            <div class="panel-footer">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="pull-left">
                            <a><i class="glyphicon glyphicon-trash remove-question" style="margin-top: 25%;"></i></a>
                        </div>
                        <div class="pull-right">
                            :requiredCheckbox
                        </div>
                    </div>
                </div>
            </div>
        </div>`;

    var formBuildValidation =
        `<div class="row validation-inputs">
            <div class="col-sm-1">
                <a class="text-danger">
                    <i class="glyphicon glyphicon-remove remove-validation" style="margin-top: 25%; margin-left: 50%"></i>
                </a>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    <select class="validation form-control">
                        @foreach(__("woningdossier.cooperation.admin.custom-fields.index.rules") as $rule => $translation)
                            <option value="{{$rule}}">{{$translation}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    @foreach(__("woningdossier.cooperation.admin.custom-fields.index.rules") as $rule => $translation)
                        <select disabled="true" class="sub-rule form-control" data-sub-rule="{{$rule}}" style="display: none;">
                            @foreach(__("woningdossier.cooperation.admin.custom-fields.index.optional-rules.".$rule) as $optionalRule => $optionalRuleTranslation)
                                <option value="{{$optionalRule}}">{{$optionalRuleTranslation}}</option>
                            @endforeach
                        </select>
                    @endforeach
                </div>
            </div>
        </div>`;

    let validationButton = '<a class="btn btn-primary add-validation">@lang('cooperation/admin/cooperation/questionnaires.edit.add-validation')</a>';
    let addOptionButton = '<a class="btn btn-primary add-option" data-id=":guid">@lang('cooperation/admin/cooperation/questionnaires.edit.add-option')</a>';

    let requiredCheckboxLabel =
        `<label class="control-label" for="required-:guid">
            @lang('default.required')
            <input id="required-:guid" type="checkbox" name="questions[:guid][required]">
        </label>`;

    let questionPanel =
        `<div class="form-group">
            <div class="input-group">
                <span class="input-group-addon">:locale</span>
                <input class="form-control" placeholder=":placeholder" name="questions[:guid][question][:locale]" type="text">
            </div>
        </div>`;

    let optionPanel =
        `<div class="option-group">
            <label data-index=":index">@lang('cooperation/admin/cooperation/questionnaires.shared.types.default-option-label') :index</label>
            <div class="form-group">
                <div class="input-group">
                    <span class="input-group-addon">:locale</span>
                    <input class="form-control option-text" placeholder="@lang('cooperation/admin/cooperation/questionnaires.shared.types.default-option-placeholder')"
                        name="questions[:guid][options][:optionGuid][:locale]" type="text" autofocus="autofocus">
                    <span class="input-group-addon">
                        <a class="text-danger"><i class="glyphicon glyphicon-remove remove-option"></i></a>
                    </span>
                </div>
            </div>
        </div>`;

    // in the comments you may see "options"
    // these will be the inputs that hold the name off the option that the user can choose from
    // a question could have options, those options are the "option" inputs.

    // Preset variables that are used often
    let sortable = $('#sortable');
    let toolBox = $('#tool-box');
    let body = $('body');

    let supportedLocales = [];

    // creates two pushes, but it works.
    @foreach(config('hoomdossier.supported_locales') as $locale)
        supportedLocales.push('{{$locale}}');
    @endforeach

    // create guid
    function createGuid() {
        return "ss-s-s-s-sss".replace(/s/g, s4());
    }
    // some quick maths
    function s4() {
        return Math.floor((1 + Math.random()) * 0x10000).toString(16).substring(1);
    }

    // Toolbox related
    function createComponent(type, guid = null) {
        // Get config from type
        let configData = config[type];

        // Generate a guid
        if (guid === null) {
            guid = createGuid();
        }

        // We build off from the template. Less changes to the DOM is always better
        let temp = formBuildPanel;

        // Add the text from the toolbox to the panel-heading
        temp = temp.replace(':heading', configData.header);

        // Add the type
        temp = temp.replace(':type', type);

        // Add checkbox to template
        temp = temp.replace(':requiredCheckbox', requiredCheckboxLabel);

        // Add validation button if needed, else replace with nothing
        let validationReplace = '';
        if (configData.validation === true) {
            validationReplace = validationButton;
        }
        temp = temp.replace(':validationButton', validationReplace);

        // Add question
        temp = temp.replace(':question', getInputQuestion(guid, configData.placeholder));

        // Same as with validation, but for option
        let optionReplace = '';
        let optionButtonReplace = '';
        if (configData.hasOption === true) {
            // We can pass 1, as this will always be the first option
            optionReplace = getAdditionalQuestionOptions(1);
            optionButtonReplace = addOptionButton;
        }
        temp = temp.replace(':option', optionReplace);
        temp = temp.replace(':addOptionButton', optionButtonReplace);

        // As last step, replace all :guid with the created guid
        temp = temp.replaceAll(':guid', guid);
        sortable.prepend(temp);
        sortable.sortable('refresh');
    }

    toolBox.find('a').on('click', function (event) {
        event.preventDefault();
        // Get type
        let type = $(this).attr('data-type');

        createComponent(type);
    });

    /**
     * Returns the option index for a given question
     */
    function getOptionIndex(guidOrId) {
        return $('.option-group input[name^="questions[' + guidOrId + '][options]"]').length + 1;
    }

    /**
     * Returns a new option.
     */
    function getAdditionalQuestionOptions(index, additionalQuestionOptionGuid = null){
        // we need to create this for every new option
        // so we can make a difference between the multiple options
        if (additionalQuestionOptionGuid === null) {
            additionalQuestionOptionGuid = createGuid();
        }

        let append = '';

        $(supportedLocales).each(function (i, locale) {
            let temp = optionPanel;
            temp = temp.replaceAll(':index', index);
            temp = temp.replaceAll(':locale', locale);
            temp = temp.replace(':optionGuid', additionalQuestionOptionGuid);
            append += temp;
        });

        return append;
    }

    /**
     * Returns the input where the user can fill in the main question.
     */
    function getInputQuestion(guid, placeholder) {
        let append = '';

        $(supportedLocales).each(function (index, locale) {
            let temp = questionPanel;
            temp = temp.replaceAll(':locale', locale);
            temp = temp.replace(':placeholder', placeholder);
            append += temp;
        });

        return append;
    }

    // Body related
    /**
     * Function to add the validation inputs
     */
    function addValidationInputs(question, guid) {
        // add the validation options to the form
        question.append(formBuildValidation);
        // after that we add the name attribute
        question.find('.validation').attr('name', 'validation['+guid+'][main-rule]').trigger('change');
        question.find('.sub-rule').attr('name', 'validation['+guid+'][sub-rule]');
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
     * Add validation to a question
     */
    $(document).on('click', '.add-validation', function (event) {
        event.preventDefault();
        var question = $(this).parent().parent().parent().find('.question');
        var guid = getQuestionId(question);

        addValidationInputs(question, guid);
        $(this).hide();
    });

    /**
     * Adds new option
     */
    $(document).on('click', '.add-option', function(event) {
        event.preventDefault();
        let guidOrId = $(this).attr('data-id');
        // The question is always the parent of the hidden input that has the guid or id as value
        let question = $('input[value="' + guidOrId + '"]').parent();

        let option = getAdditionalQuestionOptions(getOptionIndex(guidOrId));
        option = option.replace(':guid', guidOrId);
        question.append(option);
    });

    /**
     * Removes validation
     */
    $(document).on('click', '.remove-validation', function(event) {
        $(this).parents('.panel-body').first().find('.add-validation').show();
        $(this).parents('.validation-inputs').first().remove();
    });

    /**
     * Changes the sub-rule select
     */
    body.on('change', 'select.validation', function () {
        let selectedMainRule = $(this);

        let validationRuleRow = selectedMainRule.parents('.validation-inputs').first();

        let subRule = setSubRule(validationRuleRow, selectedMainRule.val());
        // We set the first option value
        subRule.val(subRule.find('option').first().val());
        subRule.trigger('change');
    });

    function setSubRule(validationRuleRow, selectedMainRuleValue) {
        // get the select sub-rule that we dont want to show
        let subRuleNotSelected = validationRuleRow.find('select[data-sub-rule!='+ selectedMainRuleValue +'].sub-rule');

        // after that we hide & disable the input. No need to remove the name, see:
        // https://www.w3.org/TR/html5/forms.html#constructing-the-form-data-set
        subRuleNotSelected.hide();
        subRuleNotSelected.attr('disabled', true);

        // We then get the select sub-rule that we do want to show
        let subRule = validationRuleRow.find('select[data-sub-rule='+ selectedMainRuleValue +'].sub-rule');
        // Show and enable
        subRule.attr('disabled', false);
        subRule.show();
        // We return the subRule, because if it doesn't get changed programmatically, we want to be able to trigger a
        // change event.
        return subRule;
    }

    /**
     * Changes the rule input, adds validation inputs if needed
     */
    body.on('change', 'select.sub-rule', function (event) {
        let selectedValidationOption = $(this);
        let question = selectedValidationOption.parents('.question').first();
        let selectedValidationValue = $(this).val();
        let guid = getQuestionId(question);

        setSubRuleValueInputs(question, guid, selectedValidationValue);

    });

    function setSubRuleValueInputs(question, guidOrId, selectedValidationValue) {
        switch (selectedValidationValue) {
            case 'between':
                addSubRuleCheckValueInput(question, guidOrId, ['Min..', 'Max..']);
                break;
            case 'min':
                addSubRuleCheckValueInput(question, guidOrId, ['Min..']);
                break;
            case 'max':
                addSubRuleCheckValueInput(question, guidOrId, ['Max..']);
                break;
            case 'email':
                removeRuleInput(question);
                break;
        }
    }

    /**
     * Remove a whole question
     */
    body.on('click', '.remove-question', function (event) {
        event.preventDefault();

        var deleteQuestionRoute = '{{route('cooperation.admin.cooperation.questionnaires.delete', ['questionId' => ':question_id'])}}';
        if (confirm('@lang('cooperation/admin/cooperation/questionnaires.destroy.question-are-you-sure')')) {

            var questionId = $(this).parent().parent().parent().parent().parent().parent().find('.question_id').val();
            if (typeof questionId === "undefined") {
                console.log('the question id is empty ohno!');
            }
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: deleteQuestionRoute.replace(':question_id', questionId),
                type: 'post',
                method: 'delete'
            });

            $(this).parent().parent().parent().parent().parent().parent().remove();
        }

        return false;
    });

    /**
     * Remove a option from a question
     */
    body.on('click', '.remove-option', function (event) {
        event.preventDefault();
        var deleteOptionRoute = '{{route('cooperation.admin.cooperation.questionnaires.delete-question-option', ['questionId' => ':question_id', 'optionId' => ':option_id'])}}';
        var currentOptionGroup = $(this).parents('.option-group').first()
        var question = currentOptionGroup.parent();
        var questionId = question.find('.question_id').val();
        var questionOptionId = currentOptionGroup.find('.question_option_id').val();

        if (confirm('@lang('cooperation/admin/cooperation/questionnaires.destroy.option-are-you-sure')')) {

            if (typeof questionId !== "undefined" || typeof questionOptionId !== "undefined") {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    // this is what is to be considered a pro gamer move.
                    url: deleteOptionRoute.replace(':question_id', questionId).replace(':option_id', questionOptionId),
                    type: 'post',
                    method: 'delete'
                });
            }

            // Get index
            let index = currentOptionGroup.find('label').attr('data-index');
            // Update siblings with higher index value (e.g. from "option 3" to "option 2")
            currentOptionGroup.siblings('.option-group').each(function(i, element) {
                let label = $(element).find('label');
                if (label.attr('data-index') > index) {
                    let newIndex = label.attr('data-index') - 1;
                    label.text('@lang('cooperation/admin/cooperation/questionnaires.shared.types.default-option-label') ' + newIndex);
                    label.attr('data-index', newIndex);
                }
            });

            currentOptionGroup.remove();
        }

        return false;
    });

    // Miscellaneous
    /**
     * Check if a question has a question id.
     * The question will have a question id if its a existing question
     * The question wont have it if its a new question, in that case it wil have a guid
     */
    function hasQuestionQuestionId(question)
    {
        return question.find('input.question_id').length > 0
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

    $('#leave-creation-tool').on('click', function (event) {
        if (confirm('@lang('cooperation/admin/cooperation/questionnaires.shared.leave-creation-tool-warning')')) {

        } else {
            event.preventDefault();
            return false;
        }
    });

    function getQuestionByGuidOrId(guidOrId) {
        // The hidden input always is the first descendant of the question div, where we need to
        // append the options too
        return $('input[name^="questions['+ guidOrId +']"]').first().parent();
    }

    // On load
    $(document).ready(function () {
        var blocks = [];
        var master = $('#sortable');

        // get the id's off the blocks / panels
        $('.form-builder').each(function () {
            blocks.push($(this).attr('id'));
        });

        // make it sortable
        master.sortable({

            update: function () {

                var order = [];

                $(".form-builder").each(function () {
                    order.push($(this).attr('id'));
                });

                // create a new array with the order of the item and the navId
                var questionOrder = blocks.map(function (questionOrder, questionId) {
                    return questionOrder, order[questionId];
                });

            }
        });

        // Deal with old values
        let oldQuestions = {!! json_encode(old('questions')) !!};

        $.each(oldQuestions, function(guidOrId, question) {
            // If it has an ID, this will return false and a new component won't be created
            if (question.guid) {
                createComponent(question.type, guidOrId);
                // Set the value for each locale (we don't have to do this for ID, as that gets handled by php)
                $.each(question.question, function(locale, value) {
                    $('input[name="questions['+ guidOrId +'][question]['+ locale +']"]').val(value);
                });
            }

            let questionDiv = getQuestionByGuidOrId(guidOrId);

            // If question has options
            if (question.options) {
                let savedIds = question.option_ids === null ? [] : question.option_ids;
                $.each(question.options, function(optionGuidOrId, option) {
                    // If the value is not in the array, then we will append it (saved options also get handled by php)
                    if ($.inArray(optionGuidOrId, savedIds) === -1) {
                        let optionPanel = getAdditionalQuestionOptions(getOptionIndex(guidOrId), optionGuidOrId);
                        optionPanel = optionPanel.replaceAll(':guid', guidOrId);
                        questionDiv.append(optionPanel);

                        $.each(option, function(locale, value) {
                            $('input[name="questions['+ guidOrId +'][options]['+ optionGuidOrId +']['+ locale +']"]').val(value);
                        });
                    }
                });
            }
        });

        let oldValidation = {!! json_encode(old('validation')) !!};
        // Last step is validation. Unlike options, validation has no guid or ID, so we just have to check
        // if a validation field already exists or not.

        $.each(oldValidation, function(guidOrId, validation) {
            let questionDiv = getQuestionByGuidOrId(guidOrId);
            // We fetch the inputs of the validation. If length == 0, then we create the field first
            let fields = $('.validation-inputs').find('input[name="validation['+ guidOrId +'][sub-rule-check-value][]"]')
            if (fields.length === 0) {
                addValidationInputs(questionDiv, guidOrId);
            }
            // Then we set the values (php doesn't handle this one)
            let mainRule = $('select[name="validation['+ guidOrId + '][main-rule]"]');
            mainRule.val(validation['main-rule']);
            setSubRule(mainRule.parents('.validation-inputs').first(), validation['main-rule']);
            $('select[name="validation['+ guidOrId + '][sub-rule]"]').val(validation['sub-rule']);
            setSubRuleValueInputs(questionDiv, guidOrId, validation['sub-rule']);

            $('input[name="validation['+ guidOrId + '][sub-rule-check-value][]"]').each(function(index, element) {
                let val = validation['sub-rule-check-value'][index] == null ? '' : validation['sub-rule-check-value'][index];

                $(element).val(val);
            });
        });

        // We get the error field keys
        let validationErrors = {!! json_encode($errors->keys()) !!};

        $.each(validationErrors, function(index, key) {
            // To construct the name, we split by '.'
            let parts = key.split('.');
            let name = '';
            $.each(parts, function(index, value) {
                // Add parts based on each index
                switch (index) {
                    case 0:
                        name += value;
                        break;

                    case 1:
                        name += '[' + value;
                        break;

                    default:
                        name += '][' + value;
                }
            });

            // Now we need to check whether the last char is numeric or not. If it's numeric, we need to remove it and
            // replace it with a ']' (since then it's an array). Otherwise we just add a ']'.
            let lastPart = name.substring(name.lastIndexOf('[') + 1);
            if (! isNaN(parseInt(lastPart))) {
                name = name.substring(0, name.length - lastPart.length);
            }
            name += ']';

            // We now have the name. For most cases, we can just add 'has-error' to the parent form group
            // However, for arrays, we will manually add a border, else it doesn't display quite right
            if (name.substring(name.length-2) === '[]') {
                $('[name="'+ name +'"]').eq(lastPart).css('border-color', '#a94442');
            } else {
                $('[name="'+ name +'"]').parents('.form-group').first().addClass('has-error');
            }
        });
    });
</script>