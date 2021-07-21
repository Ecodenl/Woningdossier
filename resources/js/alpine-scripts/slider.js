export default () => ({
    initialized: false,
    value: 0,

    init() {
        this.value = this.$refs['slider'].value;
        this.updateVisuals();
        this.initialized = true;
    },
    updateVisuals() {
        let currentPosition = this.getThumbPosition();
        this.$refs['slider-bubble'].style.left = currentPosition + 'px';
        this.$refs['slider'].style.background = `linear-gradient(90deg, var(--slider-before) ${currentPosition}px, var(--slider-after) ${currentPosition}px)`;
    },
    getThumbPosition() {
        let slider = this.$refs['slider'];
        // Get slider thumb CSS
        let thumbStyle = window.getComputedStyle(slider, '::-webkit-slider-thumb');
        if (thumbStyle.length === 0 || this.parsePixelWidth(thumbStyle.width) === slider.offsetWidth) {
            thumbStyle = window.getComputedStyle(slider, '::-moz-range-thumb');
        }

        let thumbWidth = this.parsePixelWidth(thumbStyle.width);
        if (thumbWidth === slider.offsetWidth) {
            // Use fallback width (for now)
            // TODO: Figure out how to get computed style of webkit thumb
            thumbWidth = '42px'; // Defined in form.css
        }

        // Slider width
        let width = slider.offsetWidth;
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
        return width / totalSteps * currentStep - offsetPerStep * currentStep
    },
    parsePixelWidth(value) {
        return parseInt(value, 10);
    }
});