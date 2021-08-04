export default (initiallyOpen = false) => ({
    // Is the dropdown open?
    open: initiallyOpen,

    toggle() {
        this.$event.preventDefault();
        this.open = ! this.open
    },
    close() {
        this.$event.preventDefault();
        this.open = false;
    },
});