@extends('layouts.app')

@section('custom-css')

<style>
    #vouchers {padding-top:50px;height:500px;color: #fff; background-color: #1E88E5;}
    #procurement {padding-top:50px;height:500px;color: #fff; background-color: #673ab7;}
</style>

@endsection

@section('main-content')

<div class="row wow animated fadeIn">
    <div class="col-md-12">
        <div class="card" style="background: #00000036;">
            <div class="card-body">
                <ul class="nav md-pills nav-justified pills-blue" style="background: #00000045;">
                    <li class="nav-item">
                        <a class="nav-link white-text active" data-toggle="tab" href="#dashboard-1" role="tab" aria-selected="true"
                           onclick="$(this).initDashboard('dashboard-1');">
                            Procurement
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link white-text" data-toggle="tab" href="#dashboard-2" role="tab" aria-selected="false"
                           onclick="$(this).initDashboard('dashboard-2');">
                            Cash Advance, Reimbursement, & Liquidation
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link white-text" data-toggle="tab" href="#dashboard-3" role="tab" aria-selected="false"
                           onclick="$(this).initDashboard('dashboard-3');">
                            Payment
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link white-text" data-toggle="tab" href="#dashboard-4" role="tab" aria-selected="false"
                           onclick="$(this).initDashboard('dashboard-4');">
                            Fund Utilization
                        </a>
                    </li>
                </ul>

                <!-- Tab panels -->
                <div class="tab-content">

                    <!--Panel 1-->
                    <div class="tab-pane fade in active show" id="dashboard-1" role="tabpanel">
                        <div class="dashboard-body" style="display: none;"></div>
                    </div>
                    <!--/.Panel 1-->

                    <!--Panel 2-->
                    <div class="tab-pane fade" id="dashboard-2" role="tabpanel">
                        <div class="dashboard-body" style="display: none;"></div>
                    </div>
                    <!--/.Panel 2-->

                    <!--Panel 3-->
                    <div class="tab-pane fade" id="dashboard-3" role="tabpanel">
                        <div class="dashboard-body" style="display: none;"></div>
                    </div>
                    <!--/.Panel 3-->

                    <!--Panel 4-->
                    <div class="tab-pane fade" id="dashboard-4" role="tabpanel">
                        <div class="dashboard-body" style="display: none;"></div>
                    </div>
                    <!--/.Panel 4-->

                </div>
            </div>
        </div>
    </div>
</div>

@include('modals.search-post')

@endsection

@section('custom-js')

<script>
    let chart1, chart2, _chart1, _chart2;

    $(function() {
        $('#track-pr').change(function() {
            const trackValue = $(this).val();

            $('#form-track').attr('action', "{{ url('procurement/pr/tracker') }}/" + trackValue)
                            .submit();
        });
    });
</script>

@if (Session::has('login_msg'))
    <div class="modal fade right" id="login-msg" tabindex="-1" role="dialog"
         data-backdrop="true">
        <div class="modal-dialog modal-side modal-top-right modal-notify modal-info" role="document">
            <!--Content-->
            <div class="modal-content">
                <!--Header-->
                <div class="modal-header">
                    <p class="heading">
                        <i class="fas fa-envelope"></i> System Message
                    </p>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="white-text">&times;</span>
                    </button>
                </div>

                <!--Body-->
                <div class="modal-body">
                    <div class="row">
                        <div class="col-5">
                            @if (session('user_gender') == 'male' && empty(session('user_avatar')))
                            <img width="150" class="rounded-circle img-fluid z-depth-2" alt="" alt="avatar"
                                src="{{ asset('images/avatar/male.png') }}" style="width: 120px;">
                            @elseif (session('user_gender') == 'female' && empty(session('user_avatar')))
                            <img width="150" class="rounded-circle img-fluid z-depth-2" alt="" alt="avatar"
                                src="{{ asset('images/avatar/female.png') }}" alt="avatar" style="width: 120px;">
                            @else
                                @if (!empty(session('user_avatar')))
                            <img width="150" class="rounded-circle img-fluid z-depth-2" alt="avatar"
                                src="{{ url(session('user_avatar')) }}" alt="avatar" style="width: 120px;">
                                @endif
                            @endif
                        </div>

                        <div class="col-7">
                            <p>{!! session('login_msg') !!}</p>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-light btn-block" data-dismiss="modal">
                        Close
                    </button>
                </div>
            </div>
            <!--/.Content-->
        </div>
    </div>
    <script type="text/javascript">
        $(function() {
            $('#login-msg').modal();
        });
    </script>
@endif

<script type="text/javascript" src="{{ asset('assets/js/dashboard.js') }}"></script>

@endsection
