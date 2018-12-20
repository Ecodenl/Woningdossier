@extends('cooperation.layouts.app')
@push('css')
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endpush
@section('content')
    <section class="section">
        <div class="container">
            <form action="{{ route('cooperation.admin.cooperation.coordinator.questionnaires.update') }}" method="post">
                <input type="hidden" name="questionnaire[id]" value="{{$questionnaire->id}}">
                {{csrf_field()}}
                <div class="row">
                    <div class="col-sm-6">
                        <a id="leave-creation-tool" href="{{route('cooperation.admin.cooperation.coordinator.questionnaires.index')}}" class="btn btn-warning">
                            @lang('woningdossier.cooperation.admin.cooperation.coordinator.index.create.leave-creation-tool')
                        </a>
                    </div>
                    <div class="col-sm-6">
                        <button type="submit" href="{{route('cooperation.admin.cooperation.coordinator.questionnaires.index')}}" class="btn btn-primary pull-right">
                            Opslaan
                        </button>
                    </div>
                </div>
                <!-- comment -->
                <div class="row alert-top-space">
                    <div class="col-md-3">
                        <div id="tool-box" class="list-group">
                            <a href="#" id="short-answer" class="list-group-item"><i class="glyphicon glyphicon-align-left"></i>
                                @lang('woningdossier.cooperation.admin.cooperation.coordinator.questionnaires.index.types.text')
                            </a>
                            <a href="#" id="long-answer" class="list-group-item"><i class="glyphicon glyphicon-align-justify"></i>
                                @lang('woningdossier.cooperation.admin.cooperation.coordinator.questionnaires.index.types.textarea')
                            </a>
                            <a href="#" id="radio-button" class="list-group-item"><i class="glyphicon glyphicon-record"></i> 
                                @lang('woningdossier.cooperation.admin.cooperation.coordinator.questionnaires.index.types.radio')
                            </a>
                            <a href="#" id="checkbox" class="list-group-item"><i class="glyphicon glyphicon-unchecked"></i> 
                                @lang('woningdossier.cooperation.admin.cooperation.coordinator.questionnaires.index.types.checkbox')
                            </a>
                            <a href="#" id="dropdown" class="list-group-item"><i class="glyphicon glyphicon-collapse-down"></i> 
                                @lang('woningdossier.cooperation.admin.cooperation.coordinator.questionnaires.index.types.select')
                            </a>
                            <a href="#" id="date" class="list-group-item"><i class="glyphicon glyphicon-calendar"></i>
                                @lang('woningdossier.cooperation.admin.cooperation.coordinator.questionnaires.index.types.date')
                            </a>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="panel">
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-xs-12">
                                        @foreach(config('woningdossier.supported_locales') as $locale)
                                        <div class="form-group {{ $errors->has('questionnaire.name.*') ? ' has-error' : '' }}">
                                            <label for="name">Naam:</label>
                                            <div class="input-group">
                                                <span class="input-group-addon">{{$locale}}</span>
                                                <input type="text" class="form-control" name="questionnaire[name][{{$locale}}]" value="{{$questionnaire->getTranslation('name', $locale) instanceof \App\Models\Translation ? $questionnaire->getTranslation('name', $locale)->translation : "" }}" placeholder="Nieuwe vragenlijst">
                                            </div>
                                        </div>
                                        @endforeach
                                        <div class="form-group">
                                            <label for="step_id">Na stap:</label>
                                            <select name="questionnaire[step_id]" class="form-control">
                                                @foreach($steps as $i => $step)
                                                    <option value="{{ $step->id }}" @if($questionnaire->step_id == $step->id) selected="selected" @endif >{{ $i+1 }}: {{ $step->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel">
                            <div class="panel-body" >
                                <div id="sortable">
                                    @forelse($questionnaire->questions()->orderBy('order')->get() as $question)
                                        @component('cooperation.admin.cooperation.coordinator.questionnaires.layouts.form-build-panel', ['question' => $question])

                                        @endcomponent
                                    @empty

                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection
@push('css')
    <link href="{{asset('css/bootstrap-toggle.min.css')}}" rel="stylesheet">
@endpush
@push('js')


    <script src="{{asset('js/bootstrap-toggle.min.js')}}"></script>
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
        var formBuildPanel =
            '<div class="form-builder panel panel-default">' +
                '<div class="panel-heading">' +

                '</div>'+
                '<div class="panel-body">' +
                    '<div class="row">' +
                        '<div class="col-sm-12 question">' +
                            // '<div class="form-group">' +

                            // '</div>' +
                        '</div>' +
                    '</div>' +
                    '<div class="row">' +
                        '<div class="col-sm-12">' +
                            '<a class="btn btn-primary add-validation">Voeg validatie toe</a>'+
                        '</div>' +
                    '</div>'+
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
        var toolBox = $('#tool-box');
        var formGroupElement = '<div class="form-group"></div>';
        var inputGroupElement = '<div class="input-group"></div>';
        var inputGroupAddon = '<span class="input-group-addon"></span>';
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
            return Math.floor((1 + Math.random()) * 0x10000).toString(16).substring(1);
        }


        toolBox.find('a').on('click', function (event) {
            // always add the empty form build panel
            // we add the input types after that
            sortable.prepend(formBuildPanel);
            event.preventDefault();

            // add the text from the toolbox to the panel-heading
            var questionPanel = sortable.find('.panel').first();
            questionPanel.find('.panel-heading').text($(this).text().trim())
        });



        /**
         * function to add a hidden input with the type the question should be
         */
        function addHiddenInputWithInputType(question, guid, type)
        {
            var hiddenInputWithInputTypeName = 'questions['+guid+'][type]';
            var hiddenInputWithInputType = $('<input>').attr({
                name: hiddenInputWithInputTypeName,
                type: 'hidden',
                value: type
            });
            question.append(hiddenInputWithInputType);
        }



        function addAdditionalQuestionOptions(question, guid)
        {
            console.log(question);
            // we need to create this for every new option
            // so we can make a difference between the multiple options
            var additionalQuestionOptionGuid = createGuid();

            // we append a option-group for every new added option
            question.append(optionGroupElement);

            // we add "option" inputs for each supported language
            $(supportedLocales).each(function (index, locale) {
                var fullQuestionName = 'questions['+guid+'][options]['+additionalQuestionOptionGuid+']['+locale+']';

                var formGroup = $($(formGroupElement).append(inputGroupElement));
                // raging because i dont know why $(optionGroup).append(formGroup) does not work.
                $(question).find('.option-group').last().append(formGroup);

                var totalOptions = question.find('.option-group').length;

                $(question).find('.option-group').last().prepend('<label> Optie '+totalOptions +'</label>');


                var additionalTextInput = $('<input>').addClass('form-control option-text').attr({
                    placeholder: 'Optie toevoegen',
                    name: fullQuestionName,
                    type: 'text'
                });

                // the remove cross that we will append next to the option
                var removeOptionButton = $('<a>').addClass('text-danger');
                var removeGlyphicon = $('<i>').addClass('glyphicon glyphicon-remove');

                formGroup.find('.input-group').append($(inputGroupAddon).append(locale));
                formGroup.find('.input-group').append(additionalTextInput);
                formGroup.find('.input-group').append($(inputGroupAddon).append(removeOptionButton.append(removeGlyphicon)));
            });
        }

        /**
         * the input where the user can fill in the main question
         */
        function addInputQuestion(question, guid, placeholder)
        {
            $(supportedLocales).each(function (index, locale) {
                var fullQuestionName = 'questions['+guid+'][question]['+locale+']';
                var formGroup = $($(formGroupElement).append(inputGroupElement)).appendTo(question);

                var textInput = $('<input>').addClass('form-control').attr({
                    placeholder: placeholder,
                    name: fullQuestionName,
                    type: 'text'
                });

                formGroup.find('.input-group').append($(inputGroupAddon).append(locale));
                formGroup.find('.input-group').append(textInput);

            });
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
                name: 'questions['+guid+'][guid]',
                type: 'hidden',
                value: guid,
            }).addClass('guid');

            question.append(guidHiddenInput);
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
                name: 'questions['+guid+'][required]'
            });

            panelFooter.find('.pull-right').html(rbl);
            requiredCheckbox.appendTo(panelFooter.find('.pull-right > label'));
        }


        toolBox.find('#short-answer').on('click', function () {
            var questionPanel = sortable.find('.panel').first();
            var question = questionPanel.find('.question');
            var panelFooter = questionPanel.find('.panel-footer');
            var guid = createGuid();

            addInputQuestion(question, guid, 'Vraag');

            addHiddenInputWithInputType(question, guid, 'text');

            addHiddenGuidInput(question, guid);

            addRequiredCheckbox(panelFooter, guid);

            sortable.sortable('refresh');

        });

        toolBox.find('#date').on('click', function () {
            var questionPanel = sortable.find('.panel').first();
            var question = questionPanel.find('.question');
            var panelFooter = questionPanel.find('.panel-footer');
            var guid = createGuid();
            // no validation needed here
            questionPanel.find('.add-validation').remove();

            addInputQuestion(question, guid, 'Vraag');

            addHiddenInputWithInputType(question, guid, 'date');

            addHiddenGuidInput(question, guid);

            addRequiredCheckbox(panelFooter, guid);

            sortable.sortable('refresh');
        });


        toolBox.find('#long-answer').on('click', function () {
            var questionPanel = sortable.find('.panel').first();
            var question = questionPanel.find('.question');
            var panelFooter = questionPanel.find('.panel-footer');
            var guid = createGuid();

            addInputQuestion(question, guid, 'Stel uw vraag waar een langer antwoord voor nodig is...');

            addHiddenInputWithInputType(question, guid, 'textarea');

            addHiddenGuidInput(question, guid);

            addRequiredCheckbox(panelFooter, guid);


            sortable.sortable('refresh');
        });

        toolBox.find('#radio-button').on('click', function () {
            var questionPanel = sortable.find('.panel').first();
            var question = questionPanel.find('.question');
            var panelFooter = questionPanel.find('.panel-footer');
            var guid = createGuid();

            // no validation needed here
            questionPanel.find('.add-validation').remove();

            addHiddenInputWithInputType(question, guid, 'radio');

            addHiddenGuidInput(question, guid);

            addInputQuestion(question, guid, 'Vraag');

            addAdditionalQuestionOptions(question, guid);

            addRequiredCheckbox(panelFooter, guid);

            // now let it autofocus to the first option input
            question.find('.option-text').first().attr('autofocus', true);

            sortable.sortable('refresh')
        });

        toolBox.find('#checkbox').on('click', function () {
            var questionPanel = sortable.find('.panel').first();
            var question = questionPanel.find('.question');
            var panelFooter = questionPanel.find('.panel-footer');
            var guid = createGuid();

            // no validation needed here
            questionPanel.find('.add-validation').remove();

            addHiddenInputWithInputType(question, guid, 'checkbox');

            addHiddenGuidInput(question, guid);

            addInputQuestion(question, guid, 'Vraag');

            addAdditionalQuestionOptions(question, guid);

            addRequiredCheckbox(panelFooter, guid);

            // now let it autofocus to the first option input
            question.find('.option-text').first().attr('autofocus', true);

            sortable.sortable('refresh')
        });

        toolBox.find('#dropdown').on('click', function () {
            var questionPanel = sortable.find('.panel').first();
            var question = questionPanel.find('.question');
            var panelFooter = questionPanel.find('.panel-footer');
            var guid = createGuid();

            // no validation needed here
            questionPanel.find('.add-validation').remove();

            addHiddenInputWithInputType(question, guid, 'select');

            addHiddenGuidInput(question, guid);

            addInputQuestion(question, guid, 'Vraag');

            addAdditionalQuestionOptions(question, guid);

            addRequiredCheckbox(panelFooter, guid);

            // now let it autofocus to the first option input
            question.find('.option-text').first().attr('autofocus', true);

            sortable.sortable('refresh')
        });

        // add the validation to a question
        $(document).on('click', '.add-validation', function (event) {
            event.preventDefault();
            var question = $(this).parent().parent().parent().find('.question');
            var guid = getQuestionId(question);

            addValidationInputs(question, guid);

            return false;
        });

        $(document).on('focusout', 'input.option-text', function (event) {
            if($(this).val() === "") {
                $(this).val('Optie...')
            }
        });

        $(document).on('focus', 'input.option-text', function (event) {

            // we need to check if all the language inputs are filled
            // so we get the option group
            var optionGroup = $(this).parent().parent().parent();

            var lastInputFromOptionGroup = optionGroup.find('input:last');

            // check if the last input from the option group is empty
            // and if the current focussed input is equal to the last input from the option group
            // because if so, we need to add a new option group

            if (lastInputFromOptionGroup.val() === "" && $(this)[0] === lastInputFromOptionGroup[0]) {

                var question = $(this).parent().parent().parent().parent().parent().find('.question');
                var guid = getQuestionId(question);

                addAdditionalQuestionOptions(question, guid);
            }
        });

        $('body').on('change', 'select.validation', function () {
            var selectedMainRule = $(this);

            var validationRuleRow = selectedMainRule.parent().parent().parent();

            // get the select sub-rule that we dont want to show
            var subRuleNotSelected = validationRuleRow.find('select[data-sub-rule!='+selectedMainRule.val()+'].sub-rule');

            // after that we hide & disable the input. No need to remove the name, see:
            // https://www.w3.org/TR/html5/forms.html#constructing-the-form-data-set
            subRuleNotSelected.hide();
            subRuleNotSelected.attr('disabled', true);

            var subRule = validationRuleRow.find('select[data-sub-rule='+selectedMainRule.val()+'].sub-rule');
            subRule.removeAttr('disabled');
            subRule.show();
            subRule.trigger('change');
        });


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



        // add the validation inputs if needed
        $('body').on('change', 'select.sub-rule', function (event) {
            var selectedValidationOption = $(this);
            var question = selectedValidationOption.parent().parent().parent().parent();
            var selectedValidationValue = $(this).val();
            var guid = getQuestionId(question);

            switch (selectedValidationValue) {
                case 'between':
                    addSubRuleCheckValueInput(question, guid, ['Min..', 'Max..']);
                    break;
                case 'min':
                    addSubRuleCheckValueInput(question, guid, ['Min..']);
                    break;
                case 'max':
                    addSubRuleCheckValueInput(question, guid, ['Max..']);
                    break;
                case 'email':
                    removeRuleInput(question);
                    break;
            }

        });

        /**
         * Remove a whole question
         */
        $('body').on('click', '.glyphicon-trash', function (event) {
            event.preventDefault();


            if (confirm('Dit verwijderd de vraag, u kunt deze actie NIET terugdraaien. Weet u zeker dat u wilt verdergaan ?')) {

                var questionId = $(this).parent().parent().parent().parent().parent().parent().find('.question_id').val();
                if (typeof questionId === "undefined") {
                    console.log('the question id is empty ohno!');
                }
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: '{{url('admin/cooperatie/coordinator/questionnaire/delete-question')}}/'+questionId,
                    method: "DELETE"
                });

                $(this).parent().parent().parent().parent().parent().parent().remove();
            }

            return false;
        });

        /**
         * Remove a option from a question
         */
        $('body').on('click', '.glyphicon-remove', function (event) {
            event.preventDefault();
            var currentOptionGroup = $(this).parent().parent().parent().parent().parent();
            var question = currentOptionGroup.parent();
            var questionId = question.find('.question_id').val();
            var questionOptionId = currentOptionGroup.find('.question_option_id').val();

            console.log(currentOptionGroup, question);

            if (confirm('Dit verwijderd de optie van deze vraag, u kunt deze actie NIET terugdraaien. Weet u zeker dat u wilt verdergaan ?')) {

                if (typeof questionId !== "undefined" || typeof questionOptionId !== "undefined") {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url: '{{url('admin/cooperatie/coordinator/questionnaire/delete-option')}}/'+questionId+'/'+questionOptionId,
                        method: "DELETE"
                    });
                }

                currentOptionGroup.remove();
            }

            return false;
        });

        $('#leave-creation-tool').on('click', function (event) {
           if (confirm('@lang('woningdossier.cooperation.admin.cooperation.coordinator.index.create.leave-creation-tool-warning')')) {

           } else {
               event.preventDefault();
               return false;
           }
        });



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
        });

        // $('input, select').trigger('change');
    </script>
@endpush