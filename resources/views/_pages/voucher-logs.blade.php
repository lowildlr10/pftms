@extends('layouts.app')

@section('main-content')

@if ($toggle == 'pr-rfq')
    @php $type = 'Purchase Request to RFQ'; @endphp
@elseif ($toggle == 'rfq-abstract')
    @php $type = 'RFQ to Abstract'; @endphp
@elseif ($toggle == 'abstract-po')
    @php $type = 'Abstract to PO/JO'; @endphp
@elseif ($toggle == 'po-ors')
    @php $type = 'PO/JO to ORS/BURS'; @endphp
@elseif ($toggle == 'po-iar')
    @php $type = 'PO/JO to IAR'; @endphp
@elseif ($toggle == 'iar-stock')
    @php $type = 'IAR to PAR/RIS/ICS'; @endphp
@elseif ($toggle == 'iar-dv')
    @php $type = 'IAR to DV'; @endphp
@elseif ($toggle == 'ors-dv')
    @php $type = 'ORS/BURS to DV'; @endphp
@endif

<div class="row wow animated fadeIn">
    <section class="mb-5 col-12">
        <div class="card text-white mdb-color darken-3">
            <div class="card-body">
                <h5 class="card-title">
                    <strong>
                        <i class="fas fa-chalkboard"></i> Voucher Tracking: {{ $type }}
                    </strong>
                </h5>
                <hr class="white">
                <ul class="breadcrumb mdb-color darken-3 mb-4">
                    <li>
                        <i class="fa fa-caret-right mx-2" aria-hidden="true"></i>
                    </li>
                    <li class="active">
                        <a href="{{ url('voucher-tracking/' . $toggle) }}" class="waves-effect waves-light cyan-text">
                            {{ $type }}
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
                            <div class="form-row align-items-center">
                                <div class="col-auto">
                                    <!-- Default input -->
                                    <label class="sr-only" for="date-from">From</label>
                                    <div class="input-group mb-2">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="fas fa-calendar-alt"></i>
                                            </div>
                                        </div>
                                        <input type="date" id="date-from" name="date_from" placeholder="From"
                                               class="form-control form-control-sm required" max="{{ date('Y-m-d') }}">
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-long-arrow-alt-right"></i>
                                </div>
                                <div class="col-auto">
                                    <!-- Default input -->
                                    <label class="sr-only" for="date-to">To</label>
                                    <div class="input-group mb-2">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="fas fa-calendar-alt"></i>
                                            </div>
                                        </div>
                                        <input type="date" id="date-to" name="date_to" placeholder="To" min="{{ date('Y-m-d') }}"
                                              class="form-control form-control-sm required" max="{{ date('Y-m-d') }}">
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <!-- Default input -->
                                    <label class="sr-only" for="input-search">Search</label>
                                    <div class="input-group mb-2">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="fas fa-search"></i>
                                            </div>
                                        </div>
                                        <input type="search" class="form-control form-control-sm" id="input-search"
                                               placeholder="Search tag (Optional)">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div>
                            <button id="btn-generate-table" class="btn btn-outline-white btn-rounded btn-sm"
                                    onclick="$(this).generate('{{ $toggle }}');">
                                <i class="fas fa-file-invoice"></i> Generate
                            </button>
                            <a href="{{ url('voucher-tracking/' . $toggle) }}" class="btn btn-outline-white btn-rounded btn-sm px-2">
                                <i class="fas fa-sync-alt fa-pulse"></i>
                            </a>

                        </div>
                    </div>
                    <!--/Card image-->

                    <div class="px-4">

                        <div id="table-generate" class="table-wrapper table-responsive">

                            <!--Table-->
                            <table id="table-list" class="table table-bordered table-hover table-b table-sm">

                                <!--Table head-->
                                @if ($toggle == 'pr-rfq')
                                <thead>
                                    <tr>
                                        <td width="2%"></td>
                                        <td colspan="5" width="39%">Purchase Request</td>
                                        <td colspan="5" width="39%">Request for Qoutations</td>
                                        <td colspan="2" width="10%">Date Time Count</td>
                                        <td colspan="2" width="10%"></td>
                                    </tr>
                                </thead>
                                <thead>
                                    <tr>
                                        <td>#</td>

                                        <td>Code</td>
                                        <td>Submitted By</td>
                                        <td>Submitted On</td>
                                        <td>Approved By</td>
                                        <td>Approved On</td>

                                        <td>Code</td>
                                        <td>Issued By</td>
                                        <td>Issued On</td>
                                        <td>Received By</td>
                                        <td>Received On</td>

                                        <td>PR</td>
                                        <td>Canvass</td>

                                        <td>History</td>
                                    </tr>
                                </thead>
                                <!--Table head-->

                                <!--Table body-->
                                <tbody>
                                    <tr>
                                        <td colspan="14">
                                            <h5>
                                               <p align="center" class="text-danger">
                                                    Input <strong>"Date & Time"</strong> range then
                                                    click the <strong>"Generate Button"</strong>.
                                                </p>
                                            </h5>
                                        </td>
                                    </tr>

                                    @for($row = 1; $row <= 20; $row++)
                                    <tr><td colspan="14"></td></tr>
                                    @endfor

                                </tbody>
                                <!--Table body-->
                                @elseif ($toggle == 'rfq-abstract')
                                <thead>
                                    <tr>
                                        <td width="2%"></td>
                                        <td colspan="6" width="64%">Request for Qoutations</td>
                                        <td colspan="2" width="24%">Abstract</td>
                                        <td width="10%">Date Time Count</td>
                                    </tr>
                                </thead>
                                <thead>
                                    <tr>
                                        <td>#</td>

                                        <td>Code</td>
                                        <td>Issued By</td>
                                        <td>Issued To</td>
                                        <td>Issued On</td>
                                        <td>Received By</td>
                                        <td>Received On</td>

                                        <td>Code</td>
                                        <td>PO/JO On</td>

                                        <td>RFQ => Abstract</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="10">
                                            <h5>
                                               <p align="center" class="text-danger">
                                                    Input <strong>"Date & Time"</strong> range then
                                                    click the <strong>"Generate Button"</strong>.
                                                </p>
                                            </h5>
                                        </td>
                                    </tr>

                                    @for($row = 1; $row <= 20; $row++)
                                    <tr><td colspan="10"></td></tr>
                                    @endfor

                                </tbody>
                                @elseif ($toggle == 'abstract-po')
                                <thead>
                                    <tr>
                                        <td width="2%"></td>
                                         <td colspan="2" width="24%">Abstract</td>
                                         <td colspan="7" width="54%">Purchase/Job Order</td>
                                         <td colspan="2" width="10%">Date Time Count</td>
                                         <td width="10%"></td>
                                    </tr>
                                </thead>
                                <thead>
                                    <tr>
                                        <td>#</td>

                                        <td>Code</td>
                                        <td>PO/JO On</td>

                                        <td>Code</td>
                                        <td>Approved On</td>
                                        <td>Issued By</td>
                                        <td>Issued To</td>
                                        <td>Issued On</td>
                                        <td>Received By</td>
                                        <td>Received On</td>

                                        <td>Abstract</td>
                                        <td>PO/JO</td>

                                        <td>History</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="13">
                                            <h5>
                                               <p align="center" class="text-danger">
                                                    Input <strong>"Date & Time"</strong> range then
                                                    click the <strong>"Generate Button"</strong>.
                                                </p>
                                            </h5>
                                        </td>
                                    </tr>

                                    @for($row = 1; $row <= 20; $row++)
                                    <tr><td colspan="13"></td></tr>
                                    @endfor

                                </tbody>
                                @elseif ($toggle == 'po-ors')
                                <thead>
                                    <tr>
                                        <td width="2%"></td>
                                        <td colspan="6" width="39%">
                                            Purchase/Job Order
                                        </td>
                                        <td colspan="6" width="39%">
                                            Obligation / Budget Utilization & Request Status
                                        </td>
                                        <td colspan="2" width="10%">Date Time Count</td>
                                        <td width="10%"></td>
                                    </tr>
                                </thead>
                                <thead>
                                    <tr>
                                        <td>#</td>

                                        <td>Code</td>
                                        <td>Approved On</td>
                                        <td>Submitted By</td>
                                        <td>Submitted On</td>
                                        <td>Received By</td>
                                        <td>Received On</td>

                                        <td>Code</td>
                                        <td>Issued By</td>
                                        <td>Issued On</td>
                                        <td>Received By</td>
                                        <td>Received On</td>
                                        <td>Obligated On</td>

                                        <td>PO/JO</td>
                                        <td>ORS/BURS</td>

                                        <td>History</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="16">
                                            <h5>
                                               <p align="center" class="text-danger">
                                                    Input <strong>"Date & Time"</strong> range then
                                                    click the <strong>"Generate Button"</strong>.
                                                </p>
                                            </h5>
                                        </td>
                                    </tr>

                                    @for($row = 1; $row <= 20; $row++)
                                    <tr><td colspan="16"></td></tr>
                                    @endfor

                                </tbody>
                                @elseif ($toggle == 'po-iar')
                                <thead>
                                    <tr>
                                        <td width="2%"></td>
                                        <td colspan="6" width="39%">
                                            Purchase/Job Order
                                        </td>
                                        <td colspan="6" width="39%">
                                            Inspection & Acceptance Report
                                        </td>
                                        <td colspan="2" width="10%">Date Time Count</td>
                                        <td width="10%"></td>
                                    </tr>
                                </thead>
                                <thead>
                                    <tr>
                                        <td>#</td>

                                        <td>Code</td>
                                        <td>Submitted By</td>
                                        <td>Responsible</td>
                                        <td>Submitted On</td>
                                        <td>Received By</td>
                                        <td>Received On</td>

                                        <td>Code</td>
                                        <td>Issued By</td>
                                        <td>Issued To</td>
                                        <td>Issued On</td>
                                        <td>Received By</td>
                                        <td>Received On</td>

                                        <td>PO/JO</td>
                                        <td>IAR</td>

                                        <td>History</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="16">
                                            <h5>
                                               <p align="center" class="text-danger">
                                                    Input <strong>"Date & Time"</strong> range then
                                                    click the <strong>"Generate Button"</strong>.
                                                </p>
                                            </h5>
                                        </td>
                                    </tr>

                                    @for($row = 1; $row <= 20; $row++)
                                    <tr><td colspan="16"></td></tr>
                                    @endfor

                                </tbody>
                                @elseif ($toggle == 'iar-stock')
                                <thead>
                                    <tr>
                                        <td width="2%"></td>
                                        <td colspan="6" width="39%">
                                            Inspection & Acceptance Report
                                        </td>
                                        <td colspan="6" width="39%">
                                            Disbursement Voucher
                                        </td>
                                        <td colspan="2" width="10%">Date Time Count</td>
                                        <td width="10%"></td>
                                    </tr>
                                </thead>
                                <thead>
                                    <tr>
                                        <td>#</td>

                                        <td>Code</td>
                                        <td>Issued By</td>
                                        <td>Issued To</td>
                                        <td>Issued On</td>
                                        <td>Inspected By</td>
                                        <td>Inspected On</td>

                                        <td>Code</td>
                                        <td>Submitted By</td>
                                        <td>Submitted On</td>
                                        <td>Received By</td>
                                        <td>Received On</td>
                                        <td>Disbursed On</td>

                                        <td width="5%">IAR</td>
                                        <td width="5%">Stocks</td>

                                        <td>History</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="16">
                                            <h5>
                                               <p align="center" class="text-danger">
                                                    Input <strong>"Date & Time"</strong> range then
                                                    click the <strong>"Generate Button"</strong>.
                                                </p>
                                            </h5>
                                        </td>
                                    </tr>

                                    @for($row = 1; $row <= 20; $row++)
                                    <tr><td colspan="16"></td></tr>
                                    @endfor

                                </tbody>
                                @elseif ($toggle == 'iar-dv')
                                <thead>
                                    <tr>
                                        <td width="2%"></td>
                                        <td colspan="6" width="39%">
                                            Inspection & Acceptance Report
                                        </td>
                                        <td colspan="6" width="39%">
                                            Disbursement Voucher
                                        </td>
                                        <td colspan="2" width="10%">Date Time Count</td>
                                        <td width="10%"></td>
                                    </tr>
                                </thead>
                                <thead>
                                    <tr>
                                        <td>#</td>

                                        <td>Code</td>
                                        <td>Issued By</td>
                                        <td>Issued To</td>
                                        <td>Issued On</td>
                                        <td>Received By</td>
                                        <td>Received On</td>

                                        <td>Code</td>
                                        <td>Submitted By</td>
                                        <td>Submitted On</td>
                                        <td>Received By</td>
                                        <td>Received On</td>
                                        <td>Disbursed On</td>

                                        <td>IAR</td>
                                        <td>DV</td>

                                        <td>History</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="16">
                                            <h5>
                                               <p align="center" class="text-danger">
                                                    Input <strong>"Date & Time"</strong> range then
                                                    click the <strong>"Generate Button"</strong>.
                                                </p>
                                            </h5>
                                        </td>
                                    </tr>

                                    @for($row = 1; $row <= 20; $row++)
                                    <tr><td colspan="16"></td></tr>
                                    @endfor

                                </tbody>
                                @elseif ($toggle == 'ors-dv')
                                <thead>
                                    <tr>
                                        <td width="2%"></td>
                                        <td colspan="8" width="44%">Obligation / Budget Utilization & Request Status</td>
                                        <td colspan="6" width="36%">Disbursement Voucher</td>
                                        <td colspan="2" width="10%">Date Time Count</td>
                                        <td width="10%"></td>
                                    </tr>
                                </thead>
                                <thead>
                                    <tr>
                                        <td>#</td>

                                        <td>Code</td>
                                        <td>Document Type</td>
                                        <td>Submitted By</td>
                                        <td>Submitted On</td>
                                        <td>Received By</td>
                                        <td>Received On</td>
                                        <td>Obligated By</td>
                                        <td>Obligated On</td>

                                        <td>Code</td>
                                        <td>Issued By</td>
                                        <td>Submitted On</td>
                                        <td>Received By</td>
                                        <td>Received On</td>
                                        <td>Disbursed On</td>

                                        <td>Obligation</td>
                                        <td>Disbursement</td>

                                        <td>History</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="18">
                                            <h5>
                                               <p align="center" class="text-danger">
                                                    Input <strong>"Date & Time"</strong> range then
                                                    click the <strong>"Generate Button"</strong>.
                                                </p>
                                            </h5>
                                        </td>
                                    </tr>

                                    @for($row = 1; $row <= 20; $row++)
                                    <tr><td colspan="18"></td></tr>
                                    @endfor

                                </tbody>
                                @endif

                            </table>
                            <!--Table-->
                        </div>
                    </div>

                    <div class="col-md-12 mb-2 mt-2">
                        <!--
                        <button class="btn btn-default btn-block" onclick="$(this).showPrint('1', 'voucher-logs', '{{ $toggle }}');"
                                disabled="disabled" id="btn-generate">
                            <i class="fas fa-print text-info"></i> Generate Document
                        </button>
                        -->

                        <button class="btn btn-outline-primary btn-block" onclick="$(this).generateExcel('{{ $toggle }}');"
                                disabled="disabled" id="btn-generate">
                            <i class="fas fa-file-excel text-success"></i> Download as Excel
                        </button>
                    </div>
                </div>
                <!-- Table with panel -->

            </div>
        </div>
    </section>
</div>

@endsection

@section('custom-js')

<script src="{{ asset('assets/js/voucher-logs.js') }}"></script>
<script src="{{ asset('js/xlsx.full.min.js') }}"></script>
<script src="{{ asset('js/FileSaver.min.js') }}"></script>

@endsection
