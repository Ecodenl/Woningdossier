export default () => ({
    opened: false,

    init() {
        // Set custom DOM prop so we can access methods and values here from the DOM.
        this.$el.modal = this;
    },
    open() {
        this.$event?.preventDefault();
        this.opened = true;
        this.dispatchEvent(this.$el);
    },
    toggle() {
        this.$event?.preventDefault();
        this.opened = ! this.opened
        this.dispatchEvent(this.$el);
    },
    close() {
        this.$event?.preventDefault();
        this.opened = false;
        this.dispatchEvent(this.$el);
    },
    dispatchEvent(element) {
        element.triggerCustomEvent('modal-toggled', {modalOpened: this.opened});
    }
});