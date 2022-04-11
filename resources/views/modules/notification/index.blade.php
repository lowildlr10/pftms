@extends('layouts.app')

@section('main-content')

<div class="row wow animated fadeIn">
    <div class="container">
        <section class="mb-5 col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        <strong>
                            <i class="fas fa-bell"></i> Your Notifications
                        </strong>
                    </h5>
                    <hr>
                    <div class="card p-5">
                        @if (count(Auth::user()->notifications) > 0)
                            @foreach(Auth::user()->notifications as $notification)
                                @if (isset($notification->data['sub_module']))
                        <a onclick="$(this).redirectToDoc('{{ route($notification->data['sub_module']) }}',
                        '{{ $notification->data['id'] }}');" class="dropdown-item">
                            <div class="notification-content">
                                <div class="icon font-weight-bolder pb-3">
                                    @if ($notification->data['module'] == 'cash_advance')
                                    <i class="fas fa-money-bill-wave-alt mdb-color-text"></i> CASH ADVANCE, REIMBURSEMENT, AND LIQUIDATION REPORT
                                    @elseif ($notification->data['module'] == 'procurement')
                                    <i class="fas fa-shopping-cart mdb-color-text"></i> PROCUREMENT
                                    @elseif ($notification->data['module'] == 'payment')
                                    <i class="fas fa-money-check-alt mdb-color-text"></i> PAYMENT
                                    @elseif ($notification->data['module'] == 'inventory')
                                    <i class="fas fa-box mdb-color-text"></i> INVENTORY
                                    @elseif ($notification->data['module'] == 'account_management')
                                    <i class="fas fa-user mdb-color-text"></i> ACCOUNT MANAGEMENT
                                    @endif
                                </div>
                                <div class="content">
                                    <div class="notification-detail text-wrap">
                                        @if ($notification->read_at)

                                        @endif
                                        {!! $notification->data['msg'] !!}
                                    </div>
                                    <div class="notification-time">
                                        <i class="far fa-calendar-alt"></i> {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                                    </div>
                                </div>
                            </div>
                        </a>
                                @else
                    {{--
                                    @if ($notification->data['type'] == 'message')
                    <a onclick="$(this).redirectToDoc('{{ route($notification->data['module']) }}',
                       '{{ $notification->data['ors_id'] }}'); $(this).setAsReadNotification('{{ $notification->id }}');"
                       class="dropdown-item">
                        <div class="notification-content">
                            <div class="icon font-weight-bolder pb-3">
                                <i class="fa fa-envelope"></i> MESSAGE
                            </div>
                            <div class="content">
                                <div class="notification-detail text-wrap">
                                    {!! $notification->data['msg'] !!}
                                </div>
                                <div class="notification-time">
                                    <i class="far fa-calendar-alt"></i> {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                                </div>
                            </div>
                        </div>
                    </a>
                                    @endif
                    --}}
                                @endif
                        <hr>
                            @endforeach
                        <a class="dropdown-item text-center" href="#">
                            -- End of Notification --
                        </a>
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
                </div>
            </div>
        </section>
    </div>
</div>

@include('modals.search')

@endsection

@section('custom-js')

<script type="text/javascript" src="{{ asset('assets/js/notification.js') }}"></script>

@endsection
