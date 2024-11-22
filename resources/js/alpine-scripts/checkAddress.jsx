export default (checks, tailwind = true) => ({
    bagAvailable: true,
    showPostalCodeError: false,
    showDuplicateError: false,
    checks: checks,
    oldValues: {},
    availableExtensions: [],

    postcode: {
        ['x-ref']: 'postcode',
        ['x-on:change']() {
            this.performChecks();
            this.oldValues['postcode'] = this.$el.value;
        },
        ['x-on:keydown.enter']() {
            this.$event.preventDefault();
            this.$el.triggerEvent('change');
        }
    },
    houseNumber: {
        ['x-ref']: 'houseNumber',
        ['x-on:change']() {
            this.performChecks();
            this.oldValues['houseNumber'] = this.$el.value;
        },
        ['x-on:keydown.enter']() {
            this.$event.preventDefault();
            this.$el.triggerEvent('change');
        }
    },
    houseNumberExtensionField: {
        // Used when BAG is down. Shouldn't do anything.
        ['x-ref']: 'houseNumberExtensionField',
        ['x-on:change']() {
            this.performChecks();
        },
        ['x-on:keydown.enter']() {
            this.$event.preventDefault();
            this.$el.triggerEvent('change');
        }
    },
    houseNumberExtensionSelect: {
        ['x-ref']: 'houseNumberExtensionSelect',
        ['x-on:change']() {
            this.performChecks();
            this.oldValues['houseNumberExtension'] = this.$el.value;
        },
        ['x-on:keydown.enter']() {
            this.$event.preventDefault();
            this.$el.triggerEvent('change');
        }
    },
    city: {
        ['x-ref']: 'city',
    },
    street: {
        ['x-ref']: 'street',
    },
    init() {
        if (! this.checks instanceof Object) {
            this.checks = {};
        }

        // Another thread wait, else the refs have not yet been bound
        setTimeout(() => {
            this.switchAvailability();

            // After a form request, data might be filled from old values, so we will perform a check.
            this.performChecks();
        });
    },
    performChecks() {
        if (Object.hasOwn(this.checks, 'correct_address')) {
            this.getAddressData(this.checks['correct_address']);
        }
        if (Object.hasOwn(this.checks, 'duplicates')) {
            this.checkDuplicates(this.checks['duplicates']);
        }
    },
    getAddressData(apiUrl) {
        // Get inputs from refs
        let postcode = this.$refs['postcode'];
        let houseNumber = this.$refs['houseNumber'];
        let houseNumberExtensionSelect = this.$refs['houseNumberExtensionSelect'];
        let city = this.$refs['city'];
        let street = this.$refs['street'];

        // So we only want to make a request if both postcode and house number are set.
        if (postcode.value && houseNumber.value) {
            let url = this.getUrl(apiUrl);
            url = this.appendAddressData(url);

            // We also only want to make requests if there's actually something that's changed.
            let makeRequest = false;
            if (this.isDirty('postcode', postcode.value) || this.isDirty('houseNumber', houseNumber.value)) {
                makeRequest = true;
                url.searchParams.append('fetch_extensions', '1');
                url.searchParams.delete('extension');
            } else if (this.isDirty('houseNumberExtension', houseNumberExtensionSelect.value)) {
                makeRequest = true;
            }

            if (makeRequest) {
                // Restore old value
                let oldOption = houseNumberExtensionSelect.querySelector('option.old');
                let oldValue = null;
                if (oldOption) {
                    oldValue = oldOption.value;
                    // We want to pass the old value to the backend, as otherwise the initial address might be wrong.
                    url.searchParams.set('extension', oldValue);
                    oldOption.remove();
                }
                let context = this;
                performRequest({
                    'url': url,
                    'done': function (request) {
                        context.removeError(postcode);
                        context.removeError(houseNumber);
                        context.removeError(city);
                        context.removeError(street);

                        let response = request.response;
                        let faultyData = request.status === 422;

                        // Show postal code error if address is wrongly validated.
                        context.showPostalCodeError = faultyData;

                        if (response.available_extensions || faultyData) {
                            context.availableExtensions = response.available_extensions || [];
                        }

                        // So, if no BAG address ID was returned, there was a BAG endpoint failure.
                        // We will consider the BAG available on form request errors also.
                        context.bagAvailable = typeof response.bag_addressid !== 'undefined' || faultyData;
                        context.switchAvailability();

                        if (oldValue !== null) {
                            setTimeout(() => {
                                houseNumberExtensionSelect.value = oldValue;
                            });
                        }

                        // If the request was successful, we fill the data in the field
                        if (request.status === 200) {
                            // Show postal code error if BAG is available and no data was returned.
                            context.showPostalCodeError = response.postal_code === '' && context.bagAvailable;

                            // Don't want to overwrite user data with nothing
                            if (context.bagAvailable) {
                                // If BAG is available we want to reset the street/city so the user
                                // cannot spoof the validation (without being a hackerman).
                                street.value =  response.street;
                                city.value =  response.city;
                            }
                        } else {
                            // Else we add errors
                            let errors = response.errors;
                            for (let error in errors) {
                                if (errors.hasOwnProperty(error)) {
                                    let errorMessage = errors[error][0]; // Grab first message

                                    let input = document.querySelector(`input[name="${error}"]`);
                                    context.appendError(input, errorMessage);
                                }
                            }
                        }
                    }
                });
            }
        }
    },
    checkDuplicates(apiUrl) {
        let url = this.getUrl(apiUrl);
        url = this.appendAddressData(url);

        let context = this;
        performRequest({
            'url': url,
            'done': function (request) {
                context.showDuplicateError = request.response.count > 0;
                context.$dispatch('duplicates-checked', {'showDuplicateError': context.showDuplicateError});
            }
        });
    },
    appendError(input, text) {
        if (typeof input !== 'undefined' && input) {
            // "Legacy" support
            let tag = (tailwind ? 'p' : 'span');
            let className = (tailwind ? 'form-error-label' : 'help-block');
            let parentClassName = (tailwind ? 'form-error' : 'has-error');
            // Don't add double errors
            if (! input.parentElement.querySelector('.form-error-label')) {
                let newError = document.createElement(tag);
                newError.appendChild(document.createTextNode(text));
                newError.classList.add('address-error', className);

                input.parentElement.appendChild(newError);
                input.parentElement.classList.add(parentClassName);
            }
        }
    },
    removeError(input) {
        if (typeof input !== 'undefined' && input) {
            let errors = input.parentElement.getElementsByClassName('address-error');
            if (errors.length > 0) {
                input.parentElement.classList.remove((tailwind ? 'form-error' : 'has-error'));

                for (let i = 0; i < errors.length; i++) {
                    errors[i].remove();
                }
            }
        }
    },
    getUrl(apiUrl) {
        let url = null;
        if (apiUrl) {
            try {
                url = new URL(apiUrl);
            } catch (e) {
                //apiUrl = null
            }
        }

        return url;
    },
    appendAddressData(url) {
        // URL is an object, and so changes made are in reference to the original object. Technically we don't have to
        // return the URL variable.
        if (url instanceof URL) {
            let postcode = this.$refs['postcode'];
            let houseNumber = this.$refs['houseNumber'];
            let houseNumberExtension = this.bagAvailable
                ? this.$refs['houseNumberExtensionSelect']
                : this.$refs['houseNumberExtensionField'];

            if (postcode.value) {
                url.searchParams.append('postal_code', postcode.value)
            }

            if (houseNumber.value) {
                url.searchParams.append('number', houseNumber.value)
            }

            if (houseNumberExtension.value) {
                url.searchParams.append('extension', houseNumberExtension.value)
            }
        }

        return url;
    },
    isDirty(name, newValue) {
        return this.makeComparable(this.oldValues[name]) !== this.makeComparable(newValue);
    },
    makeComparable(value) {
        // If we use String(value) it will become "null"/"undefined"...
        if (value === null || value === undefined) {
            value = '';
        }
        return String(value).replace(/(\s|-|_)/g, '').toLowerCase();
    },
    switchAvailability() {
        // Always hide, conditionally unhidden.
        this.$refs['houseNumberExtensionSelect'].style.display = 'none';
        this.$refs['houseNumberExtensionSelect'].setAttribute('disabled', 'disabled');
        let label = this.$refs['houseNumberExtensionSelect'].closest('.form-group').querySelector('label');
        label.style.display = 'none';

        if (this.bagAvailable) {
            this.$refs['street'].setAttribute('readonly', 'readonly');
            this.$refs['city'].setAttribute('readonly', 'readonly');
            this.$refs['houseNumberExtensionField'].style.display = 'none';
            this.$refs['houseNumberExtensionField'].setAttribute('disabled', 'disabled');

            if (this.availableExtensions.length > 0) {
                this.$refs['houseNumberExtensionSelect'].style.display = '';
                this.$refs['houseNumberExtensionSelect'].removeAttribute('disabled');
                label.style.display = '';
            }
        } else {
            this.$refs['street'].removeAttribute('readonly');
            this.$refs['city'].removeAttribute('readonly');
            this.$refs['houseNumberExtensionField'].style.display = '';
            this.$refs['houseNumberExtensionField'].removeAttribute('disabled');
            label.style.display = '';
        }
    },
});