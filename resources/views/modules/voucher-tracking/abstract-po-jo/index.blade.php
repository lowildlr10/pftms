@extends('layouts.partials.vlogs')

@section('log-content')

<table id="table-list" class="table table-bordered table-hover table-b table-sm">
    <thead>
        <tr>
            <td width="2%"></td>
            <td class="table-divider" colspan="2" width="24%">Abstract</td>
            <td class="table-divider" colspan="7" width="54%">Purchase/Job Order</td>
            <td class="table-divider" colspan="2" width="10%">Date Time Count</td>
            <td class="table-divider" width="10%"></td>
        </tr>
    </thead>
    <thead>
        <tr>
            <td>#</td>

            <td class="table-divider">Code</td>
            <td>PO/JO On</td>

            <td class="table-divider">Code</td>
            <td>Approved On</td>
            <td>Issued By</td>
            <td>Issued To</td>
            <td>Issued On</td>
            <td>Received By</td>
            <td>Received On</td>

            <td class="table-divider" width="5%">
                Abstract
                <a href="#" data-toggle="tooltip"
                   title="{{ $abstractTooltip }}">
                    <i class="fas fa-exclamation-circle"></i>
                </a>
            </td>
            <td width="5%">
                PO/JO
                <a href="#" data-toggle="tooltip"
                   title="{{ $poTooltip }}">
                    <i class="fas fa-exclamation-circle"></i>
                </a>
            </td>

            <td class="table-divider">History</td>
        </tr>
    </thead>

    @if (count($absPoData) > 0)
        @foreach ($absPoData as $listCounter => $log)
    <tr>
        <td class="table-border-left" align="center">
            {{ ($listCounter + 1) + (($absPoData->currentpage() - 1) * $absPoData->perpage()) }}
        </td>

        <!-- Abstract Division -->
        <td class="table-divider">
            <strong>{{ $log->abstract_code }}</strong><br>
            [PR No: {{ $log->pr_no }}]
        </td>
        <td class="table-border-left" align="center">
            {{ $log->date_abstract_approved }}
        </td>

        <!-- PO/JO Division -->
        @if ($log->po_code)
        <td class="table-divider">
            <strong>{{ $log->po_code }}</strong>
        </td>
        <td class="table-border-left">{{ strtoupper($log->date_po_approved) }}</td>
        <td class="table-border-left">{{ strtoupper($log->po_document_status->issued_by) }}</td>
        <td class="table-border-left">{{ strtoupper($log->po_document_status->issued_to) }}</td>
        <td class="table-border-left" align="center">{{ $log->po_document_status->date_issued }}</td>
        <td class="table-border-left">{{ strtoupper($log->po_document_status->received_by) }}</td>
        <td class="table-border-left" align="center">{{ $log->po_document_status->date_received }}</td>
        @else
        <td colspan="7" class="table-divider" align="center">
            <h6>
                <strong class="red-text">Not yet created.</strong>
            </h6>
        </td>
        @endif

        <!-- Count Division -->
        <td class="table-divider" align="center" data-toggle="tooltip"
            title="{{ $abstractTooltip }}">
            <strong>{{ $log->abs_range_count }}</strong>
        </td>
        <td class="table-border-left" align="center" data-toggle="tooltip"
            title="{{ $poTooltip }}">
            <strong>{{ $log->po_range_count }}</strong>
        </td>

        <!-- History Division -->
        <td class="table-divider">
            @if (!empty($log->po_document_history))
            <strong>PO/JO</strong><br>
            {!! $log->po_document_history !!}
            @endif
        </td>
    </tr>
        @endforeach
    @else
    <tr>
        <td colspan="13">
            <h4>
                <p align="center" class="text-danger">
                    <strong>No available data.</strong>
                </p>
            </h4>
        </td>
    </tr>
    @endif
</table>

@endsection
