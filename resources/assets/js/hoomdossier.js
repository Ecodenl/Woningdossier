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

// default set to false, will be set to true once polled
// so if we get no notification response back and the var is set to true we now the recalc is done.
var wasRecalculating = false;
var recalculatingToast = null;
function updateNotifications() {
    $.ajax({
        url: window.location.origin + '/notifications',
        type: "GET",
        success: function (response) {
            // for now this will do, this will be changed, hopefully, to livewire in the near future
            if (typeof response.notifications[0] !== "undefined" && recalculatingToast === null) {
                $('.pdf-report').prop('disabled', 'disabled').addClass('disabled')
                wasRecalculating = true;
                recalculatingToast = $.toast({
                    text: "Actieplan word herberekend. <span class='glyphicon-spin glyphicon glyphicon-refresh '></span>", // Text that is to be shown in the toast
                    icon: 'info', // Type of toast icon
                    showHideTransition: 'fade', // fade, slide or plain
                    allowToastClose: false, // Boolean value true or false
                    hideAfter: false, // false to make it sticky or number representing the miliseconds as time after which toast needs to be hidden
                    stack: 2, // false if there should be only one toast at a time or a number representing the maximum number of toasts to be shown at a time
                    position: 'bottom-right', // bottom-left or bottom-right or bottom-center or top-left or top-right or top-center or mid-center or an object representing the left, right, top, bottom values

                    textAlign: 'left',  // Text alignment i.e. left, right or center
                    loader: false,  // Whether to show loader or not. True by default
                    loaderBg: '#31708f',  // Background color of the toast loader
                });
            }
            if (wasRecalculating && typeof response.notifications[0] === "undefined") {
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