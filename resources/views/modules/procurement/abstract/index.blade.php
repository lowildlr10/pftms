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
                        <i class="fas fa-shopping-cart"></i> Abstract of Bids & Quotations
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
                    <li>
                        <a href="{{ route('rfq') }}" class="waves-effect waves-light white-text">
                            Request for Quotations
                        </a>
                    </li>
                    <li>
                        <i class="fa fa-caret-right mx-2" aria-hidden="true"></i>
                    </li>
                    <li class="active">
                        <a href="{{ route('abstract') }}" class="waves-effect waves-light cyan-text">
                            Abstract of Bids & Quotations
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
                            <a href="{{ route('abstract') }}" class="btn btn-outline-white btn-rounded btn-sm px-2">
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
                                            @sortablelink('rfq.date_abstract', 'RFQ Date', [], ['class' => 'white-text'])
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
                                        @foreach ($list as $listCtr => $abs)
                                    <tr class="hidden-xs">
                                        <td align="center"></td>
                                        <td align="center">
                                            @if($abs->status >= 6)
                                            <i class="fas fa-trophy fa-lg text-success material-tooltip-main"
                                               data-toggle="tooltip" data-placement="right" title="Approved for PO/JO"></i>
                                            @else
                                            <i class="far fa-lg fa-file material-tooltip-main"
                                               data-toggle="tooltip" data-placement="right" title="Pending"></i>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $abs->pr_no }}
                                        </td>
                                        <td>{{ $abs->abstracttract['date_abstract'] }}</td>
                                        <td>{{ $abs->funding['source_name'] }}</td>
                                        <td>
                                            <i class="fas fa-caret-right"></i> {{
                                                (strlen($abs->purpose) > 150) ?
                                                 substr($abs->purpose, 0, 150).'...' : $abs->purpose
                                            }}
                                        </td>
                                        <td>{{ Auth::user()->getEmployee($abs->requestor['id'])->name }}</td>
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
                                            [ PR NO: {{ $abs->pr_no }} ] <i class="fas fa-caret-right"></i> {{
                                                (strlen($abs->purpose) > 150) ?
                                                substr($abs->purpose, 0, 150).'...' : $abs->purpose
                                            }}<br>
                                            <small>
                                                @if($abs->status >= 6)
                                                <b>Status:</b> Approved for PO/JO
                                                @else
                                                <b>Status:</b> Pending
                                                @endif
                                            </small><br>
                                            <small>
                                                <b>Requested By:</b> {{ Auth::user()->getEmployee($abs->requestor['id'])->name }}
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
    @foreach ($list as $listCtr => $abs)
        @php
        $countVisible = 0;
        $isVisiblePrint = true;
        $isVisibleCreate = $isAllowedCreate;
        $isVisibleUpdate = $isAllowedUpdate;
        $isVisibleDelete = $isAllowedDelete;
        $isVisibleViewAttachment = true;
        $isVisibleApprove = $isAllowedApprove;
        $isVisibleRFQ = $isAllowedRFQ;
        $isVisiblePO = $isAllowedPO;

        //Cancel and Un-cancel
        if ($roleHasBudget || $roleHasAccountant) {
            $isVisibleCreate = false;
            $isVisibleUpdate = false;
            $isVisibleDelete = false;
            $isVisibleViewAttachment = false;
            $isVisibleApprove = false;
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
                    <strong>PR NO: {{ $abs->pr_no }}</strong>
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
                                         onclick="$(this).showPrint('{{ $abs->abstract['id'] }}', 'proc_abstract');">
                                     <i class="fas fa-print blue-text"></i> Print Abstract
                                 </button>
                                 @endif
                                 <!-- End Print Button Section -->

                                <!-- Edit Button Section-->
                                @if ($abs->toggle == 'store' && $isVisibleCreate)
                                <button type="button" class="btn btn-outline-mdb-color
                                        btn-sm px-2 waves-effect waves-light"
                                        onclick="$(this).showCreate('{{ route('abstract-show-create', ['id' => $abs->abstract['id']]) }}',
                                                                    '{{ $abs->abstract['id'] }}');">
                                    <i class="fas fa-pencil-alt green-text"></i> Create
                                </button>
                                @endif
                                <!-- End Edit Button Section -->

                                <!-- Edit Button Section-->
                                @if ($abs->toggle == 'update' && $isVisibleUpdate)
                                <button type="button" class="btn btn-outline-mdb-color
                                        btn-sm px-2 waves-effect waves-light"
                                        onclick="$(this).showEdit('{{ route('abstract-show-edit', ['id' => $abs->abstract['id']]) }}',
                                                                  '{{ $abs->abstract['id'] }}');">
                                    <i class="fas fa-edit orange-text"></i> Edit
                                </button>
                                @endif
                                <!-- End Edit Button Section -->

                                <!-- Edit Button Section-->
                                @if ($abs->toggle == 'store' && $isVisibleDelete)
                                <button type="button" class="btn btn-outline-mdb-color
                                        btn-sm px-2 waves-effect waves-light"
                                        disabled="disabled">
                                    <i class="fas fa-trash-alt red-text"></i> Delete
                                </button>
                                @elseif ($abs->toggle == 'update' && $isVisibleDelete)
                                    @if (!$abs->abstract['date_abstract_approved'])
                                <button type="button" class="btn btn-outline-mdb-color
                                        btn-sm px-2 waves-effect waves-light"
                                        onclick="$(this).showDelete('{{ route('abstract-delete-items',
                                                                              ['id' => $abs->abstract['id']]) }}',
                                                                              '{{ $abs->pr_no }}');">
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
                                <!-- End Edit Button Section -->

                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <p>
                            <strong>PR Date: </strong> {{ $abs->date_pr }}<br>
                            <strong>RFQ Date: </strong> {{ $abs->rfq['date_canvass'] }}<br>
                            <strong>Abstract Date: </strong> {{ $abs->abstract['date_abstract'] }}<br>
                            <strong>Charging: </strong> {{ $abs->funding['source_name'] }}<br>
                            <strong>Purpose: </strong> {{
                                (strlen($abs->purpose) > 150) ?
                                substr($abs->purpose, 0, 150).'...' : $abs->purpose
                            }}<br>
                            <strong>Requested By: </strong> {{ Auth::user()->getEmployee($abs->requestor['id'])->name }}<br>
                        </p>

                        <div class="btn-menu-2">
                            <!-- View Attachment Button Section -->
                                @if ($isVisibleViewAttachment)
                            <button type="button" class="btn btn-sm btn-outline-elegant btn-rounded
                                    btn-block waves-effect mb-2"
                                    onclick="$(this).showAttachment('{{ $abs->id }}', 'proc-rfq');">
                                <i class="fas fa-paperclip fa-lg"></i> View Attachment
                            </button>
                                @endif
                            <!-- End View Attachment Button Section -->

                            <button type="button" class="btn btn-sm btn-mdb-color btn-rounded
                                    btn-block waves-effect mb-2"
                                    onclick="$(this).showItem('{{ route('pr-show-items', ['id' => $abs->id]) }}');">
                                <i class="far fa-list-alt fa-lg"></i> View Items
                            </button>
                        </div>
                    </div>
                </div>
                <hr>
                <ul class="btn-menu-3 list-group z-depth-1">
                    <li class="list-action-header list-group-item justify-content-between">
                        <h5><strong><i class="fas fa-pen-nib"></i> Actions</strong></h5>
                    </li>

                    <!-- Regenerate RFQ Button Section -->
                        @if ($isVisibleRFQ)
                            @php $countVisible++ @endphp
                    <li class="list-group-item justify-content-between">
                        <a onclick="$(this).redirectToDoc('{{ route('rfq') }}', '{{ $abs->id }}');"
                          class="btn btn-outline-mdb-color waves-effect btn-block btn-md btn-rounded">
                            <i class="fas fa-angle-double-left"></i> Regenerate RFQ
                        </a>
                    </li>
                        @endif
                    <!-- End Regenerate RFQ Button Section -->

                    @if ($abs->status >= 6)

                    <!-- Generate PO/JO Button Section -->
                        @if ($isVisiblePO)
                            @php $countVisible++ @endphp
                    <li class="list-group-item justify-content-between">
                        <a onclick="$(this).redirectToDoc('{{ route('po-jo') }}', '{{ $abs->id }}');"
                           class="btn btn-outline-mdb-color waves-effect btn-block btn-md btn-rounded">
                            Generate PO/JO <i class="fas fa-angle-double-right"></i>
                        </a>
                    </li>
                        @endif
                    <!-- End Generate PO/JO Button Section -->

                    @else

                    <!-- To PO/JO Button Section -->
                        @if ($isVisibleApprove)
                            @php $countVisible++ @endphp
                    <li class="list-group-item justify-content-between">
                        <button type="button" class="btn btn-outline-green waves-effect btn-md btn-block btn-rounded"
                                onclick="$(this).showApprove('{{ route('abstract-approve', ['id' => $abs->abstract['id']]) }}',
                                                             '{{ $abs->pr_no }}');">
                            <i class="fas fa-trophy"></i> To PO/JO
                        </button>
                    </li>
                        @endif
                    <!-- End To PO/JO Button Section -->

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
@include('modals.create')
@include('modals.edit')
@include('modals.approve')
@include('modals.delete')
@include('modals.print')
@include('modals.attachment')

@endsection

@section('custom-js')

<script type="text/javascript" src="{{ asset('plugins/select2/js/select2.full.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/input-validation.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/abstract.js') }}"></script>
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
