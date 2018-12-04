@extends('cooperation.layouts.app')
@push('css')
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endpush
@section('content')
    <section class="section">
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    <div id="tool-box" class="list-group">
                        <a href="#" id="short-answer" class="list-group-item"><i class="glyphicon glyphicon-align-left"></i> Kort antwoord</a>
                        <a href="#" id="long-answer" class="list-group-item"><i class="glyphicon glyphicon-align-justify"></i>  Alinea</a>
                        <a href="#" id="radio-button" class="list-group-item"><i class="glyphicon glyphicon-record"></i>  Meerkeuze</a>
                        <a href="#" id="checkbox" class="list-group-item"><i class="glyphicon glyphicon-unchecked"></i>  Selectievakjes</a>
                        <a href="#" id="dropdown" class="list-group-item"><i class="glyphicon glyphicon-collapse-down"></i>  Dropdownmenu</a>
                        <a href="#" id="date" class="list-group-item"><i class="glyphicon glyphicon-calendar"></i>  Datum</a>
                        <a href="#" id="time" class="list-group-item"><i class="glyphicon glyphicon-time"></i>  Tijd</a>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="panel">
                        <div class="panel-body" >
                            <div id="sortable">
{{--                                @foreach([1,2,3] as $number)--}}
{{--                                    @component('form-build-panel', ['id' => $number])--}}
                                        {{--<div class="form-group">--}}
                                            {{--<input id="f" name="" placeholder="Vraag" type="text" class="form-control">--}}
                                        {{--</div>--}}
                                    {{--@endcomponent--}}
                                {{--@endforeach--}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@push('css')
    <link href="{{asset('css/bootstrap-toggle.min.css')}}" rel="stylesheet">
@push('js')


    <script src="{{asset('js/bootstrap-toggle.min.js')}}"></script>
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>

        <?php $id=0;?>
        // var formBuildPanel = '<div class="form-builder panel panel-default" ><div class="panel-body"><div class="row"><div class="col-sm-12">  </div></div></div></div>';

                {{--var formBuildPanel = '{{view('validation-options', compact('id'))}}';--}}

        var formBuildPanel = '<div class="form-builder panel panel-default">' +
            '<div class="panel-body">' +
            '<div class="row">' +
            '<div class="col-sm-12" id="question">' +
            '<div class="form-group">' +

            '</div>' +
            '</div>' +
            '</div>' +
            '<div class="row validation-rules">' +

            '</div>' +
            '</div>' +
            '<div class="panel-footer">' +
            '<div class="row"><div class="col-sm-12"><div class="pull-left"><a><i class="glyphicon glyphicon-trash"></i></a></div><div class="pull-right"><label class="control-label" for="required-{{$id}}">Verplicht <input id="required-{{$id}}" name="required[{{$id}}]" type="checkbox"></label></div></div></div>'
        '</div>'

        var formBuildValidation = '<div class="col-sm-4">' +
            '<div class="form-group">' +
            '<select class="validation form-control" name="validation[]" id="">' +
            '@foreach(__("woningdossier.cooperation.admin.custom-fields.index.rules") as $rule => $translation)' +
            '<option value="{{$rule}}">{{$translation}}</option>' +
            '@endforeach' +
            '</select>' +
            '</div>' +
            '</div>' +
            '<div class="col-sm-4">' +
                '<div class="form-group">' +
                    '@foreach(__("woningdossier.cooperation.admin.custom-fields.index.rules") as $rule => $translation)' +
                        '<select class="form-control" name="validation-options[]" id="{{$rule}}">' +
                        '@foreach(__("woningdossier.cooperation.admin.custom-fields.index.optional-rules.".$rule) as $optionalRule => $optionalRuleTranslation)' +
                            '<option value="{{$optionalRule}}">{{$optionalRuleTranslation}}</option>' +
                        '@endforeach' +
                        '</select>' +
                    '@endforeach' +
                '</div>' +
            '</div>';


        var sortable = $('#sortable');
        var toolBox = $('#tool-box');
        var formGroupElement = '<div class="form-group"></div>';

        // used input for the dropdown builder
        var dropdownMenuInputElement = '<input name="" placeholder="Optie toevoegen" type="text" class="option-text form-control">';

        toolBox.find('a').on('click', function (event) {
            // always add the empty form build panel
            // we add the input types after that
            sortable.prepend(formBuildPanel);
            event.preventDefault();
        });


        toolBox.find('#short-answer').on('click', function () {
            var question = sortable.find('.panel').first().find('#question');
            var formGroup = question.find('.form-group');

            formGroup.append("<input class='form-control' placeholder='Vraag'>");
            question.parent().parent().find('.validation-rules').append(formBuildValidation);

            sortable.sortable('refresh');

        });

        toolBox.find('#dropdown').on('click', function () {

            var question = sortable.find('.panel').first().find('#question');
            // first we want to add one with a default value
            question.find('.form-group').append('<input name="" placeholder="Optie toevoegen" value="Optie..." type="text" class="option-text form-control">');

            // add a new form group with input that only has a placehodler
            question.append($(formGroupElement).append('<input name="" placeholder="Optie toevoegen"  type="text" class="option-text form-control">'));

            // now let it autofocus to the first option input
            question.find('.option-text').first().attr('autofocus', true);

            sortable.sortable('refresh')
        });



        $(document).on('focusout', 'input.option-text', function (event) {
            if($(this).val() === "") {
                $(this).val('Optie...')
            }
        });

        $(document).on('focus', 'input.option-text', function (event) {

            if ($(this).val() === "") {

                var formGroup = $(this).parent().parent();


                formGroup.append($(formGroupElement).append(dropdownMenuInputElement));

            }
        });

        $('body').on('change', 'select[name*=validation]', function () {
            var selectedMainRule = $(this);

            var validationRuleRow = selectedMainRule.parent().parent().parent();

            var optionalRuleThatIsNotSelected = validationRuleRow.find('select[name*=validation-options][id!='+selectedMainRule.val()+']');
            optionalRuleThatIsNotSelected.hide();

            var optionalRule = validationRuleRow.find('select[name*=validation-options][id='+selectedMainRule.val()+']');
            optionalRule.show();
        });

        $('body').on('click', '.glyphicon-trash', function (event) {
            console.log('bier');
            event.preventDefault();
            $(this).parent().parent().parent().parent().parent().parent().remove();
            return false;
        }) ;





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

        $('input, select').trigger('change');
    </script>
@endpush