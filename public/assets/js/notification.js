$(function() {
    $.fn.setAsReadNotification = function(notifID) {
        const url = baseURL + "/notification/mark-as-read/" + notifID;

        $.get(url, function(notifCount) {
            if (notifCount > 0) {
                $('#disp-notif-count').text(notifCount);
                $('#disp-notif-count-mobile').text(notifCount);
            } else {
                $('#disp-notif-count').removeClass('badge')
                                      .removeClass('red')
                                      .text('');
                $('#disp-notif-count-mobile').removeClass('badge')
                                      .removeClass('red')
                                      .text('');
            }
        });
    }

    $.fn.clearAllNotifications = function() {
        const url = baseURL + "/notification/display";

        $.get(url, function() {
            $('.sidebar-wrapper').find('span.notification')
                                 .removeClass('.badge')
                                 .removeClass('.badge-pill')
                                 .removeClass('.badge-danger')
                                 .html('');
        });
    }
});
