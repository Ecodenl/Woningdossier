export default (addressUrl, tailwind = true) => ({
    showPossibleError: false,
    apiUrl: addressUrl,

    postcode: {
        ['x-ref']: 'postcode',
        ['x-on:change']() {
            this.getAddressData();
        },
    },
    houseNumber: {
        ['x-ref']: 'houseNumber',
        ['x-on:change']() {
            this.getAddressData();
        },
    },
    houseNumberExtension: {
        ['x-ref']: 'houseNumberExtension',
        ['x-on:change']() {
            this.getAddressData();
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
    getAddressData() {
        // Get inputs from refs
        let postcode = this.$refs['postcode'];
        let houseNumber = this.$refs['houseNumber'];
        let houseNumberExtension = this.$refs['houseNumberExtension'];
        let city = this.$refs['city'];
        let street = this.$refs['street'];
        let addressId = this.$refs['addressId'];

        let url = null;
        if (this.apiUrl) {
            try {
                url = new URL(this.apiUrl);
            } catch (e) {
                this.apiUrl = null
            }
        }

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

                // Build request url
                if (postcode.value) {
                    url.searchParams.append('postal_code', postcode.value)
                }
                if (houseNumber.value) {
                    url.searchParams.append('number', houseNumber.value)
                }
                if (typeof houseNumberExtension !== 'undefined' && houseNumberExtension.value) {
                    url.searchParams.append('house_number_extension', houseNumberExtension.value)
                }

                request.open('GET', url.href);
                request.setRequestHeader('Accept', 'application/json');
                request.responseType = 'json';
                request.send();
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
    }
});