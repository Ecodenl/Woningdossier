export default (initiallyOpen = false) => ({
    // Is the dropdown open?
    open: initiallyOpen,
    toggle() {
        this.$event.preventDefault();
        this.open = ! this.open

        if (this.open == true) {
            this.updatePosition();
        }
    },
    close() {
        this.$event.preventDefault();
        this.open = false;
    },
    updatePosition(tries = 0) {
        let dropdown = this.$refs['dropdown'];

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