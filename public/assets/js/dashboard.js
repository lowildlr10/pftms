$(function () {
    let lastDashboardID = "#dashboard-1";

    $.fn.initDashboard = (dashboardID) => {
        $("#mdb-preloader").css("background", "#000000ab").fadeIn(300);

        let loadURL = `${baseURL}/show-dashboard/${dashboardID}`;
        dashboardID = `#${dashboardID}`;
        $(dashboardID).find(".dashboard-body").html("").fadeOut(10);
        let loadSegment = $(dashboardID)
            .find(".dashboard-body")
            .load(loadURL, function () {
                $("#mdb-preloader").fadeOut(300);
                $(this).delay(800).slideToggle(500);

                if (lastDashboardID != dashboardID) {
                    $(lastDashboardID).find(".dashboard-body").html("").hide();
                    lastDashboardID = dashboardID;
                } else {
                    $(this).slideDown(500);
                }
            });

        loadSegment.onreadystatechange = null;
        loadSegment.abort = null;
        loadSegment = null;
    };
    $(this).initDashboard("dashboard-1");
});
