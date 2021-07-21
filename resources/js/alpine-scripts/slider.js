export default () => ({
    initialized: false,
    value: 0,

    init() {
        this.value = this.$refs['slider'].value;
        this.updateBubble();
        this.initialized = true;
    },
    updateBubble() {
        let currentPosition = this.getThumbPosition();
        this.$refs['slider-bubble'].style.left = currentPosition + 'px';
    },
    getThumbPosition() {
        let slider = this.$refs['slider'];
        // Get slider thumb CSS
        let thumbStyle = window.getComputedStyle(slider, '::-moz-range-thumb');
        if (thumbStyle.length === 0) {
            thumbStyle = window.getComputedStyle(slider, '::-webkit-slider-thumb');
        }

        // Slider width
        let width = slider.offsetWidth;
        // Total amount of steps in the slider
        let totalSteps = (slider.max - slider.min / slider.step);
        // Offset per step of the thumb (this is applied to the thumb, otherwise it would go past the end of the slider
        // when the end is reached. This is important because if we don't apply this to the bubble, it will not position
        // correctly)
        let offsetPerStep = parseInt(thumbStyle.width, 10) / totalSteps;
        // The step we're currently at
        let currentStep = slider.value / slider.step;

        // We calculate the left position per following logic: We calculate the width per step, then, we multiply
        // that value by our current step to get the position of the thumb currently, and then we remove the offset
        // of the thumb that is applied so we have the exact position of the thumb
        return width / totalSteps * currentStep - offsetPerStep * currentStep
    },
});