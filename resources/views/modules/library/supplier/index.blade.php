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
                        <i class="fas fa-book"></i> Libraries: Suppliers
                    </strong>
                </h5>
                <hr class="white">
                <ul class="breadcrumb mdb-color darken-3 mb-0 p-1 white-text">
                    <li>
                        <i class="fa fa-caret-right mx-2" aria-hidden="true"></i>
                    </li>
                    <li class="active">
                        <a href="{{ route('supplier') }}" class="waves-effect waves-light cyan-text">
                            Suppliers
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
                                    onclick="$(this).showCreate('{{ route('supplier-show-create') }}');">
                                <i class="fas fa-pencil-alt"></i> Create
                            </button>
                        </div>
                        <div>
                            <button class="btn btn-outline-white btn-rounded btn-sm px-2"
                                    data-target="#top-fluid-modal" data-toggle="modal">
                                <i class="fas fa-search"></i>
                            </button>
                            <a href="{{ route('supplier') }}" class="btn btn-outline-white btn-rounded btn-sm px-2">
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
                                        <th class="th-md" width="25%">
                                            <strong>Company Name</strong>
                                        </th>
                                        <th class="th-md" width="35%">
                                            <strong>Address</strong>
                                        </th>
                                        <th class="th-md" width="11%">
                                            <strong>Contact Person</strong>
                                        </th>
                                        <th class="th-md" width="20%">
                                            <strong>Classification</strong>
                                        </th>
                                        <th class="th-md" width="3%"></th>
                                        <th class="th-md" width="3%"></th>
                                    </tr>
                                </thead>
                                <!--Table head-->

                                <!--Table body-->
                                <tbody>
                                    @if (count($list) > 0)
                                        @foreach ($list as $listCtr => $supplier)
                                    <tr>
                                        <td></td>
                                        <td>{{ $supplier->company_name }}</td>
                                        <td>{{ $supplier->address }}</td>
                                        <td>{{ $supplier->contact_person }}</td>
                                        <td>{{ $supplier->classification }}</td>
                                        <td align="center">
                                            <a class="btn-floating btn-sm btn-orange p-2 waves-effect material-tooltip-main mr-0"
                                               onclick="$(this).showEdit('{{ route('supplier-show-edit',
                                                                                   ['id' => $supplier->id]) }}');"
                                               data-toggle="tooltip" data-placement="left" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </td>
                                        <td align="center">
                                            <a class="btn-floating btn-sm btn-red p-2 waves-effect material-tooltip-main mr-0"
                                               onclick="$(this).showDelete('{{ route('supplier-delete', ['id' => $supplier->id]) }}',
                                                                           '{{ $supplier->company_name }}');"
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
@include('modals.sm-create')
@include('modals.sm-edit')
@include('modals.delete')

@endsection

@section('custom-js')

<!-- DataTables JS -->
<script type="text/javascript" src="{{ asset('plugins/mdb/js/addons/datatables.min.js') }}"></script>

<!-- DataTables Select JS -->
<script type="text/javascript" src="{{ asset('plugins/mdb/js/addons/datatables-select.min.js') }}"></script>

<script src="{{ asset('assets/js/input-validation.js') }}"></script>
<script src="{{ asset('assets/js/supplier.js') }}"></script>
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
