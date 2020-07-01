@extends('layouts.app')

@section('main-content')

<div class="row wow animated fadeIn">
    <section class="mb-5 col-12 module-container">
        <div class="card mdb-color darken-3">
            <div class="card-body">
                <h5 class="card-title white-text">
                    <strong>
                        <i class="fas fa-money-bill-wave-alt"></i> Disbursement Voucher
                    </strong>
                </h5>
                <hr class="white">
                <ul class="breadcrumb mdb-color darken-3 mb-0 p-1 white-text">
                    <li>
                        <i class="fa fa-caret-right mx-2" aria-hidden="true"></i>
                    </li>
                    <li class="active">
                        <a href="{{ url('procurement/dv') }}" class="waves-effect waves-light cyan-text">
                            Disbursement Voucher
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
                                    onclick="$(this).showCreate('{{ route('ca-dv-show-create') }}');">
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
                            <a href="{{ route('ca-dv') }}" class="btn btn-outline-white btn-rounded btn-sm px-2">
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
                                        <th class="th-md" width="13%">
                                            @sortablelink('dv_no', 'DV No.', [], ['class' => 'white-text'])
                                        </th>
                                        <th class="th-md" width="58%">
                                            @sortablelink('particulars', 'Particulars', [], ['class' => 'white-text'])
                                        </th>
                                        <th class="th-md" width="20%">
                                            @sortablelink('emppayee.firstname', 'Payee', [], ['class' => 'white-text'])
                                        </th>
                                        <th class="th-md" width="3%"></th>
                                    </tr>
                                </thead>
                                <!--Table head-->

                                <!--Table body-->
                                <tbody>
                                    @if (count($list) > 0)
                                        @foreach ($list as $listCtr => $dv)

                                            @if (!$roleHasOrdinary && empty($dv->doc_status->date_issued))
                                        <tr class="hidden-xs d-none">
                                                @else
                                        <tr class="hidden-xs">
                                            @endif
                                        <td align="center">
                                            @if (!empty($dv->date_disbursed))
                                            <i class="far fa-money-bill-alt fa-lg text-success material-tooltip-main"
                                           data-toggle="tooltip" data-placement="right" title="Disbursed"></i>
                                            @else
                                                @if (!empty($dv->doc_status->date_issued) &&
                                                     empty($dv->doc_status->date_received) &&
                                                     empty($dv->doc_status->date_issued_back) &&
                                                     empty($dv->doc_status->date_received_back))
                                            <i class="fas fa-paper-plane fa-lg orange-text material-tooltip-main"
                                               data-toggle="tooltip" data-placement="right" title="Submitted"></i>
                                            @elseif (!empty($dv->doc_status->date_issued) &&
                                                     !empty($dv->doc_status->date_received) &&
                                                     empty($dv->doc_status->date_issued_back) &&
                                                     empty($dv->doc_status->date_received_back))
                                            <i class="fas fa-hand-holding fa-lg text-success material-tooltip-main"
                                               data-toggle="tooltip" data-placement="right" title="Received"></i>
                                            @elseif (!empty($dv->doc_status->date_issued) &&
                                                     !empty($dv->doc_status->date_received) &&
                                                     !empty($dv->doc_status->date_issued_back) &&
                                                     empty($dv->doc_status->date_received_back))
                                            <i class="fas fa-undo-alt fa-lg orange-text material-tooltip-main"
                                               data-toggle="tooltip" data-placement="right" title="Sumbitted Back"></i>
                                            @elseif (!empty($dv->doc_status->date_issued) &&
                                                     !empty($dv->doc_status->date_received) &&
                                                     !empty($dv->doc_status->date_issued_back) &&
                                                     !empty($dv->doc_status->date_received_back))
                                            <i class="fas fa-hand-holding fa-lg text-success material-tooltip-main"
                                               data-toggle="tooltip" data-placement="right" title="Received"></i>
                                                @else
                                            <i class="far fa-lg fa-file material-tooltip-main"
                                               data-toggle="tooltip" data-placement="right" title="Pending"></i>
                                                @endif
                                            @endif
                                        </td>
                                        <td></td>
                                        <td>{{ !empty($dv->dv_no) ? $dv->dv_no : 'NA' }}</td>
                                        <td>
                                            <i class="fas fa-caret-right"></i> {{
                                                (strlen($dv->particulars) > 150) ?
                                                substr($dv->particulars, 0, 150).'...' : $dv->particulars
                                            }}
                                        </td>
                                        <td>{{ $dv->emppayee['firstname'] }} {{ $dv->emppayee['lastname'] }}</td>
                                        <td align="center">
                                            <a class="btn-floating btn-sm btn-mdb-color p-2 waves-effect material-tooltip-main mr-0"
                                               data-target="#right-modal-{{ $listCtr + 1 }}" data-toggle="modal"
                                               data-toggle="tooltip" data-placement="left" title="Open">
                                                <i class="fas fa-folder-open"></i>
                                            </a>
                                        </td>
                                    </tr>
                                        @endforeach
                                    @else
                                    <tr>
                                        <td colspan="7" class="text-center py-5">
                                            <h6 class="red-text">
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
    @foreach ($list as $listCtr => $dv)
<div class="modal custom-rightmenu-modal fade right" id="right-modal-{{ $listCtr + 1 }}" tabindex="-1"
     role="dialog">
    <div class="modal-dialog modal-full-height modal-right" role="document">
        <!--Content-->
        <div class="modal-content">
            <!--Header-->
            <div class="modal-header stylish-color-dark white-text">
                <h6>
                    <i class="fas fa-shopping-cart"></i>
                    <strong>DV NO: {{ $dv->dv_no }}</strong>
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
                                        onclick="$(this).showPrint('{{ $dv->id }}', 'ca_dv');">
                                    <i class="fas fa-print blue-text"></i> Print DV
                                </button>
                                <button type="button" class="btn btn-outline-mdb-color
                                        btn-sm px-2 waves-effect waves-light"
                                        onclick="$(this).showEdit('{{ route('ca-dv-show-edit',
                                                 ['id' => $dv->id]) }}');">
                                    <i class="fas fa-edit orange-text"></i> Edit
                                </button>

                                @if (!$dv->date_disbursed)
                                <button type="button" class="btn btn-outline-mdb-color
                                        btn-sm px-2 waves-effect waves-light"
                                        onclick="$(this).showDelete('{{ route('ca-dv-delete', ['id' => $dv->id]) }}',
                                                                              '{{ $dv->id }}');">
                                    <i class="fas fa-trash-alt red-text"></i> Delete
                                </button>
                                @else
                                <button type="button" class="btn btn-outline-mdb-color
                                        btn-sm px-2 waves-effect waves-light" disabled>
                                    <i class="fas fa-trash-alt red-text"></i> Delete
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <p>
                            <strong>DV Date: </strong> {{ $dv->date_dv }}<br>
                            <strong>ORS/BURS Date: </strong> {{ $dv->procors['date_ors_burs'] }}<br>
                            <strong>Particulars: </strong> {{
                                (strlen($dv->particulars) > 150) ?
                                substr($dv->particulars, 0, 150).'...' : $dv->particulars
                            }}<br>
                            <strong>Payee: </strong> {{ $dv->emppayee['firstname'] }} {{ $dv->emppayee['lastname'] }}<br>

                            @if (!empty($dv->dv['date_disbursed']))
                                @if (!empty($dv->doc_status->issued_remarks) &&
                                     !empty($dv->doc_status->date_issued) &&
                                     empty($dv->doc_status->date_received) &&
                                     empty($dv->doc_status->date_issued_back) &&
                                     empty($dv->doc_status->date_received_back) &&
                                     $isAllowedReceive)
                            <strong class="red-text">
                                <i class="fas fa-exclamation-triangle fa-sm"></i> Remarks:
                                {{ $dv->doc_status->issued_remarks }}
                            </strong><br>
                                @elseif (!empty($dv->doc_status->received_remarks) &&
                                         !empty($dv->doc_status->date_issued) &&
                                         !empty($dv->doc_status->date_received) &&
                                         empty($dv->doc_status->date_issued_back) &&
                                         empty($dv->doc_status->date_received_back) &&
                                         $isAllowedIssue)
                            <strong class="red-text">
                                <i class="fas fa-exclamation-triangle fa-sm"></i> Remarks:
                                {{ $dv->doc_status->received_remarks }}
                            </strong><br>
                                @elseif (!empty($dv->doc_status->issued_back_remarks) &&
                                         !empty($dv->doc_status->date_issued) &&
                                         !empty($dv->doc_status->date_received) &&
                                         !empty($dv->doc_status->date_issued_back) &&
                                         empty($dv->doc_status->date_received_back) &&
                                         $isAllowedReceiveBack)
                            <strong class="red-text">
                                <i class="fas fa-exclamation-triangle fa-sm"></i> Remarks:
                                {{ $dv->doc_status->issued_back_remarks }}
                            </strong><br>
                                @elseif (!empty($dv->doc_status->received_back_remarks) &&
                                         !empty($dv->doc_status->date_issued) &&
                                         !empty($dv->doc_status->date_received) &&
                                         !empty($dv->doc_status->date_issued_back) &&
                                         !empty($dv->doc_status->date_received_back) &&
                                         $isAllowedReceive)
                            <strong class="red-text">
                                <i class="fas fa-exclamation-triangle fa-sm"></i> Remarks:
                                {{ $dv->doc_status->received_back_remarks }}
                            </strong><br>
                                @endif
                            @endif
                        </p>
                        <button type="button" class="btn btn-sm btn-outline-elegant btn-rounded
                                btn-block waves-effect mb-2"
                                onclick="$(this).showAttachment('{{ $dv->id }}', 'proc-rfq');">
                            <i class="fas fa-paperclip fa-lg"></i> View Attachment
                        </button>
                        <button type="button" class="btn btn-sm btn-mdb-color btn-rounded
                                btn-block waves-effect mb-2"
                                onclick="$(this).showRemarks('{{ route('ca-dv-show-remarks',
                                                             ['id' => $dv->id]) }}');">
                            <i class="far fa-comment-dots"></i> View Remarks
                        </button>
                    </div>
                </div>
                <hr>
                <ul class="list-group z-depth-0">
                    <li class="list-group-item justify-content-between">
                        <h5><strong><i class="fas fa-pen-nib"></i> Actions</strong></h5>
                    </li>

                    @if ($isAllowedORS)
                        @if ($dv->has_ors)
                    <li class="list-group-item justify-content-between">
                        <a type="button" class="btn btn-outline-warning waves-effect btn-block btn-md btn-rounded"
                           onclick="$(this).redirectToDoc('{{ route('ca-ors-burs') }}', '{{ $dv->ors_id }}');">
                            <i class="fas fa-file-signature orange-text"></i> Edit ORS/BURS
                        </a>
                    </li>
                        @endif
                    @endif

                    @if ($dv->transaction_type == 'cash_advance' && $isAllowedLR)
                        @if ($dv->has_lr)
                    <li class="list-group-item justify-content-between">
                        <a type="button" class="btn btn-outline-warning waves-effect btn-block btn-md btn-rounded"
                           onclick="$(this).redirectToDoc('{{ route('ca-lr') }}', '{{ $dv->id }}');">
                            <i class="fas fa-file-signature orange-text"></i> Edit Liquidation Report
                        </a>
                    </li>
                        @else
                    <li class="list-group-item justify-content-between">
                        <button type="button" class="btn btn-outline-green waves-effect btn-block btn-md btn-rounded"
                                onclick="$(this).showCreateLR('{{ route('ca-dv-show-create-lr', ['dvID' => $dv->id]) }}');">
                            <i class="fas fa-pencil-alt green-text"></i> Create Liquidation Report
                        </button>
                    </li>
                        @endif
                    @endif

                    @if (empty($dv->date_disbursed))
                        @if (empty($dv->doc_status->date_issued) &&
                             empty($dv->doc_status->date_received) &&
                             empty($dv->doc_status->date_issued_back) &&
                             empty($dv->doc_status->date_received_back) &&
                             $isAllowedIssue)
                    <li class="list-group-item justify-content-between">
                        <button type="button" class="btn btn-outline-orange waves-effect btn-block btn-md btn-rounded"
                                onclick="$(this).showIssue('{{ route('ca-dv-show-issue', ['id' => $dv->id]) }}',
                                                           `{{ 'Disbursement Voucher '.$dv->id }}`);">
                            <i class="fas fa-paper-plane"></i> Submit
                        </button>
                    </li>
                        @elseif (!empty($dv->doc_status->date_issued) &&
                                 empty($dv->doc_status->date_received) &&
                                 empty($dv->doc_status->date_issued_back) &&
                                 empty($dv->doc_status->date_received_back) &&
                                 $isAllowedReceive)
                    <li class="list-group-item justify-content-between">
                        <button type="button" class="btn btn-outline-green waves-effect btn-block btn-md btn-rounded"
                                onclick="$(this).showReceive('{{ route('ca-dv-show-receive', ['id' => $dv->id]) }}',
                                                             `{{ 'Disbursement Voucher '.$dv->id }}`);">
                            <i class="fas fa-hand-holding"></i> Receive
                        </button>
                    </li>
                        @elseif (!empty($dv->doc_status->date_issued) &&
                                 !empty($dv->doc_status->date_received) &&
                                 empty($dv->doc_status->date_issued_back) &&
                                 empty($dv->doc_status->date_received_back))
                            @if ($isAllowedPayment)
                    <li class="list-group-item justify-content-between">
                        <button type="button" class="btn btn-outline-green waves-effect btn-block btn-md btn-rounded"
                                onclick="$(this).showPayment('{{ route('ca-dv-show-payment', ['id' => $dv->id]) }}',
                                                              `{{ 'Disbursement Voucher '.$dv->id }}`);">
                            <i class="fas fa-angle-double-right"></i> Payment/LDDAP
                        </button>
                    </li>
                            @endif

                            @if ($isAllowedIssueBack)
                    <li class="list-group-item justify-content-between">
                        <button type="button" class="btn btn-outline-orange waves-effect btn-block btn-md btn-rounded"
                                onclick="$(this).showIssueBack('{{ route('ca-dv-show-issue-back', ['id' => $dv->id]) }}',
                                                               `{{ 'Disbursement Voucher '.$dv->id }}`);">
                            <i class="fas fa-undo-alt"></i> Submit Back
                        </button>
                    </li>
                            @endif
                        @elseif (!empty($dv->doc_status->date_issued) &&
                                 !empty($dv->doc_status->date_received) &&
                                 !empty($dv->doc_status->date_issued_back) &&
                                 empty($dv->doc_status->date_received_back) &&
                                 $isAllowedReceiveBack)
                    <li class="list-group-item justify-content-between">
                        <button type="button" class="btn btn-outline-green waves-effect btn-block btn-md btn-rounded"
                                onclick="$(this).showReceiveBack('{{ route('ca-dv-show-receive-back', ['id' => $dv->id]) }}',
                                                                 `{{ 'Disbursement Voucher '.$dv->id }}`);">
                            <i class="fas fa-hand-holding"></i> Receive Back
                        </button>
                    </li>
                        @endif
                    @else
                        @if ($isAllowedPayment)
                    <li class="list-group-item justify-content-between">
                        <a onclick="$(this).redirectToDoc('{{ route('lddap') }}', '{{ $dv->id }}');"
                          class="btn btn-outline-mdb-color waves-effect btn-block btn-md btn-rounded">
                            <i class="fas fa-angle-double-right"></i> Generate Payment/LDDAP
                        </a>
                    </li>
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

@include('modals.search-post')
@include('modals.show')
@include('modals.create')
@include('modals.edit')
@include('modals.delete-destroy')
@include('modals.issue')
@include('modals.receive')
@include('modals.issue-back')
@include('modals.receive-back')
@include('modals.payment')
@include('modals.print')
@include('modals.attachment')

@endsection

@section('custom-js')

<script type="text/javascript" src="{{ asset('assets/js/input-validation.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/dv.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/print.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/attachment.js') }}"></script>

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