export default (initiallyOpen = false) => ({
    // Text display
    text: null,
    // Current value of the select
    value: null,
    // If the select is disabled
    disabled: false,
    // Is the dropdown open?
    open: false,

    init() {
        // This is almost the same as the default alpine select, but this dropdown will have pre-defined options.
        // These will be the sources for each question.
        this.open = initiallyOpen;

        let select = this.$refs['source-select'];

        // Get attributes
        this.value = select.value;
        this.text = select.options[select.selectedIndex].textContent;
        this.disabled = select.hasAttribute('disabled');

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
            this.setValue(element.getAttribute('data-value'), element.textContent);
        }
    },
    setValue(value, text = null) {
        this.value = value;
        this.$refs['source-select'].value = value;

        this.text = null === text ? value : text;
        this.text = this.text.trim();
        this.open = false;
    },
});