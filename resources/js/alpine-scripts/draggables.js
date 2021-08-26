export default (supportedClasses = ['card-wrapper'], hoverColor = 'rgba(100, 117, 133, 0.2)', defaultClass = 'card', placeholderClass = 'card-placeholder') => ({
    // Docs: use supportedClasses to define classes in which elements can be dropped
    // Supported classes should be PARENT classes.
    // Define hoverColor for the background color that gets used when hovering over supported classes
    // Define defaultClass as the class the CHILD element has when it's not dragged.
    // Define placeholderClass as the class the CHILD element has when it gets dragged.
    dragged: null,
    draggedOrder: -1,
    lastEntered: null,
    defaultClass: defaultClass,
    placeholderClass: placeholderClass,
    supportedClasses: supportedClasses,
    ghost: null,

    container: {
        ['x-on:drop.prevent']() {
            if (null !== this.dragged) {
                let eventTarget = this.$event.target;
                let target = this.getSupportedTarget(eventTarget);

                // If the dropped target is itself, we don't need to do anything
                if (null !== target && eventTarget !== this.dragged) {
                    let clientX = this.$event.clientX;
                    let clientY = this.$event.clientY;

                    let positionInfo = this.getPosition(target, clientX, clientY);

                    // Just to ensure we're not outside the parent.
                    if (positionInfo.position !== 'outside') {
                        // Get ghost position, that's where we're dropping
                        // If it's not there, it's the old position, so we do nothing
                        let ghost = this.getGhost();

                        if (ghost) {
                            let ghostParentElement = ghost.parentElement;

                            let order = Array.from(ghostParentElement.children).indexOf(ghost);

                            if (order > -1) {
                                let parentElement = this.dragged.parentElement;
                                // Remove dragged item from original parent
                                parentElement.removeChild(this.dragged);

                                // Swap ghost with moved card
                                ghostParentElement.replaceChild(this.dragged, ghost);

                                // Dispatch the dropped position
                                let event = new CustomEvent('item-dragged', {
                                    detail: {
                                        from: parentElement,
                                        to: target,
                                        id: this.dragged.id,
                                        order: order,
                                    },
                                    bubbles: true,
                                });
                                dispatchEvent(event);
                            }
                        }
                    }
                }
            }
        },
        ['x-on:dragover.prevent']() {
            // This needs to be prevented, else drop doesn't work

            // We need the draggable
            if (null !== this.dragged) {
                let eventTarget = this.$event.target;
                let target = this.getSupportedTarget(eventTarget);

                // getSupportedTarget will check eventTarget. We won't get anything back if eventTarget is an invalid
                // nodeType. Therefore we don't need to check it multiple times
                if (null !== target) {
                    // We don't need to check anything if it's a ghost
                    if (! eventTarget.classList.contains('draggable-ghost')) {
                        if (eventTarget === this.dragged) {
                            this.clearGhost();
                        } else {
                            let target = this.getSupportedTarget(eventTarget);

                            // Get coords of where the user is holding the mouse
                            let clientX = this.$event.clientX;
                            let clientY = this.$event.clientY;

                            let positionInfo = this.getPosition(target, clientX, clientY);

                            let position = positionInfo.position;
                            if (position !== 'outside') {
                                let draggedOrder = this.draggedOrder;
                                let order = positionInfo.order

                                let isValidPosition = true;
                                // We don't want to build a ghost if the current position is around this.dragged
                                if (target === this.dragged.parentElement && ((order === draggedOrder + 1 && position === 'top') || (order === draggedOrder - 1 && position === 'bottom')
                                    || (order > draggedOrder && this.dragged.nextElementSibling === null) || (order === draggedOrder))) {
                                    isValidPosition = false;
                                }

                                if (isValidPosition) {
                                    let ghost = this.ghost;

                                    if (null === ghost) {
                                        ghost = this.buildGhost();
                                    }

                                    let hoveredChild = Array.from(target.children)[positionInfo.order];
                                    let beforeOrAfter = positionInfo.position === 'top' ? 'before' : 'after';

                                    // Insert new ghost on given position
                                    this.insertElement(ghost, hoveredChild, target, beforeOrAfter);
                                }
                                else {
                                    this.clearGhost();
                                }
                            }
                        }
                    }
                }
            }
        },
        ['x-on:dragenter']() {
            if (null !== this.dragged) {
                let eventTarget = this.$event.target;
                this.lastEntered = eventTarget;
                let target = this.getSupportedTarget(eventTarget);

                if (null !== target) {
                    target.style.backgroundColor = hoverColor;
                }
            }
        },
        ['x-on:dragleave']() {
            if (null !== this.dragged) {
                let eventTarget = this.$event.target;
                let target = this.getSupportedTarget(eventTarget);

                // Enter triggers before leave. We check the last element that we entered. If it's not set, then we left
                // the container and it should be reset
                if (null !== target && null === this.lastEntered) {
                    target.style.backgroundColor = '';
                }

                this.lastEntered = null
            }
        },
    },
    draggable: {
        ['x-on:dragstart.self']() {
            this.dragged = this.$el;
            this.draggedOrder = Array.from(this.dragged.parentElement.children).indexOf(this.dragged);
        },
        ['x-on:drag']() {
            // No need to call this many times, this event triggers on each drag movement
            if (this.$el.classList.contains(this.defaultClass)) {
                this.$el.classList.remove(this.defaultClass);
                this.$el.classList.add(this.placeholderClass);
            }
        },
        ['x-on:dragend']() {
            this.$el.classList.remove(this.placeholderClass);
            this.$el.classList.add(this.defaultClass);

            // We dropped. We clear the ghost and backgrounds
            this.clearGhost();
            this.clearAllBackgrounds();
            // Always clear dragged info
            this.dragged = null;
            this.draggedOrder = -1;
        }
    },
    getSupportedTarget(element) {
        let target = null;
        let supportedClasses = this.supportedClasses;

        if (Array.isArray(supportedClasses) && element && element.nodeType === Node.ELEMENT_NODE) {
            // Check if the current target is supported
            supportedClasses.forEach((className) => {
                if (element.classList.contains(className)) {
                    target = element;
                }
            });

            // If it's not, we check if a potential parent is supported
            if (null === target) {
                supportedClasses.forEach((className) => {
                    let parent = element.closest('.' + className);
                    if (null !== parent) {
                        target = parent;
                    }
                });
            }
        }

        return target;
    },
    getPosition(target, xCoord, yCoord) {
        // We need to decide whether the element is more related to the upper side or the bottom side

        // Get bounding rectangle for the target
        let targetRect = target.getBoundingClientRect();
        // We are inside the boundaries!
        if (xCoord >= targetRect.left && xCoord <= targetRect.right && yCoord >= targetRect.top && yCoord <= targetRect.bottom) {
            // Get the draggable element for info
            let element = this.dragged;

            if (null !== element) {
                // let elementRect = element.getBoundingClientRect();
                // let style = getComputedStyle(element);

                // Define total height per element
                // let totalHeight = elementRect.height + parseInt(style['margin-top']) + parseInt(style['margin-bottom']);

                // Use fixed value for now, cards + margin should always be 108px
                let totalHeight = 108;

                // Do some maths to define the exact position within the target
                let exactPositionInTarget = yCoord - targetRect.top;
                let order = Math.floor(exactPositionInTarget / totalHeight);
                let posInEl = exactPositionInTarget - order * totalHeight;

                let position = posInEl > (totalHeight / 2) ? 'bottom' : 'top';

                return {
                    order: order,
                    position: position,
                }
            }
        }

        return {
            order: -1,
            position: 'outside',
        };
    },
    getGhost() {
        if (null === this.ghost) {
            this.ghost = document.querySelector('.draggable-ghost');
        }

        return this.ghost;
    },
    buildGhost() {
        // We can't call getGhost because during drag, it doesn't properly update this.ghost and will result in
        // null
        let potentialGhost = document.querySelector('.draggable-ghost');
        // Check if one exists before we built a new one
        if (potentialGhost) {
            this.ghost = potentialGhost;
        } else {
            let newPlaceholder = document.createElement('div');
            newPlaceholder.classList.add(this.placeholderClass, 'draggable-ghost');
            this.ghost = newPlaceholder;
        }

        return this.ghost;
    },
    clearGhost() {
        // Remove all ghosts from the document
        let ghost = document.querySelector('.draggable-ghost');
        if (ghost) {
            ghost.remove();
        }

        this.ghost = null;
    },
    insertElement(newNode, element, parentElement, beforeOrAfter) {
        if (newNode !== null) {
            let referenceNode = null
            if (element) {
                // If we insert before, we grab the element. Else, we grab the sibling
                referenceNode = beforeOrAfter === 'before' ? element : element.nextElementSibling;

                // If it's the first item, we want it at the top no matter the beforeOrAfter status
                if (element.previousElementSibling === null) {
                    referenceNode = element;
                }
            }
            // We don't need to check for null, because if there's no sibling, insertBefore automatically appends to the end
            parentElement.insertBefore(newNode, referenceNode);
        }
    },
    clearAllBackgrounds() {
        let supportedClasses = this.supportedClasses;

        if (Array.isArray(supportedClasses)) {
            supportedClasses.forEach((className) => {
                let elements = document.getElementsByClassName(className);

                for (let i = 0; i < elements.length; i++) {
                    if (elements[i]) {
                        elements[i].style.backgroundColor = '';
                    }
                }
            });
        }
    },
});