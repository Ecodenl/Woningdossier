export default (defaultTab = null) => ({
    currentTab: defaultTab,

    init() {
        document.addEventListener('DOMContentLoaded', () => {
            // In case no tab is set, we check if a different way of tabbing is set.
            if (this.currentTab === null) {
                if (location.hash) {
                    const fragment = location.hash.split('#').pop();
                    // If the tab exists, the query selector will return a value and so we know the fragment is valid.
                    // If we just set the fragment, we might not know if it actually exists. We call dataset.tab
                    // to keep it a one-liner instead of having to check if the tab is valid and then setting the fragment.
                    this.currentTab = this.$el.querySelector(`[data-tab="${fragment}"]`)?.dataset.tab;
                }

                // If it's still null, we check other options.
                if (this.currentTab === null) {
                    let mainTab = this.$refs['main-tab'];
                    if (mainTab) {
                        // Set main tab by default if available
                        this.currentTab = mainTab.dataset.tab;
                    } else {
                        // No main tab? Set first available
                        this.currentTab = this.$el.querySelector('[x-bind="tab"]')?.dataset.tab;
                    }
                }

                // Tab has been set.
                if (this.currentTab !== null) {
                    // We trigger an update.
                    triggerCustomEvent('tab-switched', {from: null, to: this.currentTab});
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
        },
        'role': 'tab',
    },
    container: {
        ['x-show']() {
            return this.$el.getAttribute('data-tab') === this.currentTab;
        },
        'x-cloak': '',
        'role': 'tabpanel',
    },
    switchTab(element) {
        if (element) {
            const oldTab = this.currentTab;
            this.currentTab = this.$el.getAttribute('data-tab');
            // Use replaceState instead of setting hash directly to not affect history stack.
            history.replaceState(undefined, undefined, `#${this.currentTab}`);
            // location.hash = this.currentTab;
            triggerCustomEvent('tab-switched', {from: oldTab, to: this.currentTab});
        }
    }
});