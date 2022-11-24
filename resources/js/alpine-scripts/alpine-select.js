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
        let context = this;
        setTimeout(() => {
            context.constructSelect();

            if (null !== context.select) {
                let observer = new MutationObserver(function(mutations) {
                    mutations.forEach(function(mutation) {
                        context.constructSelect();
                        window.triggerEvent(context.select, 'change');
                    });
                });

                observer.observe(this.select, { childList: true });

                let attributeObserver = new MutationObserver(function(mutations) {
                    mutations.forEach(function(mutation) {
                        context.constructSelect();
                    });
                });

                attributeObserver.observe(this.select, { attributeFilter: ['disabled'] });
            }
        });
    },
    constructSelect() {
        let wrapper = this.$refs['select-wrapper'];

        if (wrapper) {
            // Get the select element
            this.select = wrapper.querySelector('select');
            // Select is defined!
            if (null !== this.select) {
                this.multiple = this.select.hasAttribute('multiple');

                // Bind event listener for change
                this.select.addEventListener('change', (event) => {
                    this.updateSelectedValues();
                });
                if (this.multiple) {
                    // If it's multiple, we will add an event listener to rebuild the input on resizing,
                    // as well as on switching tabs.
                    window.addEventListener('resize', (event) => {
                        this.setInputValue();
                    });

                    window.addEventListener('tab-switched', (event) => {
                        setTimeout(() => {
                            this.setInputValue();
                        });
                    });
                }

                this.disabled = this.select.hasAttribute('disabled');

                // Add class if disabled, so CSS can do magic
                if (this.disabled) {
                    this.$refs['select-input'].classList.add('disabled');
                    this.open = false;
                } else {
                    this.$refs['select-input'].classList.remove('disabled');
                }

                // Build the alpine select
                let optionDropdown = this.$refs['select-options'];
                // Clear any options there might be left
                optionDropdown.children.remove();
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

                setTimeout(() => {
                    this.updateSelectedValues();
                });
            }
        }
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
            const inputHeight = 44; // px, same as 2.75rem
            const right = 88; // px, same as 5.5rem
            const topMargin = 2 // px, same as 0.125rem
            const leftMargin = 4 // px, same as 0.25rem
            const maxWidth = parseInt(getComputedStyle(input).width) - right;
            let currentWidth = 0;
            let rows = 1;

            for (let key of Object.keys(this.values)) {
                let option = this.$refs['select-options'].querySelector(`span[data-value="${key}"]`);

                let text = this.values[key];
                let newInputOption = document.createElement('span');

                if (option && option.hasAttribute("data-icon")) {
                    let icon = document.createElement('i');
                    icon.classList.add('icon-sm', option.getAttribute("data-icon"), 'mr-2', 'static');
                    newInputOption.appendChild(icon);
                }

                newInputOption.appendChild(document.createTextNode(text));
                newInputOption.classList.add('form-input-option');

                if (this.disabled) {
                    newInputOption.classList.add('disabled');
                }

                newInputOption.setAttribute("data-value", key);
                newInputOption.setAttribute("x-on:click", "changeOption($el)");
                inputGroup.appendChild(newInputOption);

                // Use timeout, so it processes after the current thread. Else, computedStyle will be 'auto'
                setTimeout(() => {
                    let newWidth = currentWidth + leftMargin + parseInt(getComputedStyle(newInputOption).width);

                    if (newWidth > maxWidth) {
                        rows++;
                        currentWidth = 0;
                    }

                    newInputOption.style.left = currentWidth + leftMargin + "px";
                    newInputOption.style.top = topMargin + (rows - 1) * inputHeight + 'px';

                    // Always set height
                    input.style.height = rows * inputHeight + 'px';

                    currentWidth += leftMargin + parseInt(getComputedStyle(newInputOption).width);
                });

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

        if (option.hasAttribute("data-icon")) {
            newOption.setAttribute("data-icon", option.getAttribute("data-icon"));
        }

        // Add alpine functions
        newOption.setAttribute("x-bind:class", "Object.keys(values).includes('" + value + "') ? 'selected' : ''");
        newOption.setAttribute("x-on:click", "changeOption($el)");
        newOption.classList.add('select-option');

        if (option.hasAttribute('disabled')) {
            newOption.classList.add('disabled');
        } else if (this.disabled) {
            newOption.classList.add('disabled', 'readonly');
        }

        // Append to list
        parent.appendChild(newOption);
    },
});