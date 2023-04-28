export default (checks, tailwind = true) => ({
    showPossibleError: false,
    checks: checks,

    init() {
        if (! this.checks instanceof Object) {
            this.checks = {};
        }
    },
    postcode: {
        ['x-ref']: 'postcode',
        ['x-on:change']() {
            this.performChecks();
        },
    },
    houseNumber: {
        ['x-ref']: 'houseNumber',
        ['x-on:change']() {
            this.performChecks();
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
        if (Object.hasOwn(this.checks, 'duplicates')) {
            this.checkDuplicates(this.checks['duplicates']);
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

        let url = this.getUrl(apiUrl);

        // We can't do anything if we don't have these
        if (typeof postcode !== 'undefined' && typeof houseNumber !== 'undefined') {
            // We need these to make ajax calls
            if ((window.XMLHttpRequest || window.ActiveXObject) && url) {
                let request = window.XMLHttpRequest ? new window.XMLHttpRequest() : new window.ActiveXObject("Microsoft.XMLHTTP");
                // We need to be able to access this context
                let context = this;
                request.onreadystatechange =function () {
                    // Ajax finished and ready
                    if (request.readyState == window.XMLHttpRequest.DONE) {
                        context.removeError(postcode);
                        context.removeError(houseNumber);
                        context.removeError(city);
                        context.removeError(street);
                        context.showPossibleError = false;

                        let response = request.response;

                        // If the request was successful, we fill the data in the field
                        if (request.status == 200) {
                            if (response.postal_code === '') {
                                context.showPossibleError = true;
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
                                if(errors.hasOwnProperty(error)) {
                                    let errorMessage = errors[error][0]; // Grab first message

                                    let input = document.querySelector(`input[name="${error}"]`);
                                    context.appendError(input, errorMessage);
                                }
                            }
                        }
                    }
                };

                let params = url.searchParams;

                if (postcode.value) {
                    params.append('postal_code', postcode.value)
                }

                if (houseNumber.value) {
                    params.append('number', houseNumber.value)
                }

                if (typeof houseNumberExtension !== 'undefined' && houseNumberExtension.value) {
                    params.append('house_number_extension', houseNumberExtension.value)
                }
                request.open('GET', url.toString());
                request.setRequestHeader('Accept', 'application/json');
                request.responseType = 'json';
                request.send();
            }
        }
    },
    checkDuplicates(apiUrl) {
        let postcode = this.$refs['postcode'];
        let houseNumber = this.$refs['houseNumber'];
        let houseNumberExtension = this.$refs['houseNumberExtension'];

        let url = this.getUrl(apiUrl);

        // TODO:
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
                newError.classList.add('pico-address-error', className);

                input.parentElement.appendChild(newError);
                input.parentElement.classList.add(parentClassName);
            }
        }
    },
    removeError(input) {
        if (typeof input !== 'undefined' && input) {
            let errors = input.parentElement.getElementsByClassName('pico-address-error');
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
    }
});