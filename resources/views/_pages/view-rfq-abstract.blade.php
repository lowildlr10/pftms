@extends('layouts.partials.vlogs')

@section('log-content')

<table id="table-list" class="table table-bordered table-hover table-b table-sm">
    <thead>
        <tr>
            <td width="2%"></td>
            <td class="table-divider" colspan="6" width="64%">
                Request for Qoutations
            </td>
            <td class="table-divider" colspan="2" width="24%">
                Abstract
            </td>
            <td class="table-divider" width="10%">
                Date Time Count
            </td>
        </tr>
    </thead>
    <thead>
        <tr>
            <td>#</td>

            <td class="table-divider">Code</td>
            <td>Issued By</td>
            <td>Issued To</td>
            <td>Issued On</td>
            <td>Received By</td>
            <td>Received On</td>

            <td class="table-divider">Code</td>
            <td>PO/JO On</td>

            <td class="table-divider">
                RFQ => Abstract
                <a href="#" data-toggle="tooltip"
                   title="{{ $rfqabstractTooltip }}">
                    <i class="fas fa-exclamation-circle"></i>
                </a>
            </td>
        </tr>
    </thead>

    @if (count($data) > 0)
        @foreach ($data as $listCounter => $log)
    <tr>
        <td class="table-border-left" align="center">
            {{ ($listCounter + 1) + (($data->currentpage() - 1) * $data->perpage()) }}
        </td>

        <!-- RFQ Division -->
        <td class="table-border-left table-divider">
            <strong>{{ $log->rfq_code }}</strong><br>
            [Quotation No: {{ $log->pr_no }}]
        </td>
        <td class="table-border-left">{{ strtoupper($log->rfq_document_status->issued_by) }}</td>
        <td class="table-border-left">{{ strtoupper($log->rfq_document_status->issued_to) }}</td>
        <td class="table-border-left" align="center">{{ $log->rfq_document_status->date_issued }}</td>
        <td class="table-border-left">{{ strtoupper($log->rfq_document_status->received_by) }}</td>
        <td class="table-border-left" align="center">{{ $log->rfq_document_status->date_received }}</td>

        <!-- Abstract Division -->
        <td class="table-border-left table-divider">
            <strong>{{ $log->abstract_code }}</strong>
        </td>
        <td class="table-border-left" align="center">{{ $log->date_abstract_approved }}</td>

        <!-- Count Division -->
        <td class="table-divider" align="center" data-toggle="tooltip"
            title="{{ $rfqabstractTooltip }}">
            <strong>{{ $log->abstract_range_count }}</strong>
        </td>
    </tr>
            @endforeach
        @else
    <tr>
        <td colspan="10">
            <h4>
                <p align="center" class="text-danger">
                    <strong>No available data.</strong>
                </p>
            </h4>
        </td>
    </tr>
        @for($row = 1; $row <= 20; $row++)
    <tr>
        <td colspan="10"></td>
    </tr>
        @endfor
    @endif
</table>

@endsection
