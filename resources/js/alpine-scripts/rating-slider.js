export default (defaultValue = 0, activeClass = 'bg-green', disabled = false) => ({
    index: -1,
    value: defaultValue,
    inactiveClass: 'bg-green',
    activeClass: activeClass,
    disabled: disabled,

    init() {
        // Ensure the slider gets updated with the default value
        if (this.value > 0) {
            let element = this.$refs['rating-slider'].querySelector(`div[data-value="${this.value}"]`);
            if (! (null === element)) {
                // Ensure we can set the value on init, so we temporary enable, even if it's disabled.
                let tempDisable = this.disabled;
                this.disabled = false;
                this.selectOption(element);
                this.disabled = tempDisable;
            } else {
                this.value = 0;
            }
        }
    },
    mouseEnter(element) {
        if (! this.disabled) {
            this.setAllGray();
            // Set this and all previous as green
            this.setActive(element);
            while((element = element.previousElementSibling) != null) {
                this.setActive(element);
            }
        }
    },
    mouseLeave(element) {
        if (! this.disabled) {
            this.setIndexActive();
        }
    },
    selectOption(element) {
        if (! this.disabled) {
            let parent = this.$refs['rating-slider'];
            this.index = Array.from(parent.children).indexOf(element);
            this.value = element.getAttribute('data-value');
            this.setIndexActive();
        }
    },
    setIndexActive() {
        this.setAllGray();
        let parent = this.$refs['rating-slider'];
        let children = Array.from(parent.children);
        children.forEach((element) => {
            if (children.indexOf(element) <= this.index) {
                this.setActive(element);
            } else {
                this.setInactive(element)
            }
        });
    },
    setAllGray() {
        let parent = this.$refs['rating-slider'];
        let children = Array.from(parent.children);
        // Set all elements as gray
        children.forEach((element) => this.setInactive(element));
    },
    setInactive(element) {
        element.classList.remove(this.activeClass);
        element.classList.add(this.inactiveClass);
    },
    setActive(element) {
        element.classList.remove(this.inactiveClass);
        element.classList.add(this.activeClass);
    },
});