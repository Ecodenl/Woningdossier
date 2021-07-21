export default () => ({
    allowAccess: false,
    showEmailWarning: false,

    checkEmail(element) {
        let goodDomains = new RegExp('\\b(nl|be|net|com|info|nu|de)\\b', 'i');

        // If the email does not contain a good domain return a message
        this.showEmailWarning = ! goodDomains.test(element.value);
    },
});