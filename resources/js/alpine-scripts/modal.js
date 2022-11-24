export default () => ({
    opened: false,

    open() {
        this.$event.preventDefault();
        this.opened = true;
        this.dispatchEvent(this.$el);
    },
    toggle() {
        this.$event.preventDefault();
        this.opened = ! this.opened
        this.dispatchEvent(this.$el);
    },
    close() {
        this.$event.preventDefault();
        this.opened = false;
        this.dispatchEvent(this.$el);
    },
    dispatchEvent(element) {
        window.triggerCustomEvent(element, 'modal-toggled', {modalOpened: this.opened});
    }
});