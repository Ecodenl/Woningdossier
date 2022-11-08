export default () => ({
    initialized: false,
    value: 0,

    init() {
        this.initialized = true;
        this.$watch('value', value => {
            this.updateVisuals();
        });

        if (this.$wire && this.$refs['slider'].hasAttribute('wire:model')) {
            this.value = $wire.get(this.$refs['slider'].getAttribute('wire:model'));
        }

        this.updateVisuals();
    },
    updateVisuals() {
        this.value = this.$refs['slider'].value;
        let currentPosition = this.getThumbPosition();
        this.$refs['slider-bubble'].style.left = currentPosition + 'px';
        this.$refs['slider'].style.background = `linear-gradient(90deg, var(--slider-before) ${currentPosition}px, var(--slider-after) ${currentPosition}px)`;
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