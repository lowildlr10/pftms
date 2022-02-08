<form class="wow animated fadeIn">
    <div class="card">
        <div class="card-body">
            <h4>Disbursement Ledger (Special Project)</h4>
            <h6>{{ $projectTitle }}</h6>
            <hr>
            <div class="row">
                <div class="col-md-12 px-0 table-responsive">
                    <table id="table-ledger" class="table table-sm table-hover table-bordered" style="width: max-content;">
                        <thead class="text-center">
                            <tr>
                                <th class="align-middle" colspan="5"></th>

                                @foreach ($classItemCounts as $classKey => $count)
                                    @if ($count > 0)
                                <th class="align-middle" colspan="{{ $count }}">
                                    {{ $classKey }}
                                </th>
                                    @endif
                                @endforeach
                            </tr>
                        </thead>

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

                                @foreach ($allotments as $grpClassItems)
                                    @foreach ($grpClassItems as $ctr => $item)
                                        @if (is_int($ctr))
                                <th class="align-top" width="100px">
                                    <small class="font-weight-bold" id="allot-name-{{ $allotmentCounter + 1 }}">
                                        {{ $item->allotment_name }}
                                    </small>
                                </th>
                                            @php $allotmentCounter++; @endphp
                                        @else
                                            @foreach ($item as $itm)
                                <th class="align-top" width="100px">
                                    <small class="font-weight-bold" id="allot-name-{{ $allotmentCounter + 1 }}">
                                        {{ explode('::', $itm->allotment_name)[1] }}
                                    </small>
                                </th>
                                                @php $allotmentCounter++; @endphp
                                            @endforeach
                                        @endif
                                    @endforeach
                                @endforeach

                                <th class="align-top" width="100px">
                                    <small class="font-weight-bold">
                                        Total
                                    </small>
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($approvedBudgets as $approvedCtr => $approvedBud)
                                @php $allotmentCounter = 0; @endphp
                            <tr>
                                <td align="right" class="red-text font-weight-bold">
                                    {{ $approvedBud->label }}
                                </td>
                                <td></td>
                                <td></td>
                                <td></td>

                                @foreach ($allotments as $grpClassItems)
                                    @foreach ($grpClassItems as $ctr => $item)
                                        @if ($approvedCtr == 0)
                                            @if ($isRealignment)
                                                @if (is_int($ctr))
                                <td align="center" class="red-text font-weight-bold">
                                    <div class="md-form form-sm my-0">
                                        {{ $item->allotment_cost ?
                                           number_format($item->allotment_cost, 2) : '-' }}
                                    </div>
                                </td>
                                                @else
                                                    @foreach ($item as $itm)
                                <td align="center" class="red-text font-weight-bold">
                                    <div class="md-form form-sm my-0">
                                        {{ $itm->allotment_cost ?
                                           number_format($itm->allotment_cost, 2) : '-' }}
                                    </div>
                                </td>
                                                    @endforeach
                                                @endif
                                            @else
                                                @if (is_int($ctr))
                                <td align="center" class="red-text font-weight-bold">
                                    <div class="md-form form-sm my-0">
                                        <input type="hidden" id="allotment-id-{{ $allotmentCounter + 1 }}"
                                               name="allotment_id[{{ $allotmentCounter }}]"
                                               value="{{ $item->id }}">
                                        <input type="hidden" id="allotment-cost-{{ $allotmentCounter + 1 }}"
                                               value="{{ $item->allotment_cost }}">
                                        {{ $item->allotment_cost ?
                                           number_format($item->allotment_cost, 2) : '-' }}
                                    </div>
                                </td>
                                                    @php $allotmentCounter++; @endphp
                                                @else
                                                    @foreach ($item as $itm)
                                <td align="center" class="red-text font-weight-bold">
                                    <div class="md-form form-sm my-0">
                                        <input type="hidden" id="allotment-id-{{ $allotmentCounter + 1 }}"
                                               name="allotment_id[{{ $allotmentCounter }}]"
                                               value="{{ $itm->allotment_id }}">
                                        <input type="hidden" id="allotment-cost-{{ $allotmentCounter + 1 }}"
                                               value="{{ $itm->allotment_cost }}">
                                        {{ $itm->allotment_cost ?
                                           number_format($itm->allotment_cost, 2) : '-' }}
                                    </div>
                                </td>
                                                        @php $allotmentCounter++; @endphp
                                                    @endforeach
                                                @endif
                                            @endif
                                        @else
                                            @php $realignOrderKey = "realignment_$approvedCtr"; @endphp

                                            @if ($approvedCtr == (count($approvedBudgets) - 1))
                                                @if (is_int($ctr))
                                <td align="center" class="red-text font-weight-bold">
                                    <div class="md-form form-sm my-0">
                                        <input type="hidden" id="allotment-id-{{ $allotmentCounter + 1 }}"
                                               name="allotment_id[{{ $allotmentCounter }}]"
                                               value="{{ $item->{$realignOrderKey}->allotment_id }}">
                                        <input type="hidden" id="allot-realign-id-{{ $allotmentCounter + 1 }}"
                                               name="allot_realign_id[{{ $allotmentCounter }}]"
                                               value="{{ $item->{$realignOrderKey}->allotment_realign_id }}">
                                        <input type="hidden" id="allotment-cost-{{ $allotmentCounter + 1 }}"
                                               value="{{ $item->{$realignOrderKey}->allotment_cost }}">
                                        {{ $item->{$realignOrderKey}->allotment_cost ?
                                           number_format($item->{$realignOrderKey}->allotment_cost, 2) : '-' }}
                                    </div>
                                </td>
                                                    @php $allotmentCounter++; @endphp
                                                @else
                                                    @foreach ($item as $itm)
                                <td align="center" class="red-text font-weight-bold">
                                    <div class="md-form form-sm my-0">
                                        <input type="hidden" id="allotment-id-{{ $allotmentCounter + 1 }}"
                                               name="allotment_id[{{ $allotmentCounter }}]"
                                               value="{{ $itm->{$realignOrderKey}->allotment_id }}">
                                        <input type="hidden" id="allot-realign-id-{{ $allotmentCounter + 1 }}"
                                               name="allot_realign_id[{{ $allotmentCounter }}]"
                                               value="{{ $itm->{$realignOrderKey}->allotment_realign_id }}">
                                        <input type="hidden" id="allotment-cost-{{ $allotmentCounter + 1 }}"
                                               value="{{ $itm->{$realignOrderKey}->allotment_cost }}">
                                        {{ $itm->{$realignOrderKey}->allotment_cost ?
                                           number_format($itm->{$realignOrderKey}->allotment_cost, 2) : '-' }}
                                    </div>
                                </td>
                                                        @php $allotmentCounter++; @endphp
                                                    @endforeach
                                                @endif
                                            @else
                                                @if (is_int($ctr))
                                <td align="center" class="red-text font-weight-bold">
                                    <div class="md-form form-sm my-0">
                                        {{ $item->{$realignOrderKey}->allotment_cost ?
                                           number_format($item->{$realignOrderKey}->allotment_cost, 2) : '-' }}
                                    </div>
                                </td>
                                                @else
                                                    @foreach ($item as $itm)
                                <td align="center" class="red-text font-weight-bold">
                                    <div class="md-form form-sm my-0">
                                        {{ $itm->{$realignOrderKey}->allotment_cost ?
                                           number_format($itm->{$realignOrderKey}->allotment_cost, 2) : '-' }}
                                    </div>
                                </td>
                                                    @endforeach
                                                @endif
                                            @endif
                                        @endif
                                    @endforeach
                                @endforeach

                                <td align="center" class="red-text font-weight-bold">
                                    @if ($approvedCtr == count($approvedBudgets) - 1)
                                    <input type="hidden" id="current-total-budget" value="{{ $approvedBud->total }}">
                                    @endif

                                    {{ number_format($approvedBud->total, 2) }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>

                        <tbody id="item-row-container" class="sortable">
                            @php $allotmentAmount = 0; @endphp

                            @if (count($groupedVouchers) > 0)
                                @foreach ($groupedVouchers as $groupedVoucher)
                                    @if (count($groupedVoucher->vouchers))
                                        @foreach ($groupedVoucher->vouchers as $dv)
                                            @php $allotmentCounter = 0; @endphp
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
                                    @php
                                        if (strpos($dv->ledger_particulars, "\n") !== FALSE) {
                                            $searchStr = ["\r\n", "\n", "\r"];
                                            $dv->ledger_particulars = str_replace($searchStr, '<br>', $dv->ledger_particulars);
                                        }
                                    @endphp
                                    {!! $dv->ledger_particulars !!}
                                </td>

                                            @foreach ($dv->allotments as $allotCtr => $item)
                                <td align="center" class="material-tooltip-main" data-toggle="tooltip"
                                    title="Particulars: {{ $dv->particulars }}">
                                    <input type="hidden"
                                           id="allot-remain-{{ $itemCounter }}-{{ $allotCtr + 1 }}"
                                           value="{{ $item->amount }}">
                                    {!! $item->amount ? number_format($item->amount, 2) : '<b>-</b>' !!}
                                </td>
                                                @php $allotmentCounter++; @endphp
                                            @endforeach

                                <td align="center" class="material-tooltip-main" data-toggle="tooltip"
                                    title="Particulars: {{ $dv->particulars }}">
                                    {{ number_format($dv->amount ? $dv->amount : $dv->total, 2) }}
                                </td>
                            </tr>

                                            @php $itemCounter++ @endphp
                                        @endforeach
                            <tr class="blue-grey lighten-4">
                                <td colspan="4" class="font-weight-bold">
                                    Disbursements for the Month of {{ $groupedVoucher->month_label }}
                                </td>

                                @foreach ($groupedVoucher->month_totals as $mTotal)
                                <td align="center" class="font-weight-bold">
                                    {{ number_format($mTotal, 2) }}
                                </td>
                                @endforeach

                                <td align="center" class="font-weight-bold">
                                    {{ number_format($groupedVoucher->month_total, 2) }}
                                </td>
                            </tr>
                            <tr class="blue-grey lighten-4">
                                <td colspan="4" class="font-weight-bold">
                                    Total Disbursement to date
                                </td>

                                @foreach ($groupedVoucher->totals as $total)
                                <td align="center" class="font-weight-bold">
                                    {{ number_format($total, 2) }}
                                </td>
                                @endforeach

                                <td align="center" class="font-weight-bold">
                                    {{ number_format($groupedVoucher->total, 2) }}
                                </td>
                            </tr>
                            <tr class="blue-grey lighten-4">
                                <td colspan="4" class="font-weight-bold red-text">
                                    Available Allotment
                                </td>

                                @foreach ($groupedVoucher->remainings as $rTotal)
                                <td align="center" class="font-weight-bold">
                                    {{ number_format($rTotal, 2) }}
                                </td>
                                @endforeach

                                <td align="center" class="font-weight-bold red-text">
                                    {{ number_format($groupedVoucher->remaining, 2) }}
                                </td>
                            </tr>
                                    @else
                            <tr>
                                <td class="font-weight-bold red-text py-2" colspan="{{ $allotmentCounter + 5 }}">
                                    <em>
                                        No disbursement for the month of {{ $groupedVoucher->month_label }}
                                    </em>
                                </td>
                            </tr>
                                    @endif
                                @endforeach
                            @else
                            <tr>
                                <td id="item-row-empty" class="py-3 red-text pl-4" colspan="{{ $allotmentCounter + 5 }}">
                                    <h5>
                                        <i class="fas fa-times-circle"></i> <em>No voucher is disbursed nor created.</em>
                                    </h5>
                                </td>
                                <tr id="item-row-0" class="item-row">
                                    <td colspan="{{ $allotmentCounter + 5 }}"></td>
                                </tr>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" name="allotment_count" id="allotment-count" value="{{ $allotmentCounter }}">
    <input type="hidden" name="is_realignment" id="is-realignment" value="{{ $isRealignment ? 'y' : 'n' }}">
    <input type="hidden" id="for" value="disbursement">
    <input type="hidden" id="type" value="saa">
</form>
