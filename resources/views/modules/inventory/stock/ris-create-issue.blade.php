<form id="form-store" class="wow animated fadeIn d-flex justify-content-center" method="POST"
      action="{{ route('stocks-store-issue-item', [
          'invStockID' => $invStockID,
          'classification' => $classification,
      ]) }}">
    @csrf

    <div class="table-responsive">
        <table class="table">
            <tr>
                <td>
                    <div class="md-form form-sm">
                        <input type="text" id="divsion" class="form-control required"
                               value="{{ $division }}" readonly>
                        <label for="divsion" class="active">
                            <strong>Division</strong>
                        </label>
                    </div>
                    <div class="md-form form-sm">
                        <input type="text" id="office" class="form-control required"
                               value="{{ $office }}" readonly>
                        <label for="office" class="active">
                            <strong>Office</strong>
                        </label>
                    </div>
                </td>
                <td>
                    <div class="md-form form-sm">
                        <input type="text" id="inventory-no" class="form-control required"
                               name="inventory_no" value="{{ $inventoryNo }}" readonly>
                        <label for="inventory-no" class="active">
                            <strong>RIS No</strong>
                        </label>
                    </div>
                </td>
            </tr>

            <tr>
                <td colspan="2">
                    <div class="col-md-12 px-0 table-responsive border">
                        <table class="table table-hover">
                            <tr id="pr-item-header">
                                <th class="text-center" colspan="4" width="55%">
                                    Requisition
                                </th>
                                <th class="text-center" colspan="2" width="15%">
                                    Stock Available <span class="red-text">* </span>
                                </th>
                                <th class="text-center" colspan="2" width="28%">
                                    Issue
                                </th>
                                <th class="text-center" colspan="1"></th>
                            </tr>
                            <tr id="pr-item-header-2">
                                <td class="text-center" width="10%">
                                    Stock No. <span class="red-text">* </span>
                                </td>
                                <td class="text-center" width="5%">
                                    Unit
                                </td>
                                <td class="text-center" width="35%">
                                    Description
                                </td>
                                <td class="text-center" width="5%">
                                    Quantity
                                </td>
                                <td class="text-center" width="7.5%">
                                    Yes
                                </td>
                                <td class="text-center" width="7.5%">
                                    No
                                </td>
                                <td class="text-center" width="10%">
                                    Quantity <span class="red-text">* </span>
                                </td>
                                <td class="text-center" width="18%">
                                    Remarks <span class="red-text">* </span>
                                </td>
                                <td class="text-center" width="2%"></td>
                            </tr>

                            <tbody id="row-items">
                            @if (count($stocks) > 0)
                                @foreach ($stocks as $ctr => $stock)
                                    @if ($stock->available_quantity > 0)
                                <tr id="row-{{ $ctr + 1 }}">
                                    <td>
                                        <div class="md-form form-sm my-0">
                                            <textarea class="md-textarea form-control required" name="prop_stock_no[]"
                                                    placeholder="Value..." rows="1"></textarea>
                                        </div>
                                        <input type="hidden" name="inv_stock_item_id[]" value="{{ $stock->id }}">
                                    </td>
                                    <td align="center">
                                        <div class="md-form form-sm my-0">
                                            <input class="form-control form-control-sm" type="text"
                                                readonly="readonly" value="{{ $stock->unit }}">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="md-form form-sm my-0">
                                            <textarea class="md-textarea form-control required"
                                                    placeholder="Item description..."
                                                    rows="1" readonly>{{ $stock->description }}</textarea>
                                        </div>
                                    </td>
                                    <td align="center">
                                        <div class="md-form form-sm my-0">
                                            <input class="form-control form-control-sm" type="number"
                                                value="{{ $stock->available_quantity }}" readonly>
                                        </div>
                                    </td>
                                    <td align="center">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input" id="avail-y-{{ $ctr }}"
                                                name="stock_available[{{ $ctr }}]" value="y" checked>
                                            <label class="custom-control-label" for="avail-y-{{ $ctr }}"></label>
                                        </div>
                                    </td>
                                    <td align="center">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input" id="avail-n-{{ $ctr }}"
                                                name="stock_available[{{ $ctr }}]" value="n">
                                            <label class="custom-control-label" for="avail-n-{{ $ctr }}"></label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="md-form form-sm my-0">
                                            <input class="form-control form-control-sm quantity required" type="number"
                                                name="quantity[]" min="0" max="{{ $stock->available_quantity }}"
                                                placeholder="avail: {{ $stock->available_quantity }}">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="md-form form-sm my-0">
                                            <textarea class="md-textarea form-control required" name="issued_remarks[]"
                                                    placeholder="Value..." rows="1"></textarea>
                                        </div>
                                    </td>
                                    <td>
                                        <a onclick="$(this).deleteRow('#row-{{ $ctr + 1 }}');"
                                        class="btn btn-outline-red px-1 py-0">
                                            <i class="fas fa-minus-circle"></i>
                                        </a>
                                    </td>
                                </tr>
                                    @else
                                <tr>
                                    <td colspan="9">
                                        <h6 class="text-center red-text">
                                            {{ $stock->description }} (Out of Stock)
                                        </h6>
                                    </td>
                                </tr>
                                    @endif
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                </td>
            </tr>

            <tr>
                <td width="50%">
                    <div class="md-form">
                        <select id="sig-approved-by" name="sig_approved_by" searchable="Search here.."
                                class="mdb-select crud-select md-form my-0 required">
                            <option value="" disabled selected>
                                Choose an approved by
                            </option>

                            @if (count($signatories) > 0)
                                @foreach ($signatories as $sig)
                                    @if ($sig->module->ris->approved_by)
                            <option value="{{ $sig->id }}">
                                {!! $sig->name !!} [{!! $sig->module->ris->designation !!}]
                            </option>
                                    @endif
                                @endforeach
                            @endif
                        </select>
                        <label class="mdb-main-label">
                            Approved By <span class="red-text">*</span>
                        </label>
                    </div>
                    <div class="md-form">
                        <select id="sig-received-by" name="sig_received_by" searchable="Search here.."
                                class="mdb-select crud-select md-form my-0 required">
                            <option value="" disabled selected>
                                Choose a received by
                            </option>

                            @if (count($employees) > 0)
                                @foreach ($employees as $emp)
                            <option value="{{ $emp->id }}">
                                {{ $emp->firstname }} {{ $emp->lastname }}
                            </option>
                                @endforeach
                            @endif
                        </select>
                        <label class="mdb-main-label">
                            Received By <span class="red-text">*</span>
                        </label>
                    </div>
                </td>
                <td width="50%">
                    <div class="md-form">
                        <select id="sig-requested-by" name="sig_requested_by" searchable="Search here.."
                                class="mdb-select crud-select md-form my-0 required">
                            <option value="" disabled selected>
                                Choose a requested by
                            </option>

                            @if (count($employees) > 0)
                                @foreach ($employees as $emp)
                            <option value="{{ $emp->id }}" {{ $emp->id == $requestedBy ? 'selected' : '' }}>
                                {{ $emp->firstname }} {{ $emp->lastname }}
                            </option>
                                @endforeach
                            @endif
                        </select>
                        <label class="mdb-main-label">
                            Requested By <span class="red-text">*</span>
                        </label>
                    </div>
                    <div class="md-form">
                        <select id="sig-issued-by" name="sig_issued_by" searchable="Search here.."
                                class="mdb-select crud-select md-form my-0 required">
                            <option value="" disabled selected>
                                Choose an issued by
                            </option>

                            @if (count($signatories) > 0)
                                @foreach ($signatories as $sig)
                                    @if ($sig->module->ris->issued_by)
                            <option value="{{ $sig->id }}">
                                {!! $sig->name !!} [{!! $sig->module->ris->designation !!}]
                            </option>
                                    @endif
                                @endforeach
                            @endif
                        </select>
                        <label class="mdb-main-label">
                            Issued By <span class="red-text">*</span>
                        </label>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</form>
