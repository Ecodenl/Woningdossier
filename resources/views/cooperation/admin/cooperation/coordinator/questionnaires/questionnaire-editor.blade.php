@extends('cooperation.layouts.app')
@push('css')
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endpush
@section('content')
    <section class="section">
        <div class="container">
            <form action="{{ route('cooperation.admin.cooperation.coordinator.questionnaires.store') }}" method="post">
                <input type="hidden" name="questionnaire_id" value="{{$questionnaire->id}}">
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
                            <a href="#" id="short-answer" class="list-group-item"><i class="glyphicon glyphicon-align-left"></i> Kort antwoord</a>
                            <a href="#" id="long-answer" class="list-group-item"><i class="glyphicon glyphicon-align-justify"></i> Alinea</a>
                            <a href="#" id="radio-button" class="list-group-item"><i class="glyphicon glyphicon-record"></i> Meerkeuze</a>
                            <a href="#" id="checkbox" class="list-group-item"><i class="glyphicon glyphicon-unchecked"></i> Selectievakjes</a>
                            <a href="#" id="dropdown" class="list-group-item"><i class="glyphicon glyphicon-collapse-down"></i> Dropdownmenu</a>
                            <a href="#" id="date" class="list-group-item"><i class="glyphicon glyphicon-calendar"></i> Datum</a>
                            <a href="#" id="time" class="list-group-item"><i class="glyphicon glyphicon-time"></i> Tijd</a>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="panel">
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-xs-12">
                                        <div class="form-group">
                                            <label for="name">Naam:</label>
                                            <input type="text" class="form-control" name="name" value="{{ $questionnaire->name }}" placeholder="Nieuwe vragenlijst">
                                        </div>
                                        <div class="form-group">
                                            <label for="step_id">Na stap:</label>
                                            <select name="step_id" class="form-control">
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
                                    @forelse($questionnaire->questions()->orderBy('order', 'DESC')->get() as $question)
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
                    '<select class="validation form-control"  id="">' +
                        '@foreach(__("woningdossier.cooperation.admin.custom-fields.index.rules") as $rule => $translation)' +
                            '<option value="{{$rule}}">{{$translation}}</option>' +
                        '@endforeach' +
                    '</select>' +
                '</div>' +
            '</div>' +
            '<div class="col-sm-4">' +
                '<div class="form-group">' +
                    '@foreach(__("woningdossier.cooperation.admin.custom-fields.index.rules") as $rule => $translation)' +
                        '<select class="validation-options form-control" name="questions[new][][validation-options]" id="{{$rule}}">' +
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


        toolBox.find('a').on('click', function (event) {
            // always add the empty form build panel
            // we add the input types after that
            sortable.prepend(formBuildPanel);
            event.preventDefault();
        });

        /**
         * function to add the validation inputs
         */
        function addValidationInputs(question, guid)
        {

            // add the validation options to the form
            question.append(formBuildValidation);
            // after that we add the name attribute
            question.find('.validation').attr('name', 'validation['+guid+'][validation]');
            question.find('.validation-options').attr('name', 'validation['+guid+'][validation-options]');

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
        function addInputQuestion(question, guid)
        {
            $(supportedLocales).each(function (index, locale) {
                var fullQuestionName = 'questions[new]['+guid+'][question]['+locale+']';
                var formGroup = $($(formGroupElement).append(inputGroupElement)).appendTo(question);

                var textInput = $('<input>').addClass('form-control').attr({
                    placeholder: 'Vraag',
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

        toolBox.find('#short-answer').on('click', function () {
            var questionPanel = sortable.find('.panel').first();
            var question = questionPanel.find('.question');
            var panelFooter = questionPanel.find('.panel-footer');
            var guid = createGuid();

            addInputQuestion(question, guid);

            addHiddenInputWithInputType(question, guid, 'text');

            var guidHiddenInput = $('<input>').attr({
                type: 'hidden',
                value: guid,
            }).addClass('guid');

            question.append(guidHiddenInput);

            addRequiredCheckbox(panelFooter, guid);

            addValidationInputs(question, guid);

            sortable.sortable('refresh');
            $('input, select').trigger('change');

        });

        toolBox.find('#dropdown').on('click', function () {
            var questionPanel = sortable.find('.panel').first();
            var question = questionPanel.find('.question');
            var panelFooter = questionPanel.find('.panel-footer');
            var guid = createGuid();

            addHiddenInputWithInputType(question, guid, 'select');

            var guidHiddenInput = $('<input>').attr({
                type: 'hidden',
                value: guid,
            }).addClass('guid');

            question.append(guidHiddenInput);

            addInputQuestion(question, guid);

            addAdditionalQuestionOptions(question, guid);

            addRequiredCheckbox(panelFooter, guid);

            // now let it autofocus to the first option input
            question.find('.option-text').first().attr('autofocus', true);

            sortable.sortable('refresh')
            $('input, select').trigger('change');
        });

        toolBox.find('#long-answer').on('click', function () {
            var question = sortable.find('.panel').first().find('#question');
            var formGroup = question.find('.form-group');

            formGroup.append("<input class='form-control' placeholder='Stel uw vraag waar een langer antwoord voor nodig is'>");
            question.parent().parent().find('.validation-rules').append(formBuildValidation);

            sortable.sortable('refresh');
            $('input, select').trigger('change');
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

                var question = $(this).parent().parent().parent().parent();
                var guid = question.find('.guid').val();

                addAdditionalQuestionOptions(question, guid);
            }
        });

        $('body').on('change', 'select.validation', function () {
            var selectedMainRule = $(this);

            var validationRuleRow = selectedMainRule.parent().parent().parent();

            var optionalRuleThatIsNotSelected = validationRuleRow.find('select[name*=validation-options][id!='+selectedMainRule.val()+']');
            optionalRuleThatIsNotSelected.hide();

            var optionalRule = validationRuleRow.find('select[name*=validation-options][id='+selectedMainRule.val()+']');
            optionalRule.show();
        });


        /**
         * Add the min and max input rule to the validation row
         *
         * @param event
         * @param question
         * @param guid
         */
        function addBetweenRuleInputs(question, guid) {
            // remove the old rule inputs
            removeOldRuleInput(question, guid);

            var validationInputRow = question.find('.validation-inputs');

            // create the min and max inputs
            var betweenMinInput = $('<div class="col-sm-2"></div>').append($('<input>').attr({
                name: 'validation['+guid+'][validation-options][between][min]',
                placeholder: 'Min..',
                type: 'text'
            }).addClass('form-control'));

            var betweenMaxInput = $('<div class="col-sm-2"></div>').append($('<input>').attr({
                name: 'validation['+guid+'][validation-options][between][max]',
                placeholder: 'Max..',
                type: 'text'
            }).addClass('form-control'));

            validationInputRow.append(betweenMinInput);
            validationInputRow.append(betweenMaxInput);

        }

        function removeOldRuleInput(question) {

            var validationInputRow = question.find('.validation-inputs');
            validationInputRow.find('.col-sm-2').remove();
        }

        function addMinRuleInputs(question, guid) {

            // remove the old rule inputs
            removeOldRuleInput(question);

            var validationInputRow = question.find('.validation-inputs');
            // create the min and max inputs
            var minInput = $('<div class="col-sm-2"></div>').append($('<input>').attr({
                name: 'validation['+guid+'][validation-options][between][min]',
                placeholder: 'Min..',
                type: 'text'
            }).addClass('form-control'));

            validationInputRow.append(minInput);
        }
        
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
        $('body').on('change', 'select.validation-options', function (event) {
            var selectedValidationOption = $(this);
            var question = selectedValidationOption.parent().parent().parent().parent();
            var selectedValidationValue = $(this).val();
            var guid = getQuestionId(question);

            if (event.originalEvent !== undefined) {
                switch (selectedValidationValue) {
                    case 'between':
                        addBetweenRuleInputs(question, guid);
                        break;
                    case 'min':
                        addMinRuleInputs(question, guid)
                }
            }


        });

        $('body').on('click', '.glyphicon-trash', function (event) {
            event.preventDefault();
            $(this).parent().parent().parent().parent().parent().parent().remove();
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
                    console.log(questionOrder);

                }
            });
        });

        // $('input, select').trigger('change');
    </script>
@endpush