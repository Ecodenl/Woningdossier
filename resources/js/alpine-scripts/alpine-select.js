export default (initiallyOpen = false) => ({
    // Select element
    select: null,
    // Current value(s) of the select (to by synced)
    values: null,
    // If the select is disabled
    disabled: false,
    // Is the dropdown open?
    open: initiallyOpen,
    // Is the dropdown multiple supported?
    multiple: false,
    livewire: false,
    wireModel: null,

    init() {
        try {
            this.livewire = !! this.$wire;
        } catch (e) {
            this.livewire = false;
        }

        let context = this;
        setTimeout(() => {
            context.constructSelect();

            if (null !== context.select) {
                let observer = new MutationObserver(function(mutations) {
                    mutations.forEach(function(mutation) {
                        context.constructSelect();
                        if (! context.livewire) {
                            window.triggerEvent(context.select, 'change');
                        }
                    });
                });

                observer.observe(context.select, { childList: true });

                // Bind event listener for change
                context.select.addEventListener('change', (event) => {
                    this.updateSelectedValues();
                });
            }

            if (context.livewire && null !== context.select) {
                this.wireModel = context.select.getAttribute('wire:model');
                if (this.wireModel) {
                    context.values = context.$wire.get(this.wireModel);
                }
            }

            if (this.values === null && this.multiple) {
                this.values = [];
            }
        });

        if (this.multiple) {
            // If it's multiple, we will add an event listener to rebuild the input on resizing
            window.addEventListener('resize', (event) => {
                this.setInputValue();
            });
        }

        this.$watch('values', (value, oldValue) => {
            this.setInputValue();
        });
    },
    // Construct a fresh custom select
    constructSelect() {
        let wrapper = this.$refs['select-wrapper'];
        // Get the select element
        this.select = wrapper.querySelector('select');
        // Select is defined!
        if (null !== this.select) {
            this.multiple = this.select.hasAttribute('multiple');

            this.disabled = this.select.hasAttribute('disabled');

            // Add class if disabled, so CSS can do magic
            if (this.disabled) {
                this.$refs['select-input'].classList.add('disabled');
                this.open = false;
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

            // No need to fetch values because they will have been received from Livewire
            if (! this.livewire) {
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
    // Handle the click of a custom option
    changeOption(element) {
        if (! element.classList.contains('disabled')) {
            this.updateValue(element.getAttribute('data-value'));
            if (! this.multiple) {
                this.close();
            }

            if (this.livewire) {
                if (this.wireModel) {
                    this.$wire.set(this.wireModel, this.values);
                }
            } else {
                window.triggerEvent(this.select, 'change');
            }
        }
    },
    // Update a/the selected value
    updateValue(value) {
        if (this.multiple) {
            let values = this.values ?? [];

            // If it's multiple, we want to remove the value if the clicked value is already selected.
            // Otherwise we append the value to the values.
            if (values.includes(value)) {
                values.splice(values.indexOf(value));
            } else {
                values.push(value);
            }

            this.values = values;

            this.setSelectedOptions();
        } else {
            // If it's not multiple, we simply set the value.
            this.values = value;
            this.select.value = value;
        }
    },
    // Use the values to select the option elements
    setSelectedOptions() {
        let options = this.select.options;

        let values = this.values;
        for (let option of options) {
            option.selected = values.indexOf(option.value) >= 0;
        }
    },
    // Get the values that should be selected based on the option elements
    updateSelectedValues() {
        let values = this.multiple ? [] : null;

        let options = this.select.options;
        for (let option of options) {
            if (option.selected && ! option.hasAttribute('disabled')) {
                if (this.multiple) {
                    values.push(option.value);
                } else {
                    values = option.value;
                    break;
                }
            }
        }

        this.values = values;
    },
    // Display the values in the input field (human readable)
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

            for (let value of this.values) {
                let option = this.findOptionByValue(value);

                let text = option?.textContent ?? value;
                let newInputOption = document.createElement('span');

                if (option && option.hasAttribute("data-icon")) {
                    let icon = document.createElement('i');
                    icon.classList.add('icon-sm', option.getAttribute("data-icon"), 'mr-2', 'static');
                    newInputOption.appendChild(icon);
                }

                newInputOption.appendChild(document.createTextNode(text));
                newInputOption.classList.add('form-input-option');
                newInputOption.setAttribute("data-value", value);
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
            this.$refs['select-input'].value = this.findOptionByValue(this.values)?.textContent ?? this.values;
        }
    },
    // Build a custom option
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
        if (this.multiple) {
            newOption.setAttribute("x-bind:class", `Array.isArray(values) && values.includes('${value}') ? 'selected' : ''`);
        } else {
            newOption.setAttribute("x-bind:class", `values == '${value}' ? 'selected' : ''`);
        }
        newOption.setAttribute("x-on:click", "changeOption($el)");
        newOption.classList.add('select-option');

        if (option.hasAttribute('disabled')) {
            newOption.classList.add('disabled');
        }

        // Append to list
        parent.appendChild(newOption);
    },
    // Find a custom select option by given value
    findOptionByValue(value) {
        return this.$refs['select-options'].querySelector(`span[data-value="${value}"]`);
    }
});