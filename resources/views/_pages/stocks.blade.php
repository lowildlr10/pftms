@extends('layouts.app')

@section('main-content')

<div class="row wow animated fadeIn">
    <section class="mb-5 col-12">
        <div class="card text-white mdb-color darken-3">
            <div class="card-body">
                <h5 class="card-title">
                    <strong>
                        <i class="fas fa-box"></i> Inventory Stocks
                    </strong>
                </h5>
                <hr class="white">
                <ul class="breadcrumb mdb-color darken-3 mb-0 p-1">
                    <li>
                        <i class="fa fa-caret-right mx-2" aria-hidden="true"></i>
                    </li>
                    <li class="active">
                        <a href="{{ url('inventory/stocks') }}" class="waves-effect waves-light cyan-text">
                            Inventory Stocks
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
                            <div class="dropdown">
                                <button type="button" class="btn btn-outline-white btn-rounded btn-sm px-2
                                                             dropdown-toggle"
                                        data-toggle="dropdown">
                                    <i class="fas fa-pencil-alt"></i> Create
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" onclick="$(this).showCreate('par');">
                                        Property Aknowledgement Receipt (PAR)
                                    </a>
                                    <a class="dropdown-item" onclick="$(this).showCreate('ris');">
                                        Requisition and Issue Slip (RIS)
                                    </a>
                                    <a class="dropdown-item" onclick="$(this).showCreate('ics');">
                                        Inventory Custodian Slip (ICS)
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="{{ url('procurement/iar') }}" target="_blank">
                                        <i class="fas fa-angle-double-left"></i> Go Back to Inspection & Acceptance Report
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div>
                            <button class="btn btn-outline-white btn-rounded btn-sm px-2"
                                    data-target="#top-fluid-modal" data-toggle="modal">
                                <i class="fas fa-search"></i>
                            </button>
                            <a href="{{ url('inventory/stocks') }}" class="btn btn-outline-white btn-rounded btn-sm px-2">
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
                                href="{{ url('inventory/stocks') }}">
                                    <small><i class="fas fa-times"></i> Reset</small>
                                </a>
                            </div>
                            @endif

                            <!--Table-->
                            <table class="table module-table table-hover table-b table-sm mb-0">

                                <!--Table head-->
                                <thead class="mdb-color darken-3 white-text">
                                    <tr>
                                        <th class="th-md" width="3%"></th>
                                        <th class="th-md" width="3%" style="text-align: center;">
                                            <strong>#</strong>
                                        </th>
                                        <th class="th-md" width="10%">
                                            <strong>Inventory No</strong>
                                        </th>
                                        <th class="th-md" width="59%">
                                            <strong>Stocks</strong>
                                        </th>
                                        <th class="th-md" width="13%">
                                            <strong>Classification</strong>
                                        </th>
                                        <th class="th-md" width="9%">
                                            <strong>Status</strong>
                                        </th>
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

                                            @foreach ($list as $listCtr => $inv)
                                                @php $countItem++; @endphp

                                                @if (empty($inv->pr_deleted_at))

                                        <tr>
                                            <td align="center"><i class="far fa-lg fa-file material-tooltip-main"
                                                data-toggle="tooltip" data-placement="right" title="Pending"></i></td>
                                            <td align="center" class="border-left">
                                                {{ ($listCtr + 1) + (($list->currentpage() - 1) * $list->perpage()) }}
                                            </td>
                                            <td class="border-left"><strong>{{ $inv->inventory_no }}</strong></td>
                                            <td class="border-left py-1 px-0">
                                                <div class="card z-depth-0 m-0">
                                                    <div class="card-body mdb-color-text">
                                                        @if (!empty($inv->po_item))
                                                            @foreach ($inv->po_item as $cntr => $item)
                                                        <i class="fas fa-caret-right"></i>

                                                                @if (strlen($item->item_description) > 60)
                                                        <strong>{{ $cntr + 1 }}.|</strong> {{ substr($item->item_description, 0, 60) }}...
                                                                @else
                                                        <strong>{{ $cntr + 1 }}.|</strong> {{ $item->item_description }}.
                                                                @endif

                                                                @if ($item->current_quantity > 0)
                                                        <strong class="green-text">
                                                            [{{ $item->current_quantity }}/{{ $item->original_quantity }}]
                                                        </strong>
                                                        <button type="button" class="btn btn-outline-orange btn-sm py-0 px-1 my-0 ml-1 mb-2 z-depth-0
                                                                                     waves-effect waves-light"
                                                                onclick="$(this).showEdit('{{  $item->inventory_id }}',
                                                                                          '{{ $inv->classification_abrv }}',
                                                                                          'this');
                                                                         $('#issue-title').text('ISSUE INVENTORY STOCK [ {{ $inv->inventory_no }} ]');">
                                                            <i class="fas fa-paper-plane"></i> Issue
                                                        </button>
                                                                @else
                                                        <strong class="red-text">
                                                            [{{ $item->current_quantity }}/{{ $item->original_quantity }}] - <i class="fas fa-ban"></i> Out of Stock
                                                        </strong>
                                                                @endif

                                                        <br>

                                                            @endforeach
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td align="center" class="border-left">{{ $inv->classification }}</td>
                                            <td align="center" class="border-left"><strong>{{ $inv->status }}</strong></td>
                                            <td align="center" class="border-left">
                                                <a class="btn-floating btn-sm btn-mdb-color p-2 waves-effect material-tooltip-main mr-0"
                                                   data-target="#right-modal-{{ $listCtr + 1 }}" data-toggle="modal"
                                                   data-toggle="tooltip" data-placement="left" title="Open">
                                                    <i class="fas fa-folder-open"></i>
                                                </a>
                                            </td>
                                        </tr>
                                                @endif
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
    @foreach ($list as $listCtr => $inv)
        @if (empty($inv->pr_deleted_at))
<div class="modal custom-rightmenu-modal fade right" id="right-modal-{{ $listCtr + 1 }}" tabindex="-1"
     role="dialog">
    <div class="modal-dialog modal-full-height modal-right" role="document">
        <!--Content-->
        <div class="modal-content">
            <!--Header-->
            <div class="modal-header stylish-color-dark white-text">
                <h6>
                    <i class="fas fa-box"></i>
                    <strong>INVENTORY NO: {{ $inv->inventory_no }}</strong>
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
                                        onclick="$(this).showEdit('{{ $inv->inventory_no }}',
                                                                  '{{ $inv->classification_abrv }}', 'all');
                                                 $('#issue-title').text('ISSUE INVENTORY STOCK [ {{ $inv->inventory_no }} ]');">
                                    <i class="fas fa-paper-plane orange-text"></i> Issue All
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <p>
                            <strong>Created: </strong><br>
                            <strong>Classification: </strong> {{ $inv->classification }}<br>
                            <strong>Status: </strong> {{ $inv->status }}<br>
                        </p>
                        <button type="button" class="btn btn-sm btn-mdb-color btn-rounded
                                btn-block waves-effect mb-2"
                                onclick="$(this).showIssued('{{ $inv->inventory_no }}',
                                                            '{{ $inv->classification_abrv }}');">
                            <i class="fas fa-users fa-lg"></i> Show Issuee
                        </button>
                    </div>
                </div>
                <hr>
                <ul class="list-group z-depth-1">
                    <li class="list-group-item justify-content-between">
                        <h6><strong><i class="fas fa-receipt"></i> Stocks</strong></h6>
                    </li>
                    <li class="list-group-item justify-content-between">
                        @if (!empty($inv->po_item))
                            @foreach ($inv->po_item as $cntr => $item)
                        <strong>{{ $cntr + 1 }}.|</strong> {{ $item->item_description }}.
                                @if ($item->current_quantity > 0)
                        <span class="green-text font-weight-bold" style="font-size: 10px;">[In Stock]</span>
                                @else
                        <span class="red-text font-weight-bold" style="font-size: 10px;">[Out of Stock]</span>
                                @endif
                            <br>
                            @endforeach
                        @endif
                    </li>
                </ul>
                <hr>
                <ul class="list-group z-depth-1">
                    <li class="list-group-item justify-content-between">
                        <h5><strong><i class="fas fa-pen-nib"></i> Actions</strong></h5>
                    </li>
                    @if (Auth::user()->role == 1 || Auth::user()->role == 2 || Auth::user()->role == 5)
                    <li class="list-group-item justify-content-between">
                        <button type="button" class="btn btn-outline-green waves-effect btn-block btn-md btn-rounded"
                                onclick="$(this).issued('{{ $inv->inventory_no }}');">
                            <i class="fas fa-check"></i> Issued
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
        @endif
    @endforeach
@endif

@include('layouts.partials.modals.top-fluid-search')
@include('layouts.partials.modals.smcard-central')
@include('layouts.partials.modals.central-create')
@include('layouts.partials.modals.central-edit')
@include('layouts.partials.modals.central-issue')
@include('layouts.partials.modals.print')

@endsection

@section('custom-js')

<script type="text/javascript" src="{{ asset('assets/js/inventory.js') }}"></script>
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
