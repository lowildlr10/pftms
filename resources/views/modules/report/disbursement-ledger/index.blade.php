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
                        <i class="far fa-copy"></i> Report: Disbursement Ledgers
                    </strong>
                </h5>
                <hr class="white hidden-xs">
                <ul class="breadcrumb mdb-color darken-3 mb-0 p-1 white-text hidden-xs">
                    <li>
                        <i class="fa fa-caret-right mx-2" aria-hidden="true"></i>
                    </li>
                    <li>
                        <a href="{{ route('report-disbursement-ledger') }}" class="waves-effect waves-light white-text">
                            Disbursement Ledgers
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
                            <a href="{{ route('report-disbursement-ledger') }}" class="btn btn-outline-white btn-rounded btn-sm px-2">
                                <i class="fas fa-sync-alt fa-pulse"></i>
                            </a>
                        </div>
                    </div>
                    <!--/Card image-->

                    <div class="px-2">
                        <ul class="nav nav-tabs mt-2" id="myTab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="tree-tab" data-toggle="tab" href="#tree-view" role="tab">
                                    Tree View
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="list-tab" data-toggle="tab" href="#list-view" role="tab">
                                    List View
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content p-0">
                            <div class="tab-pane fade show active" id="tree-view" role="tabpanel">
                                <br>
                                <div class="treeview-animated mx-4 my-4 mdb-color-text h5">
                                    <ul class="treeview-animated-list mb-3">
                                        @if (isset($directories['folder']) && count($directories['folder']) > 0)
                                            @foreach ($directories['folder'] as $folder)
                                        <li class="treeview-animated-items">
                                            <a class="closed">
                                                <i class="fas fa-angle-right"></i>
                                                <span>
                                                    <i class="fas fa-folder-open"></i> {{ $folder['name'] }}
                                                </span>
                                            </a>
                                            <ul class="nested">
                                                @if (count($folder['files']) > 0)
                                                    @php $lastDir = '' @endphp

                                                    @foreach ($folder['files'] as $file)
                                                <li>
                                                    <div class="treeview-animated-element">
                                                        {!! $file->directory && $lastDir != $file->directory ?
                                                        '<br><i class="fas fa-folder-open mdb-color-text"></i> '.$file->directory.' / <br><br>' : '' !!}
                                                        @php $lastDir = $file->directory @endphp
                                                        &rarr; <i class="fas fa-file-alt"></i> {{ $file->title }}
                                                        <a href="#" class="btn btn-link btn-sm mdb-color-text px-0 py-1"
                                                           data-target="#right-modal-{{ $file->id }}" data-toggle="modal"
                                                           data-toggle="tooltip" data-placement="left" title="Open">
                                                            <i class="fas fa-folder-open"></i> Open
                                                        </a>
                                                    </div>
                                                </li>
                                                        @php $dirCtr++; @endphp
                                                    @endforeach
                                                @endif
                                                <br>
                                            </ul>
                                        </li>
                                            @endforeach
                                        @endif

                                        @if (isset($directories['folder']) && count($directories['folder']) > 0 &&
                                             isset($directories['file']) && count($directories['file']) > 0)
                                        <hr>
                                        @endif

                                        @if (isset($directories['file']) && count($directories['file']) > 0)
                                            @foreach ($directories['file'] as $file)
                                        <li>
                                            <div class="treeview-animated-element">
                                                <i class="fas fa-file-alt"></i> {{ $file->title }}
                                                <a href="#" class="btn btn-link btn-sm mdb-color-text px-0 py-1"
                                                   data-target="#right-modal-{{ $file->id }}" data-toggle="modal"
                                                   data-toggle="tooltip" data-placement="left" title="Open">
                                                    <i class="fas fa-folder-open"></i> Open
                                                </a>
                                            </div>
                                        </li>
                                                @php $dirCtr++; @endphp
                                            @endforeach
                                        @endif
                                    </ul>
                                    <br>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="list-view" role="tabpanel">
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
                                                        @sortablelink('project_title', 'Project Name', [], ['class' => 'white-text'])
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
                                                        @sortablelink('project_cost', 'Project Cost', [], ['class' => 'white-text'])
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
                                                    <i class="fas fa-paste fa-lg material-tooltip-main indigo-text"
                                                    data-toggle="tooltip" data-placement="right" title="Ledger"></i>
                                                </td>
                                                <td></td>
                                                <td>{{ $fund->project_title }}</td>
                                                <td>{{ date_format(date_create($fund->date_from), "F j, Y") }}</td>
                                                <td>{{ date_format(date_create($fund->date_to), "F j, Y") }}</td>
                                                <td>
                                                    &#8369; {{ number_format($fund->project_cost, 2) }}
                                                </td>

                                                <td align="center">
                                                    <a class="btn-floating btn-sm btn-mdb-color p-2 waves-effect material-tooltip-main mr-0"
                                                    data-target="#right-modal-{{ $fund->id }}" data-toggle="modal"
                                                    data-toggle="tooltip" data-placement="left" title="Open">
                                                        <i class="fas fa-folder-open"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr class="d-none show-xs">
                                                <td data-target="#right-modal-{{ $fund->id }}" data-toggle="modal">
                                                    <b>Project Title:</b> {{ $fund->project_title }}<br>
                                                    <small>
                                                        <b>Project Cost: </b>
                                                        &#8369; {!! number_format($fund->project_cost, 2) !!}
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
            </div>
        </div>
    </section>
</div>

<!-- Modals -->

@if (count($list) > 0)
    @foreach ($list as $listCtr => $fund)
<div class="modal custom-rightmenu-modal fade right" id="right-modal-{{ $fund->id }}" tabindex="-1"
     role="dialog">
    <div class="modal-dialog modal-full-height modal-righty" role="document">
        <!--Content-->
        <div class="modal-content">
            <!--Header-->
            <div class="modal-header stylish-color-dark white-text">
                <h6>
                    <i class="far fa-copy"></i>
                    <b>Project Details</b>
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
                                @if ($fund->has_ledger)
                                {{--
                                <button type="button" class="btn btn-outline-mdb-color
                                        btn-sm px-2 waves-effect waves-light"
                                        onclick="$(this).showPrint('{{ $fund->id }}', 'report_disbursement');">
                                    <i class="fas fa-print blue-text"></i> Print Ledger
                                </button>
                                --}}
                                @endif

                                @if (!$fund->has_ledger)
                                    @if ($isAllowedCreate)
                                <button type="button" class="btn btn-outline-mdb-color
                                        btn-sm px-2 waves-effect waves-light"
                                        onclick="$(this).showCreate(`{{ route('report-disbursement-ledger-show-create',
                                        [
                                            'project_id' => $fund->id,
                                            'for' => 'disbursement',
                                            'type' => $fund->project_type ? $fund->project_type : 'saa',
                                        ]) }}`);">
                                    <i class="fas fa-pencil-alt green-text"></i> Create
                                </button>
                                    @else
                                    @endif
                                @else
                                    @if ($isAllowedUpdate)
                                <button type="button" class="btn btn-outline-mdb-color
                                        btn-sm px-2 waves-effect waves-light"
                                        onclick="$(this).showEdit(`{{ route('report-disbursement-ledger-show-edit',
                                        [
                                            'id' => $fund->ledger_id,
                                            'for' => 'disbursement',
                                            'type' => $fund->project_type ? $fund->project_type : 'saa',
                                        ]) }}`);">
                                    <i class="fas fa-edit orange-text"></i> Edit
                                </button>
                                    @else
                                <button type="button" class="btn btn-outline-mdb-color
                                        btn-sm px-2 waves-effect waves-light" disabled>
                                    <i class="fas fa-edit orange-text"></i> Edit
                                </button>
                                    @endif
                                @endif

                                @if ($isAllowedDelete)
                                    @if ($fund->has_ledger)
                                <button type="button" class="btn btn-outline-mdb-color
                                        btn-sm px-2 waves-effect waves-light"
                                        onclick="$(this).showDelete(`{{ route('report-disbursement-ledger-delete',
                                        [
                                            'id' => $fund->ledger_id,
                                            'for' => 'disbursement',
                                        ]) }}`, `{{ $fund->project_title.' (Disbursement Ledger)' }}`);">
                                    <i class="fas fa-trash-alt red-text"></i> Delete
                                </button>
                                    @endif
                                @else
                                    @if ($fund->has_ledger)
                                <button type="button" class="btn btn-outline-mdb-color
                                        btn-sm px-2 waves-effect waves-light" disabled>
                                    <i class="fas fa-trash-alt red-text"></i> Delete
                                </button>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <p>
                            <b>Project Title: </b> {{ $fund->project_title }}<br>
                            <b>Project Cost: </b> &#8369; {{ number_format($fund->project_cost, 2) }}<br>
                            <b>Project Leader: </b> {{ $fund->project_leader }}<br>
                            <b>Ledger Type: </b> {{ $fund->project_type_name }}<br>
                        </p>

                        <hr>

                        @if ($fund->has_ledger)
                        <button class="btn btn-sm btn-mdb-color btn-rounded btn-block waves-effect mb-2"
                                onclick="$(this).showLedger(`{{ route('report-disbursement-ledger-show',
                                [
                                    'id' => $fund->ledger_id,
                                    'for' => 'disbursement',
                                    'type' => $fund->project_type ? $fund->project_type : 'saa',
                                ]) }}`, `{{ $fund->project_title.' (Disbursement Ledger)' }}`);">
                                <i class="fas fa-eye"></i> Show Disbursement Ledger
                        </button>
                        @endif
                    </div>
                </div>
                <hr>
                <ul class="btn-menu-3 list-group z-depth-1">
                    <li class="list-action-header list-group-item justify-content-between">
                        <h5><strong><i class="fas fa-pen-nib"></i> Actions</strong></h5>
                    </li>
                    <li class="list-group-item justify-content-between">
                        <a onclick="$(this).redirectToDoc(`{{ route('fund-project-lib') }}`, '{{ $fund->id }}');"
                           class="btn btn-outline-mdb-color waves-effect btn-block btn-md btn-rounded">
                            Go to LIB & Realignments <i class="fas fa-angle-double-right"></i>
                        </a>
                    </li>
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
@include('modals.show-full')
@include('modals.create')
@include('modals.edit')
@include('modals.delete-destroy')
@include('modals.print')

@endsection

@section('custom-js')

<script type="text/javascript" src="{{ asset('plugins/select2/js/select2.full.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/input-validation.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/funding-ledger.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/print.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/attachment.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/xlsx.full.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/FileSaver.min.js') }}"></script>
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
