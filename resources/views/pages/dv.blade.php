@extends('layouts.app')

@section('main-content')

<div class="row wow animated fadeIn">
    <section class="mb-5 col-12 module-container">
        <div class="card module-table-container text-white mdb-color darken-3">
            <div class="card-body">
                <h5 class="card-title">
                    <strong>
                        <i class="fas fa-money-bill-wave-alt"></i> Disbursement Voucher
                    </strong>
                </h5>
                <hr class="white">
                <ul class="breadcrumb mdb-color darken-3 mb-0 p-1">
                    @if ($type == "procurement")
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
                    <li>
                        <a href="{{ url('procurement/iar') }}" class="waves-effect waves-light white-text">
                            Inspection and Acceptance Report
                        </a>
                    </li>
                    <li>
                        <i class="fa fa-caret-right mx-2" aria-hidden="true"></i>
                    </li>
                    <li class="active">
                        <a href="{{ url('procurement/dv') }}" class="waves-effect waves-light cyan-text">
                            Disbursement Voucher
                        </a>
                    </li>
                    @else
                    <li>
                        <i class="fa fa-caret-right mx-2" aria-hidden="true"></i>
                    </li>
                    <li class="active">
                        <a href="{{ url('cadv-reim-liquidation/dv') }}" class="waves-effect waves-light cyan-text">
                            Disbursement Voucher
                        </a>
                    </li>
                    @endif

                </ul>

                <!-- Table with panel -->
                <div class="card card-cascade narrower">

                    <!--Card image-->
                    <div class="gradient-card-header unique-color
                                narrower py-2 px-2 mb-1 d-flex justify-content-between
                                align-items-center">
                        <div>

                            @if ($type == "cashadvance")
                            <button type="button" class="btn btn-outline-white btn-rounded btn-sm px-2"
                                    onclick="$(this).showCreate()">
                                <i class="fas fa-pencil-alt"></i> Create
                            </button>
                            @endif

                        </div>
                        <div>
                            <button class="btn btn-outline-white btn-rounded btn-sm px-2"
                                    data-target="#top-fluid-modal" data-toggle="modal">
                                <i class="fas fa-search"></i>
                            </button>

                            @if ($type == "procurement")
                            <a href="{{ url('procurement/ors-burs') }}" class="btn btn-outline-white btn-rounded btn-sm px-2">
                                <i class="fas fa-sync-alt fa-pulse"></i>
                            </a>
                            @elseif ($type == "cashadvance")
                            <a href="{{ url('cadv-reim-liquidation/ors-burs') }}" class="btn btn-outline-white btn-rounded btn-sm px-2">
                                <i class="fas fa-sync-alt fa-pulse"></i>
                            </a>
                            @endif

                        </div>
                    </div>
                    <!--/Card image-->

                    <div class="px-1">
                        <div class="table-wrapper table-responsive border rounded">
                            @if (!empty($search))
                            <div class="hidden-xs my-2">
                                <small class="red-text pl-3">
                                    <i class="fas fa-search"></i> You searched for "{{ $search }}".
                                </small>

                                @if ($type == "procurement")
                                <a class="btn btn-sm btn-outline-red waves-effect my-0 py-0 px-1"
                                   href="{{ url('procurement/dv') }}">
                                    <small><i class="fas fa-times"></i> Reset</small>
                                </a>
                                @elseif ($type == "cashadvance")
                                <a class="btn btn-sm btn-outline-red waves-effect my-0 py-0 px-1"
                                   href="{{ url('cadv-reim-liquidation/dv') }}">
                                    <small><i class="fas fa-times"></i> Reset</small>
                                </a>
                                @endif
                            </div>
                            @endif

                            <!--Table-->
                            <table class="table module-table table-hover table-b table-sm mb-0">

                                <!--Table head-->
                                <thead class="mdb-color darken-3 white-text">
                                    <tr class="hidden-xs">
                                        <th class="th-md" width="3%"></th>
                                        <th class="th-md" width="3%" style="text-align: center;">
                                            <strong>#</strong>
                                        </th>

                                        @if ($type == 'procurement')
                                        <th class="th-md" width="8%">
                                            <strong>PO/JO No</strong>
                                        </th>
                                        <th class="th-md" width="10%">
                                            <strong>Charging</strong>
                                        </th>
                                        <th class="th-md" width="47%">
                                            <strong>Particulars</strong>
                                        </th>
                                        <th class="th-md" width="13%">
                                            <strong>Requested By</strong>
                                        </th>
                                        <th class="th-md" width="13%">
                                            <strong>Payee</strong>
                                        </th>
                                        @elseif ($type == 'cashadvance')
                                        <th class="th-md" width="78%">
                                            <strong>Particulars</strong>
                                        </th>
                                        <th class="th-md" width="13%">
                                            <strong>Payee</strong>
                                        </th>
                                        @endif

                                        <th class="th-md" width="3%"></th>
                                    </tr>
                                </thead>
                                <!--Table head-->

                                <!--Table body-->
                                <tbody>
                                    <form id="form-validation" method="POST" action="#">
                                        @csrf
                                        <input type="hidden" name="type" id="type">

                                        @if (count($list) > 0)
                                            @php $countItem = 0; @endphp

                                            @foreach ($list as $listCtr => $pr)
                                                @php $countItem++; @endphp

                                        <tr class="hidden-xs">
                                            <td align="center">
                                                @if ($type == 'procurement')
                                                    @if ($pr->status_id >= 11)
                                            <i class="far fa-money-bill-alt fa-lg text-success material-tooltip-main"
                                               data-toggle="tooltip" data-placement="right" title="Disbursed"></i>
                                                    @else
                                                        @if (!empty($pr->document_status->date_issued) &&
                                                             empty($pr->document_status->date_received) &&
                                                             empty($pr->document_status->date_issued_back) &&
                                                             empty($pr->document_status->date_received_back))
                                            <i class="fas fa-paper-plane fa-lg orange-text material-tooltip-main"
                                               data-toggle="tooltip" data-placement="right" title="Issued"></i>
                                                        @elseif (!empty($pr->document_status->date_issued) &&
                                                                 !empty($pr->document_status->date_received) &&
                                                                 empty($pr->document_status->date_issued_back) &&
                                                                 empty($pr->document_status->date_received_back))
                                            <i class="fas fa-hand-holding fa-lg text-success material-tooltip-main"
                                               data-toggle="tooltip" data-placement="right" title="Received"></i>
                                                        @elseif (!empty($pr->document_status->date_issued) &&
                                                                 !empty($pr->document_status->date_received) &&
                                                                 !empty($pr->document_status->date_issued_back) &&
                                                                 empty($pr->document_status->date_received_back))
                                            <i class="fas fa-undo-alt fa-lg orange-text material-tooltip-main"
                                               data-toggle="tooltip" data-placement="right" title="Issued Back"></i>
                                                        @elseif (!empty($pr->document_status->date_issued) &&
                                                                 !empty($pr->document_status->date_received) &&
                                                                 !empty($pr->document_status->date_issued_back) &&
                                                                 !empty($pr->document_status->date_received_back))
                                            <i class="fas fa-hand-holding fa-lg text-success material-tooltip-main"
                                               data-toggle="tooltip" data-placement="right" title="Received"></i>
                                                        @else
                                            <i class="far fa-lg fa-file material-tooltip-main"
                                               data-toggle="tooltip" data-placement="right" title="Pending"></i>
                                                        @endif
                                                    @endif
                                                @elseif ($type == 'cashadvance')
                                                    @if ($pr->for_payment == 'y')
                                            <i class="far fa-money-bill-alt fa-lg text-success material-tooltip-main"
                                               data-toggle="tooltip" data-placement="right" title="Disbursed"></i>
                                                    @else
                                                        @if (!empty($pr->document_status->date_issued) &&
                                                             empty($pr->document_status->date_received) &&
                                                             empty($pr->document_status->date_issued_back) &&
                                                             empty($pr->document_status->date_received_back))
                                            <i class="fas fa-paper-plane fa-lg orange-text material-tooltip-main"
                                               data-toggle="tooltip" data-placement="right" title="Issued"></i>
                                                        @elseif (!empty($pr->document_status->date_issued) &&
                                                                 !empty($pr->document_status->date_received) &&
                                                                 empty($pr->document_status->date_issued_back) &&
                                                                 empty($pr->document_status->date_received_back))
                                            <i class="fas fa-hand-holding fa-lg text-success material-tooltip-main"
                                               data-toggle="tooltip" data-placement="right" title="Received"></i>
                                                        @elseif (!empty($pr->document_status->date_issued) &&
                                                                 !empty($pr->document_status->date_received) &&
                                                                 !empty($pr->document_status->date_issued_back) &&
                                                                 empty($pr->document_status->date_received_back))
                                            <i class="fas fa-undo-alt fa-lg orange-text material-tooltip-main"
                                               data-toggle="tooltip" data-placement="right" title="Issued Back"></i>
                                                        @elseif (!empty($pr->document_status->date_issued) &&
                                                                 !empty($pr->document_status->date_received) &&
                                                                 !empty($pr->document_status->date_issued_back) &&
                                                                 !empty($pr->document_status->date_received_back))
                                            <i class="fas fa-hand-holding fa-lg text-success material-tooltip-main"
                                               data-toggle="tooltip" data-placement="right" title="Received"></i>
                                                        @else
                                            <i class="far fa-lg fa-file material-tooltip-main"
                                               data-toggle="tooltip" data-placement="right" title="Pending"></i>
                                                        @endif
                                                    @endif
                                                @endif

                                            </td>
                                            <td align="center" class="border-left">
                                                {{ ($listCtr + 1) + (($list->currentpage() - 1) * $list->perpage()) }}
                                            </td>

                                                @if ($type == 'procurement')
                                            <td class="border-left">{{ $pr->po_no }}</td>
                                            <td class="border-left">{{ $pr->project }}</td>
                                                @endif
                                            <td class="border-left">
                                                <i class="fas fa-caret-right"></i> {{ substr($pr->particulars, 0, 150) }}...
                                            </td>
                                            <td class="border-left">{{ $pr->name }}</td>
                                                @if ($type == 'procurement')
                                            <td>{{ $pr->company_name }}</td>
                                                @endif

                                            <td align="center" class="border-left">
                                                @if (!$pr->date_disbursed)
                                                    @if ((Auth::user()->role == 1 || Auth::user()->role == 3))
                                                        @if (!empty($pr->document_status->issued_remarks) &&
                                                             !empty($pr->document_status->date_issued) &&
                                                             empty($pr->document_status->date_issued_back))
                                                <span class="red-text">
                                                    <a  data-target="#right-modal-{{ $listCtr + 1 }}" data-toggle="modal">
                                                        <i class="fas fa-exclamation-triangle fa-sm"></i>
                                                    </a>
                                                </span>
                                                        @endif
                                                    @else
                                                        @if (!empty($pr->document_status->issued_back_remarks) &&
                                                             !empty($pr->document_status->date_issued) &&
                                                             !empty($pr->document_status->date_issued_back))
                                                <span class="red-text">
                                                    <a  data-target="#right-modal-{{ $listCtr + 1 }}" data-toggle="modal">
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
                                        <tr class="show-xs" hidden>
                                            <td class="p-2" width="96%" colspan="7">
                                                <p>
                                                    {{ ($listCtr + 1) + (($list->currentpage() - 1) * $list->perpage()) }} ]
                                                    @if ($type == 'procurement')
                                                    <strong>PO NO:</strong> {{ $pr->po_no }}
                                                    @endif

                                                    [
                                                    @if ($type == 'procurement')
                                                        @if ($pr->status_id >= 11)
                                                    <i class="far fa-money-bill-alt fa-sm text-success"></i> Disbursed
                                                            @else
                                                                @if (!empty($pr->document_status->date_issued) &&
                                                                    empty($pr->document_status->date_received) &&
                                                                    empty($pr->document_status->date_issued_back) &&
                                                                    empty($pr->document_status->date_received_back))
                                                    <i class="fas fa-paper-plane fa-sm orange-text"></i> Isued
                                                                @elseif (!empty($pr->document_status->date_issued) &&
                                                                        !empty($pr->document_status->date_received) &&
                                                                        empty($pr->document_status->date_issued_back) &&
                                                                        empty($pr->document_status->date_received_back))
                                                    <i class="fas fa-hand-holding fa-sm text-success"></i> Received
                                                                @elseif (!empty($pr->document_status->date_issued) &&
                                                                        !empty($pr->document_status->date_received) &&
                                                                        !empty($pr->document_status->date_issued_back) &&
                                                                        empty($pr->document_status->date_received_back))
                                                    <i class="fas fa-undo-alt fa-sm orange-text"></i> Isued Back
                                                                @elseif (!empty($pr->document_status->date_issued) &&
                                                                        !empty($pr->document_status->date_received) &&
                                                                        !empty($pr->document_status->date_issued_back) &&
                                                                        !empty($pr->document_status->date_received_back))
                                                    <i class="fas fa-hand-holding fa-sm text-success"></i> Received Issued
                                                                @else
                                                    <i class="far fa-sm fa-file"></i>
                                                                @endif
                                                            @endif
                                                        @elseif ($type == 'cashadvance')
                                                            @if ($pr->for_payment == 'y')
                                                    <i class="far fa-money-bill-alt fa-sm text-success"></i> Disbursed
                                                            @else
                                                                @if (!empty($pr->document_status->date_issued) &&
                                                                    empty($pr->document_status->date_received) &&
                                                                    empty($pr->document_status->date_issued_back) &&
                                                                    empty($pr->document_status->date_received_back))
                                                    <i class="fas fa-paper-plane fa-sm orange-text"></i> Isued
                                                                @elseif (!empty($pr->document_status->date_issued) &&
                                                                        !empty($pr->document_status->date_received) &&
                                                                        empty($pr->document_status->date_issued_back) &&
                                                                        empty($pr->document_status->date_received_back))
                                                    <i class="fas fa-hand-holding fa-sm text-success"></i> Received
                                                                @elseif (!empty($pr->document_status->date_issued) &&
                                                                        !empty($pr->document_status->date_received) &&
                                                                        !empty($pr->document_status->date_issued_back) &&
                                                                        empty($pr->document_status->date_received_back))
                                                    <i class="fas fa-undo-alt fa-sm orange-text"></i> Isued Back
                                                                @elseif (!empty($pr->document_status->date_issued) &&
                                                                        !empty($pr->document_status->date_received) &&
                                                                        !empty($pr->document_status->date_issued_back) &&
                                                                        !empty($pr->document_status->date_received_back))
                                                    <i class="fas fa-hand-holding fa-sm text-success"></i> Received Issued
                                                                @else
                                                    <i class="far fa-sm fa-file"></i>
                                                                @endif
                                                            @endif
                                                        @endif ]<br>
                                                    <i class="fas fa-caret-right"></i> {{ substr($pr->particulars, 0, 150) }}...

                                                    @if (!$pr->date_disbursed)
                                                        @if ((Auth::user()->role == 1 || Auth::user()->role == 3))
                                                            @if (!empty($pr->document_status->issued_remarks) &&
                                                                 !empty($pr->document_status->date_issued) &&
                                                                 empty($pr->document_status->date_issued_back))
                                                    <span class="red-text">
                                                        <a data-target="#right-modal-{{ $listCtr + 1 }}" data-toggle="modal">
                                                            <i class="fas fa-exclamation-triangle fa-sm"></i> See remarks
                                                        </a>
                                                    </span>
                                                            @endif
                                                        @else
                                                            @if (!empty($pr->document_status->issued_back_remarks) &&
                                                                 !empty($pr->document_status->date_issued) &&
                                                                 !empty($pr->document_status->date_issued_back))
                                                    <span class="red-text">
                                                        <a  data-target="#right-modal-{{ $listCtr + 1 }}" data-toggle="modal">
                                                            <i class="fas fa-exclamation-triangle fa-sm"></i> See remarks
                                                        </a>
                                                    </span>
                                                            @endif
                                                        @endif
                                                    @endif
                                                </p>
                                            </td>
                                            <td width="4%">
                                                <a class="btn btn-sm btn-link waves-effect m-1 show-mobile"
                                                   data-target="#right-modal-{{ $listCtr + 1 }}" data-toggle="modal">
                                                    <i class="fas fa-folder-open"></i> Open
                                                </a>
                                            </td>
                                        </tr>
                                            @endforeach

                                            @php $remainingItem = $pageLimit - $countItem; @endphp

                                            @if ($remainingItem != 0)
                                                @for ($itm = 1; $itm <= $remainingItem; $itm++)
                                                   <tr><td colspan="8" style="border: 0;"></td></tr>
                                                @endfor
                                            @endif
                                        @else
                                        <tr>
                                            <td class="p-5" colspan="8" align="center">
                                                <h5 class="red-text">No data found.</h5>
                                            </td>
                                        </tr>

                                            @for ($itm = 1; $itm <= $pageLimit; $itm++)
                                        <tr><td colspan="8" style="border: 0;"></td></tr>
                                            @endfor
                                        @endif

                                    </form>
                                </tbody>
                                <!--Table body-->
                            </table>
                            <!--Table-->
                        </div>
                        <hr>
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
<div class="modal custom-rightmenu-modal fade right" id="right-modal-{{ $listCtr + 1 }}" tabindex="-1"
     role="dialog">
    <div class="modal-dialog modal-full-height modal-right" role="document">
        <!--Content-->
        <div class="modal-content">
            <!--Header-->
            <div class="modal-header stylish-color-dark white-text">
                <h6>
                    <i class="fas fa-shopping-cart"></i>
                    <strong>DV NO: {{ $pr->dv_no }}</strong>
                </h6>
                <button type="button" class="close white-text" data-dismiss="modal"
                        aria-label="Close">
                    &times;
                </button>
            </div>
            <!--Body-->
            <div class="modal-body">
                @if ($pr->display_menu)
                <div class="card card-cascade z-depth-1 mb-3">
                    <div class="gradient-card-header rgba-white-light p-0">
                        <div class="p-0">
                            <div class="btn-group btn-menu-1 p-0">
                                @if ($type == 'procurement')
                                <button type="button" class="btn btn-outline-mdb-color
                                        btn-sm px-2 waves-effect waves-light"
                                        onclick="$(this).showPrint('{{ $pr->id }}', 'dv');">
                                    <i class="fas fa-print blue-text"></i> Print DV
                                </button>
                                @elseif ($type == 'cashadvance')
                                <button type="button" class="btn btn-outline-mdb-color
                                        btn-sm px-2 waves-effect waves-light"
                                        onclick="$(this).showPrint('{{ $pr->id }}', 'cashadvance-dv');">
                                    <i class="fas fa-print blue-text"></i> Print DV
                                </button>
                                @endif
                                <button type="button" class="btn btn-outline-mdb-color
                                        btn-sm px-2 waves-effect waves-light"
                                        onclick="$(this).showEdit('{{ $pr->id }}', '{{ $type }}');
                                                 $('#edit-title').text('EDIT DISBURSEMENT VOUCHER [ {{ $pr->id }} ]');">
                                    <i class="fas fa-trash-alt orange-text"></i> Edit
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <p>
                            <strong>DV Date: </strong> {{ $pr->date_dv }}<br>
                            @if ($type == 'procurement')
                            <strong>Charging: </strong> {{ $pr->project }}<br>
                            @endif
                            <strong>Particulars: </strong> {{ $pr->particulars }}<br>
                            @if ($type == 'procurement')
                            <strong>Requested By: </strong> {{ $pr->name }}<br>
                            <strong>Payee: </strong> {{ $pr->company_name }}<br>
                            @elseif ($type == 'cashadvance')
                            <strong>Payee: </strong> {{ $pr->name }}<br>
                            @endif
                            @if (!$pr->date_disbursed)
                                @if ((Auth::user()->role == 1 || Auth::user()->role == 3))
                            <span class="red-text">
                                    @if (!empty($pr->document_status->issued_remarks) &&
                                         !empty($pr->document_status->date_issued) &&
                                         empty($pr->document_status->date_issued_back))
                                <strong>
                                    <i class="fas fa-exclamation-triangle fa-sm"></i> Remarks:
                                </strong>
                                {{ $pr->document_status->issued_remarks }}<br>
                                    @endif
                            </span>
                                @else
                            <span class="red-text">
                                    @if (!empty($pr->document_status->issued_back_remarks) &&
                                         !empty($pr->document_status->date_issued) &&
                                         !empty($pr->document_status->date_issued_back))
                                <strong>
                                    <i class="fas fa-exclamation-triangle fa-sm"></i> Remarks:
                                </strong>
                                {{ $pr->document_status->issued_back_remarks }}<br>
                                    @endif
                            </span>
                                @endif
                            @endif
                        </p>
                        <button type="button" class="btn btn-sm btn-mdb-color btn-rounded
                                btn-block waves-effect mb-2"
                                onclick="$(this).showPrint('{{ $pr->id }}', 'pr');">
                            <i class="far fa-list-alt fa-lg"></i> View Items
                        </button>
                    </div>
                </div>
                <hr>
                <ul class="list-group z-depth-0">
                    <li class="list-group-item justify-content-between">
                        <h5><strong><i class="fas fa-pen-nib"></i> Actions</strong></h5>
                    </li>
                    @if ($type == 'procurement')
                    <li class="list-group-item justify-content-between">
                        <a href="{{ url('procurement/iar?search='.$pr->pr_no) }}"
                           class="btn btn-outline-mdb-color waves-effect btn-block btn-md btn-rounded">
                            <i class="fas fa-angle-double-left"></i> Regenerate IAR
                        </a>
                    </li>
                        @if (Auth::user()->role == 1 || Auth::user()->role == 3 || Auth::user()->role == 4)
                            @if (!empty($pr->document_status->date_issued) &&
                                 empty($pr->document_status->date_received) &&
                                 empty($pr->document_status->date_issued_back) &&
                                 empty($pr->document_status->date_received_back))
                    <li class="list-group-item justify-content-between">
                        <button type="button" class="btn btn-outline-green waves-effect btn-block btn-md btn-rounded"
                                onclick="$(this).receive('{{ $pr->id }}');">
                            <i class="fas fa-hand-holding"></i> Receive
                        </button>
                    </li>
                            @elseif (!empty($pr->document_status->date_issued) &&
                                     !empty($pr->document_status->date_received) &&
                                     empty($pr->document_status->date_issued_back) &&
                                     empty($pr->document_status->date_received_back))
                                @if (empty($pr->date_disbursed))
                    <li class="list-group-item justify-content-between">
                        <button type="button" class="btn btn-outline-green waves-effect btn-block btn-md btn-rounded"
                                onclick="$(this).payment('{{ $pr->id }}');">
                            <i class="fas fa-money-check-alt"></i> For Payment
                        </button>
                    </li>
                    <li class="list-group-item justify-content-between">
                        <button type="button" class="btn btn-outline-orange waves-effect btn-block btn-md btn-rounded"
                                onclick="$(this).viewIssueBack('{{ $pr->id }}');">
                            <i class="fas fa-undo-alt"></i> Issue Back
                        </button>
                    </li>
                                @endif
                            @endif

                            @if ($pr->sID < 11)
                            @else
                    <li class="list-group-item justify-content-between">
                        <a href="{{ url('payment?search='.$pr->po_no) }}"
                           class="btn btn-outline-mdb-color waves-effect btn-block btn-md btn-rounded">
                            Payement <i class="fas fa-angle-double-right"></i>
                        </a>
                    </li>
                            @endif
                        @endif
                        @if (empty($pr->document_status->date_issued) &&
                             empty($pr->document_status->date_received) &&
                             empty($pr->document_status->date_issued_back) &&
                             empty($pr->document_status->date_received_back))
                    <li class="list-group-item justify-content-between">
                        <button type="button" class="btn btn-outline-orange waves-effect btn-block btn-md btn-rounded"
                                onclick="$(this).viewIssue('{{ $pr->id }}');">
                            <i class="fas fa-paper-plane"></i> Issue
                        </button>
                    </li>
                        @elseif (!empty($pr->document_status->date_issued) &&
                                 !empty($pr->document_status->date_received) &&
                                 !empty($pr->document_status->date_issued_back) &&
                                 empty($pr->document_status->date_received_back))
                    <li class="list-group-item justify-content-between">
                        <button type="button" class="btn btn-outline-green waves-effect btn-block btn-md btn-rounded"
                                onclick="$(this).receiveBack('{{ $pr->id }}');">
                            <i class="fas fa-hand-holding"></i> Receive Back
                        </button>
                    </li>
                        @endif
                    @elseif ($type == 'cashadvance')
                    <li class="list-group-item justify-content-between">
                        <a href="{{ url('cadv-reim-liquidation/ors-burs?search='.$pr->ors_id) }}"
                           class="btn btn-outline-mdb-color waves-effect btn-block btn-md btn-rounded">
                            <i class="fas fa-angle-double-left"></i> Regenerate ORS/BURS
                        </a>
                    </li>
                        @if ($pr->transaction_type == 'cash_advance')
                            @if ($pr->liquidation_count == 0)
                    <li class="list-group-item justify-content-between">
                        <button type="button" class="btn btn-outline-green waves-effect btn-block btn-md btn-rounded"
                                onclick="$(this).createLiquidation('{{ $pr->id }}');">
                            <i class="fas fa-pencil-alt"></i> Create Liquidation
                        </button>
                    </li>
                            @else
                    <li class="list-group-item justify-content-between">
                        <a href="{{ url('cadv-reim-liquidation/liquidation?search='.$pr->dv_no) }}"
                          class="btn btn-outline-orange waves-effect btn-block btn-md btn-rounded">
                            <i class="fas fa-file-signature orange-text"></i> Edit Liquidation
                        </a>
                    </li>
                            @endif
                        @endif
                        @if (empty($pr->document_status->date_issued) &&
                             empty($pr->document_status->date_received) &&
                             empty($pr->document_status->date_issued_back) &&
                             empty($pr->document_status->date_received_back))
                    <li class="list-group-item justify-content-between">
                        <button type="button" class="btn btn-outline-orange waves-effect btn-block btn-md btn-rounded"
                                onclick="$(this).viewIssue('{{ $pr->id }}');">
                            <i class="fas fa-paper-plane"></i> Issue
                        </button>
                    </li>
                        @endif
                        @if (Auth::user()->role == 1 || Auth::user()->role == 3 || Auth::user()->role == 4)
                            @if (!empty($pr->document_status->date_issued) &&
                                 empty($pr->document_status->date_received) &&
                                 empty($pr->document_status->date_issued_back) &&
                                 empty($pr->document_status->date_received_back))
                    <li class="list-group-item justify-content-between">
                        <button type="button" class="btn btn-outline-green waves-effect btn-block btn-md btn-rounded"
                                onclick="$(this).receive('{{ $pr->id }}');">
                            <i class="fas fa-hand-holding"></i> Receive
                        </button>
                    </li>
                            @elseif (!empty($pr->document_status->date_issued) &&
                                     !empty($pr->document_status->date_received) &&
                                     empty($pr->document_status->date_issued_back) &&
                                     empty($pr->document_status->date_received_back))
                                @if (empty($pr->date_disbursed))
                    <li class="list-group-item justify-content-between">
                        <button type="button" class="btn btn-outline-green waves-effect btn-block btn-md btn-rounded"
                                onclick="$(this).payment('{{ $pr->id }}');">
                            <i class="fas fa-money-check-alt"></i> For Payment
                        </button>
                    </li>
                    <li class="list-group-item justify-content-between">
                        <button type="button" class="btn btn-outline-orange waves-effect btn-block btn-md btn-rounded"
                                onclick="$(this).viewIssueBack('{{ $pr->id }}');">
                            <i class="fas fa-undo-alt"></i> Issue Back
                        </button>
                    </li>
                                @endif
                            @endif
                        @endif
                        @if (!empty($pr->document_status->date_issued) &&
                             !empty($pr->document_status->date_received) &&
                             !empty($pr->document_status->date_issued_back) &&
                             empty($pr->document_status->date_received_back))
                    <li class="list-group-item justify-content-between">
                        <button type="button" class="btn btn-outline-green waves-effect btn-block btn-md btn-rounded"
                                onclick="$(this).receiveBack('{{ $pr->id }}');">
                            <i class="fas fa-hand-holding"></i> Receive Back
                        </button>
                    </li>
                        @endif
                    @endif
                </ul>
                @else
                <ul class="list-group z-depth-0">
                    <li class="list-group-item justify-content-between text-center">
                        <h5><strong class="red-text">Not yet finalized/issued by payee.</strong></h5>
                    </li>
                </ul>
                @endif
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

@include('layouts.partials.modals.top-fluid-search')
@include('layouts.partials.modals.central-create')
@include('layouts.partials.modals.central-edit')
@include('layouts.partials.modals.smcard-central')
@include('layouts.partials.modals.print')

@endsection

@section('custom-js')

<script type="text/javascript" src="{{ asset('assets/js/input-validation.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/dv.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/print.js') }}"></script>
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
    @include('layouts.partials.modals.alert')
    <script type="text/javascript">
        $(function() {
            $('#modal-success').modal();
        });
    </script>
@elseif (!empty(session("warning")))
    @include('layouts.partials.modals.alert')
    <script type="text/javascript">
        $(function() {
            $('#modal-warning').modal();
        });
    </script>
@elseif (!empty(session("failed")))
    @include('layouts.partials.modals.alert')
    <script type="text/javascript">
        $(function() {
            $('#modal-failed').modal();
        });
    </script>
@endif

@endsection
