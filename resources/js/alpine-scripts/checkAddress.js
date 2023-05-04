export default (checks, tailwind = true) => ({
    bagAvailable: true,
    showPostalCodeError: false,
    checks: checks,
    oldValues: {},
    availableExtensions: [],

    postcode: {
        ['x-ref']: 'postcode',
        ['x-on:change']() {
            this.performChecks();
            this.oldValues['postcode'] = this.$el.value;
        },
    },
    houseNumber: {
        ['x-ref']: 'houseNumber',
        ['x-on:change']() {
            this.performChecks();
            this.oldValues['houseNumber'] = this.$el.value;
        },
    },
    houseNumberExtension: {
        // Conditionally setting x-ref seems to not work as expected, so it's set in the init
        ['x-on:change']() {
            this.oldValues['houseNumberExtension'] = this.$el.value;
        },
    },
    city: {
        ['x-ref']: 'city',
    },
    street: {
        ['x-ref']: 'street',
    },
    addressId: {
        ['x-ref']: 'addressId',
    },
    init() {
        if (! this.checks instanceof Object) {
            this.checks = {};
        }

        // Because conditionally setting x-ref doesn' work...
        Array.from(document.querySelectorAll('[x-bind="houseNumberExtension"]')).forEach((extensionField) => {
            let ref = '';
            switch (extensionField.tagName) {
                case 'INPUT':
                    ref = 'houseNumberExtensionField';
                    break;
                case 'SELECT':
                    ref = 'houseNumberExtensionSelect';
                    break;
            }
            if (ref) {
                extensionField.setAttribute('x-ref', ref);
            }
        });

        // Another thread wait, else the refs have not yet been bound
        setTimeout(() => {
            this.switchAvailability();

            // After a form request, data might be filled from old values, so we will perform a  check.
            this.performChecks();
        });
    },
    performChecks() {
        if (Object.hasOwn(this.checks, 'correct_address')) {
            this.getAddressData(this.checks['correct_address']);
        }
    },
    getAddressData(apiUrl) {
        // Get inputs from refs
        let postcode = this.$refs['postcode'];
        let houseNumber = this.$refs['houseNumber'];
        let city = this.$refs['city'];
        let street = this.$refs['street'];
        let addressId = this.$refs['addressId'];

        // So we only want to make a request if both postcode and house number are set.
        if (postcode.value && houseNumber.value) {
            // Note: the code was written this way so a change to the house number extension could also be easily made
            // to trigger an update. However, since the address should be correct anyway, just with a different
            // extension, it won't trigger an update to save on requests.

            let url = this.getUrl(apiUrl);
            url = this.appendAddressData(url);

            // We also only want to make requests if there's actually something that's changed.
            let makeRequest = false;
            if (this.isDirty('postcode', postcode.value) || this.isDirty('houseNumber', houseNumber.value)) {
                makeRequest = true;
                url.searchParams.append('fetch_extensions', '1');
                url.searchParams.delete('extension');
            }

            if (makeRequest) {
                let context = this;
                performRequest({
                    'url': url,
                    'done': function (request) {
                        context.removeError(postcode);
                        context.removeError(houseNumber);
                        context.removeError(city);
                        context.removeError(street);
                        context.showPostalCodeError = false;

                        let response = request.response;

                        // Restore old value
                        let oldOption = context.$refs['houseNumberExtensionSelect'].querySelector('option.old');
                        let oldValue = null;
                        if (oldOption) {
                            oldValue = oldOption.value;
                            oldOption.remove();
                        }

                        if (response.available_extensions) {
                            context.availableExtensions = response.available_extensions;
                        }

                        // So, if no BAG address ID was returned, there was a BAG endpoint failure.
                        context.bagAvailable = typeof response.bag_addressid !== 'undefined';
                        context.switchAvailability();

                        if (oldValue !== null) {
                            setTimeout(() => {
                                context.$refs['houseNumberExtensionSelect'].value = oldValue;
                            });
                        }

                        // If the request was successful, we fill the data in the field
                        if (request.status == 200) {
                            if (response.postal_code === '' && context.bagAvailable) {
                                context.showPostalCodeError = true;
                            }

                            // Don't want to overwrite user data with nothing
                            if (context.bagAvailable) {
                                // If BAG is available we want to reset the street/city so the user
                                // cannot spoof the validation
                                street.value =  response.street;
                                city.value =  response.city;
                                addressId.value =  response.bag_addressid;
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

        if (this.bagAvailable) {
            this.$refs['street'].setAttribute('readonly', 'readonly');
            this.$refs['city'].setAttribute('readonly', 'readonly');
            this.$refs['houseNumberExtensionField'].style.display = 'none';
            this.$refs['houseNumberExtensionField'].setAttribute('disabled', 'disabled');

            if (this.availableExtensions.length > 0) {
                this.$refs['houseNumberExtensionSelect'].style.display = '';
                this.$refs['houseNumberExtensionSelect'].removeAttribute('disabled');
            }
        } else {
            this.$refs['street'].removeAttribute('readonly');
            this.$refs['city'].removeAttribute('readonly');
            this.$refs['houseNumberExtensionField'].style.display = '';
            this.$refs['houseNumberExtensionField'].removeAttribute('disabled');
        }
    },
});