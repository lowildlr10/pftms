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
                            <button type="button" class="btn btn-outline-white btn-rounded btn-sm px-2"
                                    onclick="$(this).showCreate('{{ route('fund-project-lib-show-create') }}');">
                                <i class="fas fa-pencil-alt"></i> Create
                            </button>
                            <a type="button" class="btn btn-outline-white btn-rounded btn-sm px-2"
                                    href="{{ route('funding-source') }}">
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
                                        <th class="th-md text-center" width="3%">#</th>
                                        <th class="th-md" width="3%"></th>
                                        <th class="th-md" width="44%">
                                            <b>
                                                @sortablelink('project.project_name', 'Project Name', [], ['class' => 'white-text'])
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
                                        <th class="th-md" width="21%">
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
                                        <td></td>
                                        <td align="center">
                                            <i class="fas fa-spinner fa-lg faa-spin fa-pulse material-tooltip-main"
                                               data-toggle="tooltip" data-placement="right" title="Pending"></i>
                                        </td>
                                        <td>{{ $fund->project->project_name }}</td>
                                        <td>{{ date_format(date_create($fund->date_from), "F j, Y") }}</td>
                                        <td>{{ date_format(date_create($fund->date_to), "F j, Y") }}</td>
                                        <td class="material-tooltip-main" data-toggle="tooltip" data-html="true"
                                            title='<h6 class="text-left"><b>Original Approved Budget:</b><br>
                                                &#8369; {!! number_format($fund->approved_budget, 2) !!}<br><br>
                                                <b>Realignments:</b> <br>
                                                <b>1st =</b> &#8369; 2,000,154.50 <br>
                                                <b>2nd =</b> &#8369; 2,568,478.00 <br>
                                                <b>3rd =</b> &#8369; 2,704,586.54- <br></h6>'>
                                            &#8369; {{ number_format($fund->approved_budget, 2) }}
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
                                            <b>Project Name:</b> {{ $fund->project_name }}<br>
                                            <small>
                                                <b>Current Approved Budget: </b> -
                                            </small><br>
                                            <small>

                                            </small>
                                        </td>
                                    </tr>
                                        @endforeach
                                    @else
                                    <tr>
                                        <td class="p-5" colspan="5" class="text-center py-5">
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
                                        onclick="$(this).showPrint('{{ $fund->id }}', 'pay_summary');">
                                    <i class="fas fa-print blue-text"></i> Print LIB
                                </button>
                                <button type="button" class="btn btn-outline-mdb-color
                                        btn-sm px-2 waves-effect waves-light"
                                        onclick="$(this).showEdit('{{ route('fund-project-lib-show-edit',
                                                 ['id' => $fund->id]) }}');">
                                    <i class="fas fa-edit orange-text"></i> Edit
                                </button>
                                <button type="button" class="btn btn-outline-mdb-color
                                        btn-sm px-2 waves-effect waves-light"
                                        {{-- onclick="$(this).showDelete('route('#',['id'=>$fund->id])',
                                                                    '{{ $fund->project_name }}');" --}}>
                                    <i class="fas fa-trash-alt red-text"></i> Delete
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <p>
                            <b>Project Name: </b> {{ $fund->project->project_name }}<br>
                            <b>Approved Budget: &#8369; {{ number_format($fund->approved_budget, 2) }}</b><br>
                            <b>Realignments: </b><br>
                        </p>

                        {{--
                        <button type="button" class="btn btn-sm btn-outline-elegant btn-rounded
                                btn-block waves-effect mb-2"
                                onclick="$(this).showAttachment('{{ $fund->lddap_id }}');">
                            <i class="fas fa-paperclip fa-lg"></i> View Attachment
                        </button>

                        <button type="button" class="btn btn-sm btn-outline-elegant btn-rounded
                                btn-block waves-effect mb-2"
                                onclick="$(this).showAttachment('{{ $fund->lddap_id }}');">
                            <i class="fas fa-paperclip fa-lg"></i> View Attachment
                        </button>
                        --}}
                    </div>
                </div>
                <hr>
                <ul class="list-group z-depth-1">
                    <li class="list-group-item justify-content-between">
                        <h6><strong><i class="fas fa-list-ol"></i> Allotments</strong></h6>
                    </li>
                    <li class="list-group-item justify-content-between">
                        @if (count($fund->allotments) > 0)
                            @foreach ($fund->allotments as $cntr => $item)
                        <i class="fas fa-caret-right"></i>

                            @if (strlen($item->allotment_name) > 60)
                        <strong>{{ $cntr + 1 }}.|</strong> {{ substr($item->allotment_name, 0, 60) }}...
                            @else
                        <strong>{{ $cntr + 1 }}.|</strong> {{ $item->allotment_name }}.
                            @endif

                        <span class="font-weight-bold">
                            &#8369; {{ number_format($item->allotted_budget, 2) }}
                        </span>
                        <br>
                            @endforeach
                        @endif
                    </li>
                </ul>
                <hr>
                <ul class="btn-menu-3 list-group z-depth-0">
                    <li class="list-action-header list-group-item justify-content-between">
                        <h5><b><i class="fas fa-pen-nib"></i> Actions</b></h5>
                    </li>

                    {{--
                    @if ($fund->status == 'pending')
                    <li class="list-group-item justify-content-between">
                        <button type="button" class="btn btn-outline-black waves-effect btn-block btn-md btn-rounded"
                                onclick="$(this).showApproval('{{ route('fund-for-approval',
                                                              ['id' => $fund->id]) }}');">
                            <i class="fas fa-flag"></i> For Approval
                        </button>
                    </li>
                    @elseif ($fund->status == 'for_approval')
                    <li class="list-group-item justify-content-between">
                        <button type="button" class="btn btn-outline-green waves-effect btn-block btn-md btn-rounded"
                                onclick="$(this).showApprove('{{ route('fund-approve',
                                                          ['id' => $fund->id]) }}');">
                            <i class="fas fa-check"></i> Approve
                        </button>
                    </li>
                    @elseif ($fund->status == 'approved')
                    <li class="list-group-item justify-content-between">
                        <button type="button" class="btn btn-outline-green waves-effect btn-block btn-md btn-rounded"
                                onclick="$(this).showSubmissionBank('{{ route('fund-submission',
                                                          ['id' => $fund->id]) }}');">
                            <i class="fas fa-piggy-bank"></i> For Submission to Bank
                        </button>
                    </li>
                    @else
                    <li class="list-group-item justify-content-between">
                        No more actions available.
                    </li>
                    @endif
                    --}}
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
{{-- @include('modals.delete-destroy') --}}
@include('modals.print')

@endsection

@section('custom-js')

<script type="text/javascript" src="{{ asset('plugins/select2/js/select2.full.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/input-validation.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/funding-project.js') }}"></script>
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
