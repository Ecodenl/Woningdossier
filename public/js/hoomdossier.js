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
        statusCode: {
            401: function(){
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

function hoomdossierRound(value, bucket) {
    if (typeof bucket === "undefined") {
        bucket = 5;
    }

    return Math.round(value / bucket) * bucket;
};

function hoomdossierNumberFormat(value, locale, decimals){
    if (typeof value === "string"){
        value = parseFloat(value);
    }
    return value.toLocaleString(locale, { minimumFractionDigits: decimals });
};