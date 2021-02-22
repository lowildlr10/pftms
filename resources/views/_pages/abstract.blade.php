@extends('layouts.app')

@section('main-content')

<div class="row wow animated fadeIn">
    <section class="mb-5 col-12 module-container">
        <div class="card module-table-container text-white mdb-color darken-3">
            <div class="card-body">
                <h5 class="card-title">
                    <strong>
                        <i class="fas fa-shopping-cart"></i> Abstract of Bids & Quotations
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
                    <li class="active">
                        <a href="{{ url('procurement/abstract') }}" class="waves-effect waves-light cyan-text">
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
                                    data-target="#top-fluid-modal" data-toggle="modal">
                                <i class="fas fa-search"></i>
                            </button>
                            <a href="{{ url('procurement/abstract') }}" class="btn btn-outline-white btn-rounded btn-sm px-2">
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
                                   href="{{ url('procurement/abstract') }}">
                                    <small><i class="fas fa-times"></i> Reset</small>
                                </a>
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
                                        <th class="th-md" width="8%">
                                            <strong>PR No</strong>
                                        </th>
                                        <th class="th-md" width="10%">
                                            <strong>PR Date</strong>
                                        </th>
                                        <th class="th-md" width="10%">
                                            <strong>Charging</strong>
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
                                    <form id="form-validation" method="POST" action="#">
                                        @csrf
                                        <input type="hidden" name="type" id="type">

                                        @if (count($list) > 0)
                                            @php $countItem = 0; @endphp

                                            @foreach ($list as $listCtr => $pr)
                                                @php $countItem++; @endphp

                                        <tr class="hidden-xs">
                                            <td align="center">
                                                @if($pr->sID >= 6)
                                                <i class="fas fa-trophy fa-lg text-success material-tooltip-main"
                                                   data-toggle="tooltip" data-placement="right" title="Approved for PO/JO"></i>
                                                @else
                                                <i class="far fa-lg fa-file material-tooltip-main"
                                                   data-toggle="tooltip" data-placement="right" title="Pending"></i>
                                                @endif
                                            </td>
                                            <td align="center" class="border-left">
                                                {{ ($listCtr + 1) + (($list->currentpage() - 1) * $list->perpage()) }}
                                            </td>
                                            <td class="border-left">{{ $pr->pr_no }}</td>
                                            <td class="border-left">{{ $pr->date_pr }}</td>
                                            <td class="border-left">{{ $pr->project }}</td>
                                            <td class="border-left"><i class="fas fa-caret-right"></i> {{ substr($pr->purpose, 0, 150) }}...</td>
                                            <td class="border-left">{{ $pr->name }}</td>
                                            <td align="center" class="border-left">
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
                                                    <strong>PR NO:</strong> {{ $pr->pr_no }}
                                                    [
                                                    @if($pr->sID >= 6)
                                                    <i class="fas fa-trophy fa-lg text-success"></i> Approved for PO/JO
                                                    @else
                                                    <i class="far fa-lg fa-file"></i>
                                                    @endif
                                                    ]<br>
                                                    <i class="fas fa-caret-right"></i> {{ substr($pr->purpose, 0, 150) }}...
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
                                        @else
                                        <tr>
                                            <td class="p-5" colspan="8" align="center">
                                                <h5 class="red-text">No data found.</h5>
                                            </td>
                                        </tr>
                                            @php $remainingItem = $pageLimit - 1; @endphp
                                        @endif

                                        @if ($remainingItem != 0)
                                            @for ($itm = 1; $itm <= $remainingItem; $itm++)
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
                    <strong>PR NO: {{ $pr->pr_no }}</strong>
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
                                        onclick="$(this).showPrint('{{ $pr->id }}', 'abstract');">
                                    <i class="fas fa-print blue-text"></i> Print Abstract
                                </button>
                                @if ($pr->toggle == 'create')
                                <button type="button" class="btn btn-outline-mdb-color
                                        btn-sm px-2 waves-effect waves-light"
                                        onclick="$(this).viewCreate('{{ $pr->id }}', '{{ $pr->pr_no }}', 'create');
                                                 $('#create-title').text('CREATE ABSTRACT OF QUOTATION [ {{ $pr->pr_no }} ]');">
                                    <i class="fas fa-pencil-alt green-text"></i> Create
                                </button>
                                @elseif ($pr->toggle == 'edit')
                                <button type="button" class="btn btn-outline-mdb-color
                                        btn-sm px-2 waves-effect waves-light"
                                        onclick="$(this).viewCreate('{{ $pr->id }}', '{{ $pr->pr_no }}', 'edit');
                                                 $('#edit-title').text('EDIT ABSTRACT OF QUOTATION [ {{ $pr->pr_no }} ]');">
                                    <i class="fas fa-edit orange-text"></i> Edit
                                </button>
                                @endif
                                @if ($pr->toggle == 'create')
                                <button type="button" class="btn btn-outline-mdb-color
                                        btn-sm px-2 waves-effect waves-light"
                                        disabled="disabled">
                                    <i class="fas fa-trash-alt red-text"></i> Delete
                                </button>
                                @elseif ($pr->toggle == 'edit')
                                <button type="button" class="btn btn-outline-mdb-color
                                        btn-sm px-2 waves-effect waves-light"
                                        onclick="$(this).delete('{{ $pr->id }}');">
                                    <i class="fas fa-trash-alt red-text"></i> Delete
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <p>
                            <strong>PR Date: </strong> {{ $pr->date_pr }}<br>
                            <strong>Charging: </strong> {{ $pr->project }}<br>
                            <strong>Purpose: </strong> {{ $pr->purpose }}<br>
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
                        <a href="{{ url('procurement/rfq?search='.$pr->pr_no) }}"
                          class="btn btn-outline-mdb-color waves-effect btn-block btn-md btn-rounded">
                            <i class="fas fa-angle-double-left"></i> Regenerate RFQ
                        </a>
                    </li>
                    @if ($pr->sID >= 6)
                    <li class="list-group-item justify-content-between">
                        <a href="{{ url('procurement/po-jo?search='.$pr->pr_no) }}"
                          class="btn btn-outline-mdb-color waves-effect btn-block btn-md btn-rounded">
                            Generate PO/JO <i class="fas fa-angle-double-right"></i>
                        </a>
                    </li>
                    @else
                    <li class="list-group-item justify-content-between">
                        <button type="button" class="btn btn-outline-success waves-effect btn-block btn-md btn-rounded"
                                onclick="$(this).approve('{{ $pr->id }}');">
                            <i class="fas fa-trophy"></i> To PO/JO
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

@include('layouts.partials.modals.top-fluid-search')
@include('layouts.partials.modals.central-create')
@include('layouts.partials.modals.central-edit')
@include('layouts.partials.modals.print')

@endsection

@section('custom-js')

<script src="{{ asset('assets/js/abstract.js') }}"></script>
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
