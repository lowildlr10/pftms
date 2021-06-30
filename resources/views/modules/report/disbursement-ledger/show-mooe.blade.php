<form class="wow animated fadeIn">
    <div class="card">
        <div class="card-body">
            <h4>Disbursement Ledger (MOOE)</h4>
            <h6>{{ $projectTitle }}</h6>
            <hr>
            <div class="row">
                <div class="col-md-12  px-0 table-responsive">
                    <table class="table table-sm table-hover table-bordered" style="width: max-content;">
                        <thead class="text-center">
                            <tr>
                                <th class="align-top" width="100px">
                                    <small class="font-weight-bold">
                                        Month
                                    </small>
                                </th>
                                <th class="align-top" width="120px">
                                    <small class="font-weight-bold">
                                        ORS No
                                    </small>
                                </th>
                                <th class="align-top" width="130px">
                                    <small class="font-weight-bold">
                                        Payee
                                    </small>
                                </th>
                                <th class="align-top" width="130px">
                                    <small class="font-weight-bold">
                                        Particulars
                                    </small>
                                </th>
                                <th class="align-top" width="130px">
                                    <small class="font-weight-bold">
                                        Account Code
                                    </small>
                                </th>
                                <th class="align-top" width="100px">
                                    <small class="font-weight-bold">
                                        Prior Year
                                    </small>
                                </th>
                                <th class="align-top" width="100px">
                                    <small class="font-weight-bold">
                                        Continuing
                                    </small>
                                </th>
                                <th class="align-top" width="100px">
                                    <small class="font-weight-bold">
                                        Current
                                    </small>
                                </th>
                                <th class="align-top" width="120px">
                                    <small class="font-weight-bold">
                                        Unit
                                    </small>
                                </th>
                            </tr>
                        </thead>

                        <tbody id="item-row-container" class="sortable">
                            @if (count($groupedVouchers) > 0)
                                @foreach ($groupedVouchers as $groupedVoucher)
                                    @if (count($groupedVoucher->vouchers))
                                        @foreach ($groupedVoucher->vouchers as $dv)
                            <tr id="item-row-{{ $itemCounter }}" class="item-row">
                                <td align="center">
                                    {{ $dv->date_disbursed }}
                                </td>
                                <td align="center">
                                    {{ $dv->serial_no ? $dv->serial_no : $dv->ors_no }}
                                </td>
                                <td>
                                            @foreach ($payees as $pay)
                                                @if ($pay->id == $dv->payee)
                                    {{ $pay->name }}
                                                @endif
                                            @endforeach
                                </td>
                                <td>
                                    {{ $dv->particulars }}
                                </td>
                                <td class="material-tooltip-main" data-toggle="tooltip" title="Particulars: {{ $dv->particulars }}">
                                    @php
                                    $mooeAccounts = [];

                                    foreach ($mooeTitles as $mooe) {
                                        if (in_array($mooe->id, $dv->uacs_object_code) || in_array($mooe->id, $dv->mooe_account)) {
                                            $mooeAccounts[] = $mooe->uacs_code;
                                        }
                                    }
                                    @endphp

                                    {{ implode(', ', $mooeAccounts) }}
                                </td>
                                <td class="material-tooltip-main" data-toggle="tooltip" title="Particulars: {{ $dv->particulars }}"
                                    align="center">
                                    {!! $dv->prior_year ? number_format($dv->prior_year, 2) : '<b>-</b>' !!}
                                </td>
                                <td class="material-tooltip-main" data-toggle="tooltip" title="Particulars: {{ $dv->particulars }}"
                                    align="center">
                                    {!! $dv->continuing ? number_format($dv->continuing, 2) : '<b>-</b>' !!}
                                </td>
                                <td class="material-tooltip-main" data-toggle="tooltip" title="Particulars: {{ $dv->particulars }}"
                                    align="center">
                                    {!! $dv->current ? number_format($dv->current, 2) : '<b>-</b>' !!}
                                </td>
                                <td class="material-tooltip-main" data-toggle="tooltip" title="Particulars: {{ $dv->particulars }}">
                                            @foreach ($empUnits as $empUnit)
                                                @if ($empUnit->id == $dv->unit || $empUnit->id == $dv->ledger_unit)
                                    {{ $empUnit->name }}
                                                @endif
                                            @endforeach
                                </td>
                            </tr>
                                        @endforeach
                            <tr class="green lighten-3">
                                <td class="font-weight-bold py-2" colspan="2">
                                    TOTAL for the Month of {{ $groupedVoucher->month_label }}
                                </td>
                                <td colspan="3" class="font-weight-bold py-2"></td>
                                <td class="font-weight-bold py-2" align="center">
                                    {!! $groupedVoucher->month_prior_year ? number_format($groupedVoucher->month_prior_year, 2) : '<b>-</b>' !!}
                                </td>
                                <td class="font-weight-bold py-2" align="center">
                                    {!! $groupedVoucher->month_continuing ? number_format($groupedVoucher->month_continuing, 2) : '<b>-</b>' !!}
                                </td>
                                <td class="font-weight-bold py-2" align="center">
                                    {!! $groupedVoucher->month_current ? number_format($groupedVoucher->month_current, 2) : '<b>-</b>' !!}
                                </td>
                                <td></td>
                            </tr>
                                    @else
                            <tr>
                                <td class="font-weight-bold red-text py-2" colspan="9">
                                    <em>
                                        No disbursement for the month of {{ $groupedVoucher->month_label }}
                                    </em>
                                </td>
                            </tr>
                                    @endif
                                @endforeach
                            @else
                            <tr>
                                <td id="item-row-empty" class="py-3 red-text pl-4" colspan="9">
                                    <h5>
                                        <i class="fas fa-times-circle"></i> <em>No voucher is disbursed nor created.</em>
                                    </h5>
                                </td>
                                <tr id="item-row-0" class="item-row">
                                    <td colspan="9"></td>
                                </tr>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</form>
