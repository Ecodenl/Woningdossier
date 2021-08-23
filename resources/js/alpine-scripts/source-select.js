export default (inputSource = 'no-match') => ({
    // Text display
    text: null,
    // Current value of the select
    value: null,
    // If the select is disabled
    disabled: false,
    // Is the dropdown open?
    open: false,
    // The input-group div that will hold all inputs
    inputGroup: null,

    init() {
        // This is almost the same as the default alpine select, but this dropdown will behave differently. Inputs
        // must still be given, but these will be the sources for each question.
        let select = this.$refs['source-select'];

        this.disabled = select.hasAttribute('disabled');

        // Prepare list items for Alpine!
        // Get children injected by PHP
        let children = this.$refs['source-select-options'].children;

        // If there's no children, then there's no answers
        if (children.length === 0) {
            this.disabled = true;
            inputSource = null;
        } else {
            // Note: we cannot use forEach, as options is a HTML collection, which is not an array
            for (let i = 0; i < children.length; i++) {
                let short = children[i].getAttribute('data-input-source-short');
                children[i].setAttribute("x-on:click", "changeOption($el)");
                children[i].classList.add('source-select-option');
                children[i].classList.add(`source-${short}`);

                // If the short is null, then there's no answers and we must disable the input
                if (short === null) {
                    this.disabled = true;
                    inputSource = null;
                }
            }
        }


        // Fetch related input group
        let formGroup = this.$refs['source-select-wrapper'].closest('.form-group');
        if (null !== formGroup) {
           this.inputGroup = formGroup.querySelector('.input-group');
        }

        this.setSourceValue(inputSource);

        // Add class if disabled, so css can do magic
        if (this.disabled) {
            this.$refs['source-select-input'].classList.add('disabled');
            this.open = false;
        }
    },
    toggle() {
        // If not disabled, we will handle the click
        if (! this.disabled) {
            this.open = ! this.open
        }
    },
    changeOption(element) {
        if (! element.classList.contains('disabled')) {
            this.setSourceValue(element.getAttribute('data-input-source-short'));
            this.setElementValue(element.getAttribute('data-input-value'));
        }
    },
    setSourceValue(value, text = null) {
        let option = this.$refs['source-select'].querySelector(`option[value="${value}"]`);
        if (null === option) {
            // Option not found? Fallback to no match
            value = 'no-match';
        }
        this.value = value;

        this.text = null === text ? this.$refs['source-select'].querySelector(`option[value="${value}"]`).textContent : text;
        this.text = this.text.trim();
        this.open = false;
    },
    setElementValue(value) {
        if (this.inputGroup) {
            // If the value is JSON, we need to do something slightly different (currently only relevant for rating slider)
            let parsed = this.parseJson(value);
            if (parsed !== null && parsed instanceof Object) {
                // Set values for each input in the JSON object.
                for (const [short, value] of Object.entries(parsed)) {
                    let input = this.inputGroup.querySelector(`input:not([disabled]):not([readonly])[type="hidden"][data-short="${short}"]`);
                    if (input) {
                        input.value = value;
                        window.triggerEvent(input, 'input');
                        window.triggerEvent(input, 'change');
                    }
                }
            } else {
                // Define the input. It cannot be hidden, disabled or readonly
                let input = this.inputGroup.querySelector('input:not([disabled]):not([readonly]):not([type="hidden"])');
                // Not an input?
                if (! input) {
                    // Check if select
                    input = this.inputGroup.querySelector('select:not([disabled]):not([readonly])');
                    // Check if valid, else we get a textarea
                    input = input ? input : this.inputGroup.querySelector('textarea:not([disabled]):not([readonly])');
                }

                // If an input is found...
                if (input) {
                    let type = input.getAttribute('type');

                    // No type? Then probably select or textarea. We try the tag
                    if (type === null) {
                        type = input.tagName;
                    }

                    if (typeof type !== 'undefined') {
                        type = type.toLowerCase();

                        switch (type) {
                            case 'text':
                            case 'date':
                            case 'select':
                            case 'textarea':
                            case 'range':
                                input.value = value;
                                window.triggerEvent(input, 'input');
                                window.triggerEvent(input, 'change');
                                break;
                            case 'radio':
                                input = this.inputGroup.querySelector(`input[type="radio"][value="${value}"]`);
                                if (input) {
                                    let checkedInput = this.inputGroup.querySelector('input[type="radio"]:checked');
                                    if (checkedInput) {
                                        checkedInput.checked = false;
                                    }
                                    input.checked = true;
                                    window.triggerEvent(input, 'change');
                                }
                                break;
                            case "checkbox":
                                input = this.inputGroup.querySelector(`input[type="checkbox"][value="${value}"]`);
                                if (input) {
                                    input.checked = true;
                                    window.triggerEvent(input, 'change');
                                }
                                break;
                            default:
                                // Not a valid input type?
                                break;
                        }
                    }
                }
            }
        }
    },
    parseJson(value) {
        let parsed = null;
        try {
            parsed = JSON.parse(value);
        } catch (e) {
            parsed = null;
        }

        return parsed;
    }
});