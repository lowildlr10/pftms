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
<body class="m-0" style="background: rgb(55 78 96);">
    <div id="mdb-preloader" class="flex-center preloader">
        <div class="spinner-grow text-light fast" style="width: 3rem; height: 3rem;" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

    <!-- Navbar -->
    <nav id="system-top-nav" class="navbar navbar-expand-lg navbar-dark fixed-top top-nav-collapse"> <!-- scrolling-navbar -->
        <!--<div class="container-fluid">-->

            <!-- Brand -->
            @guest
            <a class="navbar-brand nav-link waves-effect waves-light" href="{{ url('/') }}">
                <img class="d-inline-block align-top" src="{{ asset('images/logo/pftms-logo-v2-short.svg') }}"
                     height="30" alt="pfms logo">
                <strong class="d-none show-xs mt-2" style="font-size: 7pt;">
                    Procurement & Financial Transaction<br>Management System v3
                </strong>
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
                                      waves-light px-2 mr-2">
                                <i class="fa fa-bars fa-lg" aria-hidden="true"></i>
                                <span class="sr-only">(current)</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link waves-effect waves-light" href="{{ url('/') }}"
                               id="sidebarCollapse">
                                <i class="fa fa-tachometer-alt fa-lg" aria-hidden="true"></i>&nbsp;&nbsp;Dashboard
                                <span class="sr-only">(current)</span>
                            </a>
                        </li>
                    @endguest
                </ul>

                <!-- Right -->
                <ul class="navbar-nav ml-auto nav-flex-icons">
                    @guest
                    @else
                    {{--
                    <a id="datetime" class="nav-link waves-effect waves-light white-text"></a>
                    --}}
                    <li class="nav-item">
                        <a class="nav-link waves-effect waves-light white-text px-2 mr-2" target="_blank"
                           href="https://drive.google.com/file/d/1_MPlkJelVDM8ErQpNmSq4ktvjph2oe7q/view?usp=sharing">
                            <i class="fas fa-file-pdf"></i> User Manual
                        </a>
                    </li>
                    <li class="nav-item ml-2">
                        <a onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                        class="nav-link waves-effect waves-light white-text">
                            <i class="fa fa-power-off fa-lg"></i>
                        </a>
                    </li>
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
                @include('partials.nav')
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
    <script type="text/javascript" src="{{ asset('plugins/jquery-ui/jquery-ui.min.js') }}"></script>

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
        const baseURL = "{{ url('/') }}/";

        function empty(data) {
            let count = 0;

            if (typeof(data) == 'number' || typeof(data) == 'boolean') {
                return false;
            }

            if (typeof(data) == 'undefined' || data === null) {
                return true;
            }

            if (typeof(data.length) != 'undefined') {
                return data.length == 0;
            }

            for (let i in data) {
                if (data.hasOwnProperty(i)) {
                    count ++;
                }
            }

            return count == 0;
        }

        $(function() {
            let datetime = null,
                    date = null;
            const dateTimeIco = '<i class="fas fa-clock"></i> ';
            const update = function () {
                date = moment(new Date())
                datetime.html(dateTimeIco + date.format('MMMM D, YYYY HH:mm:ss'));
            };

            $(document).ready(function(){
                datetime = $('#datetime')
                update();
                setInterval(update, 1000);
            });
            $('.preloader').fadeOut();

            $.fn.redirectToDoc = function(url, keyword) {
                $('#keyword').val(keyword);
                $('#search-keyword').attr('action', url).submit();
            }
        });
    </script>

    @yield('custom-js')

</body>
</html>
