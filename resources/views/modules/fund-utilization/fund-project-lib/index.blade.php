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
                        <i class="fas fa-hand-holding-usd"></i> Project Line-Items Budget
                    </strong>
                </h5>
                <hr class="white hidden-xs">
                <ul class="breadcrumb mdb-color darken-3 mb-0 p-1 white-text hidden-xs">
                    <li>
                        <i class="fa fa-caret-right mx-2" aria-hidden="true"></i>
                    </li>
                    <li>
                        <a href="{{ route('fund-project-lib') }}" class="waves-effect waves-light white-text">
                            Project Line-Items Budget
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
                            @if ($isAllowedCreate)
                            <button type="button" class="btn btn-outline-white btn-rounded btn-sm px-2"
                                    onclick="$(this).showCreate(`{{ route('fund-project-lib-show-create') }}`);">
                                <i class="fas fa-pencil-alt"></i> Create
                            </button>
                            @endif

                            <a type="button" class="btn btn-outline-white btn-rounded btn-sm px-2"
                                    href="{{ route('project') }}">
                                Go to Projects <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                        <div>
                            <button class="btn btn-outline-white btn-rounded btn-sm px-2"
                                    data-target="#top-fluid-modal" data-toggle="modal"
                                    onclick="$('#search').focus()">
                                <i class="fas fa-search"></i> {{ !empty($keyword) ? (strlen($keyword) > 15) ?
                                'Search: '.substr($keyword, 0, 15).'...' : 'Search: '.$keyword : '' }}
                            </button>
                            <a href="{{ route('fund-project-lib') }}" class="btn btn-outline-white btn-rounded btn-sm px-2">
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
                                        <th class="th-md text-center" width="3%"></th>
                                        <th class="th-md" width="50%">
                                            <b>
                                                @sortablelink('project.project_title', 'Project Name', [], ['class' => 'white-text'])
                                            </b>
                                        </th>
                                        <th class="th-md" width="13%">
                                            <b>
                                                @sortablelink('date_from', 'From', [], ['class' => 'white-text'])
                                            </b>
                                        </th>
                                        <th class="th-md" width="13%">
                                            <b>
                                                @sortablelink('date_to', 'To', [], ['class' => 'white-text'])
                                            </b>
                                        </th>
                                        <th class="th-md" width="15%">
                                            <b>
                                                @sortablelink('approved_budget', 'Current Budget', [], ['class' => 'white-text'])
                                            </b>
                                        </th>
                                        <th class="th-md" width="3%"></th>
                                    </tr>
                                </thead>
                                <!--Table head-->

                                <!--Table body-->
                                <tbody>
                                    @if (count($list) > 0)
                                        @foreach ($list as $listCtr => $fund)
                                    <tr class="hidden-xs">
                                        <td align="center">
                                            @if (!$fund->date_approved && !$fund->date_disapproved)
                                            <i class="fas fa-spinner fa-lg faa-spin fa-pulse material-tooltip-main"
                                               data-toggle="tooltip" data-placement="right" title="Pending"></i>
                                            @elseif (!$fund->date_approved && $fund->date_disapproved)
                                            <i class="fas fa-thumbs-down fa-lg material-tooltip-main"
                                               data-toggle="tooltip" data-placement="right" title="Disapproved"></i>
                                            @elseif ($fund->date_approved && !$fund->date_disapproved)
                                            <i class="fas fa-thumbs-up fa-lg green-text material-tooltip-main"
                                               data-toggle="tooltip" data-placement="right" title="Approved"></i>
                                            @endif
                                        </td>
                                        <td></td>
                                        <td>
                                            @if (isset($fund->project->directory) && $fund->project->directory)
                                            <small class="grey-text">
                                                <b><em>{{ implode(' / ',unserialize($fund->project->directory)) }} /</em></b>
                                            </small><br>
                                            @endif
                                            {{ isset($fund->project->project_title) ? $fund->project->project_title : '' }}
                                        </td>
                                        <td>{{ date_format(date_create($fund->date_from), "F j, Y") }}</td>
                                        <td>{{ date_format(date_create($fund->date_to), "F j, Y") }}</td>
                                        <td class="material-tooltip-main" data-toggle="tooltip" data-placement="left"
                                            data-html="true" title='
                                                <h6 class="text-left"><b>Original Approved Budget:</b><br>
                                                - &#8369; {!! number_format($fund->approved_budget, 2) !!}<br><br>
                                                <b>Realignments:</b> <br>

                                                @if ($fund->count_realignments > 0)
                                                    @foreach($fund->realignments as $ctrRealign => $realignment)
                                                <b>- R{!! $ctrRealign + 1 !!} = </b>
                                                &#8369; {!! number_format($realignment->realigned_budget, 2) !!}
                                                        @if ($realignment->date_approved)
                                                <i class="fas fa-check-circle green-text material-tooltip-main"
                                                    data-toggle="tooltip" data-placement="left" title="Approved"></i>
                                                        @elseif ($realignment->date_disapproved)
                                                <i class="fas fa-thumbs-down material-tooltip-main"
                                                    data-toggle="tooltip" data-placement="right" title="Disapproved"></i>
                                                        @else
                                                <i class="fas fa-spinner faa-spin fa-pulse material-tooltip-main"
                                                    data-toggle="tooltip" data-placement="right" title="Pending"></i>
                                                        @endif
                                                <br>
                                                    @endforeach
                                                @else
                                                <b>- None</b>
                                                @endif
                                                </h6>'>
                                            &#8369; {{ number_format($fund->current_budget, 2) }}
                                        </td>

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
                                            <b>Project Name:</b> {{ $fund->project_title }}<br>
                                            <small>
                                                <b>Current Approved Budget: </b>
                                                &#8369; {!! number_format($fund->current_budget, 2) !!}
                                            </small><br>
                                            <small>

                                            </small>
                                        </td>
                                    </tr>
                                        @endforeach
                                    @else
                                    <tr>
                                        <td class="p-5" colspan="7" class="text-center py-5">
                                            <h6 class="red-text text-center">
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
            </div>
        </div>
    </section>
</div>

<!-- Modals -->

@if (count($list) > 0)
    @foreach ($list as $listCtr => $fund)
<div class="modal custom-rightmenu-modal fade right" id="right-modal-{{ $listCtr + 1 }}" tabindex="-1"
     role="dialog">
    <div class="modal-dialog modal-full-height modal-righty" role="document">
        <!--Content-->
        <div class="modal-content">
            <!--Header-->
            <div class="modal-header stylish-color-dark white-text">
                <h6>
                    <i class="fas fa-hand-holding-usd"></i>
                    <b>Line-Item Budget Details</b>
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
                                        onclick="$(this).showPrintList(`{{ route('fund-project-lib-show-print',
                                                                        ['id' => $fund->id]) }}`);">
                                    <i class="fas fa-print blue-text"></i> Print LIB
                                </button>

                                @if ($isAllowedUpdate)
                                    @if (!$fund->date_approved)
                                    <button type="button" class="btn btn-outline-mdb-color
                                            btn-sm px-2 waves-effect waves-light"
                                            onclick="$(this).showEdit(`{{ route('fund-project-lib-show-edit',
                                                    ['id' => $fund->id]) }}`);">
                                        <i class="fas fa-edit orange-text"></i> Edit
                                    </button>
                                    @else
                                    <button type="button" class="btn btn-outline-mdb-color
                                            btn-sm px-2 waves-effect waves-light" disabled>
                                        <i class="fas fa-edit orange-text"></i> Edit
                                    </button>
                                    @endif
                                @else
                                    <button type="button" class="btn btn-outline-mdb-color
                                            btn-sm px-2 waves-effect waves-light" disabled>
                                        <i class="fas fa-edit orange-text"></i> Edit
                                    </button>
                                @endif

                                @if ($isAllowedDelete)
                                    @if (!$fund->date_approved)
                                <button type="button" class="btn btn-outline-mdb-color
                                        btn-sm px-2 waves-effect waves-light"
                                        onclick="$(this).showDelete(`{{ route('fund-project-lib-delete',
                                                                        ['id' => $fund->id]) }}`,
                                                                    `{{ $fund->project->project_title.' LIB' }}`);">
                                    <i class="fas fa-trash-alt red-text"></i> Delete
                                </button>
                                    @else
                                <button type="button" class="btn btn-outline-mdb-color
                                        btn-sm px-2 waves-effect waves-light" disabled>
                                    <i class="fas fa-trash-alt red-text"></i> Delete
                                </button>
                                    @endif
                                @else
                                <button type="button" class="btn btn-outline-mdb-color
                                        btn-sm px-2 waves-effect waves-light" disabled>
                                    <i class="fas fa-trash-alt red-text"></i> Delete
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <p>
                            <b>Project Title: </b> {{ $fund->project->project_title }}<br>
                            <b>Approved Budget: </b> &#8369; {{ number_format($fund->approved_budget, 2) }}<br>
                            <b>Realignments: </b>

                            @if ($fund->count_realignments > 0)
                            <br>
                                @foreach($fund->realignments as $ctrRealign => $realignment)
                            <b>&nbsp;&nbsp;R{!! $ctrRealign + 1 !!} = </b>
                            &#8369; {!! number_format($realignment->realigned_budget, 2) !!}

                                    @if ($realignment->date_approved)
                            <i class="fas fa-check-circle green-text material-tooltip-main"
                               data-toggle="tooltip" data-placement="left" title="Approved"></i>
                                    @elseif ($realignment->date_disapproved)
                            <i class="fas fa-thumbs-down material-tooltip-main"
                               data-toggle="tooltip" data-placement="right" title="Disapproved"></i>
                                    @else
                            <i class="fas fa-spinner faa-spin fa-pulse material-tooltip-main"
                               data-toggle="tooltip" data-placement="right" title="Pending"></i>
                                    @endif

                                    @if ((!$realignment->date_approved && !$realignment->date_disapproved) ||
                                         ($realignment->date_disapproved && !$realignment->date_approved))
                            <br>
                                        @if ($isAllowedUpdateRealignLIB)
                            <button type="button" class="btn btn-link btn-md ml-1 my-0 p-1
                                    waves-effect waves-light"
                                    onclick="$(this).showEditRealignment(
                                        `{{ route('fund-project-lib-show-edit-realignment',
                                        ['id' => $fund->id,
                                        'type' => 'update']) }}`);">
                                <b>
                                    [ <i class="far fa-edit deep-orange-text"></i>
                                    Edit (R{{ $fund->count_realignments }}) ]
                                </b>
                            </button>
                                        @endif

                                        @if ($isAllowedDeleteRealignLIB)
                            <button type="button" class="btn btn-link btn-md ml-0 my-0 p-1
                                    waves-effect waves-light"
                                    onclick="$(this).showDelete(
                                        '{{ route('fund-project-lib-destroy-realignment',
                                        ['id' => $fund->current_realigned_budget->id]) }}',
                                        `{{ $fund->project->project_title.' LIB Realignments' }}`);">
                                <b>
                                    [ <i class="far fa-trash-alt red-text"></i>
                                    Discard (R{{$fund->count_realignments}}) ]
                                </b>
                            </button>
                                        @endif
                                    @endif
                            <br>
                                @endforeach
                            @else
                            <span class="red-text">None</span>
                            @endif
                        </p>

                        @if ($fund->date_approved && $isAllowedCreateRealignLIB)
                            @if ($fund->count_realignments > 0)
                                @if ($fund->current_realigned_budget->date_approved &&
                                     !$fund->current_realigned_budget->date_disapproved)
                        <button type="button" class="btn btn-sm btn-dark-green btn-rounded
                                btn-block waves-effect mb-2"
                                onclick="$(this).showCreateRealignment(
                                    `{{ route('fund-project-lib-show-create-realignment',
                                    ['id' => $fund->id,
                                     'type' => 'create']) }}`);">
                                <i class="fas fa-pencil-alt"></i> Request/Create Realignment
                        </button>
                                @endif
                            @else
                        <button type="button" class="btn btn-sm btn-dark-green btn-rounded
                                btn-block waves-effect mb-2"
                                onclick="$(this).showCreateRealignment(
                                    `{{ route('fund-project-lib-show-create-realignment',
                                    ['id' => $fund->id,
                                     'type' => 'create']) }}`);">
                                <i class="fas fa-pencil-alt"></i> Request/Create Realignment
                        </button>
                            @endif
                        @endif
                    </div>
                </div>
                <hr>
                <ul class="list-group z-depth-1">
                    <li class="list-group-item justify-content-between">
                        <h6><b><i class="fas fa-list-ol"></i> Allotments</b></h6>
                    </li>
                    <li class="list-group-item justify-content-between overflow-auto" style="height: 200px;">
                        @if (count($fund->current_realigned_allotments) > 0)
                            @foreach ($fund->current_realigned_allotments as $cntr => $item)
                        <i class="fas fa-caret-right"></i>
                                @if (strlen($item->allotment_name) > 60)
                        <span class="font-weight-bold">
                            {{ $cntr + 1 }}.|&nbsp;
                                    @if (count(explode('::', $item->allotment_name)) >= 2)
                            {{ substr(explode('::', $item->allotment_name)[1], 0, 60) }}...
                                    @else
                            {{ substr($item->allotment_name, 0, 60) }}...
                                    @endif
                            <br>
                        </span>
                                @else
                        <span class="font-weight-bold">
                            {{ $cntr + 1 }}.|&nbsp;
                                    @if (count(explode('::', $item->allotment_name)) >= 2)
                            {{ explode('::', $item->allotment_name)[1] }}
                                    @else
                            {{ $item->allotment_name }}
                                    @endif
                            <br>
                        </span>
                                @endif

                        <span class="text-center w-100 p-4">
                            @if ($fund->current_realigned_budget->date_approved)
                            <i class="fas fa-check-circle green-text material-tooltip-main"
                               data-toggle="tooltip" data-placement="left" title="Approved"></i>
                            @elseif ($fund->current_realigned_budget->date_disapproved)
                            <i class="fas fa-thumbs-down material-tooltip-main"
                               data-toggle="tooltip" data-placement="right" title="Disapproved"></i>
                            @else
                            <i class="fas fa-spinner faa-spin fa-pulse material-tooltip-main"
                               data-toggle="tooltip" data-placement="right" title="Pending"></i>
                            @endif

                            &nbsp;&nbsp;<em>&#8369; {{ number_format($item->allotment_cost, 2) }}</em>
                        </span>
                        <hr class="my-1">
                            @endforeach
                        @endif
                    </li>
                </ul>
                <hr>
                <ul class="btn-menu-3 list-group z-depth-0">
                    <li class="list-action-header list-group-item justify-content-between">
                        <h5><b><i class="fas fa-pen-nib"></i> Actions</b></h5>
                    </li>

                    @if (!$fund->date_disapproved && !$fund->date_approved)

                        @if ($isAllowedApprove)
                    <!-- Approve Button Section -->
                    <li class="list-group-item justify-content-between">
                        <button type="button" class="btn btn-outline-green waves-effect btn-md btn-block btn-rounded"
                                onclick="$(this).showApprove(
                                    `{{ route('fund-project-lib-approve', [
                                            'id' => $fund->id,
                                            'isRealignment' => 0
                                        ]) }}`,
                                    '{{ $fund->id }}'
                                );">
                            <i class="fas fa-thumbs-up"></i> Approve LIB
                        </button>
                    </li>
                        @endif

                        @if ($isAllowedDisapprove)
                    <!-- Disapprove Button Section -->
                    <li class="list-group-item justify-content-between">
                        <button type="button" class="btn btn-outline-black waves-effect btn-md btn-block btn-rounded"
                                onclick="$(this).showDisapprove(
                                    `{{ route('fund-project-lib-disapprove', [
                                            'id' => $fund->id,
                                            'isRealignment' => 0
                                        ]) }}`,
                                    '{{ $fund->id }}'
                                );">
                            <i class="fas fa-thumbs-down"></i> Disapprove LIB
                        </button>
                    </li>
                        @endif
                    @endif

                    @if ($fund->date_approved)
                        @if ($fund->count_realignments > 0)
                            @if (!$fund->current_realigned_budget->date_approved &&
                                 !$fund->current_realigned_budget->date_disapproved)

                                @if ($isAllowedApproveRealignLIB)
                    <!-- Approve Button Section -->
                    <li class="list-group-item justify-content-between">
                        <button type="button" class="btn btn-outline-green waves-effect btn-md btn-block btn-rounded"
                                onclick="$(this).showApprove(
                                    `{{ route('fund-project-lib-approve', [
                                            'id' => $fund->current_realigned_budget->id,
                                            'isRealignment' => 1
                                        ]) }}`,
                                    '{{ $fund->id }}'
                                );">
                            <i class="fas fa-thumbs-up"></i> Approve Realignment
                        </button>
                    </li>
                                @endif

                                @if ($isAllowedDisapproveRealignLIB)
                    <!-- Disapprove Button Section -->
                    <li class="list-group-item justify-content-between">
                        <button type="button" class="btn btn-outline-black waves-effect btn-md btn-block btn-rounded"
                                onclick="$(this).showDisapprove(
                                    `{{ route('fund-project-lib-disapprove', [
                                            'id' => $fund->current_realigned_budget->id,
                                            'isRealignment' => 1
                                        ]) }}`,
                                    '{{ $fund->id }}'
                                );">
                            <i class="fas fa-thumbs-down"></i> Disapprove Realignment
                        </button>
                    </li>
                                @endif
                            @else
                    <li class="list-group-item justify-content-between text-center">
                        No more available actions.
                    </li>
                            @endif
                        @else
                    <li class="list-group-item justify-content-between text-center">
                        No more available actions.
                    </li>
                        @endif
                    @endif
                </ul>
            </div>
            <!--Footer-->
            <div class="modal-footer justify-content-end rgba-stylish-b p-1">
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
@include('modals.delete-destroy')
@include('modals.print')
@include('modals.approve')
@include('modals.disapprove')

@endsection

@section('custom-js')

<script>
    let projects = [];
    let coimplementors = [];
</script>
<script type="text/javascript" src="{{ asset('plugins/select2/js/select2.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/input-validation.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/funding-lib.js') }}"></script>
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
