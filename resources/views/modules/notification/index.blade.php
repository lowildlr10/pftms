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
                                @switch($notification->data['module'])
                                    @case('proc-pr')
                        <a onclick="$(this).redirectToDoc('{{ route('pr') }}', '{{ $notification->data['pr_id'] }}');"
                           class="dropdown-item">
                            <div class="notification-content">
                                <div class="content">
                                    <div class="notification-detail">
                                        <i class="far fa-file text-info border border-info"></i>
                                        {!! str_replace('<br>', ' ', $notification->data['msg']) !!}
                                    </div>
                                    <div class="notification-time">
                                        <i class="far fa-calendar-alt"></i> {{ $notification->created_at }}
                                        <small class="grey-text"><em>...Procurement</em></small>
                                    </div>
                                </div>
                            </div>
                        </a>
                                        @break
                                    @case('proc-rfq')
                        <a onclick="$(this).redirectToDoc('{{ route('rfq') }}', '{{ $notification->data['rfq_id'] }}');"
                           class="dropdown-item">
                            <div class="notification-content">
                                <div class="content">
                                    <div class="notification-detail">
                                        <i class="far fa-file text-info border border-info"></i>
                                        {!! str_replace('<br>', ' ', $notification->data['msg']) !!}
                                    </div>
                                    <div class="notification-time">
                                        <i class="far fa-calendar-alt"></i> {{ $notification->created_at }}
                                        <small class="grey-text"><em>...Procurement</em></small>
                                    </div>
                                </div>
                            </div>
                        </a>
                                        @break

                                    @case('proc-abs')
                        <a onclick="$(this).redirectToDoc('{{ route('abstract') }}', '{{ $notification->data['abs_id'] }}');"
                           class="dropdown-item">
                            <div class="notification-content">
                                <div class="content">
                                    <div class="notification-detail">
                                        <i class="far fa-file text-info border border-info"></i>
                                        {!! str_replace('<br>', ' ', $notification->data['msg']) !!}
                                    </div>
                                    <div class="notification-time">
                                        <i class="far fa-calendar-alt"></i> {{ $notification->created_at }}
                                        <small class="grey-text"><em>...Procurement</em></small>
                                    </div>
                                </div>
                            </div>
                        </a>
                                        @break

                                    @case('proc-po-jo')

                                        @break

                                    @case('proc-ors-burs')
                        <a onclick="$(this).redirectToDoc('{{ route('proc-ors-burs') }}', '{{ $notification->data['ors_id'] }}');"
                           class="dropdown-item">
                            <div class="notification-content">
                                <div class="content">
                                    <div class="notification-detail">
                                        <i class="far fa-file text-info border border-info"></i>
                                        {!! str_replace('<br>', ' ', $notification->data['msg']) !!}
                                    </div>
                                    <div class="notification-time">
                                        <i class="far fa-calendar-alt"></i> {{ $notification->created_at }}
                                        <small class="grey-text"><em>...Procurement</em></small>
                                    </div>
                                </div>
                            </div>
                        </a>
                                        @break
                                    @default

                                @endswitch
                        <div class="dropdown-divider"></div>
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
