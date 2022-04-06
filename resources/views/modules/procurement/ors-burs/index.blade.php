@extends('layouts.app')

@section('main-content')

<div class="row wow animated fadeIn">
    <section class="mb-5 col-12 module-container">
        <div class="card mdb-color darken-3">
            <div class="card-body">
                <h5 class="card-title white-text">
                    <strong>
                        <i class="fas fa-money-bill-wave-alt"></i> Obligation / Budget Utilization & Request Status
                    </strong>
                </h5>
                <hr class="white hidden-xs">
                <ul class="breadcrumb mdb-color darken-3 mb-0 p-1 white-text hidden-xs">
                    <li>
                        <i class="fa fa-caret-right mx-2" aria-hidden="true"></i>
                    </li>
                    <li>
                        <a href="{{ url('procurement/pr') }}" class="waves-effect waves-light white-text">
                            Purchase Request & Status
                        </a>
                    </li>
                    <li>
                        <i class="fa fa-caret-right mx-2" aria-hidden="true"></i>
                    </li>
                    <li>
                        <a href="{{ url('procurement/rfq') }}" class="waves-effect waves-light white-text">
                            Request for Quotations
                        </a>
                    </li>
                    <li>
                        <i class="fa fa-caret-right mx-2" aria-hidden="true"></i>
                    </li>
                    <li>
                        <a href="{{ url('procurement/abstract') }}" class="waves-effect waves-light white-text">
                            Abstract of Bids & Quotations
                        </a>
                    </li>
                    <li>
                        <i class="fa fa-caret-right mx-2" aria-hidden="true"></i>
                    </li>
                    <li>
                        <a href="{{ url('procurement/po-jo') }}" class="waves-effect waves-light white-text">
                            Purchase/Job Order
                        </a>
                    </li>
                    <li>
                        <i class="fa fa-caret-right mx-2" aria-hidden="true"></i>
                    </li>
                    <li class="active">
                        <a href="{{ url('procurement/ors-burs') }}" class="waves-effect waves-light cyan-text">
                            Obligation / Budget Utilization & Request Status
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
                                    data-target="#top-fluid-modal" data-toggle="modal"
                                    onclick="$('#search').focus()">
                                <i class="fas fa-search"></i> {{ !empty($keyword) ? (strlen($keyword) > 15) ?
                                'Search: '.substr($keyword, 0, 15).'...' : 'Search: '.$keyword : '' }}
                            </button>
                            <a href="{{ route('proc-ors-burs') }}" class="btn btn-outline-white btn-rounded btn-sm px-2">
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
                                            @sortablelink('po_no', 'PO/JO No.', [], ['class' => 'white-text'])
                                        </th>
                                        <th class="th-md" width="13%">
                                            @sortablelink('ors.serial_no', 'Serial No.', [], ['class' => 'white-text'])
                                        </th>
                                        <th class="th-md" width="50%">
                                            @sortablelink('ors.particulars', 'Particulars', [], ['class' => 'white-text'])
                                        </th>
                                        <th class="th-md" width="20%">
                                            @sortablelink('bidpayee.company_name', 'Payee', [], ['class' => 'white-text'])
                                        </th>
                                        <th class="th-md" width="3%"></th>
                                    </tr>
                                </thead>
                                <!--Table head-->

                                <!--Table body-->
                                <tbody>
                                    @if (count($list) > 0)
                                        @foreach ($list as $listCtr => $ors)
                                            @if (($roleHasAccountant || $roleHasBudget) &&
                                                 empty($ors->doc_status->date_issued))
                                    <tr class="d-none">
                                            @else
                                    <tr class="hidden-xs">
                                            @endif
                                        <td align="center">
                                            @if (!empty($ors->ors['date_obligated']))
                                            <i class="fas fa-file-signature fa-lg green-text material-tooltip-main"
                                               data-toggle="tooltip" data-placement="right" title="Obligated"></i>
                                            @else
                                                @if (!empty($ors->doc_status->date_issued) &&
                                                     empty($ors->doc_status->date_received) &&
                                                     empty($ors->doc_status->date_issued_back) &&
                                                     empty($ors->doc_status->date_received_back))
                                            <i class="fas fa-paper-plane fa-lg orange-text material-tooltip-main"
                                               data-toggle="tooltip" data-placement="right" title="Submitted"></i>
                                            @elseif (!empty($ors->doc_status->date_issued) &&
                                                     !empty($ors->doc_status->date_received) &&
                                                     empty($ors->doc_status->date_issued_back) &&
                                                     empty($ors->doc_status->date_received_back))
                                            <i class="fas fa-hand-holding fa-lg text-success material-tooltip-main"
                                               data-toggle="tooltip" data-placement="right" title="Received"></i>
                                            @elseif (!empty($ors->doc_status->date_issued) &&
                                                     !empty($ors->doc_status->date_received) &&
                                                     !empty($ors->doc_status->date_issued_back) &&
                                                     empty($ors->doc_status->date_received_back))
                                            <i class="fas fa-undo-alt fa-lg orange-text material-tooltip-main"
                                               data-toggle="tooltip" data-placement="right" title="Sumbitted Back"></i>
                                            @elseif (!empty($ors->doc_status->date_issued) &&
                                                     !empty($ors->doc_status->date_received) &&
                                                     !empty($ors->doc_status->date_issued_back) &&
                                                     !empty($ors->doc_status->date_received_back))
                                            <i class="fas fa-hand-holding fa-lg text-success material-tooltip-main"
                                               data-toggle="tooltip" data-placement="right" title="Received"></i>
                                                @else
                                            <i class="far fa-lg fa-file material-tooltip-main"
                                               data-toggle="tooltip" data-placement="right" title="Pending"></i>
                                                @endif
                                            @endif
                                        </td>
                                        <td align="center"></td>
                                        <td>{{ $ors->po_no }}</td>
                                        <td>{{ $ors->ors['serial_no'] }}</td>
                                        <td>
                                            <i class="fas fa-caret-right"></i> {{
                                                (strlen($ors->ors['particulars']) > 150) ?
                                                substr($ors->ors['particulars'], 0, 150).'...' : $ors->ors['particulars']
                                            }}
                                        </td>
                                        <td>{{ $ors->bidpayee['company_name'] }}</td>
                                        <td align="center">
                                            @if (!empty($ors->ors['date_obligated']))
                                                @if ((Auth::user()->role == 1 || Auth::user()->role == 4))
                                                    @if (!empty($ors->doc_status->issued_remarks) &&
                                                         !empty($ors->doc_status->date_issued) &&
                                                         empty($ors->doc_status->date_issued_back))
                                            <span class="red-text">
                                                <a data-target="#right-modal-{{ $listCtr + 1 }}" data-toggle="modal">
                                                    <i class="fas fa-exclamation-triangle fa-sm"></i>
                                                </a>
                                            </span>
                                                    @endif
                                                @else
                                                    @if (!empty($ors->doc_status->issued_back_remarks) &&
                                                         !empty($ors->doc_status->date_issued) &&
                                                         !empty($ors->doc_status->date_issued_back))
                                            <span class="red-text">
                                                <a data-target="#right-modal-{{ $listCtr + 1 }}" data-toggle="modal">
                                                    <i class="fas fa-exclamation-triangle fa-sm"></i>
                                                </a>
                                            </span>
                                                    @endif
                                                @endif
                                            @endif
                                            <a class="btn-floating btn-sm btn-mdb-color p-2 waves-effect material-tooltip-main mr-0"
                                               data-target="#right-modal-{{ $listCtr + 1 }}" data-toggle="modal"
                                               data-toggle="tooltip" data-placement="left" title="Open">
                                                <i class="fas fa-folder-open"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <tr class="d-none show-xs">
                                        <td data-target="#right-modal-{{ $listCtr + 1 }}" data-toggle="modal">
                                            [ PO NO: {{ $ors->po_no }} ] <i class="fas fa-caret-right"></i> {{
                                                (strlen($ors->ors['particulars']) > 150) ?
                                                substr($ors->ors['particulars'], 0, 150).'...' : $ors->ors['particulars']
                                            }}<br>
                                            <small>
                                                <b>Serial No:</b> {{ !empty($ors->ors['serial_no']) && $ors->ors['serial_no'] != '.' ? $ors->ors['serial_no'] : 'NA' }}
                                            </small><br>
                                            <small>
                                                @if (!empty($ors->ors['date_obligated']))
                                                <b>Status:</b> Obligated
                                                @else
                                                    @if (!empty($ors->doc_status->date_issued) &&
                                                        empty($ors->doc_status->date_received) &&
                                                        empty($ors->doc_status->date_issued_back) &&
                                                        empty($ors->doc_status->date_received_back))
                                                <b>Status:</b> Submitted
                                                @elseif (!empty($ors->doc_status->date_issued) &&
                                                        !empty($ors->doc_status->date_received) &&
                                                        empty($ors->doc_status->date_issued_back) &&
                                                        empty($ors->doc_status->date_received_back))
                                                <b>Status:</b> Received
                                                @elseif (!empty($ors->doc_status->date_issued) &&
                                                        !empty($ors->doc_status->date_received) &&
                                                        !empty($ors->doc_status->date_issued_back) &&
                                                        empty($ors->doc_status->date_received_back))
                                                <b>Status:</b> Submitted Back
                                                @elseif (!empty($ors->doc_status->date_issued) &&
                                                        !empty($ors->doc_status->date_received) &&
                                                        !empty($ors->doc_status->date_issued_back) &&
                                                        !empty($ors->doc_status->date_received_back))
                                                <b>Status:</b> Received
                                                    @else
                                                <b>Status:</b> Pending
                                                    @endif
                                                @endif
                                            </small><br>
                                            <small>
                                                <b>Payee:</b> {{ $ors->bidpayee['company_name'] }}
                                            </small>
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
    @foreach ($list as $listCtr => $ors)
        @php
        $countVisible = 0;
        $isVisiblePrint = true;
        $isVisibleUpdate = $isAllowedUpdate;
        $isVisibleViewAttachment = true;
        $isVisibleIssue = $isAllowedIssue;
        $isVisibledIssueBack = $isAllowedIssueBack;
        $isVisibleReceive = $isAllowedReceive;
        $isVisibleReceiveBack = $isAllowedReceiveBack;
        $isVisibleObligate = $isAllowedObligate;
        $isVisiblePO = $isAllowedPO;

        if ($roleHasAccountant) {
            $isVisibleUpdate = false;
            $isVisibleViewAttachment = false;
            $isVisibleIssue = false;
            $isVisibledIssueBack = false;
            $isVisibleReceive = false;
            $isVisibleReceiveBack = false;
            $isVisibleObligate = false;
        }
        @endphp
<div class="modal custom-rightmenu-modal fade right" id="right-modal-{{ $listCtr + 1 }}" tabindex="-1"
     role="dialog">
    <div class="modal-dialog modal-full-height modal-righty" role="document">
        <!--Content-->
        <div class="modal-content">
            <!--Header-->
            <div class="modal-header stylish-color-dark white-text">
                <h7>
                    <i class="fas fa-shopping-cart"></i>
                    <strong>SERIAL NO: {{ $ors->ors['serial_no'] }}</strong>
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

                                <!-- Print Button Section -->
                                @if ($isVisiblePrint)
                                <button type="button" class="btn btn-outline-mdb-color
                                        btn-sm px-2 waves-effect waves-light"
                                        onclick="$(this).showPrint('{{ $ors->ors['id'] }}',
                                                                   'proc_{{ $ors->ors['document_type'] }}');">
                                    <i class="fas fa-print blue-text"></i> Print ORS/BURS
                                </button>
                                @endif
                                <!-- End Print Button Section -->

                                <!-- Edit Button Section-->
                                @if ($isVisibleUpdate)
                                <button type="button" class="btn btn-outline-mdb-color
                                        btn-sm px-2 waves-effect waves-light"
                                        onclick="$(this).showEdit('{{ route('proc-ors-burs-show-edit',
                                                                  ['id' => $ors->ors['id']]) }}');">
                                    <i class="fas fa-edit orange-text"></i> Edit
                                </button>
                                @endif
                                <!-- End Edit Button Section -->

                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <p>
                            <strong>PR Date: </strong> {{ $ors->pr->date_pr }}<br>
                            <strong>PO/JO Date: </strong> {{ $ors->date_po }}<br>
                            <strong>ORS/BURS Date: </strong> {{ $ors->date_ors_burs }}<br>
                            <strong>Particulars: </strong> {{
                                (strlen($ors->ors['particulars']) > 150) ?
                                substr($ors->ors['particulars'], 0, 150).'...' : $ors->ors['particulars']
                            }}<br>
                            <strong>Payee: </strong> {{ $ors->bidpayee['company_name'] }}<br>

                            @if (!$ors->ors['date_obligated'])
                                @if (!empty($ors->doc_status->issued_remarks) &&
                                     !empty($ors->doc_status->date_issued) &&
                                     empty($ors->doc_status->date_received) &&
                                     empty($ors->doc_status->date_issued_back) &&
                                     empty($ors->doc_status->date_received_back) &&
                                     $isAllowedReceive)
                            <strong class="red-text">
                                <i class="fas fa-exclamation-triangle fa-sm"></i> Remarks:
                                {{ $ors->doc_status->issued_remarks }}
                            </strong><br>
                                @elseif (!empty($ors->doc_status->received_remarks) &&
                                         !empty($ors->doc_status->date_issued) &&
                                         !empty($ors->doc_status->date_received) &&
                                         empty($ors->doc_status->date_issued_back) &&
                                         empty($ors->doc_status->date_received_back) &&
                                         $isAllowedIssue)
                            <strong class="red-text">
                                <i class="fas fa-exclamation-triangle fa-sm"></i> Remarks:
                                {{ $ors->doc_status->received_remarks }}
                            </strong><br>
                                @elseif (!empty($ors->doc_status->issued_back_remarks) &&
                                         !empty($ors->doc_status->date_issued) &&
                                         !empty($ors->doc_status->date_received) &&
                                         !empty($ors->doc_status->date_issued_back) &&
                                         empty($ors->doc_status->date_received_back) &&
                                         $isAllowedReceiveBack)
                            <strong class="red-text">
                                <i class="fas fa-exclamation-triangle fa-sm"></i> Remarks:
                                {{ $ors->doc_status->issued_back_remarks }}
                            </strong><br>
                                @elseif (!empty($ors->doc_status->received_back_remarks) &&
                                         !empty($ors->doc_status->date_issued) &&
                                         !empty($ors->doc_status->date_received) &&
                                         !empty($ors->doc_status->date_issued_back) &&
                                         !empty($ors->doc_status->date_received_back) &&
                                         $isAllowedReceive)
                            <strong class="red-text">
                                <i class="fas fa-exclamation-triangle fa-sm"></i> Remarks:
                                {{ $ors->doc_status->received_back_remarks }}
                            </strong><br>
                                @endif
                            @endif
                        </p>

                        <div class="btn-menu-2">
                            <!-- View Attachment Button Section -->
                            @if ($isVisibleViewAttachment)
                            <button type="button" class="btn btn-sm btn-outline-elegant btn-rounded
                                    btn-block waves-effect mb-2"
                                    onclick="$(this).showAttachment('{{ $ors->pr->id }}', 'proc-rfq');">
                                <i class="fas fa-paperclip fa-lg"></i> View Attachment
                            </button>
                            @endif
                            <!-- End View Attachment Button Section -->

                            <!-- View Remarks Button Section -->
                            <button type="button" class="btn btn-sm btn-mdb-color btn-rounded
                                    btn-block waves-effect mb-2"
                                    onclick="$(this).showRemarks('{{ route('proc-ors-burs-show-remarks',
                                                                ['id' => $ors->ors['id']]) }}');">
                                <i class="far fa-comment-dots"></i> View Remarks
                            </button>
                            <!-- End View Remarks Button Section -->
                        </div>
                    </div>
                </div>
                <hr>
                <ul class="btn-menu-3 list-group z-depth-0">
                    <li class="list-action-header list-group-item justify-content-between">
                        <h5><strong><i class="fas fa-pen-nib"></i> Actions</strong></h5>
                    </li>

                    <!-- Regenerate PO/JO Button Section -->
                    @if ($isVisiblePO)
                        @php $countVisible++ @endphp
                    <li class="list-group-item justify-content-between">
                        <a onclick="$(this).redirectToDoc('{{ route('po-jo') }}', '{{ $ors->po_no }}');"
                          class="btn btn-outline-mdb-color waves-effect btn-block btn-md btn-rounded">
                            <i class="fas fa-angle-double-left"></i> Regenerate PO/JO
                        </a>
                    </li>
                    @endif
                    <!-- End Regenerate PO/JO Button Section -->

                    @if (empty($ors->ors['date_obligated']))
                        @if (empty($ors->doc_status->date_issued) &&
                             empty($ors->doc_status->date_received) &&
                             empty($ors->doc_status->date_issued_back) &&
                             empty($ors->doc_status->date_received_back))

                    <!-- Submit Button Section -->
                            @if ($isVisibleIssue)
                                @php $countVisible++ @endphp
                    <li class="list-group-item justify-content-between">
                        <button type="button" class="btn btn-outline-orange waves-effect btn-block btn-md btn-rounded"
                                onclick="$(this).showIssue('{{ route('proc-ors-burs-show-issue', ['id' => $ors->ors['id']]) }}',
                                                           `{{ strtoupper($ors->ors['document_type']).' '.$ors->po_no }}`);">
                            <i class="fas fa-paper-plane"></i> Submit
                        </button>
                    </li>
                            @endif
                    <!-- End Submit Button Section -->

                        @elseif (!empty($ors->doc_status->date_issued) &&
                                 empty($ors->doc_status->date_received) &&
                                 empty($ors->doc_status->date_issued_back) &&
                                 empty($ors->doc_status->date_received_back))

                    <!-- Receive Button Section -->
                            @if ($isVisibleReceive)
                                @php $countVisible++ @endphp
                    <li class="list-group-item justify-content-between">
                        <button type="button" class="btn btn-outline-green waves-effect btn-block btn-md btn-rounded"
                                onclick="$(this).showReceive('{{ route('proc-ors-burs-show-receive', ['id' => $ors->ors['id']]) }}',
                                                             `{{ strtoupper($ors->ors['document_type']).' '.$ors->po_no }}`);">
                            <i class="fas fa-hand-holding"></i> Receive
                        </button>
                    </li>
                            @endif
                    <!-- End Receive Button Section -->

                        @elseif (!empty($ors->doc_status->date_issued) &&
                                 !empty($ors->doc_status->date_received) &&
                                 empty($ors->doc_status->date_issued_back) &&
                                 empty($ors->doc_status->date_received_back))

                    <!-- Obligate Button Section -->
                            @if ($isVisibleObligate)
                                @php $countVisible++ @endphp
                    <li class="list-group-item justify-content-between">
                        <button type="button" class="btn btn-outline-green waves-effect btn-block btn-md btn-rounded"
                                onclick="$(this).showObligate('{{ route('proc-ors-burs-show-obligate', ['id' => $ors->ors['id']]) }}',
                                                              `{{ strtoupper($ors->ors['document_type']).' '.$ors->po_no }}`);">
                            <i class="fas fa-file-signature"></i> Obligate
                        </button>
                    </li>
                            @endif
                    <!-- End Obligate Button Section -->

                    <!-- Submit Back Button Section -->
                            @if ($isVisibledIssueBack)
                                @php $countVisible++ @endphp
                    <li class="list-group-item justify-content-between">
                        <button type="button" class="btn btn-outline-orange waves-effect btn-block btn-md btn-rounded"
                                onclick="$(this).showIssueBack('{{ route('proc-ors-burs-show-issue-back', ['id' => $ors->ors['id']]) }}',
                                                               `{{ strtoupper($ors->ors['document_type']).' '.$ors->po_no }}`);">
                            <i class="fas fa-undo-alt"></i> Submit Back
                        </button>
                    </li>
                            @endif
                    <!-- End Submit Back Button Section -->

                        @elseif (!empty($ors->doc_status->date_issued) &&
                                 !empty($ors->doc_status->date_received) &&
                                 !empty($ors->doc_status->date_issued_back) &&
                                 empty($ors->doc_status->date_received_back))

                    <!-- Receive Back Button Section -->
                            @if ($isVisibleReceiveBack)
                                @php $countVisible++ @endphp
                    <li class="list-group-item justify-content-between">
                        <button type="button" class="btn btn-outline-green waves-effect btn-block btn-md btn-rounded"
                                onclick="$(this).showReceiveBack('{{ route('proc-ors-burs-show-receive-back', ['id' => $ors->ors['id']]) }}',
                                                                 `{{ strtoupper($ors->ors['document_type']).' '.$ors->po_no }}`);">
                            <i class="fas fa-hand-holding"></i> Receive Back
                        </button>
                    </li>
                            @endif
                    <!-- End Receive Back Button Section -->

                        @endif
                    @endif

                    @if (!$countVisible)
                    <li class="list-group-item justify-content-between text-center">
                        No more available actions.
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
@include('modals.edit')
@include('modals.issue')
@include('modals.receive')
@include('modals.issue-back')
@include('modals.receive-back')
@include('modals.obligate')
@include('modals.uacs-items')
@include('modals.print')
@include('modals.attachment')

@endsection

@section('custom-js')

<script type="text/javascript" src="{{ asset('assets/js/input-validation.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/ors-burs.js') }}"></script>
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
