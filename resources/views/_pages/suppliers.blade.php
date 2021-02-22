@extends('layouts.app')

@section('custom-css')

<style type="text/css">
    .file-field.big .file-path-wrapper { height: 3.2rem; }
    .file-field.big .file-path-wrapper .file-path { height: 3rem; }
</style>

@endsection

@section('main-content')

<div class="row wow animated fadeIn">
    <section class="mb-5 col-12">
        <div class="card text-white mdb-color darken-3">
            <div class="card-body">
                <h5 class="card-title">
                    <strong>
                        <i class="fas fa-book"></i> Libraries: Suppliers
                    </strong>
                </h5>
                <hr class="white">
                <ul class="breadcrumb mdb-color darken-3 mb-0 p-1">
                    <li>
                        <i class="fa fa-caret-right mx-2" aria-hidden="true"></i>
                    </li>
                    <li class="active">
                        <a href="{{ url('libraries/suppliers') }}" class="waves-effect waves-light cyan-text">
                            Suppliers
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
                                    onclick="$(this).showCreate('supplier')">
                                <i class="fas fa-pencil-alt"></i> Create
                            </button>
                        </div>
                        <div>
                            <button class="btn btn-outline-white btn-rounded btn-sm px-2"
                                    data-target="#top-fluid-modal" data-toggle="modal">
                                <i class="fas fa-search"></i>
                            </button>
                            <a href="{{ url('libraries/suppliers') }}" class="btn btn-outline-white btn-rounded btn-sm px-2">
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
                                   href="{{ url('libraries/suppliers') }}">
                                    <small><i class="fas fa-times"></i> Reset</small>
                                </a>
                            </div>
                            @endif

                            <!--Table-->
                            <table class="table module-table table-hover table-b table-sm mb-0">

                                <!--Table head-->
                                <thead class="mdb-color darken-3 white-text">
                                    <tr>
                                        <th class="th-md" width="3%">
                                            <strong>#</strong>
                                        </th>
                                        <th class="th-md" width="25%">
                                            <strong>Company Name</strong>
                                        </th>
                                        <th class="th-md" width="41%">
                                            <strong>Address</strong>
                                        </th>
                                        <th class="th-md" width="15%">
                                            <strong>Contact Person</strong>
                                        </th>
                                        <th class="th-md" width="10%">
                                            <strong>Classification</strong>
                                        </th>
                                        <th class="th-md" width="3%"></th>
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

                                            @foreach ($list as $listCtr => $supplier)
                                                @php $countItem++; @endphp

                                        <tr>
                                            <td align="center">
                                                {{ ($listCtr + 1) + (($list->currentpage() - 1) * $list->perpage()) }}
                                            </td>
                                            <td class="border-left"><strong>{{ $supplier->company_name }}</strong></td>
                                            <td class="border-left">{{ $supplier->address }}</td>
                                            <td class="border-left">
                                                {{ $supplier->contact_person }} &nbsp;

                                                @if (!empty($supplier->mobile_no))
                                                    [{{ $supplier->mobile_no }}]
                                                @endif

                                            </td>
                                            <td class="border-left"><strong>{{ $supplier->classification }}</strong></td>
                                            <td align="center" class="border-left">
                                                <a class="btn-floating btn-sm btn-orange p-2 waves-effect material-tooltip-main mr-0"
                                                   onclick="$(this).showEdit('{{ $supplier->id }}', 'supplier')"
                                                   data-toggle="tooltip" data-placement="left" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </td>
                                            <td align="center">
                                                <a class="btn-floating btn-sm btn-red p-2 waves-effect material-tooltip-main mr-0"
                                                   onclick="$(this).delete('{{ $supplier->id }}', 'supplier')"
                                                   data-toggle="tooltip" data-placement="left" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                            @endforeach

                                            @php $remainingItem = $pageLimit - $countItem; @endphp

                                            @if ($remainingItem != 0)
                                                @for ($itm = 1; $itm <= $remainingItem; $itm++)
                                                   <tr><td colspan="7" style="border: 0;"></td></tr>
                                                @endfor
                                            @endif
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
@include('layouts.partials.modals.top-fluid-search')
@include('layouts.partials.modals.smcard-central')

@endsection

@section('custom-js')

<script src="{{ asset('assets/js/libraries.js') }}"></script>
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
