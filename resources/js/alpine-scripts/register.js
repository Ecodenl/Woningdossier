export default (emailUrl) => ({
    allowAccess: false,
    showEmailWarning: false,
    alreadyMember: false,
    emailExists: false,
    emailUrl: emailUrl,

    checkEmail(element) {
        let goodDomains = new RegExp('\\b(nl|be|net|com|info|nu|de)\\b', 'i');

        // If the email does not contain a good domain return a message
        this.showEmailWarning = ! goodDomains.test(element.value);
        this.checkExisting(element);
    },
    checkExisting(element) {
        let url = null;
        if (this.emailUrl) {
            try {
                url = new URL(this.emailUrl);
            } catch (e) {
                this.emailUrl = null
            }
        }

        if ((window.XMLHttpRequest || window.ActiveXObject) && url && typeof element !== 'undefined' && element.value.length > 0) {
            let request = window.XMLHttpRequest ? new window.XMLHttpRequest() : new window.ActiveXObject("Microsoft.XMLHTTP");
            // We need to be able to access this context
            let context = this;
            request.onreadystatechange =function () {
                // Ajax finished and ready
                if (request.readyState == window.XMLHttpRequest.DONE) {
                    context.alreadyMember = false;
                    context.emailExists = false;

                    let response = request.response;

                    if (request.status == 200) {
                        if (response.email_exists) {
                            if (response.user_is_already_member_of_cooperation) {
                                context.alreadyMember = true;
                            } else {
                                context.emailExists = true;
                            }
                        }
                    }
                }
            };

            // Add searchParams
            url.searchParams.append('email', element.value);

            request.open('GET', url.href);
            request.setRequestHeader('Accept', 'application/json');
            request.responseType = 'json';
            request.send();
        }
    }
});