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
                        <i class="fas fa-shopping-cart"></i> Request for Quotations
                    </strong>
                </h5>
                <hr class="white">
                <ul class="breadcrumb mdb-color darken-3 mb-0 p-1 white-text">
                    <li>
                        <i class="fa fa-caret-right mx-2" aria-hidden="true"></i>
                    </li>
                    <li>
                        <a href="{{ route('pr') }}" class="waves-effect waves-light white-text">
                            Purchase Request & Status
                        </a>
                    </li>
                    <li>
                        <i class="fa fa-caret-right mx-2" aria-hidden="true"></i>
                    </li>
                    <li class="active">
                        <a href="{{ route('rfq') }}" class="waves-effect waves-light cyan-text">
                            Request for Quotations
                        </a>
                    </li>
                </ul>

                <!-- Table with panel -->
                <div class="card card-cascade narrower">

                    <!--Card image-->
                    <div class="gradient-card-header unique-color
                                narrower py-2 px-2 mb-1 d-flex justify-content-between
                                align-items-center">
                        <div></div>
                        <div>
                            <button class="btn btn-outline-white btn-rounded btn-sm px-2"
                                    data-target="#top-fluid-modal" data-toggle="modal">
                                <i class="fas fa-search"></i>
                            </button>
                            <a href="{{ route('rfq') }}" class="btn btn-outline-white btn-rounded btn-sm px-2">
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
                                    <tr class="hidden-xs">
                                        <th class="th-md" width="3%"></th>
                                        <th class="th-md" width="3%"></th>
                                        <th class="th-md" width="8%">
                                            <strong>PR No</strong>
                                        </th>
                                        <th class="th-md" width="10%">
                                            <strong>Date</strong>
                                        </th>
                                        <th class="th-md" width="10%">
                                            <strong>Funding/Charging</strong>
                                        </th>
                                        <th class="th-md" width="50%">
                                            <strong>Purpose</strong>
                                        </th>
                                        <th class="th-md" width="13%">
                                            <strong>Requested By</strong>
                                        </th>
                                        <th class="th-md" width="3%"></th>
                                    </tr>
                                </thead>
                                <!--Table head-->

                                <!--Table body-->
                                <tbody>
                                    @if (count($list) > 0)
                                        @foreach ($list as $listCtr => $rfq)
                                    <tr>
                                        <td align="center"></td>
                                        <td align="center">
                                            @if (!empty($rfq->doc_status->date_issued) &&
                                                  empty($rfq->doc_status->date_received))
                                            <i class="fas fa-lg fa-paper-plane text-warning material-tooltip-main"
                                               data-toggle="tooltip" data-placement="right" title="Issued"></i>
                                            @elseif (!empty($rfq->doc_status->date_issued) &&
                                                     !empty($rfq->doc_status->date_received))
                                            <i class="fas fa-lg fa-hand-holding text-success material-tooltip-main"
                                               data-toggle="tooltip" data-placement="right" title="Received"></i>
                                            @else
                                            <i class="far fa-lg fa-file material-tooltip-main"
                                               data-toggle="tooltip" data-placement="right" title="Pending"></i>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $rfq->pr->pr_no }}
                                        </td>
                                        <td>{{ $rfq->pr->date_pr }}</td>
                                        <td>{{ $rfq->pr->funding_source }}</td>
                                        <td>
                                            <i class="fas fa-caret-right"></i> {{
                                                (strlen($rfq->pr->purpose) > 150) ?
                                                substr($rfq->pr->purpose, 0, 150).'...' : $rfq->pr->purpose
                                            }}
                                        </td>
                                        <td>{{ $rfq->pr->requested_by }}</td>
                                        <td align="center">
                                            <a class="btn-floating btn-sm btn-mdb-color p-2 waves-effect material-tooltip-main mr-0"
                                               data-target="#right-modal-{{ $listCtr + 1 }}" data-toggle="modal"
                                               data-toggle="tooltip" data-placement="left" title="Open">
                                                <i class="fas fa-folder-open"></i>
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
@if (count($list) > 0)
    @foreach ($list as $listCtr => $rfq)
<div class="modal custom-rightmenu-modal fade right" id="right-modal-{{ $listCtr + 1 }}" tabindex="-1"
     role="dialog">
    <div class="modal-dialog modal-full-height modal-right" role="document">
        <!--Content-->
        <div class="modal-content">
            <!--Header-->
            <div class="modal-header stylish-color-dark white-text">
                <h7>
                    <i class="fas fa-shopping-cart"></i>
                    <strong>QUOTATION NO: {{ $rfq->pr->pr_no }}</strong>
                </h7>
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
                                        onclick="$(this).showPrint('{{ $rfq->id }}', 'proc_rfq');">
                                    <i class="fas fa-print blue-text"></i> Print PR
                                </button>

                                @if ($isAllowedUpdate)
                                <button type="button" class="btn btn-outline-mdb-color
                                        btn-sm px-2 waves-effect waves-light"
                                        onclick="$(this).showEdit('{{ route('rfq-show-edit', ['id' => $rfq->id]) }}');">
                                    <i class="fas fa-edit orange-text"></i> Edit
                                </button>
                                @endif

                                @if ($isAllowedDelete)
                                    @if ($rfq->status == 1)
                                <button type="button" class="btn btn-outline-mdb-color
                                        btn-sm px-2 waves-effect waves-light"
                                        onclick="$(this).showDelete('{{ route('pr-delete', ['id' => $rfq->id]) }}',
                                                                              '{{ $rfq->pr_no }}');">
                                    <i class="fas fa-trash-alt red-text"></i> Delete
                                </button>
                                    @else
                                <button type="button" class="btn btn-outline-mdb-color
                                        btn-sm px-2 waves-effect waves-light"
                                        disabled="disabled">
                                    <i class="fas fa-trash-alt red-text"></i> Delete
                                </button>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <p>
                            <strong>PR Date: </strong> {{ $rfq->pr->date_pr }}<br>
                            <strong>Charging: </strong> {{ $rfq->pr->funding_source }}<br>
                            <strong>Purpose: </strong> {{
                                (strlen($rfq->pr->purpose) > 150) ?
                                substr($rfq->pr->purpose, 0, 150).'...' : $rfq->pr->purpose
                            }}<br>
                            <strong>Requested By: </strong> {{ $rfq->pr->requested_by }}<br>
                        </p>
                        <button type="button" class="btn btn-sm btn-mdb-color btn-rounded
                                btn-block waves-effect mb-2"
                                onclick="$(this).showItem('{{ route('pr-show-items', ['id' => $rfq->pr_id]) }}');">
                            <i class="far fa-list-alt fa-lg"></i> View Items
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-elegant btn-rounded
                                btn-block waves-effect mb-2"
                                onclick="$(this).showAttachment('{{ $rfq->id }}', 'proc-rfq');">
                            <i class="fas fa-paperclip fa-lg"></i> View Attachment
                        </button>
                    </div>
                </div>
                <hr>
                <ul class="list-group z-depth-1">
                    <li class="list-group-item justify-content-between">
                        <h5><strong><i class="fas fa-pen-nib"></i> Actions</strong></h5>
                    </li>

                    @if (empty($rfq->doc_status->date_issued) &&
                         empty($rfq->doc_status->date_received))
                        @if ($isAllowedIssue)
                    <li class="list-group-item justify-content-between">
                        <button type="button" class="btn btn-outline-orange waves-effect btn-block btn-md btn-rounded"
                                onclick="$(this).showIssue('{{ route('rfq-show-issue', ['id' => $rfq->id]) }}');">
                            <i class="fas fa-paper-plane"></i> Issue
                        </button>
                    </li>
                        @endif
                    @elseif (!empty($rfq->doc_status->date_issued) &&
                              empty($rfq->doc_status->date_received))
                        @if ($isAllowedReceive)
                    <li class="list-group-item justify-content-between">
                        <button type="button" class="btn btn-outline-success waves-effect btn-block btn-md btn-rounded"
                                onclick="$(this).showReceive('{{ route('rfq-show-receive', ['id' => $rfq->id]) }}');">
                            <i class="fas fa-hand-holding"></i> Receive
                        </button>
                    </li>
                        @endif
                    @else
                        @if ($isAllowedAbstract)
                    <li class="list-group-item justify-content-between">
                        <a onclick="$(this).redirectToDoc('{{ route('abstract') }}', '{{ $rfq->pr_id }}');"
                          class="btn btn-outline-mdb-color waves-effect btn-block btn-md btn-rounded">
                            Generate Abstract <i class="fas fa-angle-double-right"></i>
                        </a>
                    </li>
                        @else
                    <ul class="list-group z-depth-0">
                        <li class="list-group-item justify-content-between text-center">
                            No more available actions.
                        </li>
                    </ul>
                        @endif
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

@include('modals.search')
@include('modals.show')
@include('modals.edit')
@include('modals.issue')
@include('modals.receive')
@include('modals.print')
@include('modals.attachment')

@endsection

@section('custom-js')

<!-- DataTables JS -->
<script type="text/javascript" src="{{ asset('plugins/mdb/js/addons/datatables.min.js') }}"></script>

<!-- DataTables Select JS -->
<script type="text/javascript" src="{{ asset('plugins/mdb/js/addons/datatables-select.min.js') }}"></script>

<script type="text/javascript" src="{{ asset('assets/js/input-validation.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/rfq.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/print.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/attachment.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/custom-datatables.js') }}"></script>

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
