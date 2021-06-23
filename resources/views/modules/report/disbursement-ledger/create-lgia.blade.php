<form id="form-store" class="wow animated fadeIn" method="POST"
      action="{{ route('report-obligation-ledger-store', [
            'project_id' => $projectID,
            'for' => 'disbursement',
            'type' => 'lgia',
        ]) }}">
    @csrf
    <div class="card">
        <div class="card-body">
            <h4>Disbursement Ledger</h4>
            <hr>
            <div class="row">
                <div class="col-md-12  px-0 table-responsive">
                    <table class="table table-sm table-hover table-bordered" style="width: max-content;">
                        <thead class="text-center">
                            <tr>
                                <th class="align-top" width="150px">
                                    <small class="font-weight-bold">
                                        <span class="red-text">* </span> Date
                                    </small>
                                </th>
                                <th class="align-top" width="200px">
                                    <small class="font-weight-bold">
                                        <span class="red-text">* </span> ORS No
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
                                <th class="align-top" width="180px">
                                    <small class="font-weight-bold">
                                        <span class="red-text">* </span> Prior Year
                                    </small>
                                </th>
                                <th class="align-top" width="180px">
                                    <small class="font-weight-bold">
                                        <span class="red-text">* </span> Continuing
                                    </small>
                                </th>
                                <th class="align-top" width="180px">
                                    <small class="font-weight-bold">
                                        <span class="red-text">* </span> Current
                                    </small>
                                </th>
                                <th class="align-top" width="180px">
                                    <small class="font-weight-bold">
                                        <span class="red-text">* </span> Total
                                    </small>
                                </th>
                                <th class="align-top" width="5px"></th>
                                <th width="1px"></th>
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
                                <td></td>
                                <td></td>
                                <td></td>
                                <td align="center" class="red-text font-weight-bold">
                                    @if ($approvedCtr == count($approvedBudgets) - 1)
                                    <input type="hidden" id="current-total-budget" value="{{ $approvedBud->total }}">
                                    @endif

                                    {{ number_format($approvedBud->total, 2) }}
                                </td>
                                <td colspan="2"></td>
                            </tr>
                            @endforeach

                            <tr><td class="py-3 grey" colspan="10"></td></tr>
                        </tbody>

                        <tbody id="item-row-container" class="sortable">
                            @if (count($vouchers) > 0)
                                @foreach ($vouchers as $itemCounter => $dv)
                            <tr id="item-row-{{ $itemCounter }}" class="item-row">
                                <td>
                                    <div class="md-form form-sm my-0">
                                        <input type="date" name="date_ors_burs[{{ $itemCounter }}]"
                                               class="form-control required form-control-sm date-dv py-1"
                                               value="{{ $dv->date_dv }}">
                                    </div>
                                </td>
                                <td>
                                    <div class="md-form form-sm my-0">
                                        <input type="hidden" name="ors_id[{{ $itemCounter }}]" value="{{ $dv->ors_id }}">
                                        <input type="hidden" name="dv_id[{{ $itemCounter }}]" value="{{ $dv->id }}">
                                        <input type="text" name="ors_no[{{ $itemCounter }}]"
                                               class="form-control required form-control-sm serial-no py-1"
                                               value="{{ $dv->serial_no }}"
                                               placeholder="Value...">
                                    </div>
                                </td>
                                <td>
                                    <select class="mdb-select form-control-sm required payee-tokenizer"
                                            name="payee[{{ $itemCounter }}]">
                                        @foreach ($payees as $pay)
                                        <option {{ $pay->id == $dv->payee ? 'selected' : '' }}
                                                value="{{ $pay->id }}">
                                            {{ $pay->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <div class="md-form form-sm my-0">
                                        <textarea name="particulars[{{ $itemCounter }}]" placeholder=" Value..."
                                                  class="md-textarea required form-control-sm w-100 py-1 particulars"
                                                  placeholder="Value..."
                                        >{{ $dv->particulars }}</textarea>
                                    </div>
                                </td>
                                <td class="material-tooltip-main" data-toggle="tooltip" title="Particulars: {{ $dv->particulars }}">
                                    <div class="md-form form-sm my-0">
                                        <input type="number" name="prior_year[{{ $itemCounter }}]"
                                               class="form-control required form-control-sm date-ors-burs py-1 text-center
                                                      material-tooltip-main prior-year"
                                               data-toggle="tooltip" data-placement="left"
                                               title="Column: Prior Year"
                                               value="0"
                                               onkeyup="$(this).computeTotalPriorYear();"
                                               onchange="$(this).computeTotalPriorYear();"
                                               placeholder="Value...">
                                    </div>
                                </td>
                                <td class="material-tooltip-main" data-toggle="tooltip" title="Particulars: {{ $dv->particulars }}">
                                    <div class="md-form form-sm my-0">
                                        <input type="number" name="continuing[{{ $itemCounter }}]"
                                               class="form-control required form-control-sm date-ors-burs py-1 text-center
                                                      material-tooltip-main continuing"
                                               data-toggle="tooltip" data-placement="left"
                                               title="Column: Continuing"
                                               value="0"
                                               onkeyup="$(this).computeTotalContinuing();"
                                               onchange="$(this).computeTotalContinuing();"
                                               placeholder="Value...">
                                    </div>
                                </td>
                                <td class="material-tooltip-main" data-toggle="tooltip" title="Particulars: {{ $dv->particulars }}">
                                    <div class="md-form form-sm my-0">
                                        <input type="number" name="current[{{ $itemCounter }}]"
                                               class="form-control required form-control-sm date-ors-burs py-1 text-center
                                                      material-tooltip-main current"
                                               data-toggle="tooltip" data-placement="left"
                                               title="Column: Current"
                                               value="0"
                                               onkeyup="$(this).computeTotalCurrent();"
                                               onchange="$(this).computeTotalCurrent();"
                                               placeholder="Value...">
                                    </div>
                                </td>
                                <td class="material-tooltip-main" data-toggle="tooltip" title="Particulars: {{ $dv->particulars }}">
                                    <div class="md-form form-sm my-0">
                                        <input type="number" name="amount[{{ $itemCounter }}]"
                                               class="form-control required form-control-sm date-ors-burs py-1 text-center
                                                      material-tooltip-main amount"
                                               data-toggle="tooltip" data-placement="left"
                                               title="Column: Total"
                                               value="{{ $dv->amount }}"
                                               onkeyup="$(this).computeTotalRemaining();"
                                               onchange="$(this).computeTotalRemaining();"
                                               placeholder="Value...">
                                    </div>
                                </td>
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
                                @endforeach
                            @else
                            <tr>
                                <td id="item-row-empty" class="py-3 red-text pl-4" colspan="10">
                                    <h5>
                                        <i class="fas fa-times-circle"></i> <em>No voucher is disbursed nor created.</em>
                                    </h5>
                                </td>
                                <tr id="item-row-0" class="item-row">
                                    <td colspan="10"></td>
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
                                    <input type="number" id="prior-year" class="text-center"
                                           value="0.00" readonly>
                                </td>
                                <td class="font-weight-bold red-text">
                                    <input type="number" id="continuing" class="text-center"
                                           value="0.00" readonly>
                                </td>
                                <td class="font-weight-bold red-text">
                                    <input type="number" id="current" class="text-center"
                                           value="0.00" readonly>
                                </td>
                                <td class="font-weight-bold red-text">
                                    <input type="number" id="total-remaining" class="text-center"
                                           value="0.00" readonly>
                                </td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" name="is_realignment" id="is-realignment" value="{{ $isRealignment ? 'y' : 'n' }}">
    <input type="hidden" id="for" value="disbursement">
</form>
