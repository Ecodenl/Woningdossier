export default (initiallyOpen = false) => ({
    // Select element
    select: null,
    // HTML options
    options: null,
    // Text display
    text: null,
    // Current value of the select
    value: null,
    // If the select is disabled
    disabled: false,
    // Is the dropdown open?
    open: initiallyOpen,

    init() {
        let wrapper = this.$refs['select-wrapper'];
        // Get the select element
        this.select = wrapper.querySelector('select');
        // Select is defined!
        if (! (null === this.select)) {
            // Bind event listener for change
            let context = this;
            this.select.addEventListener('change', function (event) {
                context.setValue(event.target.value);
            });

            // Get options
            this.options = this.select.getElementsByTagName('option');
            // There are options!
            if (this.options.length > 0) {
                // Get attributes
                this.value = this.select.value;
                this.text = this.select.options[this.select.selectedIndex].textContent.trim();
                this.disabled = this.select.hasAttribute('disabled');

                // Add class if disabled, so css can do magic
                if (this.disabled) {
                    this.$refs['select-input'].classList.add('disabled');
                    this.open = false;
                }

                // Build the alpine select
                let optionDropdown = this.$refs['select-options'];
                // Loop options to build
                // Note: we cannot use forEach, as options is a HTML collection, which is not an array
                for (let i = 0; i < this.options.length; i++) {
                    this.buildOption(optionDropdown, this.options[i]);
                }

                // Hide the original select
                this.select.style.display = 'none';
                // Show the new alpine select
                this.$refs['select-input-group'].style.display = '';
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
            this.setValue(element.getAttribute('data-value'), element.textContent);
            this.close();
            window.triggerEvent(this.select, 'change');
        }
    },
    setValue(value, text = null) {
        this.value = value;
        this.select.value = value;
        let option = this.$refs['select-options'].querySelector(`span[data-value="${value}"]`);

        this.text = null === text ? (option ? option.textContent : value) : text;
        this.text = this.text.trim();
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