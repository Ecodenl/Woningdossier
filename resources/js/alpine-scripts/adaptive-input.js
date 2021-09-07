export default () => ({

    init() {
        document.addEventListener('DOMContentLoaded', () => {
            // Set height on init
            this.setHeight(this.$refs['typable']);
        });
    },
    typable: {
        ['x-ref']: 'typable',
        ['x-on:input']() {
            this.setHeight(this.$el);
        },
    },
    setHeight(element) {
        // Compute the height difference which is caused by border and outline
        let outerHeight = parseInt(window.getComputedStyle(element).height, 10);
        let diff = outerHeight - element.clientHeight;

        // Reset height to handle shrinking
        element.style.height = 0;

        // Set new height
        element.style.height = element.scrollHeight + diff + 'px';
    }
});