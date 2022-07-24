@extends('layouts.app')

@section('main-content')

<div class="row wow animated fadeIn">
    <section class="mb-5 col-12 module-container">
        <div class="card mdb-color darken-3">
            <div class="card-body">
                <h5 class="card-title white-text">
                    <strong>
                        <i class="fas fa-box"></i> &#8594; <i class="fas fa-users"></i>
                        Summary of Issued Items per Person Based on the PAR/ICS/RIS
                    </strong>
                </h5>
                <hr class="white hidden-xs">
                <ul class="breadcrumb mdb-color darken-3 mb-0 p-1 white-text hidden-xs">
                    <li>
                        <i class="fa fa-caret-right mx-2" aria-hidden="true"></i>
                    </li>
                    <li class="active">
                        <a href="{{ route('inv-summary-per-person') }}" class="waves-effect waves-light cyan-text">
                            Summary of Issued Items per Person Based on the PAR/ICS/RIS
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
                            <a href="{{ route('inv-summary-per-person') }}" class="btn btn-outline-white btn-rounded btn-sm px-2">
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
                                        <th class="th-md">
                                            @sortablelink('firstname', 'Employee Name', [], ['class' => 'white-text'])
                                        </th>
                                    </tr>
                                </thead>
                                <!--Table head-->

                                <!--Table body-->
                                <tbody>
                                    @if (count($list) > 0)
                                        @foreach ($list as $listCtr => $emp)
                                    <tr class="hidden-xs">
                                        <td>
                                            <a href="{{ route('inv-summary-per-person-view', ["empID" => $emp->id]) }}"
                                               class="btn btn-link btn-block btn-lg text-left
                                                      {{ $emp->is_active == 'n' ? 'red-text' : '' }}">
                                                {{ $emp->name }}
                                                <small class="grey-text"> | {{ $emp->position }}</small>
                                            </a>
                                        </td>
                                    </tr>
                                    <tr class="d-none show-xs">
                                        {{--
                                        <td data-target="#right-modal-{{ $listCtr + 1 }}" data-toggle="modal">
                                            [ PR NO: {{ $pr->pr_no }} ] <i class="fas fa-caret-right"></i> {{
                                                (strlen($pr->purpose) > 150) ?
                                                substr($pr->purpose, 0, 150).'...' : $pr->purpose
                                            }}<br>
                                            <small>
                                                <b>Status:</b> {{ $pr->stat['status_name'] }}
                                            </small><br>
                                            <small>
                                                <b>Requested By:</b> {{ Auth::user()->getEmployee($pr->requestor['id'])->name }}
                                            </small>
                                        </td>
                                        --}}
                                    </tr>
                                        @endforeach
                                    @else
                                    <tr>
                                        <td colspan="9" class="text-center py-5">
                                            <h6 class="red-text">
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
                    </div>

                    <div class="mt-3">
                        {!! $list->appends(\Request::except('page'))->render('pagination') !!}
                    </div>
                </div>
                <!-- Table with panel -->

            </div>
        </div>
    </section>
</div>

@endsection

@section('custom-js')

@endsection
