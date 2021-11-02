export default () => ({
    opened: false,

    open() {
        this.$event.preventDefault();
        this.opened = true;
    },
    toggle() {
        this.$event.preventDefault();
        this.opened = ! this.opened
    },
    close() {
        this.$event.preventDefault();
        this.opened = false;
    },
});