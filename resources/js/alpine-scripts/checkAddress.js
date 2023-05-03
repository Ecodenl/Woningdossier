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
        //TODO: Trace old house number + postcode. Only perform request if not dirty and both are filled.
        // No request needed on changed extension. On dirty, hide extension. Ignore also in request. Extra parameter
        // in call to request the extensions.

        // Get inputs from refs
        let postcode = this.$refs['postcode'];
        let houseNumber = this.$refs['houseNumber'];
        let houseNumberExtension = this.$refs['houseNumberExtension'];
        let city = this.$refs['city'];
        let street = this.$refs['street'];
        let addressId = this.$refs['addressId'];

        let url = this.getUrl(apiUrl);
        url = this.appendAddressData(url);

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

            if (typeof houseNumberExtension !== 'undefined' && houseNumberExtension.value) {
                url.searchParams.append('extension', houseNumberExtension.value)
            }
        }

        return url;
    }
});