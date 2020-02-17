<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="full-height">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'PFTMS') }}</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" type="text/css" href="{{ asset('plugins/fa/css/all.min.css') }}">

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" type="text/css" href="{{ asset('plugins/mdb/css/bootstrap.min.css') }}" rel="stylesheet">

    <!-- Material Design Bootstrap -->
    <link rel="stylesheet" type="text/css" href="{{ asset('plugins/mdb/css/mdb.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{ asset('plugins/mdb/css/bootstrap-select.min.css') }}" rel="stylesheet">

    <!-- Your custom styles (optional) -->
    <link rel="stylesheet" type="text/css" href="{{ asset('plugins/sidebar/css/sidebar-main.css') }}" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{ asset('plugins/sidebar/css/sidebar-themes.css') }}" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/app.css') }}" rel="stylesheet">

    @yield('custom-css')
</head>
<body class="mdb-color lighten-3">
    <div id="mdb-preloader" class="flex-center preloader">
        <div class="spinner-grow text-light fast" style="width: 3rem; height: 3rem;" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top top-nav-collapse"> <!-- scrolling-navbar -->
        <!--<div class="container-fluid">-->

            <!-- Brand -->
            @guest
            <a class="navbar-brand nav-link waves-effect waves-light" href="{{ url('/') }}">
                <img class="d-inline-block align-top" src="{{ asset('images/logo/pftms-logo-2.jpg') }}"
                     height="30" alt="pfms logo">
                <strong></strong>
            </a>
            @endguest

            <!-- Collapse -->
            @guest
            @else
            <button class="navbar-toggler button-collapse waves-effect waves-light toggle-sidebar"
                    type="button" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <button class="navbar-toggler button-collapse waves-effect waves-light"
                    type="button" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="Toggle navigation"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fa fa-power-off"></i>
            </button>
            @endguest

            <!-- Links -->
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <!-- Left -->
                <ul class="navbar-nav mr-auto">
                    @guest
                    @else
                        <li class="nav-item active">
                            <a class="toggle-sidebar rounded button-collapse nav-link waves-effect
                                      waves-light px-3 mr-2">
                                <i class="far fa-window-restore" aria-hidden="true"></i>
                                <span class="sr-only">(current)</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link waves-effect waves-light" href="{{ url('/') }}"
                               id="sidebarCollapse">
                                <i class="fa fa-tachometer-alt" aria-hidden="true"></i> Dashboard
                                <span class="sr-only">(current)</span>
                            </a>
                        </li>
                    @endguest
                </ul>

                <!-- Right -->
                <ul class="navbar-nav ml-auto nav-flex-icons">
                    @guest
                    @else
                    <a id="datetime" class="nav-link waves-effect waves-light white-text"></a>
                    <a onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                       class="nav-link waves-effect waves-light white-text">
                        <i class="fa fa-power-off"></i>
                    </a>
                    @endguest
                </ul>
            </div>
        <!--</div>-->
    </nav>
    <!-- Navbar -->

    @guest
        <!-- Login content -->
        @yield('login-content')
    @else
        <!-- Main content -->
        <div id="module-body" class="container-fluid mt-4">
            <div class="page-wrapper default-theme sidebar-bg">
                @include('layouts.partials.nav')
                <main class="page-content pt-5">
                    <div id="overlay" class="overlay"></div>
                    @yield('main-content')
                </main>
            </div>
            <!-- page-wrapper -->
        </div>
    @endif

    <!-- Footer -->
    <footer class="page-footer font-small navbar-dark">
        <!-- Copyright -->
        <div class="footer-copyright text-center py-2 text-white">
            <small>© Department of Science & Technology - Cordillera Administrative Region All Rights Reserved {{ date('Y') }}</small>
        </div>
        <!-- Copyright -->
    </footer>

    <!-- Scripts -->

    <!-- JQuery -->
    <script type="text/javascript" src="{{ asset('plugins/mdb/js/jquery.min.js') }}"></script>
    <!-- Bootstrap tooltips -->
    <script type="text/javascript" src="{{ asset('plugins/mdb/js/popper.min.js') }}"></script>
    <!-- Bootstrap core JavaScript -->
    <script type="text/javascript" src="{{ asset('plugins/mdb/js/bootstrap.min.js') }}"></script>
    <!-- MDB core JavaScript -->
    <script type="text/javascript" src="{{ asset('plugins/mdb/js/mdb.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('plugins/mdb/js/bootstrap-select.min.js') }}"></script>
    <!-- Font awesome scripts -->
    <script type="text/javascript" src="{{ asset('plugins/fa/js/all.min.js') }}"></script>
    <!-- Moment scripts -->
    <script type="text/javascript" src="{{ asset('plugins/moment/moment-with-locales.min.js') }}"></script>

    <script type="text/javascript" src="{{ asset('plugins/sidebar/js/jquery.mCustomScrollbar.concat.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('plugins/sidebar/js/sidebar-main.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/notification.js') }}"></script>

    <script type="text/javascript">
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
    </script>

    @yield('custom-js')

</body>
</html>
