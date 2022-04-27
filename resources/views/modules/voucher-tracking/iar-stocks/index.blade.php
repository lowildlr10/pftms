@extends('layouts.partials.vlogs')

@section('log-content')

<table id="table-list" class="table table-bordered table-hover table-b table-sm">
    <thead>
        <tr>
            <td width="2%"></td>
            <td class="table-divider" colspan="3" width="28%">
                Inspection & Acceptance Report
            </td>
            <td class="table-divider" colspan="6" width="50%">
                RIS/PAR/ICS
            </td>
            <td class="table-divider" colspan="2" width="10%">Date Time Count</td>
            <td class="table-divider" width="10%"></td>
        </tr>
    </thead>
    <thead>
        <tr>
            <td>#</td>

            <td class="table-divider">Code</td>
            <td>Inspected By</td>
            <td>Inspected On</td>

            <td class="table-divider">Code</td>
            <td>Created On</td>
            <td>Issued By</td>
            <td>Issued To</td>
            <td>Qty</td>
            <td>Issued On</td>

            <td class="table-divider" width="5%">
                IAR
                <a href="#" data-toggle="tooltip"
                   title="{{ $iarTooltip }}">
                    <i class="fas fa-exclamation-circle"></i>
                </a>
            </td>
            <td width="5%">
                Stocks
                <a href="#" data-toggle="tooltip"
                   title="{{ $stockTooltip }}">
                    <i class="fas fa-exclamation-circle"></i>
                </a>
            </td>

            <td class="table-divider">History</td>
        </tr>
    </thead>

    @if (count($iarStockData) > 0)
        @foreach ($iarStockData as $listCounter => $log)
    <tr>
        <td class="table-border-left" align="center">
            {{ ($listCounter + 1) + (($iarStockData->currentpage() - 1) * $iarStockData->perpage()) }}
        </td>

        <!-- PO/JO Division -->
        <td class="table-border-left table-divider">
            <strong>{{ $log->iar_code }}</strong><br>
            [{{ $log->inv_classification }} No: {{ $log->po_no }}]
        </td>
        <td class="table-border-left" align="center">{{ strtoupper($log->iar_document_status->received_by) }}</td>
        <td class="table-border-left" align="center">{{ $log->iar_document_status->date_received }}</td>

        <!-- IAR Division -->
        @if (!empty($log->inv_code))
        <td class="table-divider">
            <strong>{{ $log->inv_code }}</strong>
        </td>
        <td class="table-border-left" align="center">{{ $log->inv_created_at }}</td>
        <td class="table-border-left" align="center">
            {{ isset($log->inv_document_status[0]->issued_by) ?
               strtoupper($log->inv_document_status[0]->issued_by): '' }}
        </td>
        <td class="table-border-left">

        @section('log-content')

        <table id="table-list" class="table table-bordered table-hover table-b table-sm">
            <thead>
                <tr>
                    <td width="2%"></td>
                    <td class="table-divider" colspan="3" width="28%">
                        Inspection & Acceptance Report
                    </td>
                    <td class="table-divider" colspan="6" width="50%">
                        RIS/PAR/ICS
                    </td>
                    <td class="table-divider" colspan="2" width="10%">Date Time Count</td>
                    <td class="table-divider" width="10%"></td>
                </tr>
            </thead>
            <thead>
                <tr>
                    <td>#</td>

                    <td class="table-divider">Code</td>
                    <td>Inspected By</td>
                    <td>Inspected On</td>

                    <td class="table-divider">Code</td>
                    <td>Created On</td>
                    <td>Issued By</td>
                    <td>Issued To</td>
                    <td>Qty</td>
                    <td>Issued On</td>

                    <td class="table-divider" width="5%">
                        IAR
                        <a href="#" data-toggle="tooltip"
                           title="{{ $iarTooltip }}">
                            <i class="fas fa-exclamation-circle"></i>
                        </a>
                    </td>
                    <td width="5%">
                        Stocks
                        <a href="#" data-toggle="tooltip"
                           title="{{ $stockTooltip }}">
                            <i class="fas fa-exclamation-circle"></i>
                        </a>
                    </td>

                    <td class="table-divider">History</td>
                </tr>
            </thead>

            @if (count($iarStockData) > 0)
                @foreach ($iarStockData as $listCounter => $log)
            <tr>
                <td class="table-border-left" align="center">
                    {{ ($listCounter + 1) + (($iarStockData->currentpage() - 1) * $iarStockData->perpage()) }}
                </td>

                <!-- IAR Division -->
                <td class="table-border-left table-divider">
                    <strong>{{ $log->iar_code }}</strong><br>
                    [{{ $log->inv_classification }} PO No: {{ $log->po_no }}]
                </td>
                <td class="table-border-left" align="center">{{ strtoupper($log->iar_document_status->received_by) }}</td>
                <td class="table-border-left" align="center">{{ $log->iar_document_status->date_received }}</td>

                <!-- INV Division -->
                @if (!empty($log->inv_code))
                <td class="table-divider">
                    <strong>{{ $log->inv_code }}</strong><br>
                    [Inventory No: {{ $log->inventory_no }}]
                </td>
                <td class="table-border-left" align="center">{{ $log->inv_created_at }}</td>
                <td class="table-border-left" align="center">
                    {{ isset($log->inv_document_status[0]->issued_by) ?
                       strtoupper($log->inv_document_status[0]->issued_by): '' }}
                </td>
                <td class="table-border-left" align="center">
                    {!! isset($log->inv_document_status[0]->issued_to) ?
                       strtoupper($log->inv_document_status[0]->issued_to): '' !!}</td>
                <td class="table-border-left" align="center">
                    {{ isset($log->inv_document_status[0]->quantity) ?
                       $log->inv_document_status[0]->quantity: '' }}
                </td>
                <td class="table-border-left" align="center">
                    {{ isset($log->inv_document_status[0]->date_issued) ?
                       $log->inv_document_status[0]->date_issued: '' }}
                </td>
                @else
                <td colspan="6" class="table-divider" align="center">
                    <h6>
                        <strong class="red-text">Not yet created.</strong>
                    </h6>
                </td>
                @endif

                <!-- Count Division -->
                <td class="table-divider" align="center" data-toggle="tooltip"
                    title="{{ $iarTooltip }}">
                    <strong> {{ $log->iar_range_count }} </strong>
                </td>
                <td class="table-border-left" align="center" data-toggle="tooltip"
                    title="{{ $stockTooltip }}">
                    <strong>
                        {{ isset($log->inv_range_count[0]) ? $log->inv_range_count[0]: 'n/A' }}
                    </strong>
                </td>

                <!-- History Division -->
                <td class="table-divider">
                    <p>
                        @if (!empty($log->iar_document_history))
                        <strong>IAR</strong><br>
                        {!! $log->iar_document_history !!}
                        @else
                        <strong>N/a</strong>
                        @endif
                    </p>
                </td>
            </tr>


                @if (count($log->inv_document_status) > 2 && count($log->inv_range_count) > 2)
                    @foreach ($log->inv_document_status as $logKey => $logStock)
                        @if ($logKey > 0)
            <tr>
                <td class="table-border-left" align="center"></td>

                <!-- PO/JO Division -->
                <td class="table-border-left table-divider"></td>
                <td class="table-border-left" align="center"></td>
                <td class="table-border-left" align="center"></td>

                <!-- IAR Division -->
                <td class="table-divider"></td>
                <td class="table-border-left" align="center"></td>
                <td class="table-border-left" align="center">{{ strtoupper($logStock->issued_by) }}</td>
                <td class="table-border-left" align="center">{!! strtoupper($logStock->issued_to) !!}</td>
                <td class="table-border-left" align="center">{{ $logStock->quantity }}</td>
                <td class="table-border-left" align="center">{{ $logStock->date_issued }}</td>

                <!-- Count Division -->
                <td class="table-divider" align="center" data-toggle="tooltip"</td>
                <td class="table-border-left" align="center" data-toggle="tooltip"
                    title="{{ $stockTooltip }}">
                    <strong>{{ $log->inv_range_count[$logKey] }}</strong>
                </td>

                <!-- History Division -->
                <td class="table-divider"></td>
            </tr>
                            @endif
                        @endforeach
                    @endif
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
                @for($row = 1; $row <= 20; $row++)
            <tr>
                <td colspan="13"></td>
            </tr>
                @endfor
            @endif
        </table>

        @endsection
        <td align="center">
            {!! isset($log->inv_document_status[0]->issued_to) ?
               strtoupper($log->inv_document_status[0]->issued_to): '' !!}</td>
        <td class="table-border-left" align="center">
            {{ isset($log->inv_document_status[0]->quantity) ?
               $log->inv_document_status[0]->quantity: '' }}
        </td>
        <td class="table-border-left" align="center">
            {{ isset($log->inv_document_status[0]->date_issued) ?
               $log->inv_document_status[0]->date_issued: '' }}
        </td>
        @else
        <td colspan="6" class="table-divider" align="center">
            <h6>
                <strong class="red-text">Not yet created.</strong>
            </h6>
        </td>
        @endif

        <!-- Count Division -->
        <td class="table-divider" align="center" data-toggle="tooltip"
            title="{{ $iarTooltip }}">
            <strong> {{ $log->iar_range_count }} </strong>
        </td>
        <td class="table-border-left" align="center" data-toggle="tooltip"
            title="{{ $stockTooltip }}">
            <strong>
                {{ isset($log->inv_range_count[0]) ? $log->inv_range_count[0]: 'n/A' }}
            </strong>
        </td>

        <!-- History Division -->
        <td class="table-divider">
            <p>
                @if (!empty($log->iar_document_history))
                <strong>IAR</strong><br>
                {!! $log->iar_document_history !!}
                @else
                <strong>N/a</strong>
                @endif
            </p>
        </td>
    </tr>


        @if (count($log->inv_document_status) > 2 && count($log->inv_range_count) > 2)
            @foreach ($log->inv_document_status as $logKey => $logStock)
                @if ($logKey > 0)
    <tr>
        <td class="table-border-left" align="center"></td>

        <!-- PO/JO Division -->
        <td class="table-border-left table-divider"></td>
        <td class="table-border-left" align="center"></td>
        <td class="table-border-left" align="center"></td>

        <!-- IAR Division -->
        <td class="table-divider"></td>
        <td class="table-border-left" align="center"></td>
        <td class="table-border-left" align="center">{{ strtoupper($logStock->issued_by) }}</td>
        <td class="table-border-left" align="center">{!! strtoupper($logStock->issued_to) !!}</td>
        <td class="table-border-left" align="center">{{ $logStock->quantity }}</td>
        <td class="table-border-left" align="center">{{ $logStock->date_issued }}</td>

        <!-- Count Division -->
        <td class="table-divider" align="center" data-toggle="tooltip"</td>
        <td class="table-border-left" align="center" data-toggle="tooltip"
            title="{{ $stockTooltip }}">
            <strong>{{ $log->inv_range_count[$logKey] }}</strong>
        </td>

        <!-- History Division -->
        <td class="table-divider"></td>
    </tr>
                    @endif
                @endforeach
            @endif
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
        @for($row = 1; $row <= 20; $row++)
    <tr>
        <td colspan="13"></td>
    </tr>
        @endfor
    @endif
</table>

@endsection
