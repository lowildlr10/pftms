@extends('layouts.app')

@section('custom-css')

<link rel="stylesheet" type="text/css" href="{{ asset('plugins/select2/css/select2.min.css') }}" rel="stylesheet">

@endsection

@section('main-content')

<div class="row wow animated fadeIn">
    <section class="mb-5 col-12 module-container">
        <div class="card mdb-color darken-3">
            <div class="card-body">
                <h5 class="card-title white-text">
                    <strong>
                        <i class="fas fa-money-check-alt"></i> List of Due and Demandable Accounts Payable
                    </strong>
                </h5>
                <hr class="white hidden-xs">
                <ul class="breadcrumb mdb-color darken-3 mb-0 p-1 white-text hidden-xs">
                    <li>
                        <i class="fa fa-caret-right mx-2" aria-hidden="true"></i>
                    </li>
                    <li>
                        <a href="{{ url('payment/lddap') }}" class="waves-effect waves-light white-text">
                            List of Due and Demandable Accounts Payable
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
                                    onclick="$(this).showCreate('{{ route('lddap-show-create') }}');">
                                <i class="fas fa-pencil-alt"></i> Create
                            </button>
                        </div>
                        <div>
                            <button class="btn btn-outline-white btn-rounded btn-sm px-2"
                                    data-target="#top-fluid-modal" data-toggle="modal"
                                    onclick="$('#search').focus()">
                                <i class="fas fa-search"></i> {{ !empty($keyword) ? (strlen($keyword) > 15) ?
                                'Search: '.substr($keyword, 0, 15).'...' : 'Search: '.$keyword : '' }}
                            </button>
                            <a href="{{ route('lddap') }}" class="btn btn-outline-white btn-rounded btn-sm px-2">
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
                                <thead class="mdb-color darken-3 white-text hidden-xs">
                                    <tr>
                                        <th class="th-md" width="3%"></th>
                                        <th class="th-md" width="3%"></th>
                                        <th class="th-md" width="8%">
                                            <strong>
                                                @sortablelink('date_lddap', 'LDDAP Date', [], ['class' => 'white-text'])
                                            </strong>
                                        </th>
                                        <th class="th-md" width="15%">
                                            <strong>ORS Nos</strong>
                                        </th>
                                        <th class="th-md" width="24%">
                                            <strong>
                                                @sortablelink('lddap_no', 'LDDAP ADA No', [], ['class' => 'white-text'])
                                            </strong>
                                        </th>
                                        <th class="th-md" width="24%">
                                            <strong>
                                                @sortablelink('nca_no', 'NCA No', [], ['class' => 'white-text'])
                                            </strong>
                                        </th>
                                        <th class="th-md" width="10%">
                                            <strong>
                                                @sortablelink('total_amount', 'Total Amount', [], ['class' => 'white-text'])
                                            </strong>
                                        </th>
                                        <th class="th-md" width="10%">
                                            <strong>
                                                @sortablelink('status', 'Status', [], ['class' => 'white-text'])
                                            </strong>
                                        </th>
                                        <th class="th-md" width="3%"></th>
                                    </tr>
                                </thead>
                                <!--Table head-->

                                <!--Table body-->
                                <tbody>
                                    @if (count($list) > 0)
                                        @foreach ($list as $listCtr => $lddap)
                                    <tr class="hidden-xs">
                                        <td align="center" class="border-left">
                                            @if ($lddap->status == 'pending')
                                            <i class="fas fa-spinner fa-lg faa-spin fa-pulse material-tooltip-main"
                                               data-toggle="tooltip" data-placement="right" title="Pending"></i>
                                            @elseif ($lddap->status == 'for_approval')
                                            <i class="fas fa-sign fa-lg black-text material-tooltip-main"
                                               data-toggle="tooltip" data-placement="right" title="For Approval"></i>
                                            @elseif ($lddap->status == 'approved')
                                            <i class="fas fa-check fa-lg text-success material-tooltip-main"
                                               data-toggle="tooltip" data-placement="right" title="Approved"></i>
                                            @elseif ($lddap->status == 'for_summary')
                                            <i class="fas fa-list-alt fa-lg text-success material-tooltip-main"
                                               data-toggle="tooltip" data-placement="right" title="For Summary"></i>
                                            @endif
                                        </td>
                                        <td></td>
                                        <td>{{ $lddap->date_lddap }}</td>
                                        <td>
                                            @if (count($lddap->ors_nos) > 0)
                                                @foreach ($lddap->ors_nos as $orsNo)
                                            <a onclick="window.open('{{ route('proc-ors-burs', ['keyword' => $orsNo]) }}');
                                                        window.open('{{ route('ca-ors-burs', ['keyword' => $orsNo]) }}');"
                                               target="_blank" class="blue-text">
                                                [{{ $orsNo }}]
                                            </a>
                                                @endforeach
                                            @else
                                            N/A
                                            @endif
                                        </td>
                                        <td>{{ $lddap->lddap_ada_no }}</td>
                                        <td>{{ $lddap->nca_no }}</td>
                                        <td>P{{ number_format($lddap->total_amount, 2) }}</td>
                                        <td>
                                            <strong>{{ strtoupper(str_replace('_', ' ', $lddap->status)) }}</strong>
                                        </td>

                                        <td align="center">
                                            <a class="btn-floating btn-sm btn-mdb-color p-2 waves-effect material-tooltip-main mr-0"
                                               data-target="#right-modal-{{ $listCtr + 1 }}" data-toggle="modal"
                                               data-toggle="tooltip" data-placement="left" title="Open">
                                                <i class="fas fa-folder-open"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <tr class="d-none show-xs">
                                        <td data-target="#right-modal-{{ $listCtr + 1 }}" data-toggle="modal">
                                            <b>LDDAP ADA No:</b> {{ $lddap->lddap_ada_no }}<br>
                                            <small>
                                                <b>Total Cost: </b> P{{ number_format($lddap->total_amount, 2) }}
                                            </small><br>
                                            <small>
                                                @if ($lddap->status == 'pending')
                                                <b>Status: </b> Pending
                                                @elseif ($lddap->status == 'for_approval')
                                                <b>Status: </b> For Approval
                                                @elseif ($lddap->status == 'approved')
                                                <b>Status: </b> Approved
                                                @elseif ($lddap->status == 'for_summary')
                                                <b>Status: </b> For Summary
                                                @endif
                                            </small>
                                        </td>
                                    </tr>
                                    <tr class="d-none show-xs">
                                        <td class="p-0 pl-3 m-0">
                                            <table class="table table-condensed my-0 py-0">
                                                <tr>
                                                    <td class="py-1">
                                                        <small>
                                                            <b>ORS Nos: </b>
                                                        </small>
                                                @if (count($lddap->ors_nos) > 0)
                                                    @foreach ($lddap->ors_nos as $orsNo)

                                                        <a onclick="window.open('{{ route('proc-ors-burs', ['keyword' => $orsNo]) }}');
                                                                    window.open('{{ route('ca-ors-burs', ['keyword' => $orsNo]) }}');"
                                                            target="_blank" class="blue-text">
                                                            [{{ $orsNo }}]
                                                        </a>
                                                    @endforeach
                                                @else
                                                N/A
                                            @endif
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                        @endforeach
                                    @else
                                    <tr>
                                        <td class="p-5" colspan="9" class="text-center py-5">
                                            <h6 class="red-text text-center">
                                                No available data.
                                            </h6>
                                        </td>
                                    </tr>
                                    @endif
                                </tbody>
                                <!--Table body-->
                            </table>
                            <!--Table-->
                        </div>

                        <div class="mt-3">
                            {!! $list->appends(\Request::except('page'))->render('pagination') !!}
                        </div>
                    </div>
                </div>
                <!-- Table with panel -->
            </div>
        </div>
    </section>
</div>

<!-- Modals -->

@if (count($list) > 0)
    @foreach ($list as $listCtr => $lddap)
<div class="modal custom-rightmenu-modal fade right" id="right-modal-{{ $listCtr + 1 }}" tabindex="-1"
     role="dialog">
    <div class="modal-dialog modal-full-height modal-righty" role="document">
        <!--Content-->
        <div class="modal-content">
            <!--Header-->
            <div class="modal-header stylish-color-dark white-text">
                <h6>
                    <i class="fas fa-money-check-alt"></i>
                    <strong>LDDAP ID: {{ $lddap->lddap_id }}</strong>
                </h6>
                <button type="button" class="close white-text" data-dismiss="modal"
                        aria-label="Close">
                    &times;
                </button>
            </div>
            <!--Body-->
            <div class="modal-body">
                <div class="card card-cascade z-depth-1 mb-3">
                    <div class="gradient-card-header rgba-white-light p-0">
                        <div class="p-0">
                            <div class="btn-group btn-menu-1 p-0">
                                <button type="button" class="btn btn-outline-mdb-color
                                        btn-sm px-2 waves-effect waves-light"
                                        onclick="$(this).showPrint('{{ $lddap->id }}', 'pay_lddap');">
                                    <i class="fas fa-print blue-text"></i> Print LDDAP
                                </button>
                                <button type="button" class="btn btn-outline-mdb-color
                                        btn-sm px-2 waves-effect waves-light"
                                       onclick="$(this).showEdit('{{ route('lddap-show-edit',
                                                 ['id' => $lddap->id]) }}');">
                                    <i class="fas fa-edit orange-text"></i> Edit
                                </button>
                                <button type="button" class="btn btn-outline-mdb-color
                                        btn-sm px-2 waves-effect waves-light"
                                        onclick="$(this).showDelete('{{ route('lddap-delete', ['id' => $lddap->id]) }}',
                                                                              '{{ $lddap->id }}');">
                                    <i class="fas fa-trash-alt red-text"></i> Delete
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <p>
                            <strong>Department: </strong> {{ $lddap->department }}<br>
                            <strong>Entity Name: </strong> {{ $lddap->entity_name }}<br>
                            <strong>Operating Unit: </strong> {{ $lddap->operating_unit }}<br>
                            <strong>NCA No: </strong> {{ $lddap->nca_no }}<br>
                            <strong>LDDAP-ADA No: </strong> {{ $lddap->lddap_ada_no }}<br>
                            <strong>Date: </strong> {{ $lddap->date_lddap }}<br>
                            <strong>Fund Cluster: </strong> {{ $lddap->fund_cluster }}<br>
                            <strong>MDS-GSB Branch/MDS Sub Account: </strong> {{ $lddap->mds_gsb_accnt_no }}<br>
                        </p>
                        <!--
                        <button type="button" class="btn btn-sm btn-outline-elegant btn-rounded
                                btn-block waves-effect mb-2"
                                onclick="$(this).showAttachment('{{ $lddap->lddap_id }}');">
                            <i class="fas fa-paperclip fa-lg"></i> View Attachment
                        </button>
                        -->
                    </div>
                </div>
                <hr>
                <ul class="btn-menu-3 list-group z-depth-0">
                    <li class="list-action-header list-group-item justify-content-between">
                        <h5><strong><i class="fas fa-pen-nib"></i> Actions</strong></h5>
                    </li>

                    @if ($lddap->status == 'pending')
                    <li class="list-group-item justify-content-between">
                        <button type="button" class="btn btn-outline-black waves-effect btn-block btn-md btn-rounded"
                                onclick="$(this).showApproval('{{ route('lddap-for-approval',
                                                              ['id' => $lddap->id]) }}');">
                            <i class="fas fa-flag"></i> For Approval
                        </button>
                    </li>
                    @elseif ($lddap->status == 'for_approval')
                    <li class="list-group-item justify-content-between">
                        <button type="button" class="btn btn-outline-green waves-effect btn-block btn-md btn-rounded"
                                onclick="$(this).showApprove('{{ route('lddap-approve',
                                                          ['id' => $lddap->id]) }}');">
                            <i class="fas fa-check"></i> Approve
                        </button>
                    </li>
                    @elseif ($lddap->status == 'approved')
                    <li class="list-group-item justify-content-between">
                        <button type="button" class="btn btn-outline-green waves-effect btn-block btn-md btn-rounded"
                                onclick="$(this).showSummary('{{ route('lddap-summary',
                                                          ['id' => $lddap->id]) }}');">
                            <i class="fas fa-list-alt"></i> For Summary
                        </button>
                    </li>
                    @else
                    <li class="list-group-item justify-content-between">
                        No more actions available.
                    </li>
                    @endif
                </ul>
            </div>
            <!--Footer-->
            <div class="modal-footer justify-content-end rgba-stylish-strong p-1">
                <a type="button" class="btn btn-sm btn btn-light waves-effect py-1" data-dismiss="modal">
                    <i class="far fa-window-close"></i> Close
                </a>
            </div>
        </div>
      <!--/.Content-->
    </div>
</div>
    @endforeach
@endif

@include('modals.search-post')
@include('modals.show')
@include('modals.create')
@include('modals.edit')
@include('modals.delete-destroy')
@include('modals.approve')
@include('modals.approval')
@include('modals.summary')
@include('modals.print')
@include('modals.calculator')

@endsection

@section('custom-js')

<script type="text/javascript" src="{{ asset('assets/js/amount-words-converter.js') }}"></script>
<script type="text/javascript" src="{{ asset('plugins/select2/js/select2.full.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/input-validation.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/lddap.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/print.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/attachment.js') }}"></script>
<script>
    // Tooltips Initialization
    $(function () {
        var template = '<div class="tooltip md-tooltip">' +
                       '<div class="tooltip-arrow md-arrow"></div>' +
                       '<div class="tooltip-inner md-inner stylish-color"></div></div>';
        $('.material-tooltip-main').tooltip({
            template: template
        });
    });
</script>

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
