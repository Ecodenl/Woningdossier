export default (defaultValue = 0, activeClass = 'bg-green', disabled = false) => ({
    index: -1,
    value: defaultValue,
    inactiveClass: 'bg-gray',
    activeClass: activeClass,
    disabled: disabled,
    livewire: false,

    init() {
        this.livewire = !! this.$wire;

        // Ensure the slider gets updated with the default value
        if (this.value > 0) {
            this.selectOptionByValue(this.value, false);
        } else if (isNaN(this.value)) {
            this.value = 0;
        }

        this.$watch('value', value => {
            this.selectOptionByValue(value);
        });
    },
    input: {
        ['x-ref']: 'rating-slider-input',
        ['x-model']: 'value',
    },
    block: {
        ['x-on:mouseenter']() {
            if (! this.disabled) {
                let element = this.$el;
                this.setAllGray();
                // Set this and all previous as green
                this.setActive(element);
                while ((element = element.previousElementSibling) != null) {
                    this.setActive(element);
                }
            }
        },
        ['x-on:mouseleave']() {
            if (! this.disabled) {
                this.setIndexActive();
            }
        },
        ['x-on:click']() {
            if (! this.disabled) {
                this.selectOption(this.$el);
                if (! this.livewire) {
                    // If we don't use Livewire, the value won't be entangled and as such we should trigger events
                    window.triggerEvent(this.$refs['rating-slider-input'], 'input');
                    window.triggerEvent(this.$refs['rating-slider-input'], 'change');
                }
            }
        }
    },
    selectOption(element, update = true) {
        let parent = this.$refs['rating-slider'];
        this.index = Array.from(parent.children).indexOf(element);
        if (update) {
            this.value = element.getAttribute('data-value');
        }
        this.setIndexActive();
    },
    selectOptionByValue(value, update = true) {
        let element = this.$refs['rating-slider'].querySelector(`div[data-value="${value}"]`);
        if (element) {
            this.selectOption(element, update);
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