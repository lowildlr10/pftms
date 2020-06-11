@extends('layouts.partials.vlogs')

@section('log-content')

<table id="table-list" class="table table-bordered table-hover table-b table-sm">
    <thead>
        <tr>
            <td width="2%"></td>
            <td class="table-divider" colspan="6" width="39%">
                Purchase/Job Order
            </td>
            <td class="table-divider" colspan="6" width="39%">
                Obligation / Budget Utilization & Request Status
            </td>
            <td class="table-divider" colspan="2" width="10%">Date Time Count</td>
            <td class="table-divider" width="10%"></td>
        </tr>
    </thead>
    <thead>
        <tr>
            <td>#</td>

            <td class="table-divider">Code</td>
            <td>Created On</td>
            <td>Submitted By</td>
            <td>Submitted On</td>
            <td>Received By</td>
            <td>Received On</td>

            <td class="table-divider">Code</td>
            <td>Issued By</td>
            <td>Issued On</td>
            <td>Received By</td>
            <td>Received On</td>
            <td>Obligated On</td>

            <td class="table-divider" width="5%">
                PO/JO
                <a href="#" data-toggle="tooltip"
                   title="{{ $poTooltip }}">
                    <i class="fas fa-exclamation-circle"></i>
                </a>
            </td>
            <td width="5%">
                ORS/BURS
                <a href="#" data-toggle="tooltip"
                   title="{{ $orsTooltip }}">
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

        <!-- PO/JO Division -->
        <td class="table-border-left table-divider">
            <strong>{{ $log->po_code }}</strong><br>
            [{{ $log->document_type }} No: {{ $log->po_no }}]
        </td>
        <td class="table-border-left" align="center">{{ $log->po_created_at }}</td>
        <td class="table-border-left" align="center">{{ strtoupper($log->po_document_status->issued_by) }}</td>
        <td class="table-border-left" align="center">{{ $log->po_document_status->date_issued }}</td>
        <td class="table-border-left" align="center">{{ strtoupper($log->po_document_status->received_by) }}</td>
        <td class="table-border-left" align="center">{{ $log->po_document_status->date_received }}</td>

        <!-- ORS/BURS Division -->
        @if (!empty($log->ors_code))
        <td class="table-divider">
            <strong>{{ $log->ors_code }}</strong>
        </td>
        <td class="table-border-left" align="center">{{ strtoupper($log->ors_document_status->issued_by) }}</td>
        <td class="table-border-left" align="center">{{ $log->ors_document_status->date_issued }}</td>
        <td class="table-border-left" align="center">{{ strtoupper($log->ors_document_status->received_by) }}</td>
        <td class="table-border-left" align="center">{{ $log->ors_document_status->date_received }}</td>
        <td class="table-border-left" align="center">{{ $log->date_obligated }}</td>
        @else
        <td colspan="6" class="table-divider" align="center">
            <h6>
                <strong class="red-text">Not yet created.</strong>
            </h6>
        </td>
        @endif

        <!-- Count Division -->
        <td class="table-divider" align="center" data-toggle="tooltip"
            title="{{ $poTooltip }}">
            <strong> {{ $log->po_range_count }} </strong>
        </td>
        <td class="table-border-left" align="center" data-toggle="tooltip"
            title="{{ $orsTooltip }}">
            <strong> {{ $log->ors_range_count }} </strong>
        </td>

        <!-- History Division -->
        <td class="table-divider">
            <p>
                @if (empty($log->po_document_history) && empty($log->ors_document_history))
                <strong>N/a</strong>
                @endif

                @if (!empty($log->po_document_history))
                <strong>PO/JO</strong><br>
                {!! $log->po_document_history !!}
                @endif

                <br>

                @if (!empty($log->ors_document_history))
                <strong>ORS/BURS</strong><br>
                {!! $log->ors_document_history !!}
                @endif
            </p>
        </td>
    </tr>
            @endforeach
        @else
    <tr>
        <td colspan="16">
            <h4>
                <p align="center" class="text-danger">
                    <strong>No available data.</strong>
                </p>
            </h4>
        </td>
    </tr>
        @for($row = 1; $row <= 20; $row++)
    <tr>
        <td colspan="16"></td>
    </tr>
        @endfor
    @endif
</table>

@endsection
