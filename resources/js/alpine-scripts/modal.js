export default () => ({
    opened: false,

    init() {
        // Set custom DOM prop so we can access methods and values here from the DOM.
        this.$el.modal = this;
    },
    open() {
        this.$event?.preventDefault();
        this.opened = true;
        this.dispatchEvent(this.$el);
    },
    toggle() {
        this.$event?.preventDefault();
        this.opened = ! this.opened
        this.dispatchEvent(this.$el);
    },
    close() {
        this.$event?.preventDefault();
        this.opened = false;
        this.dispatchEvent(this.$el);
    },
    applyEscape() {
        const active = document.activeElement;

        // If the focus happens to be on a select input (from the alpine-select) we don't want to close the modal.
        // The user might have intended to just close the dropdown.
        if (active.hasAttribute('x-ref') && active.getAttribute('x-ref') === 'select-input') {
            return;
        }

        // If there's one or less active modals, we can just close it.
        const activeModals = document.querySelectorAll('.modal-container:not([style="display: none;"])').length;
        if (activeModals <= 1) {
            this.close();
        } else {
            // Else, we check if it happens to be related to tiptap's link modal. If so, we don't want to
            // close this modal if it's not the tiptap modal.
            const id = this.$el.id;
            if (active.closest('#tiptap-link-modal') || active.dataset.tiptap === 'toggleLink') {
                if (id === 'tiptap-link-modal') {
                    this.close();
                }
            } else {
                // Not the tiptap modal? Just close.
                this.close();
            }
        }
    },
    dispatchEvent(element) {
        element.triggerCustomEvent('modal-toggled', {modalOpened: this.opened});
    }
});