class Hoomdossier {

    'use strict';

    static updateTotalUnreadMessages()
    {
        $.ajax({
            url: window.location.origin + '/messages/count',
            method: 'GET',
            success: (response) => {
                $('#total-unread-message-count').html(response.count);
            }
        });
    }

    static set pollTimeout(timeout)
    {
        this.timeout = timeout
    }


    static pollForMessageCount()
    {
        // first the timeout is not set, so we set it to 0
        // after that we timeout to 3 seconds.
        if (!this.hasOwnProperty('timeout')) {
            this.pollTimeout = 0;
        } else {
            this.pollTimeout = 3000;
        }

        setTimeout(() => {
            this.updateTotalUnreadMessages();
            this.pollForMessageCount();
        }, this.timeout)
    }

}