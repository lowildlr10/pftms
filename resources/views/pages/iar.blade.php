@extends('layouts.app')

@section('main-content')

<div class="row wow animated fadeIn">
    <section class="mb-5 col-12">
        <div class="card text-white mdb-color darken-3">
            <div class="card-body">
                <h5 class="card-title">
                    <strong>
                        <i class="fas fa-shopping-cart"></i> Inspection and Acceptance Report
                    </strong>
                </h5>
                <hr class="white">
                <ul class="breadcrumb mdb-color darken-3 mb-0 p-1">
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
                        <a href="{{ url('procurement/iar') }}" class="waves-effect waves-light cyan-text">
                            Inspection and Acceptance Report
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
                            <a href="{{ url('procurement/iar') }}" class="btn btn-outline-white btn-rounded btn-sm px-2">
                                <i class="fas fa-sync-alt fa-pulse"></i>
                            </a>

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
                                <a class="btn btn-sm btn-outline-red waves-effect my-0 py-0 px-1"
                                   href="{{ url('procurement/iar') }}">
                                    <small><i class="fas fa-times"></i> Reset</small>
                                </a>
                            </div>
                            @endif

                            <!--Table-->
                            <table class="table table-hover table-b table-sm mb-0">

                                <!--Table head-->
                                <thead class="mdb-color darken-3 white-text">
                                    <tr>
                                        <th class="th-md" width="3%"></th>
                                        <th class="th-md" width="3%" style="text-align: center;">
                                            <strong>#</strong>
                                        </th>
                                        <th class="th-md" width="8%">
                                            <strong>PR No</strong>
                                        </th>
                                        <th class="th-md" width="10%">
                                            <strong>PR Date</strong>
                                        </th>
                                        <th class="th-md" width="10%">
                                            <strong>Charging</strong>
                                        </th>
                                        <th class="th-md" width="53%">
                                            <strong>Purpose</strong>
                                        </th>
                                        <th class="th-md" width="13%">
                                            <strong>Requested By</strong>
                                        </th>
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
                                                @if ($pr->iar_count > 0)
                                                    @php $countItem++; @endphp
                                        <tr>
                                            <td align="center" class="border-left">
                                                <i class="fas fa-folder fa-lg material-tooltip-main"
                                                   data-toggle="tooltip" data-placement="right" title="PR Document"></i>
                                            </td>
                                            <td align="center" class="border-left">
                                                {{ ($listCtr + 1) + (($list->currentpage() - 1) * $list->perpage()) }}
                                            </td>
                                            <td class="border-left">
                                                <a class="btn btn-link p-0" href="{{ url('procurement/iar?search=' . $pr->pr_no) }}">
                                                    {{ $pr->pr_no }}
                                                </a>
                                            </td>
                                            <td class="border-left">{{ $pr->date_pr }}</td>
                                            <td class="border-left">{{ $pr->project }}</td>
                                            <td class="border-left"><i class="fas fa-caret-right"></i> {{ substr($pr->purpose, 0, 150) }}...</td>
                                            <td class="border-left">{{ $pr->name }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="7" class="heavy-rain-gradient"></td>
                                        </tr>
                                        <tr class="blue-grey lighten-2">
                                            <td colspan="7">
                                                <div class="card card-cascade narrower mx-3 my-2">
                                                    <div class="card-body p-2">
                                                        <table class="table table table-sm z-depth-1 mb-0">
                                                            <thead class="mdb-color darken-1 white-text">
                                                                <tr>
                                                                    <th class="th-md" width="3%"></th>
                                                                    <th class="th-md" width="10%" style="text-align: center;"><strong>IAR Number</strong></th>
                                                                    <th class="th-md" width="84%" style="text-align: center;"><strong>Awarded To</strong></th>
                                                                    <th class="th-md" width="3%"></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>

                                                            @if (count($pr->iar_item) > 0)
                                                                @foreach ($pr->iar_item as $listCtr1 => $item)
                                                                <tr>
                                                                    <td align="center">
                                                                        @if ($item->status_id > 9)
                                                                        <i class="fas fa-search fa-lg green-text material-tooltip-main"
                                                                           data-toggle="tooltip" data-placement="right" title="Inspected"></i>
                                                                        @else
                                                                            @if (!empty($item->date_issued))
                                                                        <i class="fas fa-lg fa-paper-plane orange-text material-tooltip-main"
                                                                           data-toggle="tooltip" data-placement="right" title="Issued"></i>
                                                                            @else
                                                                        <i class="far fa-lg fa-file material-tooltip-main"
                                                                           data-toggle="tooltip" data-placement="right" title="Pending"></i>
                                                                            @endif
                                                                        @endif
                                                                    </td>
                                                                    <td align="center" class="border-left"><strong>{{ $item->iar_no }}</strong></td>
                                                                    <td align="center" class="border-left">{{ $item->company_name }}</td>
                                                                    <td align="center" class="border-left">
                                                                        <a class="btn-floating btn-sm btn-mdb-color p-2 waves-effect material-tooltip-main mr-0"
                                                                            data-target="#right-modal-{{ ($listCtr + 1) + (($list->currentpage() - 1) * $list->perpage()) }}-{{ $listCtr1 }}"
                                                                            data-toggle="modal" data-toggle="tooltip" data-placement="left" title="Open">
                                                                            <i class="fas fa-folder-open"></i>
                                                                        </a>
                                                                    </td>
                                                                </tr>
                                                                @endforeach
                                                            @endif

                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="7" class="heavy-rain-gradient"></td>
                                        </tr>
                                                @else
                                        <tr>
                                            <td align="center">
                                                <i class="fas fa-folder fa-lg"></i>
                                            </td>
                                            <td align="center">
                                                {{ ($listCtr + 1) + (($list->currentpage() - 1) * $list->perpage()) }}
                                            </td>
                                            <td>{{ $pr->pr_no }}</td>
                                            <td>{{ $pr->date_pr }}</td>
                                            <td>{{ $pr->project }}</td>
                                            <td><i class="fas fa-caret-right"></i> {{ substr($pr->purpose, 0, 150) }}...</td>
                                            <td>{{ $pr->name }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="7" class="heavy-rain-gradient"></td>
                                        </tr>
                                        <tr class="blue-grey lighten-2">
                                            <td colspan="7" class="text-center">
                                                <div class="card m-2">
                                                    <div class="card-body">
                                                        <h6 class="red-text">No IAR.</h6>
                                                        <a href="{{ url('procurement/po-jo?search='.$pr->pr_no) }}"
                                                           class="btn btn-outline-mdb-color waves-effect btn-block">
                                                            <i class="fas fa-angle-double-left"></i> Back to PO/JO
                                                        </a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="7" class="blue-grey lighten-2"></td>
                                        </tr>
                                                @endif
                                            @endforeach

                                            @php $remainingItem = $pageLimit - $countItem; @endphp
                                        @else
                                        <tr>
                                            <td class="p-5" colspan="7" align="center">
                                                <h5 class="red-text">No data found.</h5>
                                            </td>
                                        </tr>
                                            @php $remainingItem = $pageLimit - 1; @endphp
                                        @endif

                                        @if ($remainingItem != 0)
                                            @for ($itm = 1; $itm <= $remainingItem; $itm++)
                                        <tr><td colspan="7" style="border: 0;"></td></tr>
                                            @endfor
                                        @endif

                                    </form>
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
        @if (count($pr->iar_item) > 0)
            @foreach ($pr->iar_item as $listCtr1 => $item)
<div id="right-modal-{{ ($listCtr + 1) + (($list->currentpage() - 1) * $list->perpage()) }}-{{ $listCtr1 }}" tabindex="-1"
     class="modal custom-rightmenu-modal fade right" role="dialog">
    <div class="modal-dialog modal-full-height modal-right" role="document">
        <!--Content-->
        <div class="modal-content">
            <!--Header-->
            <div class="modal-header stylish-color-dark white-text">
                <h6>
                    <i class="fas fa-shopping-cart"></i>
                    <strong>IAR NO: {{ $item->iar_no }}</strong>
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
                                        onclick="$(this).showPrint('{{ $item->iar_no }}', 'iar');">
                                    <i class="fas fa-print blue-text"></i> Print IAR
                                </button>
                                <button type="button" class="btn btn-outline-mdb-color
                                        btn-sm px-2 waves-effect waves-light"
                                        onclick="$(this).viewCreate('{{ $item->po_no }}');
                                                 $('#edit-title').text('EDIT INSPECTION & ACCEPTANCE RECEIPT [ {{ $item->iar_no }} ]');">
                                    <i class="fas fa-edit orange-text"></i> Edit
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <p>
                            <strong>IAR Date: </strong> {{ $item->date_iar }}<br>
                            <strong>Charging: </strong> {{ $pr->project }}<br>
                            <strong>Purpose: </strong> {{ $pr->purpose }}<br>
                            <strong>Awarded To: </strong> {{ $item->company_name }}<br>
                            <strong>Requested By: </strong> {{ $pr->name }}<br>
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
                    <li class="list-group-item justify-content-between">
                        <a href="{{ url('procurement/po-jo?search='.$item->pr_no) }}"
                          class="btn btn-outline-mdb-color waves-effect btn-block btn-md btn-rounded">
                            <i class="fas fa-angle-double-left"></i> Regenerate PO/JO
                        </a>
                    </li>
                    @if (!empty($item->date_issued))
                        @if ($item->status_id > 9)
                            @if ($item->inventory_count == 0)
                    <li class="list-group-item justify-content-between">
                        <button type="button" class="btn btn-outline-success waves-effect btn-block btn-md btn-rounded"
                                onclick="$(this).showInventory('{{ $item->po_no }}', 'create');
                                         $('#issue-title').text('ISSUE INVENTORY STOCK [ {{ $item->po_no }} ]');">
                            <i class="fas fa-box green-text"></i> Issue/Inventory
                        </button>
                    </li>
                            @else
                    <li class="list-group-item justify-content-between">
                        <button type="button" class="btn btn-outline-warning waves-effect btn-block btn-md btn-rounded"
                                onclick="$(this).showInventory('{{ $item->po_no }}', 'update');
                                         $('#issue-title').text('UPDATE ISSUE INVENTORY STOCK [ {{ $item->po_no }} ]');">
                            <i class="fas fa-box"></i> Update Inventory
                        </button>
                    </li>
                            @endif
                    <li class="list-group-item justify-content-between">
                        <a href="{{ url('procurement/dv?search='.$item->po_no) }}"
                          class="btn btn-outline-mdb-color waves-effect btn-block btn-md btn-rounded">
                            Generate DV <i class="fas fa-angle-double-right"></i>
                        </a>
                    </li>
                        @else
                    <li class="list-group-item justify-content-between">
                        <button type="button" class="btn btn-outline-success waves-effect btn-block btn-md btn-rounded"
                                onclick="$(this).inspect('{{ $item->iar_no }}');">
                            <i class="fas fa-search"></i> Inspect
                        </button>
                    </li>
                        @endif
                    @else
                    <li class="list-group-item justify-content-between">
                        <button type="button" class="btn btn-outline-warning waves-effect btn-block btn-md btn-rounded"
                                onclick="$(this).viewIssue('{{ $item->iar_no }}');">
                            <i class="fas fa-paper-plane"></i> Issue
                        </button>
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

@include('layouts.partials.modals.top-fluid-search')
@include('layouts.partials.modals.central-edit')
@include('layouts.partials.modals.central-issue')
@include('layouts.partials.modals.smcard-central')
@include('layouts.partials.modals.print')

@endsection

@section('custom-js')

<script src="{{ asset('assets/js/iar.js') }}"></script>
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
