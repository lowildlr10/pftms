@extends('layouts.app')

@section('main-content')

<div class="row wow animated fadeIn">
    <section class="mb-5 col-12 module-container">
        <div class="card module-table-container text-white mdb-color darken-3">
            <div class="card-body">
                <h5 class="card-title">
                    <strong>
                        <i class="fas fa-money-check-alt"></i> List of Due and Demandable Accounts Payable
                    </strong>
                </h5>
                <hr class="white">
                <ul class="breadcrumb mdb-color darken-3 mb-0 p-1">
                    <li>
                        <i class="fa fa-caret-right mx-2" aria-hidden="true"></i>
                    </li>
                    <li>
                        <a href="{{ url('payment/lddap') }}" class="waves-effect waves-light white-text">
                            List of Due and Demandable Accounts Payable
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
                                    onclick="$(this).showCreate();
                                             $('#create-title').text('CREATE LDDAP');">
                                <i class="fas fa-pencil-alt"></i> Create
                            </button>
                        </div>
                        <div>
                            <button class="btn btn-outline-white btn-rounded btn-sm px-2"
                                    data-target="#top-fluid-modal" data-toggle="modal">
                                <i class="fas fa-search"></i>
                            </button>
                            <a href="{{ url('payment/lddap') }}" class="btn btn-outline-white btn-rounded btn-sm px-2">
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
                                   href="{{ url('payment/lddap') }}">
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
                                            <strong>LDDAP Date</strong>
                                        </th>
                                        <th class="th-md" width="15%">
                                            <strong>DV No</strong>
                                        </th>
                                        <th class="th-md" width="29%">
                                            <strong>LDDAP ADA No</strong>
                                        </th>
                                        <th class="th-md" width="29%">
                                            <strong>NCA No</strong>
                                        </th>
                                        <th class="th-md" width="10%">
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

                                            @foreach ($list as $listCtr => $pr)
                                                @php $countItem++; @endphp

                                        <tr class="hidden-xs">
                                            <td align="center" class="border-left">
                                                @if (empty($pr->date_for_approval) && empty($pr->date_approved))
                                                <i class="fas fa-spinner fa-lg faa-spin fa-pulse material-tooltip-main"
                                                   data-toggle="tooltip" data-placement="right" title="Pending"></i>
                                                @elseif (!empty($pr->date_for_approval) && empty($pr->date_approved))
                                                <i class="fas fa-sign fa-lg black-text material-tooltip-main"
                                                   data-toggle="tooltip" data-placement="right" title="For Approval"></i>
                                                @elseif (!empty($pr->date_for_approval) && !empty($pr->date_approved))
                                                <i class="fas fa-check fa-lg text-success material-tooltip-main"
                                                   data-toggle="tooltip" data-placement="right" title="Approved"></i>
                                                @endif
                                            </td>
                                            <td align="center" class="border-left">
                                                {{ ($listCtr + 1) + (($list->currentpage() - 1) * $list->perpage()) }}
                                            </td>
                                            <td class="border-left">{{ $pr->lddap_date }}</td>
                                            <td class="border-left">{{ $pr->dv_no }}</td>
                                            <td class="border-left">{{ $pr->lddap_ada_no }}</td>
                                            <td class="border-left">{{ $pr->nca_no }}</td>
                                            <td align="center" class="border-left">
                                                <strong>{{ $pr->status }}</strong>
                                            </td>

                                            <td align="center" class="border-left">
                                                <a class="btn-floating btn-sm btn-mdb-color p-2 waves-effect material-tooltip-main mr-0"
                                                   data-target="#right-modal-{{ $listCtr + 1 }}" data-toggle="modal"
                                                   data-toggle="tooltip" data-placement="left" title="Open">
                                                    <i class="fas fa-folder-open"></i>
                                                </a>
                                            </td>
                                        </tr>


                                        <tr class="show-xs" hidden>
                                            <td class="p-2" width="96%" colspan="8">
                                                <p>
                                                    {{ ($listCtr + 1) + (($list->currentpage() - 1) * $list->perpage()) }} ]

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

                        <div class="mt-3">

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
    <div class="modal-dialog modal-full-height modal-righty" role="document">
        <!--Content-->
        <div class="modal-content">
            <!--Header-->
            <div class="modal-header stylish-color-dark white-text">
                <h6>
                    <i class="fas fa-money-check-alt"></i>
                    <strong>LDDAP ID: {{ $pr->lddap_id }}</strong>
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
                                        onclick="$(this).showPrint('{{ $pr->lddap_id }}', 'lddap');">
                                    <i class="fas fa-print blue-text"></i> Print LDDAP
                                </button>
                                <button type="button" class="btn btn-outline-mdb-color
                                        btn-sm px-2 waves-effect waves-light"
                                        onclick="$(this).showEdit('{{ $pr->lddap_id }}');
                                                 $('#edit-title').text('EDIT LDDAP [ {{ $pr->lddap_id }} ]');">
                                    <i class="fas fa-trash-alt orange-text"></i> Edit
                                </button>
                                <button type="button" class="btn btn-outline-mdb-color
                                        btn-sm px-2 waves-effect waves-light"
                                        onclick="$(this).delete('{{ $pr->lddap_id }}');">
                                    <i class="fas fa-trash-alt red-text"></i> Delete
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <p>
                            <strong>Department: </strong> {{ $pr->department }}<br>
                            <strong>Entity Name: </strong> {{ $pr->entity_name }}<br>
                            <strong>Operating Unit: </strong> {{ $pr->operating_unit }}<br>
                            <strong>NCA No: </strong> {{ $pr->nca_no }}<br>
                            <strong>LDDAP-ADA No: </strong> {{ $pr->lddap_ada_no }}<br>
                            <strong>Date: </strong> {{ $pr->lddap_date }}<br>
                            <strong>Fund Cluster: </strong> {{ $pr->fund_cluster }}<br>
                            <strong>MDS-GSB Branch/MDS Sub Account: </strong> {{ $pr->mds_gsb_accnt_no }}<br>
                        </p>
                        <!--
                        <button type="button" class="btn btn-sm btn-outline-elegant btn-rounded
                                btn-block waves-effect mb-2"
                                onclick="$(this).showAttachment('{{ $pr->lddap_id }}');">
                            <i class="fas fa-paperclip fa-lg"></i> View Attachment
                        </button>
                        -->
                    </div>
                </div>
                <hr>
                <ul class="list-group z-depth-0">
                    <li class="list-group-item justify-content-between">
                        <h5><strong><i class="fas fa-pen-nib"></i> Actions</strong></h5>
                    </li>

                    @if (empty($pr->date_for_approval) && empty($pr->date_approved))
                    <li class="list-group-item justify-content-between">
                        <button type="button" class="btn btn-outline-black waves-effect btn-block btn-md btn-rounded"
                                onclick="$(this).forApproval('{{ $pr->lddap_id }}');">
                            <i class="fas fa-flag"></i> For Approval
                        </button>
                    </li>
                    @elseif (!empty($pr->date_for_approval) && empty($pr->date_approved))
                    <li class="list-group-item justify-content-between">
                        <button type="button" class="btn btn-outline-green waves-effect btn-block btn-md btn-rounded"
                                onclick="$(this).approve('{{ $pr->lddap_id }}');">
                            <i class="fas fa-check"></i> Approve
                        </button>
                    </li>
                    @else
                    <li class="list-group-item justify-content-between">
                        No more actions available.
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
@include('layouts.partials.modals.smcard-central')
@include('layouts.partials.modals.attachment')
@include('layouts.partials.modals.print')

@endsection

@section('custom-js')

<script type="text/javascript" src="{{ asset('assets/js/input-validation.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/lddap.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/print.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/attachment.js') }}"></script>
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
