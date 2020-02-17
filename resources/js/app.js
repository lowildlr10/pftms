// JQuery & Bootstrap
try {
    var moment = require('moment');
    window.Popper = require('popper.js').default;
    window.$ = window.jQuery = require('jquery');

    require('bootstrap');
    require('mdbootstrap');
    require('../custom_modules/sidebar/js/sidebar-main.js');
    var baseURL = "{{ url('/') }}/";
    var modalLoadingContent = "<div class='mt-5' style='height: 150px;'>"+
                                "<center>" +
                                    "<div class='preloader-wrapper big active crazy'>" +
                                        "<div class='spinner-layer spinner-blue-only'>" +
                                            "<div class='circle-clipper left'>" +
                                                "<div class='circle'></div>" +
                                            "</div>" +
                                            "<div class='gap-patch'>" +
                                                "<div class='circle'></div>" +
                                            "</div>" +
                                            "<div class='circle-clipper right'>" +
                                                "<div class='circle'></div>" +
                                            "</div>" +
                                        "</div>" +
                                    "</div><br>" +
                                "</center>" +
                            "</div>";
    $(function() {
        var datetime = null,
                date = null
                dateTimeIco = '<i class="fas fa-clock"></i> ';
        var update = function () {
            date = moment(new Date())
            datetime.html(dateTimeIco + date.format('MMMM D, YYYY HH:mm:ss'));
        };
        $(document).ready(function(){
            datetime = $('#datetime')
            update();
            setInterval(update, 1000);
        });
        $('.preloader').fadeOut();
    });
} catch (e) {}



