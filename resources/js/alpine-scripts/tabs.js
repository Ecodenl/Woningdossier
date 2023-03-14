export default (defaultTab = null) => ({
    currentTab: defaultTab,
    lastTab: null,

    init() {
        document.addEventListener('DOMContentLoaded', () => {
            // In case no tab is set we grab the main.
            if (this.currentTab === null) {
                 let mainTab = this.$refs['main-tab'];
                // Set main tab by default
                if (mainTab) {
                    this.currentTab = mainTab.getAttribute('data-tab');
                }
            }
        });
    },
    tab: {
        ['x-on:click']() {
            this.$event.preventDefault();
            this.switchTab(this.$el.getAttribute('data-tab'));
        },
        ['x-bind:class']() {
            return this.$el.getAttribute('data-tab') === this.currentTab ? 'active' : '';
        }
    },
    container: {
        ['x-show']() {
            return this.$el.getAttribute('data-tab') === this.currentTab;
        }
    },
    switchTab(element) {
        if (element) {
            this.currentTab = this.$el.getAttribute('data-tab');
            triggerCustomEvent(window, 'tab-switched');
        }
    },
    back() {
        // Go back to previous tab
        if (null !== this.lastTab) {
            this.currentTab = this.lastTab;
            this.lastTab = null;
        }
    }
});