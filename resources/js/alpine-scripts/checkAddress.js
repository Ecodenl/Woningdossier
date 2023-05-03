export default (checks, tailwind = true) => ({
    showPostalCodeError: false,
    checks: checks,
    oldValues: {},

    init() {
        if (! this.checks instanceof Object) {
            this.checks = {};
        }
    },
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
        ['x-ref']: 'houseNumberExtension',
        ['x-on:change']() {
            this.performChecks();
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
    performChecks() {
        if (Object.hasOwn(this.checks, 'correct_address')) {
            this.getAddressData(this.checks['correct_address']);
        }
    },
    getAddressData(apiUrl) {
        // Get inputs from refs
        let postcode = this.$refs['postcode'];
        let houseNumber = this.$refs['houseNumber'];
        let houseNumberExtension = this.$refs['houseNumberExtension'];
        let city = this.$refs['city'];
        let street = this.$refs['street'];
        let addressId = this.$refs['addressId'];

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
            // } else if (this.isDirty('houseNumberExtension', houseNumberExtension.value)) {
            //     makeRequest = true;
            }

            console.log(makeRequest);

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
console.log(response);
                        // If the request was successful, we fill the data in the field
                        if (request.status == 200) {
                            if (response.postal_code === '') {
                                context.showPostalCodeError = true;
                            }

                            context.setValue(houseNumber, response.number)
                            context.setValue(houseNumberExtension, response.house_number_extension)
                            context.setValue(street, response.street)
                            context.setValue(city, response.city)
                            context.setValue(addressId, response.id)
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
    setValue(input, value) {
        if (typeof input !== 'undefined' && input && value) {
            input.value = value;
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
            let houseNumberExtension = this.$refs['houseNumberExtension'];

            if (postcode.value) {
                url.searchParams.append('postal_code', postcode.value)
            }

            if (houseNumber.value) {
                url.searchParams.append('number', houseNumber.value)
            }

            // if (typeof houseNumberExtension !== 'undefined' && houseNumberExtension.value) {
            //     url.searchParams.append('extension', houseNumberExtension.value)
            // }
        }

        return url;
    },
    isDirty(name, newValue) {
        console.log(this.oldValues[name], newValue, this.makeComparable(this.oldValues[name]) !== this.makeComparable(newValue));

        return this.makeComparable(this.oldValues[name]) !== this.makeComparable(newValue);
    },
    makeComparable(value) {
        // If we use String(value) it will become "null"/"undefined"...
        if (value === null || value === undefined) {
            value = '';
        }
        return String(value).replace(/(\s|-|_)/g, '').toLowerCase();
    }
});