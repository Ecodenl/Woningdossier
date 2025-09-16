export default (emailUrl) => ({
    allowAccess: false,
    showEmailWarning: false,
    alreadyMember: false,
    noBuilding: false,
    emailExists: false,
    emailUrl: emailUrl,
    submitted: false,

    checkEmail(element, safeValue = null) {
        // Don't apply check if current value is safe value
        if (safeValue && element.value.trim() === safeValue) {
            this.showEmailWarning = false;
            this.alreadyMember = false;
            this.noBuilding = false;
            this.emailExists = false;
            return;
        }
        const goodDomains = new RegExp('\\b(nl|be|net|com|info|nu|de)\\b', 'i');

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

        if (url instanceof URL) {
            fetchRequest(url).then((response) => response.json()).then((response) => {
                this.alreadyMember = false;
                this.noBuilding = false;
                this.emailExists = false;

                if (response.email_exists) {
                    if (response.user_is_already_member_of_cooperation) {
                        this.noBuilding = response.user_has_no_building;
                        this.alreadyMember = ! response.user_has_no_building;
                    } else {
                        this.emailExists = true;
                    }
                }
            });
        }
    }
});