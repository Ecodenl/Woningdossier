// to determine if a poll has been done
var beenPolled = false;

// function to update the total unread message badge
function updateTotalUnreadMessageCount() {
    $.ajax({
        url: window.location.origin + '/messages/count',
        type: "GET",
        success: function (response) {
            $('#total-unread-message-count').html(response.count);
        },
        statusCode: {
            401: function () {
                // Redirec the to the login page.
                window.location.href = '/login';
            }
        }
    });
}

// poll for the message count
function pollForMessageCount() {

    var timeout = 0;

    if (beenPolled) {
        // 10 seconds
        timeout = 10000;
    }
    setTimeout(function () {
        beenPolled = true;
        updateTotalUnreadMessageCount();
        pollForMessageCount();
    }, timeout);
};


function updateNotifications() {
    $.ajax({
        url: window.location.origin + '/notifications',
        type: "GET",
        success: function (response) {
            console.log(response)
            // for now the first one is ok, we will upgrade to livewire in near future anyway
            if (response.notifications[0].type === 'recalculate') {

                bootoast.r
                bootoast.toast({
                    "message": "<p>Uw adviezen worden momenteel berekend</p>",
                    "type": "info",
                    "position": "rightBottom",
                    "icon": "",
                    "timeout": false,
                    "animationDuration": "300",
                    "dismissable": false
                })

            }
        },
        statusCode: {
            401: function () {
                // Redirec the to the login page.
                window.location.href = '/login';
            }
        }
    });
}

notificationBeenPolled = false;

function pollForNotifications() {
    var timeout = 0;
    if (notificationBeenPolled) {
        timeout = 5000;
    }
    setTimeout(function () {
        notificationBeenPolled = true;

        updateNotifications;

        pollForMessageCount()
    }, timeout);
}

function hoomdossierRound(value, bucket) {

    if (value !== null) {
        if (typeof bucket === "undefined") {
            bucket = 5;
        }

        return Math.round(value / bucket) * bucket;
    }
    return 0;
};

function hoomdossierNumberFormat(value, locale, decimals) {
    if (value !== null) {
        if (typeof value === "string") {
            value = parseFloat(value);
        }
        return value.toLocaleString(locale, {minimumFractionDigits: decimals});
    }
    return 0;
};