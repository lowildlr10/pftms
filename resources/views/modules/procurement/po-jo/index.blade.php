@extends('layouts.app')

@section('main-content')

<div class="row wow animated fadeIn">
    <section class="mb-5 col-12 module-container">
        <div class="card mdb-color darken-3">
            <div class="card-body">
                <h5 class="card-title white-text">
                    <strong>
                        <i class="fas fa-shopping-cart"></i> Purchase/Job Order
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
                    <li class="active">
                        <a href="{{ url('procurement/po-jo') }}" class="waves-effect waves-light cyan-text">
                            Purchase/Job Order
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
                            <a href="{{ route('po-jo') }}" class="btn btn-outline-white btn-rounded btn-sm px-2">
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
                                    <tr class="hidden-xs">
                                        <th class="th-md" width="3%"></th>
                                        <th class="th-md" width="3%" style="text-align: center;"></th>
                                        <th class="th-md" width="8%">
                                            @sortablelink('pr_no', 'PR No', [], ['class' => 'white-text'])
                                        </th>
                                        <th class="th-md" width="10%">
                                            @sortablelink('date_pr', 'PR Date', [], ['class' => 'white-text'])
                                        </th>
                                        <th class="th-md" width="10%">
                                            @sortablelink('funding.source_name', 'Funding/Charging', [], ['class' => 'white-text'])
                                        </th>
                                        <th class="th-md" width="53%">
                                            @sortablelink('purpose', 'Purpose', [], ['class' => 'white-text'])
                                        </th>
                                        <th class="th-md" width="13%">
                                            @sortablelink('requestor.firstname', 'Requested By', [], ['class' => 'white-text'])
                                        </th>
                                    </tr>
                                </thead>
                                <!--Table head-->

                                <!--Table body-->
                                <tbody>
                                    @if (count($list) > 0)
                                        @foreach ($list as $listCtr => $pr)
                                    <tr class="hidden-xs">
                                        <td align="center">
                                            <i class="fas fa-folder fa-lg material-tooltip-main"
                                               data-toggle="tooltip" data-placement="right" title="PR Document"></i>
                                        </td>
                                        <td align="center">
                                        </td>
                                        <td align="center">
                                            <a class="btn btn-link p-0" href="{{ route('po-jo', ['keyword' => $pr->pr_no]) }}">
                                                {{ $pr->pr_no }}
                                            </a>
                                        </td>
                                        <td>{{ $pr->date_pr }}</td>
                                        <td>{{ $pr->project }}</td>
                                        <td>
                                            <i class="fas fa-caret-right"></i> {{
                                                (strlen($pr->purpose) > 150) ?
                                                substr($pr->purpose, 0, 150).'...' : $pr->purpose
                                            }}
                                        </td>
                                        <td>{{ Auth::user()->getEmployee($pr->requestor['id'])->name }}</td>
                                    </tr>
                                    <tr class="d-none show-xs">
                                        <td colspan="7" class="text-center">
                                            <b>PR NO : {{ $pr->pr_no }}</b>
                                        </td>
                                    </tr>
                                    <tr class="blue-grey lighten-2 po-jo-table-items">
                                        <td colspan="7">
                                            <div class="card card-cascade narrower mx-3 my-2">
                                                <div class="card-body p-2">
                                                    <table class="table table-sm z-depth-1 mb-0">

                                                        @if (count($pr->po) > 0)
                                                        <thead class="mdb-color darken-1 white-text hidden-xs">
                                                            <tr class="hidden-xs">
                                                                <th class="th-md" width="3%"></th>
                                                                <th class="th-md" width="10%" style="text-align: center;"><strong>PO/JO Number</strong></th>
                                                                <th class="th-md" width="84%" style="text-align: center;"><strong>Awarded To</strong></th>
                                                                <th class="th-md" width="3%"></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($pr->po as $listCtr1 => $item)
                                                            <tr class="row-item hidden-xs">
                                                                <td align="center">
                                                                    @if (empty($item->date_cancelled))
                                                                        @if ($item->status == 3)
                                                                    <i class="fas fa-ban fa-lg text-danger material-tooltip-main"
                                                                       data-toggle="tooltip" data-placement="right" title="Cancelled"></i>
                                                                        @elseif ($item->status == 8)
                                                                    <i class="fas fa-truck fa-lg black-text material-tooltip-main"
                                                                       data-toggle="tooltip" data-placement="right" title="For Delivery"></i>
                                                                        @elseif ($item->status >= 9)
                                                                    <i class="fas fa-check fa-lg text-success material-tooltip-main"
                                                                       data-toggle="tooltip" data-placement="right" title="Complete"></i>
                                                                    @else
                                                                            @if (empty($item->date_accountant_signed) && empty($item->date_po_approved))
                                                                    <i class="far fa-lg fa-file material-tooltip-main"
                                                                       data-toggle="tooltip" data-placement="right" title="Pending"></i>
                                                                            @elseif (!empty($item->date_accountant_signed) && empty($item->date_po_approved))
                                                                    <i class="fas fa-signature fa-lg text-success material-tooltip-main"
                                                                       data-toggle="tooltip" data-placement="right" title="Clear/Signed by Accountant"></i>
                                                                            @elseif (!empty($item->date_accountant_signed) && !empty($item->date_po_approved))
                                                                    <i class="fas fa-thumbs-up fa-lg text-success material-tooltip-main"
                                                                       data-toggle="tooltip" data-placement="right" title="Approved"></i>
                                                                            @endif
                                                                        @endif
                                                                    @else
                                                                    <i class="fa fa-ban fa-lg red-text material-tooltip-main"
                                                                       data-toggle="tooltip" data-placement="right" title="Cancelled"></i>
                                                                    @endif
                                                                </td>
                                                                <td align="center">
                                                                    <strong>{{ $item->po_no }}</strong>

                                                                    @if ($item->with_ors_burs == 'y')
                                                                    <br><em>
                                                                        <small class="grey-text">(ORS/BURS Created)</small>
                                                                    </em>
                                                                    @endif
                                                                </td>
                                                                <td align="center">
                                                                    <strong>
                                                                        <h6 class="mb-0">
                                                                            {{ $item->company_name }}
                                                                            <span class="mdb-color-text">
                                                                                [ {{ strtoupper($item->document_type) }} Document ]
                                                                            </span>
                                                                        </h6>
                                                                        <span class="text-info">
                                                                            [ Created On {{ $item->created_at }} ]
                                                                        </span>
                                                                    </strong>

                                                                @if (!empty($item->date_cancelled))
                                                                    <strong class="text-danger">[ Cancelled On: {{ $item->date_cancelled }}]</strong>
                                                                @endif

                                                                </td>
                                                                <td align="center">
                                                                    <a class="btn-floating btn-sm btn-mdb-color p-2 waves-effect material-tooltip-main mr-0"
                                                                        data-target="#right-modal-{{ ($listCtr + 1) + (($list->currentpage() - 1) * $list->perpage()) }}-{{ $listCtr1 }}"
                                                                        data-toggle="modal" data-toggle="tooltip" data-placement="left" title="Open">
                                                                        <i class="fas fa-folder-open"></i>
                                                                    </a>
                                                                </td>
                                                            </tr>

                                                            <tr class="d-none show-xs">
                                                                <td data-target="#right-modal-{{ ($listCtr + 1) + (($list->currentpage() - 1) * $list->perpage()) }}-{{ $listCtr1 }}"
                                                                    data-toggle="modal" colspan="4" class="px-3">
                                                                    [ {{ strtoupper($item->document_type) }} NO: {{ $item->po_no }} ] <i class="fas fa-caret-right"></i>
                                                                    {{ $item->company_name }}
                                                                    @if ($item->with_ors_burs == 'y')
                                                                    <br><em>
                                                                        <small class="grey-text">(ORS/BURS Created)</small>
                                                                    </em>
                                                                    @endif
                                                                    <br>
                                                                    <small>
                                                                        @if (empty($item->date_cancelled))
                                                                            @if ($item->status == 3)
                                                                        <b>Status:</b> Cancelled
                                                                            @elseif ($item->status == 8)
                                                                        <b>Status:</b> For Delivery
                                                                            @elseif ($item->status >= 9)
                                                                        <b>Status:</b> Complete
                                                                            @else
                                                                                @if (empty($item->date_accountant_signed) && empty($item->date_po_approved))
                                                                        <b>Status:</b> Pending
                                                                                @elseif (!empty($item->date_accountant_signed) && empty($item->date_po_approved))
                                                                        <b>Status:</b> Clear/Signed by the Accountant
                                                                                @elseif (!empty($item->date_accountant_signed) && !empty($item->date_po_approved))
                                                                        <b>Status:</b> Approved
                                                                                @endif
                                                                            @endif
                                                                        @else
                                                                        <b>Status:</b> Cancelled
                                                                        @endif
                                                                    </small><br>
                                                                    <small>
                                                                        <b>Requested By:</b> {{ Auth::user()->getEmployee($pr->requestor['id'])->name }}
                                                                    </small>
                                                                </td>
                                                            </tr>
                                                            @endforeach

                                                            <tr class="row-item hidden-xs">
                                                                <td colspan="4" class="p-0">
                                                                    <button class="btn btn-outline-mdb-color btn-block btn-sm waves-effect py-3"
                                                                            onclick="$(this).showCreate('{{ route('po-jo-show-create', ['prID' => $pr->id]) }}');">
                                                                        <i class="fas fa-plus"></i> Add PO/JO Document
                                                                    </button>
                                                                </td>
                                                            </tr>

                                                            <tr class="row-item d-none show-xs">
                                                                <td colspan="4" class="p-0">
                                                                    <button class="btn btn-link btn-block btn-sm waves-effect py-2"
                                                                            onclick="$(this).showCreate('{{ route('po-jo-show-create', ['prID' => $pr->id]) }}');">
                                                                        <i class="fas fa-plus"></i> Add PO/JO Document
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                        @endif

                                                    </table>
                                                </div>
                                            </div>
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
                            {{ $list->links('pagination') }}
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
    @foreach ($list as $listCtr => $pr)
        @if (count($pr->po) > 0)
            @foreach ($pr->po as $listCtr1 => $item)
                @php
                $countVisible = 0;
                $isVisiblePrint = true;
                $isVisibleCreate = $isAllowedCreate;
                $isVisibleUpdate = $isAllowedUpdate;
                $isVisibleDelete = $isAllowedDelete;
                $isVisibleViewAttachment = true;
                $isVisibleAccountantSigned = $isAllowedAccountantSigned;
                $isVisibleApprove = $isAllowedApprove;
                $isVisibleIssue = $isAllowedIssue;
                $isVisibleReceive = $isAllowedReceive;
                $isVisibleCancel = $isAllowedCancel;
                $isVisibleUncancel = $isAllowedUncancel;
                $isVisibleDelivery = $isAllowedDelivery;
                $isVisibleInspection = $isAllowedInspection;
                $isVisibleORS = $isAllowedORS;
                $isVisibleORSCreate = $isAllowedORSCreate;
                $isVisibleAbstract = $isAllowedAbstract;
                $isVisibleIAR = $isAllowedIAR;

                if ($roleHasBudget || $roleHasAccountant) {
                    $isVisibleCreate = false;
                    $isVisibleUpdate = false;
                    $isVisibleDelete = false;
                    $isVisibleViewAttachment = false;
                    $isVisibleApprove = false;
                    $isVisibleIssue = false;
                    $isVisibleReceive = false;
                    $isVisibleCancel = false;
                    $isVisibleUncancel = false;
                    $isVisibleDelivery = false;
                    $isVisibleInspection = false;
                }
                @endphp
<div id="right-modal-{{ ($listCtr + 1) + (($list->currentpage() - 1) * $list->perpage()) }}-{{ $listCtr1 }}"
     tabindex="-1" class="modal custom-rightmenu-modal fade right" role="dialog">
    <div class="modal-dialog modal-full-height modal-right" role="document">
        <!--Content-->
        <div class="modal-content">
            <!--Header-->
            <div class="modal-header stylish-color-dark white-text">
                <h7>
                    <i class="fas fa-shopping-cart"></i>
                    <strong>{{ strtoupper($item->document_type) }} NO: {{ $item->po_no }}</strong>
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

                                <!-- Print Button Section-->
                                @if ($isVisiblePrint)
                                <button type="button" class="btn btn-outline-mdb-color
                                        btn-sm px-2 waves-effect waves-light"
                                        onclick="$(this).showPrint('{{ $item->id }}', 'proc_{{ $item->document_type }}');">
                                    <i class="fas fa-print blue-text"></i> Print PO/JO
                                </button>
                                @endif
                                <!-- End Print Button Section -->

                                <!-- Edit Button Section-->
                                @if ($isVisibleUpdate)
                                <button type="button" class="btn btn-outline-mdb-color
                                        btn-sm px-2 waves-effect waves-light"
                                        onclick="$(this).showEdit('{{ route('po-jo-show-edit', ['id' => $item->id]) }}');">
                                    <i class="fas fa-edit orange-text"></i> Edit
                                </button>
                                @endif
                                <!-- End Edit Button Section -->

                                <!-- Delete Button Section-->
                                @if ($isVisibleDelete)
                                <button type="button" class="btn btn-outline-mdb-color
                                        btn-sm px-2 waves-effect waves-light"
                                        onclick="$(this).showDelete('{{ route('po-jo-delete',
                                                                              ['id' => $item->id]) }}',
                                                                              `{{ strtoupper($item->document_type).' '.$item->po_no }}`);">
                                    <i class="fas fa-trash-alt red-text"></i> Delete
                                </button>
                                @endif
                                <!-- End Delete Button Section -->

                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <p>
                            <strong>PR Date: </strong> {{ $pr->date_pr }}<br>
                            <strong>{{ strtoupper($item->document_type) }} Date: </strong> {{ $item->date_po }}<br>
                            <strong>Charging: </strong> {{ $pr->funding['source_name'] }}<br>
                            <strong>Purpose: </strong> {{
                                (strlen($pr->purpose) > 150) ?
                                substr($pr->purpose, 0, 150).'...' : $pr->purpose
                            }}<br>
                            <strong>Awarded To: </strong> {{ $item->company_name }}<br>
                            <strong>Requested By: </strong> {{ Auth::user()->getEmployee($pr->requestor['id'])->name }}<br>
                        </p>

                        <div class="btn-menu-2">
                            <!-- View Attachment Button Section-->
                            @if ($isVisibleViewAttachment)
                            <button type="button" class="btn btn-sm btn-outline-elegant btn-rounded
                                    btn-block waves-effect mb-2"
                                    onclick="$(this).showAttachment('{{ $pr->id }}', 'proc-rfq');">
                                <i class="fas fa-paperclip fa-lg"></i> View Attachment
                            </button>
                            @endif
                            <!-- End View Attachment Button Section -->

                            <!-- View Items Button Section-->
                            <button type="button" class="btn btn-sm btn-mdb-color btn-rounded
                                    btn-block waves-effect mb-2"
                                    onclick="$(this).showPrint('{{ $pr->id }}', 'proc-po-jo');">
                                <i class="far fa-list-alt fa-lg"></i> View Items
                            </button>
                            <!-- End View Items Button Section -->
                        </div>
                    </div>
                </div>
                <hr>
                <ul class="btn-menu-3 list-group z-depth-0">
                    <li class="list-action-header list-group-item justify-content-between">
                        <h5><strong><i class="fas fa-pen-nib"></i> Actions</strong></h5>
                    </li>

                    <!-- Regenerate Abstract Button Section -->
                    @if ($isVisibleAbstract)
                        @php $countVisible++ @endphp
                    <li class="list-group-item justify-content-between">
                        <a onclick="$(this).redirectToDoc('{{ route('abstract') }}', '{{ $pr->id }}');"
                           class="btn btn-outline-mdb-color waves-effect btn-block btn-md btn-rounded">
                            <i class="fas fa-angle-double-left"></i> Regenerate Abstract
                        </a>
                    </li>
                    @endif
                    <!-- End Regenerate Abstract Button Section -->

                    @if (!empty($item->date_cancelled))

                    <!-- Uncancel Button Section -->
                        @if ($isVisibleUncancel)
                            @php $countVisible++ @endphp
                    <li class="list-group-item justify-content-between">
                        <button type="button" class="btn btn-outline-blue-grey waves-effect btn-block btn-md btn-rounded"
                                onclick="$(this).showUncancel('{{ route('po-jo-uncancel', ['id' => $item->id]) }}',
                                                              `{{ strtoupper($item->document_type).' '.$item->po_no }}`);">
                            <i class="fas fa-lock-open"></i> Restore Document
                        </button>
                    </li>
                        @endif
                    <!-- End Uncancel Button Section -->

                    @else
                        @if ($item->with_ors_burs == 'y')

                    <!-- Edit ORS/BURS Button Section -->
                            @if ($isVisibleORS)
                                @php $countVisible++ @endphp
                    <li class="list-group-item justify-content-between">
                        <a type="button" class="btn btn-outline-warning waves-effect btn-block btn-md btn-rounded"
                           onclick="$(this).redirectToDoc('{{ route('proc-ors-burs') }}', '{{ $item->po_no }}');">
                            <i class="fas fa-file-signature orange-text"></i> Edit ORS/BURS
                        </a>
                    </li>
                            @endif
                    <!-- End Edit ORS/BURS Button Section -->

                        @else

                    <!-- Create ORS/BURS Button Section -->
                            @if ($isVisibleORSCreate)
                                @php $countVisible++ @endphp
                    <li class="list-group-item justify-content-between">
                        <button type="button" class="btn btn-outline-green waves-effect btn-block btn-md btn-rounded"
                                onclick="$(this).showCreateORS('{{ route('po-jo-create-ors-burs', ['poID' => $item->id]) }}',
                                                               `{{ strtoupper($item->document_type).' '.$item->po_no }}`);">
                            <i class="fas fa-pencil-alt green-text"></i> Create ORS/BURS
                        </button>
                    </li>
                            @endif
                    <!-- End Create ORS/BURS Button Section -->

                        @endif

                        @if (empty($item->date_accountant_signed) && empty($item->date_po_approved))

                    <!-- Cleared/Signed by Accountant Button Section -->
                            @if ($isVisibleAccountantSigned)
                                @php $countVisible++ @endphp
                    <li class="list-group-item justify-content-between">
                        <button type="button" class="btn btn-outline-success waves-effect btn-block btn-md btn-rounded"
                                onclick="$(this).showAccountantSigned('{{ route('po-jo-accountant-signed', ['id' => $item->id]) }}',
                                                                      `{{ strtoupper($item->document_type).' '.$item->po_no }}`);">
                            <i class="fas fa-signature green-text"></i> Cleared/Signed by Accountant
                        </button>
                    </li>
                            @endif
                    <!-- End Cleared/Signed by Accountant Button Section -->

                        @elseif (!empty($item->date_accountant_signed) && empty($item->date_po_approved))

                    <!-- Approve Button Section -->
                            @if ($isVisibleApprove)
                                @php $countVisible++ @endphp
                    <li class="list-group-item justify-content-between">
                        <button type="button" class="btn btn-outline-success waves-effect btn-block btn-md btn-rounded"
                                onclick="$(this).showApprove('{{ route('po-jo-approve', ['id' => $item->id]) }}',
                                                             `{{ strtoupper($item->document_type).' '.$item->po_no }}`);">
                            <i class="fas fa-thumbs-up green-text"></i> Approve
                        </button>
                    </li>
                            @endif
                    <!-- End Approve Button Section -->

                        @elseif (!empty($item->date_accountant_signed) && !empty($item->date_po_approved))
                            @if (!empty($item->doc_status->date_issued) && $item->status != 3)

                    <!-- Cancel Button Section -->
                                @if ($isVisibleCancel)
                                    @php $countVisible++ @endphp
                    <li class="list-group-item justify-content-between">
                        <button type="button" class="btn btn-outline-danger waves-effect btn-block btn-md btn-rounded"
                                onclick="$(this).showCancel('{{ route('po-jo-cancel', ['id' => $item->id]) }}',
                                                            `{{ strtoupper($item->document_type).' '.$item->po_no }}`);">
                            <i class="fas fa-ban fa-lg red-text"></i> Cancel
                        </button>
                    </li>
                                @endif
                    <!-- End Cancel Button Section -->

                                @if ($item->with_ors_burs == 'y')
                                    @if ($item->status == 7)

                    <!-- For Delivery Button Section -->
                                        @if ($isVisibleDelivery)
                                            @php $countVisible++ @endphp
                    <li class="list-group-item justify-content-between">
                        <button type="button" class="btn btn-outline-black waves-effect btn-block btn-md btn-rounded"
                                onclick="$(this).showForDelivery('{{ route('po-jo-delivery', ['id' => $item->id]) }}',
                                                                 `{{ strtoupper($item->document_type).' '.$item->po_no }}`);">
                            <i class="fas fa-truck"></i> For Delivery
                        </button>
                    </li>
                                        @endif
                    <!-- End For Delivery Button Section -->

                                    @elseif ($item->status == 8)

                    <!-- Inspection Button Section -->
                                        @if ($isVisibleInspection)
                                            @php $countVisible++ @endphp
                    <li class="list-group-item justify-content-between">
                        <button type="button" class="btn btn-outline-indigo waves-effect btn-block btn-md btn-rounded"
                                onclick="$(this).showForInspection('{{ route('po-jo-inspection', ['id' => $item->id]) }}',
                                                                   `{{ strtoupper($item->document_type).' '.$item->po_no }}`);">
                            <i class="fas fa-search"></i> Inspection
                        </button>
                    </li>
                                        @endif
                    <!-- End Inspection Button Section -->

                                    @elseif ($item->status >= 9)

                    <!-- Generate IAR Button Section -->
                                        @if ($isVisibleIAR)
                                            @php $countVisible++ @endphp
                    <li class="list-group-item justify-content-between">
                        <a type="button" class="btn btn-outline-mdb-color waves-effect btn-block btn-md btn-rounded"
                           onclick="$(this).redirectToDoc('{{ route('iar') }}', '{{ $item->pr_id }}');">
                            Generate IAR <i class="fas fa-angle-double-right"></i>
                        </a>
                    </li>
                                        @endif
                    <!-- End Generate IAR Button Section -->

                                    @endif
                                @endif
                            @endif

                            @if (empty($item->doc_status->date_issued) && empty($item->doc_status->date_received))

                    <!-- Issue Button Section -->
                                @if ($isVisibleIssue)
                                    @php $countVisible++ @endphp
                    <li class="list-group-item justify-content-between">
                        <button type="button" class="btn btn-outline-warning waves-effect btn-block btn-md btn-rounded"
                                onclick="$(this).showIssue('{{ route('po-jo-show-issue', ['id' => $item->id]) }}',
                                                           `{{ strtoupper($item->document_type).' '.$item->po_no }}`);">
                            <i class="fas fa-paper-plane orange-text"></i> Issue
                        </button>
                    </li>
                                @endif
                    <!-- End Issue Button Section -->

                            @elseif (!empty($item->doc_status->date_issued) && empty($item->doc_status->date_received))
                                @if ($item->status != 3)

                    <!-- Receive Button Section -->
                                    @if ($isVisibleReceive)
                                        @php $countVisible++ @endphp
                    <li class="list-group-item justify-content-between">
                        <button type="button" class="btn btn-outline-success waves-effect btn-block btn-md btn-rounded"
                                onclick="$(this).showReceive('{{ route('po-jo-show-receive', ['id' => $item->id]) }}',
                                                             `{{ strtoupper($item->document_type).' '.$item->po_no }}`);">
                            <i class="fas fa-lg fa-hand-holding"></i> Receive
                        </button>
                    </li>
                                    @endif
                    <!-- End Receive Button Section -->

                                @endif
                            @endif
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
    @endforeach
@endif

@include('modals.search-post')
@include('modals.sm-create')
@include('modals.edit')
@include('modals.delete-destroy')
@include('modals.approve')
@include('modals.create-ors-po')
@include('modals.cleared')
@include('modals.cancel')
@include('modals.uncancel')
@include('modals.issue')
@include('modals.receive')
@include('modals.delivery')
@include('modals.inspection')
@include('modals.print')
@include('modals.attachment')

@endsection

@section('custom-js')

<script type="text/javascript" src="{{ asset('assets/js/amount-words-converter.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/input-validation.js') }}"></script>
<script src="{{ asset('assets/js/po-jo.js') }}"></script>
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
