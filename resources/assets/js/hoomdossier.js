function updateTotalUnreadMessages () {

    $.ajax({
        url: window.location.origin + '/messages/count',
        method: 'GET',
        success: function (response) {
            $('#total-unread-message-count').html(response.count);
        }
    });
};

function pollForMessageCount () {

    var beenPolled = false;
    // first the timeout is not set, so we set it to 0
    // after that we timeout to 3 seconds.


    var timeout = 10000;

    setTimeout(function () {
        if (beenPolled) {
            console.log(10000)
            timeout = 10000
        }
        beenPolled = true;
        updateTotalUnreadMessages();
        pollForMessageCount();
    }, timeout);
};
