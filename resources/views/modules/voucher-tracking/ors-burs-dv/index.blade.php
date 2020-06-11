@extends('layouts.partials.vlogs')

@section('log-content')

<table id="table-list" class="table table-bordered table-hover table-b table-sm">
    <thead>
        <tr>
            <td width="2%"></td>
            <td class="table-divider" colspan="8" width="44%">Obligation / Budget Utilization & Request Status</td>
            <td class="table-divider" colspan="6" width="36%">Disbursement Voucher</td>
            <td class="table-divider" colspan="2" width="10%">Date Time Count</td>
            <td class="table-divider" width="10%"></td>
        </tr>
    </thead>
    <thead>
        <tr>
            <td>#</td>

            <td class="table-divider">Code</td>
            <td>Document Type</td>
            <td>Submitted By</td>
            <td>Submitted On</td>
            <td>Received By</td>
            <td>Received On</td>
            <td>Obligated By</td>
            <td>Obligated On</td>

            <td class="table-divider">Code</td>
            <td>Issued By</td>
            <td>Submitted On</td>
            <td>Received By</td>
            <td>Received On</td>
            <td>Disbursed On</td>

            <td class="table-divider" width="5%">
                Obligation
                <a href="#" data-toggle="tooltip"
                   title="{{ $orsTooltip }}">
                    <i class="fas fa-exclamation-circle"></i>
                </a>
            </td>
            <td width="5%">
                Disbursement
                <a href="#" data-toggle="tooltip"
                   title="{{ $dvTooltip }}">
                    <i class="fas fa-exclamation-circle"></i>
                </a>
            </td>

            <td class="table-divider">History</td>
        </tr>
    </thead>

    @if (count($data) > 0)
        @foreach ($data as $listCounter => $log)
    <tr>
        <td class="table-border-left" align="center">
            {{ ($listCounter + 1) + (($data->currentpage() - 1) * $data->perpage()) }}
        </td>

        <!-- ORS/BURS Division -->
        <td class="table-divider">
            <strong>{{ $log->ors_code }}</strong><br>
            [Serial No: {{ $log->serial_no }}]
        </td>
        <td class="table-border-left" align="center"><strong>{{ strtoupper($log->doc_type) }}</strong></td>
        <td class="table-border-left">{{ strtoupper($log->ors_document_status->issued_by) }}</td>
        <td class="table-border-left" align="center">{{ $log->ors_document_status->date_issued }}</td>
        <td class="table-border-left">{{ strtoupper($log->ors_document_status->received_by) }}</td>
        <td class="table-border-left" align="center">{{ $log->ors_document_status->date_received }}</td>
        <td class="table-border-left">{{ strtoupper($log->obligated_by) }}</td>
        <td class="table-border-left" align="center">{{ $log->date_obligated }}</td>

        <!-- DV Division -->
        @if (!empty($log->dv_code))
        <td class="table-divider">
            <strong>{{ $log->dv_code }}</strong><br>
            [DV No: {{ $log->dv_no }}]
        </td>
        <td class="table-border-left">{{ strtoupper($log->dv_document_status->issued_by) }}</td>
        <td class="table-border-left" align="center">{{ $log->dv_document_status->date_issued }}</td>
        <td class="table-border-left">{{ strtoupper($log->dv_document_status->received_by) }}</td>
        <td class="table-border-left" align="center">{{ $log->dv_document_status->date_received }}</td>
        <td class="table-border-left" align="center">{{ $log->date_disbursed }}</td>
        @else
        <td colspan="6" class="table-divider" align="center">
            <h6>
                <strong class="red-text">Not yet created.</strong>
            </h6>
        </td>
        @endif

        <!-- Count Division -->
        <td class="table-divider" align="center" data-toggle="tooltip"
            title="{{ $orsTooltip }}">
            <strong>{{ $log->ors_range_count }}</strong>
        </td>
        <td class="table-border-left" align="center" data-toggle="tooltip"
            title="{{ $dvTooltip }}">
            <strong>{{ $log->dv_range_count }}</strong>
        </td>

        <!-- History Division -->
        <td class="table-divider">
            @if (empty($log->ors_document_history) && empty($log->dv_document_history))
            <strong>N/a</strong>
            @endif

            @if (!empty($log->ors_document_history))
            <strong>ORS/BURS</strong><br>
            {!! $log->ors_document_history !!}
            @endif

            <br>

            @if (!empty($log->dv_document_history))
            <strong>DV</strong><br>
            {!! $log->dv_document_history !!}
            @endif
        </td>
    </tr>
            @endforeach
        @else

    <tr>
        <td colspan="18">
            <h4>
                <p align="center" class="text-danger">
                    <strong>No available data.</strong>
                </p>
            </h4>
        </td>
    </tr>
        @for($row = 1; $row <= 20; $row++)
    <tr>
        <td colspan="18"></td>
    </tr>
        @endfor

    @endif
</table>

@endsection

