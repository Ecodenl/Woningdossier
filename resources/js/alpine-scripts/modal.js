export default () => ({
    open: false,

    toggle() {
        this.$event.preventDefault();
        this.open = ! this.open
    },
    close() {
        this.$event.preventDefault();
        this.open = false;
    },
});