export default (defaultValue = 0) => ({
    initialized: false,
    value: defaultValue,
    visual: 0,
    livewire: false,

    init() {
        try {
            this.livewire = !! this.$wire;
        } catch (e) {
            this.livewire = false;
        }

        this.$watch('value', value => {
            // It could be that the slider value is not properly updated
            // This is usually the case if we use Livewire.
            let slider = this.$refs['slider'];
            if (slider.value != this.value) {
                slider.value = this.value;
            }

            this.updateVisuals();
        });

        // Use timeout to allow DOM to fully load
        setTimeout(() => {
            // Set slider value to match with default
            this.$refs['slider'].value = this.value;
            this.updateVisuals();
            this.initialized = true;
        });
    },
    slider: {
        ['x-ref']: 'slider',
        ['x-on:input']() {
            this.updateVisuals();
        },
        ['x-on:change.debounce.500ms']() {
            // We use a change event to not cause the slider to jump when it syncs with Livewire; input triggers
            // each movement. We use a debounce, as arrow keys also trigger a change, but a user might not tap
            // fast enough.
            this.value = this.visual;
        },
    },
    updateVisuals() {
        let slider = this.$refs['slider'];
        this.visual = slider.value;
        let currentPosition = this.getThumbPosition();
        this.$refs['slider-bubble'].style.left = currentPosition + 'px';
        slider.style.background = `linear-gradient(90deg, var(--slider-before) ${currentPosition}px, var(--slider-after) ${currentPosition}px)`;
    },
    getThumbPosition() {
        let slider = this.$refs['slider'];

        let sliderWidth = slider.offsetWidth;
        let thumbWidth = this.parsePixelWidth(window.getComputedStyle(slider, '::-webkit-slider-thumb').width);

        // If not a number, or exact width of slider, we will try other pseudo-element tags, and if we can't find it,
        // we'll use the style of the thumb (this isn't dynamic, but better than the slider not functioning correctly)
        if (isNaN(thumbWidth) || thumbWidth === sliderWidth) {
            thumbWidth = this.parsePixelWidth(window.getComputedStyle(slider, '::-moz-range-thumb').width);

            if (isNaN(thumbWidth) || thumbWidth === sliderWidth) {
                // Use fallback width
                thumbWidth = '42px'; // Defined in form.css
            }
        }

        // Total amount of steps in the slider
        let totalSteps = (slider.max - slider.min) / slider.step;
        // Offset per step of the thumb (this is applied to the thumb, otherwise it would go past the end of the slider
        // when the end is reached. This is important because if we don't apply this to the bubble, it will not position
        // correctly)
        let offsetPerStep = this.parsePixelWidth(thumbWidth) / totalSteps;
        // The step we're currently at
        let currentStep = (slider.value - slider.min) / slider.step ;

        // We calculate the left position per following logic: We calculate the width per step, then, we multiply
        // that value by our current step to get the position of the thumb currently, and then we remove the offset
        // of the thumb that is applied so we have the exact position of the thumb
        return sliderWidth / totalSteps * currentStep - offsetPerStep * currentStep
    },
    parsePixelWidth(value) {
        return parseInt(value, 10);
    }
});