@extends('layouts.app')

@section('main-content')

<div class="row wow animated fadeIn">
    <section class="mb-5 col-12 module-container">
        <div class="card module-table-container text-white mdb-color darken-3">
            <div class="card-body">
                <h5 class="card-title">
                    <strong>
                            <i class="far fa-eye"></i> Track Purchase Request: {{ $prNo }}
                            {{ $isPrDisapproved ? '(Disapproved)': '' }}
                            {{ $isPrCancelled ? '(Cancelled)': '' }}
                    </strong>
                </h5>
                <hr class="white">

                <!-- Table with panel -->
                <div class="card card-cascade narrower mt-2">

                    <!--Card image-->
                    <div class="gradient-card-header unique-color
                                narrower py-2 px-2 mb-1 d-flex justify-content-between
                                align-items-center">
                        <div>
                            <a href="{{ url('procurement/pr?search=' . $prNo) }}"
                               class="btn btn-outline-white btn-rounded btn-sm px-2">
                                <i class="fas fa-chevron-left"></i> Back
                            </a>
                        </div>
                        <div>
                            <a href="{{ url('procurement/pr/tracker/' . $prNo) }}"
                               class="btn btn-outline-white btn-rounded btn-sm px-2">
                                <i class="fas fa-sync-alt fa-pulse"></i>
                            </a>
                        </div>
                    </div>
                    <!--/Card image-->

                    <div class="px-1">
                        <div class="row">
                            <div class="col-md-12">
                                <!-- Stepers Wrapper -->
                                <ul class="stepper stepper-vertical py-0 px-5">

                                    <!-- PR Step -->
                                    <li>
                                        <a href="#!">
                                            <span class="circle {{ $prTrackData->main_status_color }}">
                                                {!! $prTrackData->main_status_symbol !!}
                                            </span>
                                            <span class="label">Purchase Request</span>
                                        </a>
                                        <div class="step-content black-text py-0">
                                            <!-- Stepers Wrapper -->
                                            <ul class="stepper stepper-vertical py-0 my-0">
                                                <!-- Approved Step -->
                                                <li>
                                                    <a href="#!">
                                                        <span class="circle {{ $prTrackData->_approved_status_color }}">
                                                            {!! $prTrackData->_approved_status_symbol !!}
                                                        </span>
                                                        <span class="label">
                                                            Approved
                                                        </span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </li>

                                    <!-- RFQ Step -->
                                    <li>
                                        <a href="#!">
                                            <span class="circle {{ $rfqTrackData->main_status_color }}">
                                                {!! $rfqTrackData->main_status_symbol !!}
                                            </span>
                                            <span class="label">Request for Quotation</span>
                                        </a>
                                        <div class="step-content black-text py-0">
                                            <!-- Stepers Wrapper -->
                                            <ul class="stepper stepper-vertical py-0 my-0">
                                                <!-- Issued Step -->
                                                <li>
                                                    <a href="#!">
                                                        <span class="circle {{ $rfqTrackData->_issued_status_color }}">
                                                            {!! $rfqTrackData->_issued_status_symbol !!}
                                                        </span>
                                                        <span class="label">
                                                            Issued
                                                        </span>
                                                    </a>
                                                </li>

                                                <!-- Received Step -->
                                                <li>
                                                    <a href="#!">
                                                        <span class="circle {{ $rfqTrackData->_received_status_color }}">
                                                            {!! $rfqTrackData->_received_status_symbol !!}
                                                        </span>
                                                        <span class="label">
                                                            Received
                                                        </span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </li>

                                    <!-- Abstract Step -->
                                    <li>
                                        <a href="#!">
                                            <span class="circle {{ $abstractTrackData->main_status_color }}">
                                                {!! $abstractTrackData->main_status_symbol !!}
                                            </span>
                                            <span class="label">Abstract of Bids and Quotations</span>
                                        </a>
                                        <div class="step-content black-text py-0">
                                            <!-- Stepers Wrapper -->
                                            <ul class="stepper stepper-vertical py-0 my-0">
                                                <!-- Approved for PO/JO Step -->
                                                <li>
                                                    <a href="#!">
                                                        <span class="circle {{ $abstractTrackData->_approved_status_color }}">
                                                            {!! $abstractTrackData->_approved_status_symbol !!}
                                                        </span>
                                                        <span class="label">
                                                            Approved for PO/JO
                                                        </span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </li>

                                    <!-- PO/JO Step -->
                                    <li>
                                        <a href="#!">
                                            <span class="circle {{ $mainPOTrackData->main_status_color }}">
                                                {!! $mainPOTrackData->main_status_symbol !!}
                                            </span>
                                            <span class="label">Purchase/Job Order</span>
                                        </a>
                                        <div class="step-content black-text py-0">
                                            <!-- Stepers Wrapper -->
                                            <ul class="stepper stepper-vertical py-0 my-0">
                                                <!-- PO Docs Step -->
                                                @foreach ($po as $_po)
                                                <li>
                                                    <a href="#!">
                                                        <span class="circle {{ $_po->po_status->main_status_color }}">
                                                            {!! $_po->po_status->main_status_symbol !!}
                                                        </span>
                                                        <span class="label">
                                                            {{ $_po->document_abrv }}: {{ $_po->po_no }}
                                                        </span>
                                                    </a>
                                                    <div class="step-content black-text py-0">
                                                        <!-- Stepers Wrapper -->
                                                        <ul class="stepper stepper-vertical py-0 my-0">
                                                            <!-- Cleared/Signed by Accountant Step -->
                                                            <li>
                                                                <a href="#!">
                                                                    <span class="circle {{ $_po->po_status->_signed_status_color }}">
                                                                        {!! $_po->po_status->_signed_status_symbol !!}
                                                                    </span>
                                                                    <span class="label">Cleared/Signed by Accountant</span>
                                                                </a>
                                                            </li>

                                                            <!-- Approved Step -->
                                                            <li>
                                                                <a href="#!">
                                                                    <span class="circle {{ $_po->po_status->_approved_status_color }}">
                                                                        {!! $_po->po_status->_approved_status_symbol !!}
                                                                    </span>
                                                                    <span class="label">Approved</span>
                                                                </a>
                                                            </li>

                                                            <!-- Issued Step -->
                                                            <li>
                                                                <a href="#!">
                                                                    <span class="circle {{ $_po->po_status->_issued_status_color }}">
                                                                        {!! $_po->po_status->_issued_status_symbol !!}
                                                                    </span>
                                                                    <span class="label">Issued</span>
                                                                </a>
                                                            </li>

                                                            <!-- Received Step -->
                                                            <li>
                                                                <a href="#!">
                                                                    <span class="circle {{ $_po->po_status->_received_status_color }}">
                                                                        {!! $_po->po_status->_received_status_symbol !!}
                                                                    </span>
                                                                    <span class="label">Received</span>
                                                                </a>
                                                            </li>

                                                            <!-- ORS/BURS Step -->
                                                            <li>
                                                                <a href="#!">
                                                                    <span class="circle {{ $_po->ors_status->main_status_color }}">
                                                                        {!! $_po->ors_status->main_status_symbol !!}
                                                                    </span>
                                                                    <span class="label">Obligation / Budget Utilization and Request Status</span>
                                                                </a>
                                                                <div class="step-content black-text py-0">
                                                                    <!-- Stepers Wrapper -->
                                                                    <ul class="stepper stepper-vertical py-0 my-0">
                                                                        <!-- Issued Step -->
                                                                        <li>
                                                                            <a href="#!">
                                                                                <span class="circle {{ $_po->ors_status->_issued_status_color }}">
                                                                                        {!! $_po->ors_status->_issued_status_symbol !!}
                                                                                </span>
                                                                                <span class="label">Issued</span>
                                                                            </a>
                                                                        </li>

                                                                        <!-- Received Step -->
                                                                        <li>
                                                                            <a href="#!">
                                                                                <span class="circle {{ $_po->ors_status->_received_status_color }}">
                                                                                    {!! $_po->ors_status->_received_status_symbol !!}
                                                                                </span>
                                                                                <span class="label">Received</span>
                                                                            </a>
                                                                        </li>

                                                                        <!-- Obligated Step -->
                                                                        <li>
                                                                            <a href="#!">
                                                                                <span class="circle {{ $_po->ors_status->_obligated_status_color }}">
                                                                                    {!! $_po->ors_status->_obligated_status_symbol !!}
                                                                                </span>
                                                                                <span class="label">Obligated</span>
                                                                            </a>
                                                                        </li>
                                                                    </ul>
                                                                </div>
                                                            </li>

                                                            <!-- IAR Step -->
                                                            <li>
                                                                <a href="#!">
                                                                    <span class="circle {{ $_po->iar_status->main_status_color }}">
                                                                        {!! $_po->iar_status->main_status_symbol !!}
                                                                    </span>
                                                                    <span class="label">Inspection and Acceptance Report</span>
                                                                </a>
                                                                <div class="step-content black-text py-0">
                                                                    <!-- Stepers Wrapper -->
                                                                    <ul class="stepper stepper-vertical py-0 my-0">
                                                                        <!-- Issued Step -->
                                                                        <li>
                                                                            <a href="#!">
                                                                                <span class="circle {{ $_po->iar_status->_issued_status_color }}">
                                                                                    {!! $_po->iar_status->_issued_status_symbol !!}
                                                                                </span>
                                                                                <span class="label">Issued</span>
                                                                            </a>
                                                                        </li>

                                                                        <!-- Inspected Step -->
                                                                        <li>
                                                                            <a href="#!">
                                                                                <span class="circle {{ $_po->iar_status->_inspected_status_color }}">
                                                                                    {!! $_po->iar_status->_inspected_status_symbol !!}
                                                                                </span>
                                                                                <span class="label">Inspected</span>
                                                                            </a>
                                                                        </li>

                                                                        <!-- Issued to Inventory Step -->
                                                                        <li>
                                                                            <a href="#!">
                                                                                <span class="circle {{ $_po->iar_status->_issued_inventory_status_color }}">
                                                                                    {!! $_po->iar_status->_issued_inventory_status_symbol !!}
                                                                                </span>
                                                                                <span class="label">Issued to Inventory</span>
                                                                            </a>
                                                                        </li>
                                                                    </ul>
                                                                </div>
                                                            </li>

                                                            <!-- DV Step -->
                                                            <li>
                                                                <a href="#!">
                                                                    <span class="circle {{ $_po->dv_status->main_status_color }}">
                                                                        {!! $_po->dv_status->main_status_symbol !!}
                                                                    </span>
                                                                    <span class="label">Disbursement Voucher</span>
                                                                </a>
                                                                <div class="step-content black-text py-0">
                                                                    <!-- Stepers Wrapper -->
                                                                    <ul class="stepper stepper-vertical py-0 my-0">
                                                                        <!-- Issued Step -->
                                                                        <li>
                                                                            <a href="#!">
                                                                                <span class="circle {{ $_po->dv_status->_issued_status_color }}">
                                                                                    {!! $_po->dv_status->_issued_status_symbol !!}
                                                                                </span>
                                                                                <span class="label">Issued</span>
                                                                            </a>
                                                                        </li>

                                                                        <!-- Received Step -->
                                                                        <li>
                                                                            <a href="#!">
                                                                                <span class="circle {{ $_po->dv_status->_received_status_color }}">
                                                                                    {!! $_po->dv_status->_received_status_symbol !!}
                                                                                </span>
                                                                                <span class="label">Received</span>
                                                                            </a>
                                                                        </li>

                                                                        <!-- Disbursed Step -->
                                                                        <li>
                                                                            <a href="#!">
                                                                                <span class="circle {{ $_po->dv_status->_disbursed_status_color }}">
                                                                                    {!! $_po->dv_status->_disbursed_status_symbol !!}
                                                                                </span>
                                                                                <span class="label">Disbursed</span>
                                                                            </a>
                                                                        </li>
                                                                    </ul>
                                                                </div>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </li>

                                </ul>
                                <!-- /.Stepers Wrapper -->
                            </div>
                        </div>
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
