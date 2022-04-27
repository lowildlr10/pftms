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
                        <i class="fas fa-users-cog"></i> Account Management: User Accounts
                    </strong>
                </h5>
                <hr class="white">
                <ul class="breadcrumb mdb-color darken-3 mb-0 p-1 white-text">
                    <li>
                        <i class="fa fa-caret-right mx-2" aria-hidden="true"></i>
                    </li>
                    <li class="active">
                        <a href="{{ route('emp-account') }}" class="waves-effect waves-light cyan-text">
                            User Accounts
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
                            <button type="button" class="btn btn-outline-white btn-rounded btn-sm px-2"
                                    onclick="$(this).showCreate('{{ route('emp-account-show-create') }}');">
                                <i class="fas fa-pencil-alt"></i> Create
                            </button>
                        </div>
                        <div>
                            <button class="btn btn-outline-white btn-rounded btn-sm px-2"
                                    data-target="#top-fluid-modal" data-toggle="modal">
                                <i class="fas fa-search"></i>
                            </button>
                            <a href="{{ route('emp-account') }}" class="btn btn-outline-white btn-rounded btn-sm px-2">
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
                                        <th class="th-md" width="3%">
                                            @sortablelink('is_active', 'Status', [], ['class' => 'white-text'])
                                        </th>
                                        <th class="th-md" width="10%">
                                            {{-- <strong>ID</strong> --}}
                                            @sortablelink('emp_id', 'ID', [], ['class' => 'white-text'])
                                        </th>
                                        <th class="th-md" width="22%">
                                            {{-- <strong>FullName</strong> --}}
                                            @sortablelink('firstname', 'Full Name', [], ['class' => 'white-text'])
                                        </th>
                                        <th class="th-md" width="10%">
                                            {{-- <strong>EmploymentType</strong> --}}
                                            @sortablelink('emp_type', 'Employment Type', [], ['class' => 'white-text'])
                                        </th>
                                        <th class="th-md" width="16%">
                                            {{-- <strong>Position</strong> --}}
                                            @sortablelink('position', 'Position', [], ['class' => 'white-text'])
                                        </th>
                                        <th class="th-md" width="16%">
                                            {{-- <strong>Division</strong> --}}
                                            @sortablelink('div.division_name', 'Division', [], ['class' => 'white-text'])
                                        </th>
                                        <th class="th-md" width="14%">
                                            {{-- <strong>LastLogin</strong> --}}
                                            @sortablelink('last_login', 'Last Login', [], ['class' => 'white-text'])
                                        </th>
                                        <th class="th-md" width="3%"></th>
                                        <th class="th-md" width="3%"></th>
                                    </tr>
                                </thead>
                                <!--Table head-->

                                <!--Table body-->
                                <tbody>
                                    @if (count($list) > 0)
                                        @foreach ($list as $listCtr => $user)
                                    <tr>
                                        <td></td>
                                        <td>
                                            {!! $user->is_active == 'y' ?
                                            '<i class="fas fa-user-check green-text material-tooltip-main"
                                                data-toggle="tooltip" data-placement="left" title="Active"></i>' :
                                            '<i class="fas fa-user-times red-text material-tooltip-main"
                                                data-toggle="tooltip" data-placement="left" title="Inactive"></i>' !!}
                                        </td>
                                        <td>{{ $user->emp_id }}</td>
                                        <td>
                                            {{ $user->firstname }}{{ !empty($user->middlename) ? ' '.$user->middlename[0].'. ' : ' ' }}{{ $user->lastname }}
                                            <hr class="my-1">
                                            <small class="grey-text">
                                                <em>
                                                    <b>Role/s: </b><br>
                                                    {{ Auth::user()->getEmployee($user->id)->roleName }}
                                                </em>
                                            </small>
                                        </td>
                                        <td>
                                            {{ $user->emp_type == 'regular' ? 'Regular' : 'Contractual' }}
                                        </td>
                                        <td>{{ $user->position }}</td>
                                        <td>{{ $user->div['division_name'] }}</td>
                                        <td>{{ $user->last_login }}</td>
                                        <td align="center">
                                            <a class="btn-floating btn-sm btn-orange p-2 waves-effect material-tooltip-main mr-0"
                                               onclick="$(this).showEdit('{{ route('emp-account-show-edit',
                                                                                   ['id' => $user->id]) }}');"
                                               data-toggle="tooltip" data-placement="left" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </td>
                                        <td align="center">
                                            <a class="btn-floating btn-sm btn-red p-2 waves-effect material-tooltip-main mr-0"
                                               onclick="$(this).showDelete('{{ route('emp-account-delete', ['id' => $user->id]) }}',
                                                                           '{{ $user->id }}');"
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

                    <div class="mt-3">
                        {!! $list->appends(\Request::except('page'))->render('pagination') !!}
                    </div>
                </div>
                <!-- Table with panel -->

            </div>
        </div>
    </section>
</div>

<!-- Modals -->
@include('modals.search-post')
@include('modals.sm-create')
@include('modals.sm-edit')
@include('modals.delete')

@endsection

@section('custom-js')

{{--
<!-- DataTables JS -->
<script type="text/javascript" src="{{ asset('plugins/mdb/js/addons/datatables.min.js') }}"></script>

<!-- DataTables Select JS -->
<script type="text/javascript" src="{{ asset('plugins/mdb/js/addons/datatables-select.min.js') }}"></script>
--}}

<script src="{{ asset('assets/js/input-validation.js') }}"></script>
<script src="{{ asset('assets/js/emp-account.js') }}"></script>

{{--
<script src="{{ asset('assets/js/custom-datatables.js') }}"></script>
--}}

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
