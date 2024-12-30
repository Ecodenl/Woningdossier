/**
 * Expand HTML object functionality.
 */

//--- Window

/**
 * Trigger a default event
 *
 * @param element
 * @param eventName
 */
window.triggerEvent = function (eventName) {
    if (eventName) {
        let event = new Event(eventName, {bubbles: true});
        window.dispatchEvent(event);
    }
}

/**
 * Trigger a custom event, with potential parameters.
 *
 * @param element
 * @param eventName
 * @param params
 */
window.triggerCustomEvent = function (eventName, params = {}) {
    if (typeof params !== 'object') {
        console.error('Params is not a valid object!');
        params = {};
    }

    if (eventName) {
        let event = new CustomEvent(eventName, {bubbles: true, detail: params });
        window.dispatchEvent(event);
    }
}

/**
 * Simple wrapper for repetitive fetch requests.
 * Returns the promise for simple `.then` chaining.
 */
window.fetchRequest = function (url, method = 'GET', body = {}) {
    const config = {
        method: method,
        headers: {
            'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        },
    }

    if (Object.keys(body).length > 0 && method !== 'GET') {
        config['body'] = JSON.stringify(body);
    }
    console.log(config)

    return fetch(url, config);
}

window.searchValue = function (value, search) {
    if (value) {
        value = value.toLowerCase().replace(/[-_ ]/g, '').normalize("NFD").replace(/[\u0300-\u036f]/g, "").trim();
        search = search.toLowerCase().replace(/[-_ ]/g, '').normalize("NFD").replace(/[\u0300-\u036f]/g, "").trim();
        return value.includes(search);
    }

    return false;
}

//--- Document

/**
 * Add event listeners to multiple elements.
 *
 * @param event
 * @param selector
 * @param callback
 */
document.on = function (event, selector, callback) {
    document.addEventListener(event, (e) => {
        const target = e.target.closest(selector);
        if (document.querySelectorAll(selector).has(target)) {
            callback.apply(target, [e]);
        }
    });
}

//--- NodeList (e.g. document.querySelectorAll)

/**
 * // TODO: check native contains method
 * Check if given element exists in the collection.
 *
 * @param element
 * @returns boolean
 */
Object.defineProperty(NodeList.prototype, 'has', {
    value: function (element) {
        let has = false;
        this.forEach((nodeElement) => {
            if (nodeElement === element) {
                has = true;
            }
        });

        return has;
    },
    enumerable: false,
    configurable: false,
});

//--- Element

/**
 * Fade out an element.
 *
 * @param time (in milliseconds)
 * @param callback
 */
Object.defineProperty(Element.prototype, 'fadeOut', {
    value: function (time = 1000, callback = null) {
        // Ensure time is a valid number
        if (isNaN(time) || time === null || time === '' || time <= 0) {
            time = 1000;
        }

        // Get current opacity, ensure that that is a valid number also
        let currentOpacity = getComputedStyle(this).opacity;
        if (isNaN(currentOpacity)) {
            currentOpacity = 1;
        }

        // Set opacity in the style, to ensure the value is set
        this.style.opacity = currentOpacity;
        // We use a default timeout of 5 ms
        let timeout = 5;
        // Calculate how much of the opacity we decrease per 5 ms;
        let steps = currentOpacity / (time / timeout);

        // Each timeout, remove the calculated step from the opacity
        let fade = setInterval(() => {
            if (this.style.opacity <= 0) {
                clearInterval(fade);
                if (callback) {
                    callback();
                }
            } else {
                this.style.opacity = parseFloat(this.style.opacity) - steps;
            }
        }, timeout);
    },
    enumerable: false,
    configurable: false,
});

/**
 * Fade in an element.
 *
 * @param time (in milliseconds)
 */
Object.defineProperty(Element.prototype, 'fadeIn', {
    value: function (time = 1000, callback = null) {
        // Ensure time is a valid number
        if (isNaN(time) || time === null || time === '' || time <= 0) {
            time = 1000;
        }

        // Get current opacity, ensure that that is a valid number also
        let currentOpacity = getComputedStyle(this).opacity;
        if (isNaN(currentOpacity)) {
            currentOpacity = 0;
        }

        // Set opacity in the style, to ensure the value is set
        this.style.opacity = currentOpacity;
        // We use a default timeout of 5 ms
        let timeout = 5;
        // Calculate how much of the opacity we increase per 5 ms
        let steps = (1 - currentOpacity) / (time / timeout);

        // Each timeout, add the calculated step to the opacity
        let fade = setInterval(() => {
            if (this.style.opacity >= 1) {
                clearInterval(fade);
                if (callback) {
                    callback();
                }
            } else {
                this.style.opacity = parseFloat(this.style.opacity) + steps;
            }
        }, timeout);
    },
    enumerable: false,
    configurable: false,
});

/**
 * Trigger a default event
 *
 * @param eventName
 */
Object.defineProperty(Element.prototype, 'triggerEvent', {
    value: function (eventName) {
        if (eventName) {
            let event = new Event(eventName, {bubbles: true});
            this.dispatchEvent(event);
        }
    },
    enumerable: false,
    configurable: false,
});

/**
 * Trigger a custom event, with potential parameters.
 *
 * @param eventName
 * @param params
 */
Object.defineProperty(Element.prototype, 'triggerCustomEvent', {
    value: function (eventName, params = {}) {
        if (typeof params !== 'object') {
            console.error('Params is not a valid object!');
            params = {};
        }

        if (eventName) {
            let event = new CustomEvent(eventName, {bubbles: true, detail: params });
            this.dispatchEvent(event);
        }
    },
    enumerable: false,
    configurable: false,
});

/**
 * Add form error.
 */
Object.defineProperty(Element.prototype, 'addError', {
    value: function (message, id) {
        let element = this;
        if (! this.classList.contains('form-group')) {
            element = this.closest('.form-group');
        }

        if (element) {
            // Add manual error (support frontend (Tailwind, form-error) and backend (Bootstrap, has-error))
            element.classList.add('form-error', 'has-error');

            // Don't append feedback if already set.
            if (! element.querySelector(`small#${id}`)) {
                let feedback = document.getElementById('invalid-feedback-template').content.firstElementChild.cloneNode();
                feedback.setAttribute('id', id);
                feedback.textContent = message;
                element.appendChild(feedback);
            }
        }
    },
    enumerable: false,
    configurable: false,
});

/**
 * Remove form error.
 */
Object.defineProperty(Element.prototype, 'removeError', {
    value: function () {
        let element = this;
        if (! this.classList.contains('form-group')) {
            element = this.closest('.form-error');
        }

        if (element) {
            // Remove manual error
            element.classList.remove('form-error', 'has-error');
            // Support frontend and backend, again.
            element.querySelector('p.form-error-label')?.remove();
            element.querySelector('span.help-block')?.remove();
        }
    },
    enumerable: false,
    configurable: false,
});

//--- HTMLCollection

/**
 * Remove all elements in the HTML collection
 */
Object.defineProperty(HTMLCollection.prototype, 'remove', {
    value: function() {
        Array.from(this).forEach((element) => {
            element.remove();
        });
    },
    enumerable: false,
    configurable: false,
});

//--- NodeList

/**
 * Remove all elements in the node list
 */
Object.defineProperty(NodeList.prototype, 'remove', {
    value: function() {
        Array.from(this).forEach((element) => {
            element.remove();
        });
    },
    enumerable: false,
    configurable: false,
});