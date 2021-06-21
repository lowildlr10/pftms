<form id="form-store" class="wow animated fadeIn" method="POST"
      action="{{ route('report-obligation-ledger-store', ['type' => 'obligation']) }}">
    @csrf
    <div class="card">
        <div class="card-body">
            <h4>Obligation Ledger</h4>
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
                                <th class="align-top" width="150px">
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
                                <th class="align-top" width="250px">
                                    <small class="font-weight-bold">
                                        <span class="red-text">* </span> {{ $item->allotment_name }}
                                    </small>
                                </th>
                                            @php $allotmentCounter++; @endphp
                                        @else
                                            @foreach ($item as $itm)
                                <th class="align-top" width="250px">
                                    <small class="font-weight-bold">
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
                            <tr>
                                <td align="right" colspan="4" class="red-text font-weight-bold">
                                    {{ $approvedBud->label }}
                                </td>
                                <td align="center" class="red-text font-weight-bold">
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
                                        <input type="number" placeholder=" Value..."
                                               class="form-control required form-control-sm py-1
                                                      red-text text-center font-weight-bold"
                                               value="{{ $item->allotment_cost }}" readonly>
                                    </div>
                                </td>
                                                @else
                                                    @foreach ($item as $itm)
                                <td align="center" class="red-text font-weight-bold">
                                    <div class="md-form form-sm my-0">
                                        <input type="number" placeholder=" Value..."
                                               class="form-control required form-control-sm py-1
                                                      red-text text-center font-weight-bold"
                                               value="{{ $itm->allotment_cost }}" readonly>
                                    </div>
                                </td>
                                                    @endforeach
                                                @endif
                                            @endif
                                        @else
                                            @php $realignOrderKey = "realignment_$approvedCtr"; @endphp

                                            @if ($approvedCtr == (count($approvedBudgets) - 1))
                                                @if (is_int($ctr))
                                <td align="center" class="red-text font-weight-bold">
                                    <div class="md-form form-sm my-0">
                                        <input type="number" placeholder=" Value..."
                                               class="form-control required form-control-sm py-1
                                                      red-text text-center font-weight-bold"
                                               value="{{ $item->{$realignOrderKey}->allotment_cost }}"
                                               readonly>
                                    </div>
                                </td>
                                                @else
                                                    @foreach ($item as $itm)
                                <td align="center" class="red-text font-weight-bold">
                                    <div class="md-form form-sm my-0">
                                        <input type="number" placeholder=" Value..."
                                               class="form-control required form-control-sm py-1
                                                      red-text text-center font-weight-bold"
                                               value="{{ $itm->{$realignOrderKey}->allotment_cost }}"
                                               readonly>
                                    </div>
                                </td>
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
                            @if (count($obligations) > 0)
                                @foreach ($obligations as $itemCounter => $ors)
                            <tr id="item-row-{{ $itemCounter }}" class="item-row">
                                <td>
                                    <div class="md-form form-sm my-0">
                                        <input type="date" name="allotted_budget[{{ $itemCounter }}]"
                                               class="form-control required form-control-sm date-ors-burs py-1"
                                               value="{{ $ors->date_ors_burs }}">
                                    </div>
                                </td>
                                <td>
                                    {{ $ors->payee }}
                                </td>
                                <td>
                                    <div class="md-form form-sm my-0">
                                        <textarea name="particulars[]" placeholder=" Value..."
                                                  class="md-textarea required form-control-sm w-100 py-1"
                                                  placeholder="Value..."
                                        >{{ $ors->particulars }}</textarea>
                                    </div>
                                </td>
                                <td>
                                    <div class="md-form form-sm my-0">
                                        <input type="text" name="obr_no[{{ $itemCounter }}]"
                                               class="form-control required form-control-sm date-ors-burs py-1"
                                               value="{{ $ors->serial_no }}"
                                               placeholder="Value...">
                                    </div>
                                </td>
                                <td>
                                    <div class="md-form form-sm my-0">
                                        <input type="text" name="amount[{{ $itemCounter }}]"
                                               class="form-control required form-control-sm date-ors-burs py-1"
                                               value="{{ $ors->amount }}"
                                               placeholder="Value...">
                                    </div>
                                </td>

                                    @foreach ($allotments as $grpClassItems)
                                        @foreach ($grpClassItems as $allotCtr => $item)
                                            @if (is_int($allotCtr))
                                <td>
                                    <div class="md-form form-sm my-0">
                                        <input type="text" name="allotment[{{ $itemCounter }}][{{ $allotCtr }}]"
                                               class="form-control required form-control-sm date-ors-burs py-1"
                                               placeholder="Value...">
                                    </div>
                                </td>
                                            @else
                                                @foreach ($item as $itm)
                                <td>
                                    <div class="md-form form-sm my-0">
                                        <input type="text" name="allotment[{{ $itemCounter }}][{{ $allotCtr }}]"
                                               class="form-control required form-control-sm date-ors-burs py-1"
                                               placeholder="Value...">
                                    </div>
                                </td>
                                                @endforeach
                                            @endif
                                        @endforeach
                                    @endforeach

                                <td class="align-middle">
                                    <a onclick="$(this).deleteRow('#item-row-{{ $itemCounter }}');"
                                        class="btn btn-outline-red px-1 py-0">
                                        <i class="fas fa-minus-circle"></i>
                                    </a>
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
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</form>
