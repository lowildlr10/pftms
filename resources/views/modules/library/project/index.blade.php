@extends('layouts.app')

@section('custom-css')

<link rel="stylesheet" type="text/css" href="{{ asset('plugins/select2/css/select2.min.css') }}" rel="stylesheet">

<!-- DataTables CSS -->
<link rel="stylesheet" type="text/css" href="{{ asset('plugins/mdb/css/addons/datatables.min.css') }}" rel="stylesheet">

<!-- DataTables Select CSS -->
<link rel="stylesheet" type="text/css" href="{{ asset('plugins/mdb/css/addons/datatables-select.min.css') }}" rel="stylesheet">

@endsection

@section('main-content')

<div class="row wow animated fadeIn">
    <section class="mb-5 col-12 module-container">
        <div class="card text-white mdb-color darken-3">
            <div class="card-body">
                <h5 class="card-title white-text">
                    <strong>
                        <i class="fas fa-book"></i> Libraries: Projects
                    </strong>
                </h5>
                <hr class="white">
                <ul class="breadcrumb mdb-color darken-3 mb-0 p-1 white-text">
                    <li>
                        <i class="fa fa-caret-right mx-2" aria-hidden="true"></i>
                    </li>
                    <li class="active">
                        <a href="{{ route('project') }}" class="waves-effect waves-light cyan-text">
                            Projects
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
                                    onclick="$(this).showCreate('{{ route('project-show-create') }}');">
                                <i class="fas fa-pencil-alt"></i> Create
                            </button>
                            <a type="button" class="btn btn-outline-white btn-rounded btn-sm px-2"
                                    href="{{ route('fund-project-lib') }}">
                                Go to Line-Item Budgets <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                        <div>
                            <button class="btn btn-outline-white btn-rounded btn-sm px-2"
                                    data-target="#top-fluid-modal" data-toggle="modal">
                                <i class="fas fa-search"></i>
                            </button>
                            <a href="{{ route('project') }}" class="btn btn-outline-white btn-rounded btn-sm px-2">
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
                                                           onclick="$(this).showEdit('{{ route('project-show-edit',
                                                                                     ['id' => $file->id]) }}');">
                                                            <i class="fas fa-edit orange-text"></i> Edit
                                                        </a>
                                                        <a href="#" class="btn btn-link btn-sm mdb-color-text px-0 py-1"
                                                           onclick="$(this).showDelete('{{ route('project-delete', ['id' => $file->id]) }}',
                                                                                       '{{ $file->title }}');">
                                                            <i class="fas fa-trash red-text"></i> Delete
                                                        </a>
                                                    </div>
                                                </li>
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
                                                <a class="btn btn-link btn-sm mdb-color-text px-0 py-1"
                                                   onclick="$(this).showEdit('{{ route('project-show-edit',
                                                                                 ['id' => $file->id]) }}');">
                                                    <i class="fas fa-edit orange-text"></i> Edit
                                                </a>
                                                <a class="btn btn-link btn-sm mdb-color-text px-0 py-1"
                                                   onclick="$(this).showDelete('{{ route('project-delete', ['id' => $file->id]) }}',
                                                                               '{{ $file->title }}');">
                                                    <i class="fas fa-trash red-text"></i> Delete
                                                </a>
                                            </div>
                                        </li>
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
                                        <thead class="mdb-color darken-3 white-text">
                                            <tr>
                                                <th class="th-sm" width="3%"></th>
                                                <th class="th-sm" width="51%">
                                                    <strong></strong>
                                                </th>
                                                <th class="th-sm" width="40%">
                                                    <strong></strong>
                                                </th>
                                                <th class="th-sm" width="3%"></th>
                                                <th class="th-sm" width="3%"></th>
                                            </tr>
                                            <tr>
                                                <th class="th-md" width="3%"></th>
                                                <th class="th-md" width="51%">
                                                    <strong>Project</strong>
                                                </th>
                                                <th class="th-md" width="40%">
                                                    <strong>Project Site/s</strong>
                                                </th>
                                                <th class="th-md" width="3%"></th>
                                                <th class="th-md" width="3%"></th>
                                            </tr>
                                        </thead>
                                        <!--Table head-->

                                        <!--Table body-->
                                        <tbody>
                                            @if (count($list) > 0)
                                                @foreach ($list as $listCtr => $project)
                                            <tr>
                                                <td></td>
                                                <td>
                                                    @if ($project->directory)
                                                    <small class="grey-text">
                                                        <b><em>{{ implode(' / ', unserialize($project->directory)) }} /</em></b>
                                                    </small><br>
                                                    @endif
                                                    {{ $project->project_title }}
                                                </td>
                                                <td>{!! $project->project_site ? implode(' | ', $project->project_site) : '' !!}</td>
                                                <td align="center">
                                                    <a class="btn-floating btn-sm btn-orange p-2 waves-effect material-tooltip-main mr-0"
                                                       onclick="$(this).showEdit('{{ route('project-show-edit',
                                                                                           ['id' => $project->id]) }}');"
                                                       data-toggle="tooltip" data-placement="left" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                </td>
                                                <td align="center">
                                                    <a class="btn-floating btn-sm btn-red p-2 waves-effect material-tooltip-main mr-0"
                                                       onclick="$(this).showDelete('{{ route('project-delete', ['id' => $project->id]) }}',
                                                                                   '{{ $project->project_title }}');"
                                                       data-toggle="tooltip" data-placement="left" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                        <!--Table body-->

                                    </table>
                                    <!--Table-->

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Table with panel -->

            </div>
        </div>
    </section>
</div>

<!-- Modals -->
@include('modals.search')
@include('modals.sm-create')
@include('modals.sm-edit')
@include('modals.delete')

@endsection

@section('custom-js')

<script type="text/javascript" src="{{ asset('plugins/select2/js/select2.full.min.js') }}"></script>

<!-- DataTables JS -->
<script type="text/javascript" src="{{ asset('plugins/mdb/js/addons/datatables.min.js') }}"></script>

<!-- DataTables Select JS -->
<script type="text/javascript" src="{{ asset('plugins/mdb/js/addons/datatables-select.min.js') }}"></script>

<script src="{{ asset('assets/js/input-validation.js') }}"></script>
<script src="{{ asset('assets/js/project.js') }}"></script>
<script src="{{ asset('assets/js/custom-datatables.js') }}"></script>

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
