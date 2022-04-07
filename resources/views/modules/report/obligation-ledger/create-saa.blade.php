<form id="form-store" class="wow animated fadeIn" method="POST"
      action="{{ route('report-obligation-ledger-store', [
            'project_id' => $projectID,
            'for' => 'obligation',
            'type' => 'saa',
        ]) }}">
    @csrf
    <div class="card">
        <div class="card-body">
            <h4>Obligation Ledger</h4>
            <h6>{{ $projectTitle }}</h6>
            <hr>
            <div class="row">
                <div class="col-md-12  px-0 table-responsive">
                    <table class="table table-sm table-hover table-bordered" style="width: max-content;">
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

                                <th class="align-middle" width="5px"></th>
                                <th width="1px"></th>
                            </tr>
                        </thead>

                        <thead class="text-center">
                            <tr>
                                <th class="align-top" width="150px">
                                    <small class="font-weight-bold">
                                        <span class="red-text">* </span> Date
                                    </small>
                                </th>
                                <th class="align-top" width="200px">
                                    <small class="font-weight-bold">
                                        <span class="red-text">* </span> Payee
                                    </small>
                                </th>
                                <th class="align-top" width="300px">
                                    <small class="font-weight-bold">
                                        <span class="red-text">* </span> Particulars
                                    </small>
                                </th>
                                <th class="align-top" width="200px">
                                    <small class="font-weight-bold">
                                        <span class="red-text">* </span> ObR No
                                    </small>
                                </th>
                                <th class="align-top" width="180px">
                                    <small class="font-weight-bold">
                                        <span class="red-text">* </span> Total
                                    </small>
                                </th>

                                @foreach ($allotments as $grpClassItems)
                                    @foreach ($grpClassItems as $ctr => $item)
                                        @if (is_int($ctr))
                                <th class="align-top" width="180px">
                                    <small class="font-weight-bold" id="allot-name-{{ $allotmentCounter + 1 }}">
                                        <span class="red-text">* </span> {{ $item->allotment_name }}
                                    </small>
                                </th>
                                            @php $allotmentCounter++; @endphp
                                        @else
                                            @foreach ($item as $itm)
                                <th class="align-top" width="180px">
                                    <small class="font-weight-bold" id="allot-name-{{ $allotmentCounter + 1 }}">
                                        <span class="red-text">* </span> {{ explode('::', $itm->allotment_name)[1] }}
                                    </small>
                                </th>
                                                @php $allotmentCounter++; @endphp
                                            @endforeach
                                        @endif
                                    @endforeach
                                @endforeach

                                <th class="align-top" width="5px"></th>
                                <th width="1px"></th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($approvedBudgets as $approvedCtr => $approvedBud)
                                @php $allotmentCounter = 0; @endphp
                            <tr>
                                <td align="right" colspan="4" class="red-text font-weight-bold">
                                    {{ $approvedBud->label }}
                                </td>
                                <td align="center" class="red-text font-weight-bold">
                                    @if ($approvedCtr == count($approvedBudgets) - 1)
                                    <input type="hidden" id="current-total-budget" value="{{ $approvedBud->total }}">
                                    @endif

                                    {{ number_format($approvedBud->total, 2) }}
                                </td>

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
                                               value="{{ $itm->id }}">
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

                                <td colspan="2"></td>
                            </tr>
                            @endforeach

                            <tr><td class="py-3 grey" colspan="{{ $allotmentCounter + 7 }}"></td></tr>
                        </tbody>

                        <tbody id="item-row-container" class="sortable">
                            @if (count($vouchers) > 0)
                                @foreach ($vouchers as $itemCounter => $ors)
                                    @php $allotmentCounter = 0; @endphp
                            <tr id="item-row-{{ $itemCounter }}" class="item-row">
                                <td>
                                    <div class="md-form form-sm my-0">
                                        <input type="date" name="date_ors_burs[{{ $itemCounter }}]"
                                               class="form-control required form-control-sm date-ors-burs py-1"
                                               value="{{ $ors->date_obligated }}">
                                    </div>
                                </td>
                                <td>
                                    <select class="mdb-select form-control-sm required payee-tokenizer"
                                            name="payee[{{ $itemCounter }}]">
                                        @foreach ($payees as $pay)
                                            @if ($pay->id == $ors->payee)
                                        <option value="{{ $pay->id }}" selected>
                                            {{ $pay->name }}
                                        </option>
                                            @endif
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <div class="md-form form-sm my-0">
                                        <textarea name="particular[{{ $itemCounter }}]" placeholder=" Value..."
                                                  class="md-textarea required form-control-sm w-100 py-1"
                                                  placeholder="Value..."
                                        >{{ !empty(trim($ors->new_particulars)) ? $ors->new_particulars : $ors->particulars }}</textarea>
                                    </div>
                                </td>
                                <td>
                                    <div class="md-form form-sm my-0">
                                        <input type="hidden" name="ors_id[{{ $itemCounter }}]" value="{{ $ors->id }}">
                                        <input type="text" name="ors_no[{{ $itemCounter }}]"
                                               class="form-control required form-control-sm date-ors-burs py-1"
                                               value="{{ $ors->serial_no }}"
                                               placeholder="Value...">
                                    </div>
                                </td>
                                <td class="material-tooltip-main" data-toggle="tooltip" title="Particulars: {{ $ors->particulars }}">
                                    <div class="md-form form-sm my-0">
                                        <input type="number" name="amount[{{ $itemCounter }}]"
                                               class="form-control required form-control-sm date-ors-burs py-1 text-center
                                                      material-tooltip-main amount"
                                               data-toggle="tooltip" data-placement="left"
                                               title="Column: Total"
                                               value="{{ $ors->amount }}"
                                               onkeyup="$(this).computeTotalRemaining();"
                                               onchange="$(this).computeTotalRemaining();"
                                               placeholder="Value...">
                                    </div>
                                </td>
                                    @foreach ($allotments as $grpClassItems)
                                        @foreach ($grpClassItems as $allotCtr => $item)
                                            @if (is_int($allotCtr))
                                <td class="material-tooltip-main" data-toggle="tooltip" title="Particulars: {{ $ors->particulars }}">
                                    <div class="md-form form-sm my-0">
                                        @php
                                            $qry = [['ors_id', $ors->id], ['uacs_id', $item->uacs_id]];

                                            if ($isRealignment) {
                                                $qry = [['ors_id', $ors->id], ['uacs_id', $item->{$realignOrderKey}->uacs_id]];
                                            }

                                            $uacsDat = App\Models\OrsBursUacsItem::where($qry)->first();
                                        @endphp
                                        <input type="number" name="allotment[{{ $itemCounter }}][{{ $allotmentCounter }}]"
                                               class="form-control required form-control-sm date-ors-burs py-1 text-center
                                                      material-tooltip-main allotment-{{ $allotmentCounter + 1 }}"
                                               data-toggle="tooltip" data-placement="left"
                                               title="Column: "
                                               id="allot-remain-{{ $itemCounter }}-{{ $allotmentCounter + 1 }}"
                                               onkeyup="$(this).computeAllotmentRemaining();"
                                               onchange="$(this).computeAllotmentRemaining();"
                                               placeholder="Value..."
                                               value="{{ $uacsDat ? $uacsDat->amount : 0 }}">
                                    </div>
                                </td>
                                                @php $allotmentCounter++; @endphp
                                            @else
                                                @foreach ($item as $itm)
                                <td class="material-tooltip-main" data-toggle="tooltip" title="Particulars: {{ $ors->particulars }}">
                                    <div class="md-form form-sm my-0">
                                        @php
                                            $qry = [['ors_id', $ors->id], ['uacs_id', $itm->uacs_id]];

                                            if ($isRealignment) {
                                                $qry = [['ors_id', $ors->id], ['uacs_id', $itm->{$realignOrderKey}->uacs_id]];
                                            }

                                            $uacsDat = App\Models\OrsBursUacsItem::where($qry)->first();
                                        @endphp
                                        <input type="number" name="allotment[{{ $itemCounter }}][{{ $allotmentCounter }}]"
                                               class="form-control required form-control-sm date-ors-burs py-1 text-center
                                                      material-tooltip-main allotment-{{ $allotmentCounter + 1 }}"
                                               data-toggle="tooltip" data-placement="left"
                                               title="Column: "
                                               id="allot-remain-{{ $itemCounter }}-{{ $allotmentCounter + 1 }}"
                                               onkeyup="$(this).computeAllotmentRemaining();"
                                               onchange="$(this).computeAllotmentRemaining();"
                                               placeholder="Value..."
                                               value="{{ $uacsDat ? $uacsDat->amount : 0 }}">
                                    </div>
                                </td>
                                                    @php $allotmentCounter++; @endphp
                                                @endforeach
                                            @endif
                                        @endforeach
                                    @endforeach

                                <td class="align-middle">
                                    {{--
                                    <a onclick="$(this).deleteRow('#item-row-{{ $itemCounter }}');"
                                        class="btn btn-outline-red px-1 py-0">
                                        <i class="fas fa-minus-circle"></i>
                                    </a>
                                    --}}
                                </td>
                                <td class="align-middle">
                                    <a href="#" class="grey-text">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </a>
                                </td>
                            </tr>

                                    @php $itemCounter++ @endphp
                                @endforeach
                            @else
                            <tr>
                                <td id="item-row-empty" class="py-3 red-text pl-4" colspan="{{ $allotmentCounter + 7 }}">
                                    <h5>
                                        <i class="fas fa-times-circle"></i> <em>No voucher is obligated nor created.</em>
                                    </h5>
                                </td>
                                <tr id="item-row-0" class="item-row">
                                    <td colspan="{{ $allotmentCounter + 7 }}"></td>
                                </tr>
                            </tr>
                            @endif
                        </tbody>

                        @if (count($vouchers) > 0)
                        <tfoot>
                            <tr>
                                <td colspan="4" class="font-weight-bold red-text text-center">
                                    Available Allotment
                                </td>
                                <td class="font-weight-bold red-text">
                                    <input type="number" id="total-remaining" class="text-center"
                                           value="0.00" readonly>
                                </td>

                                @for ($allotCtr = 1; $allotCtr <= $allotmentCounter; $allotCtr++)
                                <td class="font-weight-bold red-text">
                                    <input type="number" id="remaining-{{ $allotCtr }}" class="text-center"
                                           value="0.00" readonly>
                                </td>
                                @endfor

                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" name="allotment_count" id="allotment-count" value="{{ $allotmentCounter }}">
    <input type="hidden" name="is_realignment" id="is-realignment" value="{{ $isRealignment ? 'y' : 'n' }}">
    <input type="hidden" id="for" value="obligation">
    <input type="hidden" id="type" value="saa">
</form>
