@extends('layouts.app')

@section('main-content')

<div class="row wow animated fadeIn">
    <section class="mb-5 col-12">
        <div class="card mdb-color lighten-5">
            <div class="card-body">
                <h5 class="card-title">
                    <strong>
                        <i class="fa fa-tachometer-alt"></i> DASHBOARD
                    </strong>
                </h5>

                <hr>

                <!-- Procurement -->
                <div class="row">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card">
                            <div class="row mt-3">
                                <div class="col-md-4 col-5 text-left pl-4">
                                    <a type="button" class="btn btn-grey btn-lg py-4 waves-effect waves-light"
                                       href="{{ url('procurement/pr?filter=1') }}">
                                        <i class="fas fa-spinner fa-3x fa-spin"></i>
                                    </a>
                                </div>
                                <div class="col-md-8 col-7 text-right pr-5">
                                    <h3 class="ml-4 mt-4 mb-2 font-weight-bold">
                                        {{ $pr->total_pending }}
                                    </h3>
                                    <p class="font-small font-weight-bold grey-text">
                                        Pending PR
                                    </p>
                                </div>
                            </div>

                            <div class="row my-3">
                                <div class="col-md-7 col-7 text-left pl-4">
                                    <p class="font-small dark-grey-text font-up ml-4 font-weight-bold">
                                        &nbsp;
                                    </p>
                                </div>
                                <div class="col-md-5 col-5 text-right pr-5">
                                    <p class="font-small grey-text">
                                        &nbsp;
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card">
                            <div class="row mt-3">
                                <div class="col-md-5 col-5 text-left pl-4">
                                    <a type="button" class="btn btn-success btn-lg py-4 waves-effect waves-light"
                                       href="{{ url('procurement/pr') }}">
                                        <i class="fas fa-thumbs-up fa-3x"></i>
                                    </a>
                                </div>
                                <div class="col-md-7 col-7 text-right pr-5">
                                    <h3 class="ml-4 mt-4 mb-2 font-weight-bold">
                                        {{ $pr->total_approved }}
                                    </h3>
                                    <p class="font-small font-weight-bold grey-text">
                                        Approved PR
                                    </p>
                                </div>
                            </div>
                            <div class="row my-3">
                                <div class="col-md-7 col-7 text-left pl-4">
                                    <p class="font-small dark-grey-text font-up ml-4 font-weight-bold">
                                        &nbsp;
                                    </p>
                                </div>
                                <div class="col-md-5 col-5 text-right pr-5">
                                    <p class="font-small grey-text">
                                        &nbsp;
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card">
                            <div class="row mt-3">
                                <div class="col-md-5 col-5 text-left pl-4">
                                    <a type="button" class="btn btn-info btn-lg py-4 waves-effect waves-light">
                                        <i class="fas fa-truck fa-3x"></i>
                                    </a>
                                </div>
                                <div class="col-md-7 col-7 text-right pr-5">
                                    <h3 class="ml-4 mt-4 mb-2 font-weight-bold">
                                        {{ $pr->total_for_delivery }}
                                    </h3>
                                    <p class="font-small font-weight-bold grey-text">
                                        For Delivery
                                    </p>
                                </div>
                            </div>
                            <div class="row my-3">
                                <div class="col-md-7 col-7 text-left pl-4">
                                    <p class="font-small dark-grey-text font-up ml-4 font-weight-bold">
                                        &nbsp;
                                    </p>
                                </div>
                                <div class="col-md-5 col-5 text-right pr-5">
                                    <p class="font-small grey-text">
                                        &nbsp;
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card">
                            <div class="row mt-3">
                                <div class="col-md-5 col-5 text-left pl-4">
                                    <a type="button" class="btn btn-light-green btn-lg py-4 waves-effect waves-light">
                                        <i class="fas fa-truck-loading fa-3x"></i>
                                    </a>
                                </div>

                                <div class="col-md-7 col-7 text-right pr-5">
                                    <h3 class="ml-4 mt-4 mb-2 font-weight-bold">
                                        {{ $pr->total_delivered }}
                                    </h3>
                                    <p class="font-small font-weight-bold grey-text">
                                        Delivered
                                    </p>
                                </div>
                            </div>
                            <div class="row my-3">
                                <div class="col-md-7 col-7 text-left pl-4">
                                    <p class="font-small dark-grey-text font-up ml-4 font-weight-bold">
                                        &nbsp;
                                    </p>
                                </div>
                                <div class="col-md-5 col-5 text-right pr-5">
                                    <p class="font-small grey-text">
                                        &nbsp;
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="row">
                        <div class="col-xl-5 col-md-12 mr-0">
                            <div class="card-body card-body-cascade pb-0">
                                <div class="row card-body pt-3">
                                    <div class="col-md-12">
                                        <p>
                                            Hello <strong>{{ Auth::user()->firstname }}</strong>,<br>
                                            Dashboard is currently under construction...
                                        </p>

                                        <!-- Search form -->
                                        <form id="form-track" class="form-inline md-form form-sm mt-0" method="GET"
                                              target="_blank" action="#">
                                            <i class="fas fa-search" aria-hidden="true"></i>
                                            <input id="track-pr" class="form-control form-control-sm ml-3 w-75"
                                                   type="text" placeholder="Enter your PR number to track."
                                                   aria-label="Search">
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Procurement -->

            </div>
        </div>
    </section>
</div>

@endsection

@section('custom-js')

<script>
    $(function() {
        $('#track-pr').change(function() {
            var trackValue = $(this).val();

            $('#form-track').attr('action', "{{ url('procurement/pr/tracker') }}/" + trackValue)
                            .submit();
        });
    });
</script>

@endsection
