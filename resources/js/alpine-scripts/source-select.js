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
            let input = this.inputGroup.querySelector('input');
            // Not an input?
            if (! input) {
                // Check if select
                input = this.inputGroup.querySelector('select');
                // Check if valid, else we get a textarea
                input = input ? input : this.inputGroup.querySelector('textarea');
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
                        case 'hidden':
                            input.value = value;
                            this.checkLivewire(input);
                            this.triggerChange(input);
                            break;
                        case 'radio':
                            input = this.inputGroup.querySelector(`input[type="radio"][value="${value}"]`);
                            if (input) {
                                this.inputGroup.querySelector('input[type="radio"]:checked').checked = false;
                                input.checked = true;
                                this.checkLivewire(input);
                                this.triggerChange(input);
                            }
                            break;
                        case "checkbox":
                            input = this.inputGroup.querySelector(`input[type="checkbox"][value="${value}"]`);
                            if (input) {
                                input.checked = true;
                                this.checkLivewire(input);
                                this.triggerChange(input);
                            }
                            break;
                        default:
                            // Not a valid input type?
                            break;
                    }
                }
            }
        }
    },
    checkLivewire(input) {
        if (input.hasAttribute('wire:model')) {
            window.livewire.emit('source-changed', input.getAttribute('wire:model'), input.value);
        }
    },
    triggerChange(input) {
        let event = new Event('change', { bubbles: true });
        input.dispatchEvent(event);
    }
});