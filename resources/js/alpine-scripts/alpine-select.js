export default (initiallyOpen = false) => ({
    // Select element
    select: null,
    // Current value(s) of the select
    values: {},
    // If the select is disabled
    disabled: false,
    // Is the dropdown open?
    open: initiallyOpen,
    // Is the dropdown multiple supported?
    multiple: false,

    init() {
        document.addEventListener('DOMContentLoaded', () => {
            let wrapper = this.$refs['select-wrapper'];
            // Get the select element
            this.select = wrapper.querySelector('select');
            // Select is defined!
            if (null !== this.select) {
                this.multiple = this.select.hasAttribute('multiple');

                // Bind event listener for change
                let context = this;
                this.select.addEventListener('change', function (event) {
                    context.updateSelectedValues();
                });

                this.updateSelectedValues();

                this.disabled = this.select.hasAttribute('disabled');

                // Add class if disabled, so CSS can do magic
                if (this.disabled) {
                    this.$refs['select-input'].classList.add('disabled');
                    this.open = false;
                }

                // Build the alpine select
                let optionDropdown = this.$refs['select-options'];
                let options = this.select.options;
                // Loop options to build
                // Note: we cannot use forEach, as options is a HTML collection, which is not an array
                for (let i = 0; i < options.length; i++) {
                    this.buildOption(optionDropdown, options[i]);
                }

                // Hide the original select
                this.select.style.display = 'none';
                // Show the new alpine select
                this.$refs['select-input-group'].style.display = '';
            }
        });
    },
    toggle() {
        // If not disabled, we will handle the click
        if (! this.disabled) {
            this.open = ! this.open
        }
    },
    close() {
        this.open = false;
    },
    changeOption(element) {
        if (! element.classList.contains('disabled')) {
            this.updateValue(element.getAttribute('data-value'), element.textContent);
            if (! this.multiple) {
                this.close();
            }
            window.triggerEvent(this.select, 'change');
        }
    },
    updateValue(value, text = null) {
        let option = this.$refs['select-options'].querySelector(`span[data-value="${value}"]`);

        text = null === text ? (option ? option.textContent : value) : text;
        text = text.trim();

        if (this.multiple) {
            // If it's multiple, we want to remove the value if the clicked value is already selected.
            // Otherwise we append the value to the values.
            if (this.values[value]) {
                delete this.values[value];
            } else {
                this.values[value] = text;
            }

            this.setSelectedOptions();
        } else {
            // If it's not multiple, we simply set the value.
            this.values = {
                [value]: text,
            };

            this.select.value = value;
        }
    },
    setSelectedOptions() {
        let options = this.select.options;

        let values = Object.keys(this.values);
        for (let option of options) {
            option.selected = values.indexOf(option.value) >= 0;
        }
    },
    updateSelectedValues() {
        this.values = {};

        let options = this.select.options;
        for (let option of options) {
            if (option.selected) {
                this.values[option.value] = option.textContent.trim();
            }
        }

        this.setInputValue();
    },
    setInputValue() {
        if (this.multiple) {
            // Reset first
            let input = this.$refs['select-input'];
            input.value = '';

            let inputGroup = this.$refs['select-input-group'];
            inputGroup.querySelectorAll('.form-input-option').remove();

            // Space to keep from the right at all times to accommodate the icons
            const inputHeight = '44'; // px, same as 2.75rem
            const right = '88'; // px, same as 5.5rem
            const margin = '4' // px, same as 0.25rem
            const maxWidth = parseInt(getComputedStyle(input)['width']) - right;
            let currentWidth = 0;
            let rows = 1;

            for (let key in Object.keys(this.values)) {
                let text = this.values[key];
                let newInputOption = document.createElement('span');
                newInputOption.appendChild(document.createTextNode(text));
                newInputOption.classList.add('form-input-option');
                newInputOption.setAttribute("data-value", key);
                newInputOption.setAttribute("x-on:click", "changeOption($el)");
                inputGroup.appendChild(newInputOption);

                if (currentWidth !== 0) {
                    let newWidth = currentWidth + margin + parseInt(getComputedStyle(newInputOption)['width']);
                    if (newWidth > maxWidth) {
                        rows++;
                        input.style.height = rows * inputHeight + 'px';
                        currentWidth = 0;
                    } else {
                        newInputOption.style.left = (currentWidth + margin) + "px";
                    }
                }

                newInputOption.style.top = margin + ((rows - 1) * inputHeight) + 'px';
                currentWidth += margin + parseInt(getComputedStyle(newInputOption)['width']);
            }
        } else {
            this.$refs['select-input'].value = Object.values(this.values)[0];
        }
    },
    buildOption(parent, option) {
        // Trim to ensure it's not filled with unnecessary white space (will look ugly in the input)
        let value = option.value;

        let text = option.textContent.trim();

        // Build a new alpine option
        let newOption = document.createElement('span');
        newOption.appendChild(document.createTextNode(text));
        newOption.setAttribute("data-value", value);
        // Add alpine functions
        newOption.setAttribute("x-bind:class", "value == '" + value + "' ? 'selected' : ''");
        newOption.setAttribute("x-on:click", "changeOption($el)");
        newOption.classList.add('select-option');

        if (option.hasAttribute('disabled')) {
            newOption.classList.add('disabled');
        }

        // Append to list
        parent.appendChild(newOption);
    },
});