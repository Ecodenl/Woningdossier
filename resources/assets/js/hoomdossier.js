// to determine if a poll has been done
var beenPolled = false;

// function to update the total unread message badge
function updateTotalUnreadMessageCount()
{
    $.ajax({
        url: window.location.origin + '/messages/count',
        type: "GET",
        success: function (response) {
            $('#total-unread-message-count').html(response.count);
        },
    });
}

// poll for the message count
function pollForMessageCount() {

    var timeout = 0;

    if (beenPolled) {
        timeout = 5000;
    }
    setTimeout(function () {
        beenPolled = true;
        updateTotalUnreadMessageCount();
        pollForMessageCount();
    }, timeout);
};