export default (initiallyOpen = false) => ({
    // Is the dropdown open?
    open: false,
    init() {
        // Handle the initiallyOpen variable in the init, so we can update the position as well
        // Otherwise the dropdown might be open but in an ugly location
        if (initiallyOpen) {
            this.open = true;
            this.updatePosition();
        }
    },
    toggle() {
        this.$event.preventDefault();
        this.open = ! this.open

        if (this.open) {
            this.updatePosition();
        }
    },
    close() {
        this.$event.preventDefault();
        this.open = false;
    },
    updatePosition(tries = 0) {
        let dropdown = this.$refs['dropdown'];

        // We have 5 tries. This is because when open is set, there is a slight delay before the dropdown is visible
        // on the client screen. If it's not visible, then there won't be any sizes, and so we end up with the dropdown
        // being outside of the screen
        if (tries <= 5) {
            if (getComputedStyle(dropdown).display === 'none') {
                setTimeout(() => this.updatePosition(++tries), 25);
            } else {
                let rect = dropdown.getBoundingClientRect();
                if (rect.x + rect.width > window.innerWidth) {
                    dropdown.style.right = 0;
                }
            }
        }
    }
});