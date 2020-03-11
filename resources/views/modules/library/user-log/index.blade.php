@extends('layouts.app')

@section('custom-css')

<!-- DataTables CSS -->
<link rel="stylesheet" type="text/css" href="{{ asset('plugins/mdb/css/addons/datatables.min.css') }}" rel="stylesheet">

<!-- DataTables Select CSS -->
<link rel="stylesheet" type="text/css" href="{{ asset('plugins/mdb/css/addons/datatables-select.min.css') }}" rel="stylesheet">

@endsection

@section('main-content')

<div class="row wow animated fadeIn">
    <section class="mb-5 col-12 module-container">
        <div class="card mdb-color darken-3">
            <div class="card-body">
                <h5 class="card-title white-text">
                    <strong>
                        <i class="fas fa-users-cog"></i> Account Management: User Logs
                    </strong>
                </h5>
                <hr class="white">
                <ul class="breadcrumb mdb-color darken-3 mb-0 p-1 white-text">
                    <li>
                        <i class="fa fa-caret-right mx-2" aria-hidden="true"></i>
                    </li>
                    <li class="active">
                        <a href="{{ route('emp-log') }}" class="waves-effect waves-light cyan-text">
                            User Logs
                        </a>
                    </li>
                </ul>

                <!-- Table with panel -->
                <div class="card card-cascade narrower">

                    <!--Card image-->
                    <div class="gradient-card-header unique-color
                                narrower py-2 px-2 mb-1 d-flex justify-content-between
                                align-items-center">
                        <div>
                            <button class="btn btn-outline-white btn-rounded btn-sm px-2"
                                    data-target="#top-fluid-modal" data-toggle="modal">
                                <i class="fas fa-search"></i>
                            </button>
                            <a href="{{ route('emp-log') }}" class="btn btn-outline-white btn-rounded btn-sm px-2">
                                <i class="fas fa-sync-alt fa-pulse"></i>
                            </a>

                        </div>
                    </div>
                    <!--/Card image-->

                    <div class="px-2">
                        <div class="table-wrapper table-responsive border rounded">

                            <!--Table-->
                            <table id="dtmaterial" class="table table-hover" cellspacing="0" width="100%">

                                <!--Table head-->
                                <thead class="mdb-color darken-3 white-text">
                                    <tr>
                                        <th class="th-md" width="3%"></th>
                                        <th class="th-md" width="15%">
                                            <strong>Employee</strong>
                                        </th>
                                        <th class="th-md" width="20%">
                                            <strong>Message</strong>
                                        </th>
                                        <th class="th-md" width="13%">
                                            <strong>Action</strong>
                                        </th>
                                        <th class="th-md" width="9%">
                                            <strong>Method</strong>
                                        </th>
                                        <th class="th-md" width="9%">
                                            <strong>IP</strong>
                                        </th>
                                        <th class="th-md" width="9%">
                                            <strong>Browser</strong>
                                        </th>
                                        <th class="th-md" width="9%">
                                            <strong>OS</strong>
                                        </th>
                                        <th class="th-md" width="10%">
                                            <strong>Logged At</strong>
                                        </th>
                                        <th class="th-md" width="3%"></th>
                                    </tr>
                                </thead>
                                <!--Table head-->

                                <!--Table body-->
                                <tbody>
                                    @if (count($list) > 0)
                                        @foreach ($list as $listCtr => $log)
                                    <tr>
                                        <td></td>
                                        <td>{{ $log->name }}</td>
                                        <td>{{ $log->message }}</td>
                                        <td>{{ $log->action_url }}</td>
                                        <td>{{ $log->method }}</td>
                                        <td>{{ $log->ip_add }}</td>
                                        <td>{{ $log->browser }}</td>
                                        <td>{{ $log->os }}</td>
                                        <td>{{ $log->created_at }}</td>
                                        <td align="center">
                                            <a class="btn-floating btn-sm btn-red p-2 waves-effect material-tooltip-main mr-0"
                                               onclick="$(this).showDestroy('{{ route('emp-log-destroy', ['id' => $log->id]) }}',
                                                                            '{{ $log->name }}');"
                                               data-toggle="tooltip" data-placement="left" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                                <!--Table body-->

                            </table>
                            <!--Table-->

                        </div>
                    </div>
                </div>
                <!-- Table with panel -->

            </div>
        </div>
    </section>
</div>

<!-- Modals -->
@include('modals.search')
@include('modals.delete')

@endsection

@section('custom-js')

<!-- DataTables JS -->
<script type="text/javascript" src="{{ asset('plugins/mdb/js/addons/datatables.min.js') }}"></script>

<!-- DataTables Select JS -->
<script type="text/javascript" src="{{ asset('plugins/mdb/js/addons/datatables-select.min.js') }}"></script>

<script src="{{ asset('assets/js/input-validation.js') }}"></script>
<script src="{{ asset('assets/js/emp-log.js') }}"></script>
<script src="{{ asset('assets/js/custom-datatables.js') }}"></script>

@if (!empty(session("success")))
    @include('modals.alert')
    <script type="text/javascript">
        $(function() {
            $('#modal-success').modal();
        });
    </script>
@elseif (!empty(session("warning")))
    @include('modals.alert')
    <script type="text/javascript">
        $(function() {
            $('#modal-warning').modal();
        });
    </script>
@elseif (!empty(session("failed")))
    @include('modals.alert')
    <script type="text/javascript">
        $(function() {
            $('#modal-failed').modal();
        });
    </script>
@endif

@endsection
