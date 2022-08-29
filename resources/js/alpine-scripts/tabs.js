export default (defaultTab = null) => ({
    currentTab: null,
    lastTab: null,

    init() {
        document.addEventListener('DOMContentLoaded', () => {
            // Ensure defaultTab starts with '#'
            let hash = document.location.hash || defaultTab;

            // check if the current url matches a hashtag
            if (hash) {
                this.switchTab(document.querySelector(`a[href="${hash}"]`));
            }

            // In case no tab is set we grab the main.
            if (this.currentTab === null) {
                 let mainTab = this.$refs['main-tab'];
                // Set main tab by default
                if (mainTab) {
                    this.currentTab = mainTab;
                }
            }
        });
    },
    tab: {
        ['x-on:click']() {
            this.switchTab(this.$el);
        }
    },
    switchTab(element) {
        if (element) {
            let href = element.getAttribute('href');
            if (href[0] === '#') {
                let tab = document.querySelector(href);
                if (tab && tab !== this.currentTab) {
                    // Set last tab
                    this.lastTab = this.currentTab;
                    // Set current tab
                    this.currentTab = tab;
                    // Set hash
                    window.location.hash = element.hash;

                    // Update buttons if needed
                    let navTabs = this.$refs['nav-tabs'];
                    if (navTabs) {
                        navTabs.querySelector('li.active').classList.remove('active');
                        element.parentElement.classList.add('active');
                    }
                }
            }
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