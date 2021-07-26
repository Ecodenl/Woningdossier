export default (supportedClasses = ['card-wrapper'], hoverColor = 'rgba(100, 117, 133, 0.2)') => ({
    // Docs: use supportedClasses to define classes in which elements can be dropped
    // Define hoverColor for the background color that gets used when hovering over supported classes
    // Define extra logic on elements themselves, e.g. switching classes on the dragged object
    dragged: null,

    container: {
        ['x-on:drop.prevent']() {
            if (! (null === this.dragged)) {
                let eventTarget = this.$event.target;
                let target = this.getSupportedTarget(eventTarget);

                if (! (null === target)) {
                    this.dragged.parentElement.removeChild(this.dragged);
                    target.appendChild(this.dragged);
                    target.style.backgroundColor = '';
                }

                this.dragged = null;
            }
        },
        ['x-on:dragover.prevent']() {
            // This needs to be prevented, else drop doesn't work
        },
        ['x-on:dragenter']() {
            let eventTarget = this.$event.target;
            let target = this.getSupportedTarget(eventTarget);

            if (! (null === target)) {
                target.style.backgroundColor = hoverColor;
            }
        },
        ['x-on:dragleave']() {
            let eventTarget = this.$event.target;
            let target = this.getSupportedTarget(eventTarget);

            if (! (null === target)) {
                target.style.backgroundColor = '';
            }
        },
    },
    draggable: {
        ['x-bind:draggable']: true,
        ['x-on:dragstart.self']() {
            this.dragged = this.$el;
        }
    },

    getSupportedTarget(element) {
        let target = null;

        if (Array.isArray(supportedClasses) && typeof element.classList !== 'undefined') {
            supportedClasses.forEach((className) => {
                if (element.classList.contains(className)) {
                    target = element;
                }
            });

            if (null === target) {
                supportedClasses.forEach((className) => {
                    let parent = element.closest('.' + className);
                    if (! (null === parent)) {
                        target = parent;
                    }
                });
            }
        }

        return target;
    },
});