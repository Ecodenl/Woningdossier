export default (initiallyOpen = false) => ({
    // Text display
    text: null,
    // Current value of the select
    value: null,
    // If the select is disabled
    disabled: false,
    // Is the dropdown open?
    open: initiallyOpen,

    init() {
        // This is almost the same as the default alpine select, but this dropdown will behave differently. Inputs
        // must still be given, but these will be the sources for each question.
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

        // Prepare list items for Alpine!
        // Get children injected by PHP (these will be list items with a nested anchor)
        let children = this.$refs['source-select-options'].children;

        // Note: we cannot use forEach, as options is a HTML collection, which is not an array
        for (let i = 0; i < children.length; i++) {
            children[i].setAttribute("x-on:click", "changeOption($el)");
            let short = children[i].getAttribute('data-input-source-short');
            children[i].classList.add('source-select-option');
            children[i].classList.add(`source-${short}`);
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
            this.setValue(element.getAttribute('data-input-source-short'));
        }
    },
    setValue(value, text = null) {
        this.value = value;
        this.$refs['source-select'].value = value;

        this.text = null === text ? this.$refs['source-select'].querySelector(`option[value="${value}"]`).textContent : text;
        this.text = this.text.trim();
        this.open = false;
    },
});