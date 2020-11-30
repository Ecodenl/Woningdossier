(function ($) {

    $.fn.disableAutoFill =  function () {

        // find all the inputs inside a form
        var inputs = $(this).find('input');

        // generate 1 new input name
        var newInputName =  Math.random().toString(36).replace(/[^a-z]+/g, '');

        // objects for the fake and original names
        var fakeNames = {};
        var originalNames = {};

        // loop through the inputs, collect the original name and set a fake name
        $(inputs).each(function (index, value) {

            // set original name
            originalNames[index] = $(this).attr("name");
            // set the fake name on the input
            $(this).attr('name', newInputName);
            // collect the fakename
            fakeNames[index] = $(this).attr('name');

        });

        // on submit put the original names back to the inputs
        // so we dont have a problem in the backend
        $('form').on('submit', function () {
            Object.keys(fakeNames).forEach(function(key) {
                $(inputs[key]).attr('name', originalNames[key])
            });
        });
    }
})(jQuery);