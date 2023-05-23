export default (emailUrl) => ({
    allowAccess: false,
    showEmailWarning: false,
    alreadyMember: false,
    emailExists: false,
    emailUrl: emailUrl,
    submitted: false,

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
                // Add searchParams
                url.searchParams.append('email', element.value);
            } catch (e) {
                this.emailUrl = null
            }
        }

        let context = this;
        performRequest({
            'url': url,
            'done': function (request) {
                context.alreadyMember = false;
                context.emailExists = false;

                let response = request.response;

                if (request.status === 200) {
                    if (response.email_exists) {
                        if (response.user_is_already_member_of_cooperation) {
                            context.alreadyMember = true;
                        } else {
                            context.emailExists = true;
                        }
                    }
                }
            }
        });
    }
});