<form id="form-store" class="wow animated fadeIn">
    <div class="card">
        <div class="card-body">
            <h4>Disbursement Ledger (LGIA)</h4>
            <h6>{{ $projectTitle }}</h6>
            <hr>
            <div class="row">
                <div class="col-md-12  px-0 table-responsive">
                    <table id="table-ledger" class="table table-sm table-hover table-bordered" style="width: max-content;">
                        <thead class="text-center">
                            <tr>
                                <th class="align-top" width="100px">
                                    <small class="font-weight-bold">
                                        Date
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
                                <th class="align-top" width="100px">
                                    <small class="font-weight-bold">
                                        Total
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
                                <td class="material-tooltip-main font-weight-bold" data-toggle="tooltip"
                                    title="Particulars: {{ $dv->particulars }}" align="center">
                                    <em>
                                        {!! $dv->amount ? number_format($dv->amount, 2) : '<b>-</b>' !!}
                                    </em>
                                </td>
                            </tr>
                                        @endforeach
                            <tr class="deep-orange lighten-4">
                                <td class="font-weight-bold red-text py-2">
                                    <em>
                                        {{ $groupedVoucher->month_label }}
                                    </em>
                                </td>
                                <td colspan="3" class="font-weight-bold red-text py-2"></td>
                                <td class="font-weight-bold red-text py-2" align="center">
                                    <em>
                                        {!! $groupedVoucher->month_prior_year ? number_format($groupedVoucher->month_prior_year, 2) : '<b>-</b>' !!}
                                    </em>
                                </td>
                                <td class="font-weight-bold red-text py-2" align="center">
                                    <em>
                                        {!! $groupedVoucher->month_continuing ? number_format($groupedVoucher->month_continuing, 2) : '<b>-</b>' !!}
                                    </em>
                                </td>
                                <td class="font-weight-bold red-text py-2" align="center">
                                    <em>
                                        {!! $groupedVoucher->month_current ? number_format($groupedVoucher->month_current, 2) : '<b>-</b>' !!}
                                    </em>
                                </td>
                                <td class="font-weight-bold red-text py-2" align="center">
                                    <em>
                                        {!! $groupedVoucher->month_total ? number_format($groupedVoucher->month_total, 2) : '<b>-</b>' !!}
                                    </em>
                                </td>
                            </tr>
                            <tr class="orange accent-4">
                                <td class="font-weight-bold py-2">
                                    <em>
                                        As of {{ $groupedVoucher->month_label }}
                                    </em>
                                </td>
                                <td colspan="3" class="font-weight-bold py-2"></td>
                                <td class="font-weight-bold py-2" align="center">
                                    <em>
                                        {!! $groupedVoucher->total_prior_year ? number_format($groupedVoucher->total_prior_year, 2) : '<b>-</b>' !!}
                                    </em>
                                </td>
                                <td class="font-weight-bold py-2" align="center">
                                    <em>
                                        {!! $groupedVoucher->total_continuing ? number_format($groupedVoucher->total_continuing, 2) : '<b>-</b>' !!}
                                    </em>
                                </td>
                                <td class="font-weight-bold py-2" align="center">
                                    <em>
                                        {!! $groupedVoucher->total_current ? number_format($groupedVoucher->total_current, 2) : '<b>-</b>' !!}
                                    </em>
                                </td>
                                <td class="font-weight-bold py-2" align="center">
                                    <em>
                                        {!! $groupedVoucher->total ? number_format($groupedVoucher->total, 2) : '<b>-</b>' !!}
                                    </em>
                                </td>
                            </tr>
                                    @else
                            <tr>
                                <td class="font-weight-bold red-text py-2" colspan="8">
                                    <em>
                                        No disbursement for the month of {{ $groupedVoucher->month_label }}
                                    </em>
                                </td>
                            </tr>
                                    @endif
                                @endforeach
                            @else
                            <tr>
                                <td id="item-row-empty" class="py-3 red-text pl-4" colspan="10">
                                    <h5>
                                        <i class="fas fa-times-circle"></i> <em>No voucher is disbursed nor created.</em>
                                    </h5>
                                </td>
                                <tr id="item-row-0" class="item-row">
                                    <td colspan="8"></td>
                                </tr>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" name="is_realignment" id="is-realignment" value="{{ $isRealignment ? 'y' : 'n' }}">
    <input type="hidden" id="for" value="disbursement">
    <input type="hidden" id="type" value="lgia">
</form>
