export default (
    initiallyOpen = false,
    arrow = true,
    position = 'bottom',
    offset = 8,
    trigger = ['hover'],
) => ({
    // Is the dropdown open?
    open: false,
    position: position,
    originalPosition: null,
    offset: offset,
    arrow: arrow,
    trigger: trigger,
    currentTrigger: null,

    init() {
        // Handle the initiallyOpen variable in the init, so we can update the position as well
        let that = this;
        this.originalPosition = this.position

        this.$nextTick(() => {
            if (initiallyOpen) {
                this.open = true;
            }
        });

        window.addEventListener('resize', function () {
            that.updatePosition();
        });

        this.$watch('open', function (value) {
            if (value) {
                that.updatePosition();
            }
        });
    },
    toggle(open = null) {
        this.open = open === null ? (!this.open) : open;
    },
    close() {
        this.open = false;
    },
    show() {
        this.open = true;
    },
    updatePosition(tries = 0) {
        let popover = this.$refs.body;
        let button = this.$refs.popover;
        let posY = 0,
            posX = 0;

        // We have 5 tries. This is because when open is set, there is a slight delay before the element is visible
        // on the client screen. If it's not visible, then there won't be any sizes, and so we end up with the element
        // being outside of the screen
        if (tries <= 5) {
            if (getComputedStyle(popover).display === 'none') {
                setTimeout(() => this.updatePosition(++tries), 25);
            } else {
                this.handleScreenPosition();

                switch (this.position) {
                    case "top":
                        posY = -1 * (popover.offsetHeight + (this.offset));
                        posX = -1 * (popover.offsetWidth / 2 - button.offsetWidth / 2);
                        break;
                    case "bottom":
                        posY = button.offsetHeight + (this.offset * 2)
                        posX = -1 * (popover.offsetWidth / 2 - button.offsetWidth / 2);
                        break;
                    case "right":
                        posX = (this.offset) + button.offsetWidth;
                        posY = -1 * (popover.offsetHeight / 2 - button.offsetHeight / 2) + this.offset / 2;
                        break;
                    case "left":
                        posX = -1 * (popover.offsetWidth + (this.offset + (this.offset / 2))); // for some reason, we have to add half of the offset to the offset here
                        posY = -1 * (popover.offsetHeight / 2 - button.offsetHeight / 2) + this.offset / 2
                        break;
                }

                popover.style.transform = `translate3d(${String(posX)}px, ${String(posY)}px, 0)`;
            }
        }
    },
    handleScreenPosition() {
        let popover = this.$refs.body;
        let button = this.$refs.popover;

        const buttonRect = button.getBoundingClientRect();

        switch (this.originalPosition) {
            case "top":
                if (window.innerHeight < 720 && !(window.innerHeight < (buttonRect.bottom + button.offsetHeight + this.offset + popover.offsetHeight))) {
                    this.position = 'bottom';
                } else {
                    this.position = 'top';
                }

                break;
            case "bottom":
                if (window.innerHeight < (buttonRect.bottom + button.offsetHeight + this.offset + popover.offsetHeight)) {
                    this.position = 'top';
                } else {
                    this.position = 'bottom';
                }

                break;
            case "right":
                if (window.innerWidth <= 480) {
                    this.position = 'top';
                } else if (window.innerWidth < (buttonRect.right + button.offsetWidth + this.offset + popover.offsetWidth)) {
                    this.position = 'left';
                } else {
                    this.position = 'right';
                }

                break;
            case "left":
                if (window.innerWidth <= 480) {
                    this.position = 'top';
                } else if (window.innerWidth < 1280 && ! (window.innerWidth < (buttonRect.right + button.offsetWidth + this.offset + popover.offsetWidth))) {
                    this.position = 'right';
                } else {
                    this.position = 'left';
                }

                break;
        }
    },
    popover: {
        ['x-ref']: 'popover',
        ['x-on:click']() {
            let target = this.$event.target;

            if (this.trigger.includes('click')) {
                if (! (target.tagName === 'BUTTON' || target.tagName === 'A' || target.getAttribute('type') === 'button')
                    && ! (target.closest('button') || target.closest('a'))
                ) {
                    this.$event.preventDefault();
                }

                if (this.open) {
                    if (this.currentTrigger !== 'click') {
                        this.currentTrigger = 'click';
                    } else {
                        this.currentTrigger = null;
                        this.close();
                    }
                } else {
                    this.currentTrigger = 'click';
                    this.open();
                }
            }
        },
        ['x-on:click.outside']() {
            this.currentTrigger = null;
            this.close();
        },
        ['x-on:mouseenter']() {
            if (this.trigger.includes('hover') && null === this.currentTrigger) {
                this.currentTrigger = 'hover';
                this.show()
            }
        },
        ['x-on:mouseleave']() {
            if (this.trigger.includes('hover') && 'hover' === this.currentTrigger) {
                this.currentTrigger = null;
                this.close();
            }
        },
        ['x-on:keydown.escape.window']() {
            this.close();
        }
    }
});
