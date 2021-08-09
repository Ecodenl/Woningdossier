export default (defaultTab = null) => ({
    currentTab: null,

    init() {
        document.addEventListener('DOMContentLoaded', () => {
            // get the current url
            let url = document.location.href;

            // check if the current url matches a hashtag
            if (url.match('#')) {
                try {
                    let hash = new URL(url).hash;
                    this.switchTab(document.querySelector(`a[href="${hash}"]`));
                }
                catch (e) {
                    // Not valid URL
                }
            } else if (defaultTab && defaultTab.nodeType === Node.ELEMENT_NODE) {
                this.switchTab(defaultTab);
            }
        });
    },
    tab: {
        ['x-on:click']() {
            this.switchTab(this.$el);
        }
    },
    switchTab(element) {
        let href = element.getAttribute('href');
        if (href[0] === '#') {
            let tab = document.querySelector(href);
            if (tab && tab !== this.currentTab) {
                // If tab is defined and different from current tab
                if (! this.currentTab) {
                    this.$refs['main-tab'].classList.add('hidden');
                }
                this.currentTab = tab;
                window.location.hash = element.hash;

                this.$refs['nav-tabs'].querySelector('li.active').classList.remove('active');
                element.parentElement.classList.add('active');
            }
        }
    },
});