@extends('layouts.partials.vlogs')

@section('log-content')

<table id="table-list" class="table table-bordered table-hover table-b table-sm">
    <thead>
        <tr>
            <td width="2%"></td>
            <td class="table-divider" colspan="5" width="39%">Purchase Request</td>
            <td class="table-divider" colspan="5" width="39%">Request for Qoutations</td>
            <td class="table-divider" colspan="2" width="10%">Date Time Count</td>
            <td class="table-divider" colspan="2" width="10%"></td>
        </tr>
    </thead>
    <thead>
        <tr>
            <td>#</td>

            <td class="table-divider">Code</td>
            <td>Submitted By</td>
            <td>Submitted On</td>
            <td>Approved By</td>
            <td>Approved On</td>

            <td class="table-divider">Code</td>
            <td>Issued By</td>
            <td>Issued On</td>
            <td>Received By</td>
            <td>Received On</td>

            <td class="table-divider" width="5%">
                PR
                <a href="#" data-toggle="tooltip"
                   title="{{ $prTooltip }}">
                    <i class="fas fa-exclamation-circle"></i>
                </a>
            </td>
            <td width="5%">
                RFQ
                <a href="#" data-toggle="tooltip"
                   title="{{ $rfqTooltip }}">
                    <i class="fas fa-exclamation-circle"></i>
                </a>
            </td>

            <td class="table-divider">History</td>
        </tr>
    </thead>

    @if (count($prRfqData) > 0)
        @foreach ($prRfqData as $listCounter => $log)
    <tr>
        <td class="table-border-left" align="center">
            {{ ($listCounter + 1) + (($prRfqData->currentpage() - 1) * $prRfqData->perpage()) }}
        </td>

        <!-- PR Division -->
        <td class="table-border-left table-divider">
            <strong>{{ $log->pr_code }}</strong><br>
            [PR No: {{ $log->pr_no }}]
        </td>
        <td class="table-border-left">{{ strtoupper($log->pr_document_status->issued_by) }}</td>
        <td class="table-border-left" align="center">{{ $log->pr_document_status->date_issued }}</td>
        <td class="table-border-left">{{ strtoupper($log->pr_document_status->received_by) }}</td>
        <td class="table-border-left" align="center">{{ $log->date_pr_approved }}</td>

        <!-- RFQ Division -->
        <td class="table-divider">
            @if (!empty($log->rfq_code))
            <strong>{{ $log->rfq_code }}</strong><br>
            [PR No: {{ $log->pr_no }}]
            @endif
        </td>
        <td class="table-border-left">{{ strtoupper($log->rfq_document_status->issued_by) }}</td>
        <td class="table-border-left" align="center">{{ $log->rfq_document_status->date_issued }}</td>
        <td class="table-border-left">{{ strtoupper($log->rfq_document_status->received_by) }}</td>
        <td class="table-border-left" align="center">{{ $log->rfq_document_status->date_received }}</td>

        <!-- Count Division -->
        <td class="table-divider" align="center" data-toggle="tooltip"
            title="{{ $prTooltip }}">
            <strong> {{ $log->pr_range_count }} </strong>
        </td>
        <td class="table-border-left" align="center" data-toggle="tooltip"
            title="{{ $rfqTooltip }}">
            <strong> {{ $log->rfq_range_count }} </strong>
        </td>

        <!-- History Division -->
        <td class="table-divider">
            <p>
                @if (empty($log->pr_document_history) && empty($log->rfq_document_history))
                <strong>N/a</strong>
                @endif

                @if (!empty($log->pr_document_history))
                <strong>PR</strong><br>
                {!! $log->pr_document_history !!}
                @endif

                <br>

                @if (!empty($log->rfq_document_history))
                <strong>RFQ</strong><br>
                {!! $log->rfq_document_history !!}
                @endif
            </p>
        </td>
    </tr>
            @endforeach
        @else
    <tr>
        <td colspan="14">
            <h4>
                <p align="center" class="text-danger">
                    <strong>No available data.</strong>
                </p>
            </h4>
        </td>
    </tr>
        @for($row = 1; $row <= 20; $row++)
    <tr>
        <td colspan="14"></td>
    </tr>
        @endfor
    @endif
</table>

@endsection
