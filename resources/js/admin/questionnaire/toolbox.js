var toolBox = $('#tool-box');

toolBox.find('a').on('click', function (event) {
    // always add the empty form build panel
    // we add the input types after that
    sortable.prepend(formBuildPanel);
    event.preventDefault();
});


toolBox.find('#short-answer').on('click', function () {
    var questionPanel = sortable.find('.panel').first();
    var question = questionPanel.find('.question');
    var panelFooter = questionPanel.find('.panel-footer');
    var guid = createGuid();

    addInputQuestion(question, guid, 'Vraag');

    addHiddenInputWithInputType(question, guid, 'text');

    addHiddenGuidInput(question, guid);

    addRequiredCheckbox(panelFooter, guid);

    addValidationInputs(question, guid);

    sortable.sortable('refresh');

});

toolBox.find('#dropdown').on('click', function () {
    var questionPanel = sortable.find('.panel').first();
    var question = questionPanel.find('.question');
    var panelFooter = questionPanel.find('.panel-footer');
    var guid = createGuid();

    addHiddenInputWithInputType(question, guid, 'select');

    addHiddenGuidInput(question, guid);

    addInputQuestion(question, guid, 'Vraag');

    addAdditionalQuestionOptions(question, guid);

    addRequiredCheckbox(panelFooter, guid);

    // now let it autofocus to the first option input
    question.find('.option-text').first().attr('autofocus', true);

    sortable.sortable('refresh')
    $('input, select').trigger('change');
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

    addValidationInputs(question, guid);

    sortable.sortable('refresh');
});