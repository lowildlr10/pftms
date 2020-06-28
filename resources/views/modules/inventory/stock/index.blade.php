@extends('layouts.app')

@section('main-content')

<div class="row wow animated fadeIn">
    <section class="mb-5 col-12 module-container">
        <div class="card mdb-color darken-3">
            <div class="card-body">
                <h5 class="card-title white-text">
                    <strong>
                        <i class="fas fa-box"></i> Inventory Stocks
                    </strong>
                </h5>
                <hr class="white">
                <ul class="breadcrumb mdb-color darken-3 mb-0 p-1 white-text">
                    <li>
                        <i class="fa fa-caret-right mx-2" aria-hidden="true"></i>
                    </li>
                    <li class="active">
                        <a href="{{ route('stocks') }}" class="waves-effect waves-light cyan-text">
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
                                    <a class="dropdown-item" href="{{ route('iar') }}" target="_blank">
                                        <i class="fas fa-angle-double-left"></i> Go Back to Inspection & Acceptance Report
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div>
                            <button class="btn btn-outline-white btn-rounded btn-sm px-2"
                                    data-target="#top-fluid-modal" data-toggle="modal"
                                    onclick="$('#search').focus()">
                                <i class="fas fa-search"></i> {{ !empty($keyword) ? (strlen($keyword) > 15) ?
                                'Search: '.substr($keyword, 0, 15).'...' : 'Search: '.$keyword : '' }}
                            </button>
                            <a href="{{ route('stocks') }}" class="btn btn-outline-white btn-rounded btn-sm px-2">
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
                                        <th class="th-md" width="10%">
                                            @sortablelink('inventory_no', 'Inventory No', [], ['class' => 'white-text'])
                                        </th>
                                        <th class="th-md" width="59%">
                                            <strong>Items/Properties</strong>
                                        </th>
                                        <th class="th-md" width="13%">
                                            @sortablelink('inventoryclass.classification_name', 'Classification', [], ['class' => 'white-text'])
                                        </th>
                                        <th class="th-md" width="9%">
                                            @sortablelink('procstatus.status_name', 'Status', [], ['class' => 'white-text'])
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
                                            @foreach ($list as $listCtr => $inv)
                                        <tr class="hidden-xs">
                                            <td align="center">
                                                @if ($inv->procstatus['id'] == 12)
                                                <i class="fas fa-file-signature fa-lg orange-text material-tooltip-main"
                                                   data-toggle="tooltip" data-placement="right" title="Recorded"></i>
                                                @else
                                                <i class="fas fa-check fa-lg green-text material-tooltip-main"
                                                   data-toggle="tooltip" data-placement="right" title="Issued"></i>
                                                @endif
                                            </td>
                                            <td align="center"></td>
                                            <td>{{ $inv->inventory_no }}</td>
                                            <td class="py-2 px-0">
                                                <div class="mdb-color-text">
                                                    @if (count($inv->stockitems) > 0)
                                                        @foreach ($inv->stockitems as $cntr => $item)
                                                    <i class="fas fa-caret-right"></i>

                                                            @if (strlen($item->item_description) > 60)
                                                    <strong>{{ $cntr + 1 }}.|</strong> {{ substr($item->description, 0, 60) }}...
                                                            @else
                                                    <strong>{{ $cntr + 1 }}.|</strong> {{ $item->description }}.
                                                            @endif

                                                            @if ($item->available_quantity > 0)
                                                    <strong class="green-text">
                                                        [{{ $item->available_quantity }}/{{ $item->quantity }}]
                                                    </strong>
                                                    <button type="button" class="btn btn-outline-orange btn-sm py-0 px-1 my-0 ml-1 mb-2 z-depth-0
                                                                                 waves-effect waves-light"
                                                            onclick="$(this).showCreateIssueItem(`{{ route('stocks-show-create-issue-item', [
                                                                'invStockID' => $inv->id,
                                                                'invStockItemID' => $item->id,
                                                                'classification' => strtolower($inv->inventoryclass['abbrv']),
                                                                'type' => 'single'
                                                            ]) }}`);">
                                                        <i class="fas fa-paper-plane"></i> Issue
                                                    </button>
                                                            @else
                                                    <strong class="red-text">
                                                        [{{ $item->available_quantity }}/{{ $item->quantity }}] - <i class="fas fa-ban"></i> Out of Stock
                                                    </strong>
                                                            @endif

                                                    <br>

                                                        @endforeach
                                                    @endif
                                                </div>
                                            </td>
                                            <td>{{ $inv->inventoryclass['classification_name'] }}</td>
                                            <td>{{ $inv->procstatus['status_name'] }}</td>
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
                                            <td colspan="6" class="text-center py-5">
                                                <h6 class="red-text">
                                                    No available data.
                                                </h6>
                                            </td>
                                        </tr>
                                        @endif
                                    </form>
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
    @foreach ($list as $listCtr => $inv)
<div class="modal custom-rightmenu-modal fade right" id="right-modal-{{ $listCtr + 1 }}" tabindex="-1"
     role="dialog">
    <div class="modal-dialog modal-full-height modal-righty" role="document">
        <!--Content-->
        <div class="modal-content">
            <!--Header-->
            <div class="modal-header stylish-color-dark white-text">
                <h7>
                    <i class="fas fa-box"></i>
                    <strong>{{ $inv->inventoryclass['abbrv'] }} NO: {{ $inv->inventory_no }}</strong>
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
                                <button type="button" class="btn btn-outline-green
                                        btn-sm px-2 waves-effect waves-light"
                                        onclick="$(this).showCreateIssueItem(`{{ route('stocks-show-create-issue-item', [
                                            'invStockID' => $inv->id,
                                            'invStockItemID' => 0,
                                            'classification' => strtolower($inv->inventoryclass['abbrv']),
                                            'type' => 'multiple'
                                        ]) }}`);">
                                    <i class="fas fa-paper-plane green-text"></i> Issue Item / Property
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <p>
                            <strong>Classification: </strong> {{ $inv->inventoryclass['classification_name'] }}<br>
                            <strong>Status: </strong> {{ $inv->procstatus['status_name'] }}<br>
                            <strong>Created: </strong> {{ $inv->created_at }}<br>
                        </p>
                        <button type="button" class="btn btn-sm btn-mdb-color btn-rounded
                                btn-block waves-effect mb-2"
                                onclick="$(this).showRecipients(`{{ route('stocks-show-recipients', [
                                    'id' => $inv->id
                                ]) }}`);">
                            <i class="fas fa-users fa-lg"></i> Show Recipients
                        </button>
                    </div>
                </div>
                <hr>
                <ul class="list-group z-depth-1">
                    <li class="list-group-item justify-content-between">
                        <h6><strong><i class="fas fa-receipt"></i> Items/Properties</strong></h6>
                    </li>
                    <li class="list-group-item justify-content-between">
                        @if (count($inv->stockitems) > 0)
                            @foreach ($inv->stockitems as $cntr => $item)
                        <i class="fas fa-caret-right"></i>

                            @if (strlen($item->item_description) > 60)
                        <strong>{{ $cntr + 1 }}.|</strong> {{ substr($item->description, 0, 60) }}...
                            @else
                        <strong>{{ $cntr + 1 }}.|</strong> {{ $item->description }}.
                            @endif

                            @if ($item->available_quantity > 0)
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
                <ul class="list-group z-depth-0">
                    <li class="list-group-item justify-content-between">
                        <h5><strong><i class="fas fa-pen-nib"></i> Actions</strong></h5>
                    </li>

                    <li class="list-group-item justify-content-between">
                        <button type="button" class="btn btn-outline-green waves-effect btn-block btn-md btn-rounded"
                                onclick="$(this).showIssue('{{ route('stocks-issue', ['id' => $inv->id]) }}',
                                                           `{{ $inv->inventoryclass['classification_name'] }}`);">
                            <i class="fas fa-check"></i> Set to Issued
                        </button>
                    </li>
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
@include('modals.issue-no-remarks')
@include('modals.delete-destroy')
@include('modals.print')

@endsection

@section('custom-js')

<script type="text/javascript" src="{{ asset('assets/js/input-validation.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/inventory.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/print.js') }}"></script>

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
