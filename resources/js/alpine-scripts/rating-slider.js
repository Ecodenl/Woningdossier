export default () => ({
    index: -1,
    value: 0,
    inactiveClass: 'bg-gray',
    activeClass: 'bg-green',

    init(activeClass = null) {
        if (! (null === activeClass)) {
            this.activeClass = activeClass;
        }
    },
    mouseEnter(element) {
        this.setChildrenGray();
        // Set this and all previous as green
        this.setActive(element);
        while((element = element.previousElementSibling) != null) {
            this.setActive(element);
        }
    },
    mouseLeave(element) {
        this.setChildrenGray();
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
    selectOption(element) {
        let parent = this.$refs['rating-slider'];
        this.index = Array.from(parent.children).indexOf(element);
        this.value = element.getAttribute('data-value');
    },
    setChildrenGray() {
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