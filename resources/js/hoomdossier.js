// to determine if a poll has been done
var beenPolled = false;

// function to update the total unread message badge
function updateTotalUnreadMessageCount() {
    $.ajax({
        url: window.location.origin + '/messages/count',
        type: "GET",
        success: function (response) {
            if (response.count === 0) {
                $('#total-unread-message-count').removeClass('badge-primary')
            } else {
                $('#total-unread-message-count').addClass('badge-primary')
            }
            $('#total-unread-message-count').html(response.count);
        },
        statusCode: {
            401: function () {
                // Redirect the to the login page.
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

// default set to false, will be set to true once polled
// so if we get no notification response back and the var is set to true we now the recalc is done.
var wasRecalculating = false;

// toast will actually be created in the blade my-plan/index because of speed
function updateNotifications() {
    $.ajax({
        url: window.location.origin + '/notifications',
        type: "GET",
        success: function (response) {
            // for now this will do, this will be changed, hopefully, to livewire in the near future
            if (typeof response.notifications[0] === "undefined") {
                wasRecalculating = true;
            }

            if (wasRecalculating && $('.jq-toast-wrap').length > 0) {
                $.toast().reset('all');
                $.toast({
                    text: 'Actieplan is herberekend.',
                    showHideTransition: 'slide',
                    icon: 'success',
                    hideAfter: 2000,
                    position: 'bottom-right',
                    beforeHide: function () {
                        if (window.location.pathname === '/tool/my-plan') {
                            window.location.reload();
                        }
                    }
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
    // only need this on the my plan page.
    if (window.location.pathname.indexOf('tool/my-plan') !== -1) {
        var timeout = 0;
        if (notificationBeenPolled) {
            timeout = 5000;
        }
        setTimeout(function () {
            notificationBeenPolled = true;

            updateNotifications()

            pollForNotifications()
        }, timeout);
    }
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