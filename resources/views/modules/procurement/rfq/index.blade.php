@extends('layouts.app')

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
                <hr class="white hidden-xs">
                <ul class="breadcrumb mdb-color darken-3 mb-0 p-1 white-text hidden-xs">
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
                                    data-target="#top-fluid-modal" data-toggle="modal"
                                    onclick="$('#search').focus()">
                                <i class="fas fa-search"></i> {{ !empty($keyword) ? (strlen($keyword) > 15) ?
                                'Search: '.substr($keyword, 0, 15).'...' : 'Search: '.$keyword : '' }}
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
                                            @sortablelink('pr_no', 'PR No', [], ['class' => 'white-text'])
                                        </th>
                                        <th class="th-md" width="10%">
                                            @sortablelink('rfq.date_canvass', 'RFQ Date', [], ['class' => 'white-text'])
                                        </th>
                                        <th class="th-md" width="10%">
                                            @sortablelink('funding.source_name', 'Funding/Charging', [], ['class' => 'white-text'])
                                        </th>
                                        <th class="th-md" width="50%">
                                            @sortablelink('purpose', 'Purpose', [], ['class' => 'white-text'])
                                        </th>
                                        <th class="th-md" width="13%">
                                            @sortablelink('requestor.firstname', 'Requested By', [], ['class' => 'white-text'])
                                        </th>
                                        <th class="th-md" width="3%"></th>
                                    </tr>
                                </thead>
                                <!--Table head-->

                                <!--Table body-->
                                <tbody>
                                    @if (count($list) > 0)
                                        @foreach ($list as $listCtr => $rfq)
                                    <tr class="hidden-xs">
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
                                            {{ $rfq->pr_no }}
                                        </td>
                                        <td>{{ $rfq->rfq['date_canvass'] }}</td>
                                        <td>{{ $rfq->funding['source_name'] }}</td>
                                        <td>
                                            <i class="fas fa-caret-right"></i> {{
                                                (strlen($rfq->purpose) > 150) ?
                                                substr($rfq->purpose, 0, 150).'...' : $rfq->purpose
                                            }}
                                        </td>
                                        <td>{{ Auth::user()->getEmployee($rfq->requestor['id'])->name }}</td>
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
                                            [ QTN NO: {{ $rfq->pr_no }} ] <i class="fas fa-caret-right"></i> {{
                                                (strlen($rfq->purpose) > 150) ?
                                                substr($rfq->purpose, 0, 150).'...' : $rfq->purpose
                                            }}<br>
                                            <small>
                                                @if (!empty($rfq->doc_status->date_issued) &&
                                                    empty($rfq->doc_status->date_received))
                                                <b>Status:</b> Issued
                                                @elseif (!empty($rfq->doc_status->date_issued) &&
                                                        !empty($rfq->doc_status->date_received))
                                                <b>Status:</b> Received
                                                @else
                                                <b>Status:</b> Pending
                                                @endif
                                            </small><br>
                                            <small>
                                                <b>Requested By:</b> {{ Auth::user()->getEmployee($rfq->requestor['id'])->name }}
                                            </small>
                                        </td>
                                    </tr>
                                        @endforeach
                                    @else
                                    <tr>
                                        <td colspan="8" class="text-center py-5">
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
@if (count($list) > 0)
    @foreach ($list as $listCtr => $rfq)
        @php
        $countVisible = 0;
        $isVisiblePrint = true;
        $isVisibleUpdate = $isAllowedUpdate;
        $isVisibleViewAttachment = true;
        $isVisibleIssue = $isAllowedIssue;
        $isVisibleReceive = $isAllowedReceive;
        $isVisiblePR = $isAllowedPR;
        $isVisibleAbstract = $isAllowedAbstract;

        if ($roleHasBudget || $roleHasAccountant) {
            if (Auth::user()->id == $rfq->requestor['id']) {
                $isVisibleUpdate = $isAllowedUpdate ? $isAllowedUpdate : false;
                $isVisibleViewAttachment = true;
            } else {
                $isVisibleUpdate = false;
                $isVisibleViewAttachment = false;
            }


        }
        @endphp

<div class="modal custom-rightmenu-modal fade right" id="right-modal-{{ $listCtr + 1 }}" tabindex="-1"
     role="dialog">
    <div class="modal-dialog modal-full-height modal-right" role="document">
        <!--Content-->
        <div class="modal-content">
            <!--Header-->
            <div class="modal-header stylish-color-dark white-text">
                <h7>
                    <i class="fas fa-shopping-cart"></i>
                    <strong>QUOTATION NO: {{ $rfq->pr_no }}</strong>
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
                                        onclick="$(this).showPrint('{{ $rfq->rfq['id'] }}', 'proc_rfq');">
                                    <i class="fas fa-print blue-text"></i> Print RFQ
                                </button>
                                @endif
                                <!-- End Print Button Section -->

                                <!-- Edit Button Section-->
                                @if ($isVisibleUpdate)
                                <button type="button" class="btn btn-outline-mdb-color
                                        btn-sm px-2 waves-effect waves-light"
                                        onclick="$(this).showEdit('{{ route('rfq-show-edit', ['id' => $rfq->rfq['id']]) }}');">
                                    <i class="fas fa-edit orange-text"></i> Edit
                                </button>
                                @endif
                                <!-- End Edit Button Section -->

                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <p>
                            <strong>PR Date: </strong> {{ $rfq->date_pr }}<br>
                            <strong>RFQ Date: </strong> {{ $rfq->rfq['date_canvass'] }}<br>
                            <strong>Charging: </strong> {{ $rfq->funding['source_name'] }}<br>
                            <strong>Purpose: </strong> {{
                                (strlen($rfq->purpose) > 150) ?
                                substr($rfq->purpose, 0, 150).'...' : $rfq->purpose
                            }}<br>
                            <strong>Requested By: </strong> {{ Auth::user()->getEmployee($rfq->requestor['id'])->name }}<br>
                        </p>

                        <div class="btn-menu-2">
                            <!-- View Items Button Section -->
                            <button type="button" class="btn btn-sm btn-mdb-color btn-rounded
                                    btn-block waves-effect mb-2"
                                    onclick="$(this).showItem('{{ route('pr-show-items', ['id' => $rfq->id]) }}');">
                                <i class="far fa-list-alt fa-lg"></i> View Items
                            </button>
                            <!-- End View Items Button Section -->

                            <!-- View Attachment Button Section -->
                            @if ($isVisibleViewAttachment)
                            <button type="button" class="btn btn-sm btn-outline-elegant btn-rounded
                                    btn-block waves-effect mb-2"
                                    onclick="$(this).showAttachment('{{ $rfq->id }}', 'proc-rfq');">
                                <i class="fas fa-paperclip fa-lg"></i> View Attachment
                            </button>
                            @endif
                            <!-- End View Attachment Button Section -->
                        </div>
                    </div>
                </div>
                <hr>
                <ul class="btn-menu-3 list-group z-depth-1">
                    <li class="list-action-header list-group-item justify-content-between">
                        <h5><strong><i class="fas fa-pen-nib"></i> Actions</strong></h5>
                    </li>

                    <!-- Regenerate PR Button Section -->
                    @if ($isVisiblePR)
                        @php $countVisible++ @endphp
                    <li class="list-group-item justify-content-between">
                        <a onclick="$(this).redirectToDoc('{{ route('pr') }}', '{{ $rfq->id }}');"
                          class="btn btn-outline-mdb-color waves-effect btn-block btn-md btn-rounded">
                            <i class="fas fa-angle-double-left"></i> Regenerate PR
                        </a>
                    </li>
                    @endif
                    <!-- End Regenerate PR Button Section -->

                    @if (empty($rfq->doc_status->date_issued) &&
                         empty($rfq->doc_status->date_received))

                    <!-- Issue Button Section -->
                        @if ($isAllowedIssue)
                            @php $countVisible++ @endphp
                    <li class="list-group-item justify-content-between">
                        <button type="button" class="btn btn-outline-orange waves-effect btn-block btn-md btn-rounded"
                                onclick="$(this).showIssue('{{ route('rfq-show-issue', ['id' => $rfq->rfq['id']]) }}');">
                            <i class="fas fa-paper-plane"></i> Issue
                        </button>
                    </li>
                        @endif
                    <!-- End Issue Button Section -->

                    @elseif (!empty($rfq->doc_status->date_issued) &&
                              empty($rfq->doc_status->date_received))

                    <!-- Receive Button Section -->
                        @if ($isAllowedReceive)
                            @php $countVisible++ @endphp
                    <li class="list-group-item justify-content-between">
                        <button type="button" class="btn btn-outline-success waves-effect btn-block btn-md btn-rounded"
                                onclick="$(this).showReceive('{{ route('rfq-show-receive', ['id' => $rfq->rfq['id']]) }}');">
                            <i class="fas fa-hand-holding"></i> Receive
                        </button>
                    </li>
                        @endif
                    <!-- End Receive Button Section -->

                    @else

                    <!-- Generate Abstract Button Section -->
                        @if ($isVisibleAbstract)
                            @php $countVisible++ @endphp
                    <li class="list-group-item justify-content-between">
                        <a onclick="$(this).redirectToDoc('{{ route('abstract') }}', '{{ $rfq->id }}');"
                          class="btn btn-outline-mdb-color waves-effect btn-block btn-md btn-rounded">
                            Generate Abstract <i class="fas fa-angle-double-right"></i>
                        </a>
                    </li>
                        @endif
                    <!-- End Generate Abstract Button Section -->

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
@include('modals.print')
@include('modals.attachment')

@endsection

@section('custom-js')

<script type="text/javascript" src="{{ asset('assets/js/input-validation.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/rfq.js') }}"></script>
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
