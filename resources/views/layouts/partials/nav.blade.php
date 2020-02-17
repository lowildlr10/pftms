<nav id="sidebar" class="sidebar-wrapper">
    <div class="sidebar-content special-color-dark">
        <!-- sidebar-brand  -->
        <div class="sidebar-item sidebar-brand">
            <a href="{{ url('/') }}">
                <img src="{{ asset('images/logo/pftms-logo.jpg') }}" alt="brand-logo" width="20px">
                P-F-T-M-S
            </a>
            <a href="#" class="toggle-sidebar text-right">
                <small>
                    <i class="fas fa-angle-double-left"></i> Hide
                </small>
            </a>
        </div>
        <!-- sidebar-header  -->
        <div class="sidebar-item sidebar-header d-flex flex-nowrap">
            <div class="user-pic">
                <a href="{{ url('profile') }}">
                @if (Auth::user()->gender == 'male' && empty(Auth::user()->avatar))
                    <img class="img-responsive img-rounded" alt="avatar"
                         src="{{ asset('images/avatar/male.png') }}">
                @elseif (Auth::user()->gender == 'female' && empty(Auth::user()->avatar))
                    <img class="img-responsive img-rounded" alt="avatar"
                         src="{{ asset('images/avatar/female.png') }}" alt="avatar">
                @else
                    @if (!empty(Auth::user()->avatar))
                    <img class="img-responsive img-rounded" alt="avatar"
                         src="{{ url(Auth::user()->avatar) }}" alt="avatar">
                    @endif
                @endif
                </a>
            </div>
            <div class="user-info">
                <span class="user-name">{{ Auth::user()->firstname }}
                    <strong>{{ Auth::user()->lastname }}</strong>
                </span>
                <span class="user-role">{{ Auth::user()->position }}</span>
                <span class="user-status">
                    <i class="fa fa-circle green-text" style="animation: spinner-grow 2s linear infinite;"></i>
                    <span>Online</span>
                </span>
            </div>
        </div>
        <!-- sidebar-search  -->
        <div class="sidebar-item sidebar-search">
            <div>
                <div class="input-group">
                    <input type="text" class="form-control search-menu" placeholder="Search...">
                    <div class="input-group-append">
                        <span class="input-group-text">
                            <i class="fa fa-search" aria-hidden="true"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <!-- sidebar-menu  -->
        <div class=" sidebar-item sidebar-menu">
            <ul>
                <li class="header-menu">
                    <span>General</span>
                </li>
                <li>
                    <a href="{{ url('/') }}">
                        <i class="fa fa-tachometer-alt"></i>
                        <span class="menu-text">Dashboard</span>
                    </a>
                </li>
                <li class="header-menu">
                    <span>Modules</span>
                </li>
                <li class="sidebar-dropdown">
                    <a href="#">
                        <i class="fas fa-clipboard-list"></i>
                        <span class="menu-text">PPMP</span>
                    </a>
                    <div class="sidebar-submenu">
                        <ul>
                            <li>
                                <a href="#"> --
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="sidebar-dropdown">
                    <a href="#">
                        <i class="fas fa-money-bill-wave-alt"></i>
                        <span class="menu-text">Cash Adv., Reim., & Liqui...</span>
                    </a>
                    <div class="sidebar-submenu">
                        <ul>
                            <li>
                                <a href="{{ url('cadv-reim-liquidation/ors-burs') }}" class="waves-effect">ORS & BURS</a>
                            </li>
                            <li>
                                <a href="{{ url('cadv-reim-liquidation/dv') }}" class="waves-effect">Disbursement Voucher</a>
                            </li>
                            <li>
                                <a href="{{ url('cadv-reim-liquidation/liquidation') }}" class="waves-effect">Liquidation Report</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="sidebar-dropdown">
                    <a href="#">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="menu-text">Procurement</span>
                    </a>
                    <div class="sidebar-submenu">
                        <ul>
                            <li>
                                <a href="{{ url('procurement/pr') }}" class="waves-effect">Purchase Request</a>
                            </li>
                            <li>
                                <a href="{{ url('procurement/rfq') }}" class="waves-effect">Request for Quotations</a>
                            </li>
                            <li>
                                <a href="{{ url('procurement/abstract') }}" class="waves-effect">Abstract of Bids & Quotations</a>
                            </li>
                            <li>
                                <a href="{{ url('procurement/po-jo') }}" class="waves-effect">Purchase & Job Order</a>
                            </li>
                            <li>
                                <a href="{{ url('procurement/ors-burs') }}" class="waves-effect">ORS & BURS</a>
                            </li>
                            <li>
                                <a href="{{ url('procurement/iar') }}" class="waves-effect">Inspection & Acceptance Report</a>
                            </li>
                            <li>
                                <a href="{{ url('procurement/dv') }}" class="waves-effect">Disbursement Voucher</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="sidebar-dropdown">
                    <a href="#">
                        <i class="fas fa-money-check-alt"></i>
                        <span class="menu-text">Payment</span>
                    </a>
                    <div class="sidebar-submenu">
                        <ul>
                            <li>
                                <a href="{{ url('payment/lddap') }}" class="waves-effect">LLDAP</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="sidebar-dropdown">
                    <a href="#">
                        <i class="fas fa-hand-holding-usd"></i>
                        <span class="menu-text">Fund Utilization</span>
                    </a>
                    <div class="sidebar-submenu">
                        <ul>
                            <li>
                                <a href="#" class="waves-effect">--</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="sidebar-dropdown">
                    <a href="#">
                        <i class="fas fa-box"></i>
                        <span class="menu-text">Inventory</span>
                    </a>
                    <div class="sidebar-submenu">
                        <ul>
                            <li>
                                <a href="{{ url('inventory/stocks') }}" class="waves-effect">Stocks</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="sidebar-dropdown">
                    <a href="#">
                        <i class="fas fa-cogs"></i>
                        <span class="menu-text">Maintenance</span>
                    </a>
                    <div class="sidebar-submenu">
                        <ul>
                            <li>
                                <a href="#" class="waves-effect">--</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="sidebar-dropdown">
                    <a href="#">
                        <i class="fas fa-trash"></i>
                        <span class="menu-text">Disposal</span>
                    </a>
                    <div class="sidebar-submenu">
                        <ul>
                            <li>
                                <a href="{{ url('payment/lddap') }}" class="waves-effect">LLDAP</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="sidebar-dropdown">
                    <a href="#">
                        <i class="far fa-copy"></i>
                        <span class="menu-text">Reports</span>
                    </a>
                    <div class="sidebar-submenu">
                        <ul>
                            <li>
                                <a href="#" class="waves-effect">PMF</a>
                            </li>
                            <li>
                                <a href="#" class="waves-effect">Inventory on Supply</a>
                            </li>
                            <li>
                                <a href="#" class="waves-effect">PCPPE</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="sidebar-dropdown">
                    <a href="#">
                        <i class="fas fa-chart-pie"></i>
                        <span class="menu-text">Statistics</span>
                    </a>
                    <div class="sidebar-submenu">
                        <ul>
                            <li>
                                <a href="#" class="waves-effect">User</a>
                            </li>
                            <li>
                                <a href="#" class="waves-effect">Procurement</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="sidebar-dropdown">
                    <a href="#">
                        <i class="fas fa-chalkboard"></i>
                        <span class="menu-text">Voucher Tracking</span>
                    </a>
                    <div class="sidebar-submenu">
                        <ul>
                            <li>
                                <a href="{{ url('voucher-tracking/pr-rfq') }}" class="waves-effect">PR => RFQ</a>
                            </li>
                            <li>
                                <a href="{{ url('voucher-tracking/rfq-abstract') }}" class="waves-effect">RFQ => Abstract</a>
                            </li>
                            <li>
                                <a href="{{ url('voucher-tracking/abstract-po') }}" class="waves-effect">Abstract => PO/JO</a>
                            </li>
                            <li>
                                <a href="{{ url('voucher-tracking/po-ors') }}" class="waves-effect">PO/JO => ORS/BURS</a>
                            </li>
                            <li>
                                <a href="{{ url('voucher-tracking/po-iar') }}" class="waves-effect">PO/JO => IAR</a>
                            </li>
                            <li>
                                <a href="{{ url('voucher-tracking/iar-stock') }}" class="waves-effect">IAR => PAR/RIS/ICS</a>
                            </li>
                            <li>
                                <a href="{{ url('voucher-tracking/iar-dv') }}" class="waves-effect">IAR => DV</a>
                            </li>
                            <li>
                                <a href="{{ url('voucher-tracking/ors-dv') }}" class="waves-effect">ORS/BURS => DV</a>
                            </li>
                            <li>
                                <a href="{{ url('voucher-tracking/dv-disburse') }}" class="waves-effect">DV => Disburse/LDDAP</a>
                            </li>
                            <li>
                                <a href="{{ url('voucher-tracking/disburse-summary') }}" class="waves-effect">Disburse => Summary</a>
                            </li>
                            <li>
                                <a href="{{ url('voucher-tracking/summary-bank') }}" class="waves-effect">Summary => Bank</a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="header-menu">
                    <span>Extras</span>
                </li>
                <li class="sidebar-dropdown">
                    <a href="#">
                        <i class="fas fa-book"></i>
                        <span class="menu-text">Libraries</span>
                    </a>
                    <div class="sidebar-submenu">
                        <ul>
                            <li>
                                <a href="{{ url('libraries/divisions') }}" class="waves-effect">Divisions</a>
                            </li>
                            <li>
                                <a href="{{ url('libraries/item-classification') }}" class="waves-effect">Item Classifications</a>
                            </li>
                            <!--
                            <li>
                                <a href="{{ url('libraries/modes-procurement') }}" class="waves-effect">Modes of Procurement</a>
                            </li>
                            -->
                            <li>
                                <a href="{{ url('libraries/status') }}" class="waves-effect">Procurement Status</a>
                            </li>
                            <li>
                                <a href="{{ url('libraries/projects') }}" class="waves-effect">Projects/Charging</a>
                            </li>
                            <li>
                                <a href="{{ url('libraries/signatories') }}" class="waves-effect">Signatories</a>
                            </li>
                            <li>
                                <a href="{{ url('libraries/supplier-classification') }}" class="waves-effect">Supplier Classifications</a>
                            </li>
                            <li>
                                <a href="{{ url('libraries/suppliers') }}" class="waves-effect">Suppliers</a>
                            </li>
                            <li>
                                <a href="{{ url('libraries/unit-issue') }}" class="waves-effect">Unit of Issues</a>
                            </li>
                            <li>
                                <a href="{{ url('libraries/accounts') }}" class="waves-effect">User Accounts</a>
                            </li>
                            <li>
                                <a href="{{ url('libraries/user-groups') }}" class="waves-effect">User Groups</a>
                            </li>
                        </ul>
                    </div>
                </li>
            </ul>
        </div>
        <!-- sidebar-menu  -->
    </div>
    <!-- sidebar-footer  -->
    <div class="sidebar-footer stylish-color-dark">
        <div class="dropdown">
            <a href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
               onclick="$(this).displayNotifications();">
                <i class="fa fa-bell"></i>
                @if (count(Auth::user()->unreadNotifications) > 0)
                <span class="badge badge-pill badge-danger notification">
                    {{ count(Auth::user()->unreadNotifications) > 99 ? '99+' :
                       count(Auth::user()->unreadNotifications) }}
                </span>
                @endif
            </a>
            <div class="dropdown-menu notifications" aria-labelledby="dropdownMenuMessage">
                <div class="notifications-header">
                    <i class="fa fa-bell"></i>
                    Notifications
                </div>
                <div class="dropdown-divider"></div>
                <!-- Notification content -->
                <div id="notif-body">
                    @if (count(Auth::user()->unreadNotifications) > 0)
                        @foreach(Auth::user()->unreadNotifications as $notification)
                    <a class="dropdown-item" href="#">
                        <div class="notification-content">
                            <div class="icon">
                                <i class="far fa-file text-info border border-info"></i>
                            </div>
                            <div class="content">
                                <div class="notification-detail text-wrap">
                                    {!! $notification->data['msg'] !!}
                                </div>
                                <div class="notification-time">
                                    <i class="far fa-calendar-alt"></i> {{ $notification->created_at }}
                                </div>
                            </div>
                        </div>
                    </a>
                        @endforeach
                    @else
                    <a class="dropdown-item" href="#">
                        <div class="notification-content">
                            <div class="icon">

                            </div>
                            <div class="content">
                                <div class="notification-detail">
                                    No Notification
                                </div>
                            </div>
                        </div>
                    </a>
                    @endif
                </div>
                <!-- End notification content -->
                <div class="dropdown-divider"></div>
                <a class="dropdown-item text-center" href="{{ url('notification/show-all') }}">
                    View all notifications
                </a>
            </div>
        </div>
        <div class="dropdown">
            <a href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa fa-envelope"></i>
                <!--<span class="badge badge-pill badge-success notification">7</span>-->
            </a>
            <div class="dropdown-menu messages" aria-labelledby="dropdownMenuMessage">
                <div class="messages-header">
                    <i class="fa fa-envelope"></i>
                    Messages
                </div>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="#">
                    <div class="message-content">
                        <div class="pic">
                            <img src="#" alt="">
                        </div>
                        <div class="content">
                            <div class="message-title">
                                <strong> --</strong>
                            </div>
                            <div class="message-detail">No new message.</div>
                        </div>
                    </div>
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item text-center" href="#">View all messages</a>
            </div>
        </div>
        <div class="dropdown">
            <a href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa fa-cog"></i>
                <!--<span class="badge-sonar"></span>-->
            </a>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuMessage">
                <a class="dropdown-item" href="{{ url('profile') }}">My profile</a>
                <a class="dropdown-item" href="#">About</a>
                <a class="dropdown-item" href="#">Setting</a>
            </div>
        </div>
        <div>
            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fa fa-power-off"></i>
            </a>
        </div>
        <div class="pinned-footer">
            <a href="#">
                <i class="fas fa-ellipsis-h"></i>
            </a>
        </div>
    </div>
</nav>
