export default (initiallyOpen = false, withSearch = false) => ({
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
    // Active searching for options
    search: null,
    withSearch: withSearch,

    init() {
        try {
            this.livewire = !! this.$wire;
        } catch (e) {
            this.livewire = false;
        }

        //TODO: README! For now, we will ALWAYS set Livewire as false, as it's causing unexpected behaviour
        // simply caused by the page being too slow
        this.livewire = false;

        let context = this;
        setTimeout(() => {
            context.constructSelect(true);

            if (null !== context.select) {
                let observer = new MutationObserver(function (mutations) {
                    context.constructSelect(false);
                });

                observer.observe(context.select, { childList: true });

                let attributeObserver = new MutationObserver(function (mutations) {
                    context.setDisabledState();
                });

                attributeObserver.observe(context.select, { attributeFilter: ['disabled'] });

                // Bind event listener for change
                // TODO: Check if values update correctly when data is changed on Livewire side
                context.select.addEventListener('change', (event) => {
                    context.updateSelectedValues();
                });
            }

            if (context.livewire && null !== context.select) {
                //TODO: This works for now, but the wire:model can have extra options such as .lazy, which will
                // not be caught this way. Might require different resolving in the future
                context.wireModel = context.select.getAttribute('wire:model');
            }

            if (context.values === null && context.multiple) {
                context.values = [];
            }

            if (context.multiple) {
                // If it's multiple, we will add an event listener to rebuild the input on resizing,
                // as well as on switching tabs.
                window.addEventListener('resize', (event) => {
                    context.setInputValue();
                    context.applySearchPadding();
                });

                window.addEventListener('tab-switched', (event) => {
                    setTimeout(() => {
                        context.setInputValue();
                        context.applySearchPadding();
                    });
                });
            }
        });

        this.$watch('values', (value, oldValue) => {
            this.search = null;
            this.setInputValue();
            this.applySearchPadding();

            if (this.multiple && this.values.length > 0) {
                this.$refs['select-input'].classList.add('no-placeholder');
            } else {
                this.$refs['select-input'].classList.remove('no-placeholder');
            }
        });
        this.$watch('search', (value, oldValue) => {
            this.searchOptions();
        });
        this.$watch('open', (value) => {
            if (value && this.disabled) {
                this.open = false;
            }
        });
    },
    // Construct a fresh custom select
    constructSelect(isFirstBoot) {
        let before = this.values;

        let wrapper = this.$refs['select-wrapper'];

        if (wrapper) {
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
                } else {
                    this.$refs['select-input'].classList.remove('disabled');
                }

                // Build the alpine select
                const optionDropdown = this.$refs['select-options'];
                // Clear any options there might be left
                optionDropdown.children.remove();
                const options = this.select.options;
                // Loop options to build
                // Note: we cannot use forEach, as options is a HTML collection, which is not an array
                for (let i = 0; i < options.length; i++) {
                    this.buildOption(optionDropdown, options[i]);
                }

                // Hide the original select
                this.select.style.display = 'none';
                // Show the new alpine select
                this.$refs['select-input-group'].style.display = '';

                let firstDisabledOption = this.select.querySelector('option[disabled][selected]');
                if (firstDisabledOption) {
                    this.$refs['select-input'].setAttribute('placeholder', firstDisabledOption.textContent.trim());
                }

                // Set custom DOM prop so we can access methods and values here from the DOM.
                this.select.alpineSelect = this;

                setTimeout(() => {
                    this.updateSelectedValues();
                    let after = this.values;

                    // Ensure any potentially hidden values are no longer selected if the data changes after initial boot.
                    // We compare before and after because we don't want to unnecessarily cast multiple changes.
                    if (! isFirstBoot && JSON.stringify(before) !== JSON.stringify(after)) {
                        if (this.livewire) {
                            if (this.wireModel) {
                                this.$wire.set(this.wireModel, this.values);
                            }
                        } else {
                            this.select.triggerEvent('change');
                        }
                    }
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
        if (! element.classList.contains('disabled') && ! element.classList.contains('readonly')) {
            this.updateValue(element.getAttribute('data-value'));
            if (! this.multiple) {
                this.close();
            }

            if (this.livewire) {
                if (this.wireModel) {
                    this.$wire.set(this.wireModel, this.values);
                }
            } else {
                this.select.triggerEvent('change');
            }
        }
    },
    // Update a/the selected value
    updateValue(value) {
        if (this.multiple) {
            // If it's multiple, we want to remove the value if the clicked value is already selected.
            // Otherwise we append the value to the values.
            if (this.values.includes(value)) {
                this.values.splice(this.values.indexOf(value), 1);
            } else {
                this.values.push(value);
            }

            this.setSelectedOptions();
        } else {
            const isValue = this.values === value;
            // If it's not multiple, we simply set the value.
            this.values = value;
            this.select.value = value;

            if (isValue) {
                // Technically the watcher does this too, but the watcher doesn't trigger if the value doesn't change
                this.search = null;
                this.setInputValue();
            }
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
        if (this.$refs['select-input']) {
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

                    if (this.disabled) {
                        newInputOption.classList.add('disabled');
                    } else {
                        if (option.classList.contains('disabled')) {
                            newInputOption.classList.add('disabled');
                        }
                        if (option.classList.contains('readonly')) {
                            newInputOption.classList.add('readonly');
                        }
                    }

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
                // Timeout, otherwise setting search to `null` conflicts and the input becomes empty.
                setTimeout(() => {
                    this.$refs['select-input'].value = this.findOptionByValue(this.values)?.textContent ?? this.values;
                });
            }
        }
    },
    // Build a custom option
    buildOption(parent, option) {
        let optgroup = null;
        if (option.parentElement.tagName === 'OPTGROUP') {
            optgroup = option.parentElement.label;

            if (! parent.querySelector(`.select-optgroup[data-label="${optgroup}"]`)) {
                let newOptgroup = document.createElement('span');
                newOptgroup.appendChild(document.createTextNode(optgroup));
                newOptgroup.setAttribute('data-label', optgroup);
                newOptgroup.classList.add('select-optgroup');
                parent.appendChild(newOptgroup);
            }
        }

        // Trim to ensure it's not filled with unnecessary white space (will look ugly in the input)
        let value = option.value;
        let text = option.textContent.trim();

        // Build a new alpine option
        let newOption = document.createElement('span');
        newOption.appendChild(document.createTextNode(text));
        newOption.setAttribute("data-value", value);

        if (optgroup) {
            newOption.setAttribute('data-optgroup', optgroup);
        }

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
        option.classList.forEach((className) => newOption.classList.add(className))

        if (option.hasAttribute('disabled')) {
            newOption.classList.add('disabled');
        } else if (this.disabled || option.hasAttribute('readonly')) {
            newOption.classList.add('disabled', 'readonly');
        }

        // Append to list
        parent.appendChild(newOption);
    },
    setDisabledState() {
        let disabled = this.select.hasAttribute('disabled')
        this.disabled = disabled;

        if (disabled) {
            this.open = false;
        }

        if (disabled) {
            this.$refs['select-input'].classList.add('disabled');
        } else {
            this.$refs['select-input'].classList.remove('disabled');
        }

        let groupOptions = this.$refs['select-input-group'].querySelectorAll('.form-input-option');
        for (let i = 0; i < groupOptions.length; i++) {
            let groupOption = groupOptions[i];
            if (disabled) {
                groupOption.classList.add(groupOption.classList.contains('disabled') ? 'was-disabled' : 'disabled');
            } else {
                groupOption.classList.remove(groupOption.classList.contains('was-disabled') ? 'was-disabled' : 'disabled');
            }
        }

        // let options = this.$refs['select-options'].children;
        // for (let i = 0; i < options.length; i++) {
        //     let option = options[i];
        //
        //     if (option.hasAttribute('disabled')) {
        //         newOption.classList.add('disabled');
        //     } else if (disabled || option.hasAttribute('readonly')) {
        //         newOption.classList.add('disabled', 'readonly');
        //     }
        //
        //     if (disabled) {
        //         if (! option.classList.contains('disabled')) {
        //             option.classList.add('disabled', 'readonly');
        //         }
        //     } else {
        //         if (option.classList.contains('readonly')) {
        //             option.classList.remove('disabled', 'readonly');
        //         }
        //     }
        // }
    },
    // Find a custom select option by given value
    findOptionByValue(value) {
        return this.$refs['select-options'].querySelector(`span[data-value="${value}"]`);
    },
    searchOptions() {
        const optionDropdown = this.$refs['select-options'];
        let visibleOptgroups = [];

        optionDropdown.querySelectorAll('.select-option').forEach((option) => {
            let optgroup = null;
            if (option.hasAttribute('data-optgroup')) {
                optgroup = option.dataset.optgroup;
            }

            if (! this.search?.trim()) {
                option.style.display = null;
                if (optgroup && ! visibleOptgroups.includes(optgroup)) {
                    visibleOptgroups.push(optgroup);
                }
            } else {
                const visible = searchValue(option.textContent, this.search) || searchValue(option.dataset.value, this.search);

                if (optgroup && ! visibleOptgroups.includes(optgroup) && visible) {
                    visibleOptgroups.push(optgroup);
                }

                option.style.display = visible ? null : 'none';
            }
        });

        optionDropdown.querySelectorAll('.select-optgroup').forEach((optgroup) => {
            console.log(optgroup.label)
            optgroup.style.display = visibleOptgroups.includes(optgroup.dataset.label) ? null : 'none';
        });
    },
    applySearchPadding() {
        // Padding not needed for non-multiple
        if (this.withSearch && this.multiple) {
            // Timeout, again, getComputedStyle...
            setTimeout(() => {
                const inputs = this.$refs['select-input-group'].querySelectorAll('.form-input-option');

                let paddingTop = null;
                let paddingLeft = null;
                if (inputs.length > 0) {
                    const lastOption = inputs[inputs.length - 1];

                    const optionStyle = getComputedStyle(lastOption);
                    const inputStyle = getComputedStyle(this.$refs['select-input']);

                    paddingTop = String((parseInt(inputStyle.height) - 44)) + 'px';
                    // + 4px for spacing between the last option and the text
                    paddingLeft = String(parseInt(optionStyle.width) + parseInt(optionStyle.left) + 4) + 'px';
                }

                this.$refs['select-input'].style.paddingTop = paddingTop;
                this.$refs['select-input'].style.paddingLeft = paddingLeft;
            });
        }
    }
});