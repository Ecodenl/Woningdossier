export default () => ({
    // Select element
    select: null,
    // HTML options
    options: null,
    // Current value of the select
    value: null,
    // If the select is disabled
    disabled: false,
    // Is the dropdown open?
    open: false,

    init() {
        let wrapper = this.$refs['select-wrapper'];
        // Get the select element
        this.select = wrapper.querySelector('select');
        // Select is defined!
        if (! (null === this.select)) {
            // Get options
            this.options = this.select.getElementsByTagName('option');
            // There are options!
            if (this.options.length > 0) {
                // Get attributes
                this.value = this.select.value;
                this.disabled = this.select.hasAttribute('disabled');

                // Add class if disabled, so css can do magic
                if (this.disabled) {
                    this.$refs['select-input'].classList.add('disabled');
                }

                // Build the alpine select
                let optionDropdown = this.$refs['select-options'];
                // Loop options to build
                // Note: we cannot use forEach, as options is a HTML collection, which is not an array
                for(let i = 0; i < this.options.length; i++) {
                    this.buildOption(optionDropdown, this.options[i]);
                }

                // Hide the original select
                this.select.style.display = 'none';
                // Show the new alpine select
                this.$refs['select-input'].style.display = '';
            }
        }
    },
    toggle() {
        // If not disabled, we will handle the click
        if (! this.disabled) {
            this.open = ! this.open
        }
    },
    changeOption(value) {
        this.value = value;
        this.select.value = value;
    },
    buildOption(parent, option) {
        // Build a new alpine option
        let newOption = document.createElement('span');
        newOption.appendChild(document.createTextNode(option.text));
        // Add alpine functions
        newOption.setAttribute('x-bind:class', 'value == ' + option.value);
        newOption.setAttribute('x-on:click', 'changeOption(' + option.value + ')');
        newOption.classList.add('select-option');

        if (option.hasAttribute('disabled')) {
            newOption.classList.add('disabled');
        }

        // Append to list
        parent.appendChild(newOption);
    },
});