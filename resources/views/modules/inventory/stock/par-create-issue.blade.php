<form id="form-store" class="wow animated fadeIn d-flex justify-content-center" method="POST"
      action="{{ route('stocks-store-issue-item', [
          'invStockID' => $invStockID,
          'classification' => $classification,
      ]) }}">
    @csrf

    <div class="table-responsive">
        <table class="table">
            <tr>
                <td colspan="2">
                    <div class="form-group">
                        <div class="md-form form-sm">
                            <input type="text" id="inventory-no" class="form-control required"
                                   name="inventory_no" value="{{ $inventoryNo }}" readonly>
                            <label for="inventory-no" class="active">
                                <strong>PAR No</strong>
                            </label>
                        </div>
                    </div>
                </td>
            </tr>

            <tr>
                <td colspan="2">
                    <div class="col-md-12 px-0 table-responsive border">
                        <table class="table table-hover">
                            <tr id="pr-item-header">
                                <th class="text-center" width="8%">
                                    Quantity <span class="red-text">* </span>
                                </th>
                                <th class="text-center" width="7%">
                                    Unit
                                </th>
                                <th class="text-center" width="45%">
                                    Description
                                </th>
                                <th class="text-center" width="15%">
                                    Property Number <span class="red-text">* </span>
                                </th>
                                <th class="text-center" width="14%">
                                    Date Acquired <span class="red-text">* </span>
                                </th>
                                <th class="text-center" width="9%">
                                    Amount
                                </th>
                                <th class="text-center" width="2%"></th>
                            </tr>

                            <tbody id="row-items">
                            @if (count($stocks) > 0)
                                @foreach ($stocks as $ctr => $stock)
                                    @if ($stock->available_quantity > 0)
                                <tr id="row-{{ $ctr + 1 }}">
                                    <td>
                                        <div class="md-form form-sm my-0">
                                            <input class="form-control quantity required form-control-sm required" type="number"
                                                name="quantity[]"
                                                min="0" max="{{ $stock->available_quantity }}" required="required"
                                                placeholder="avail: {{ $stock->available_quantity }}">
                                            <input type="hidden" name="inv_stock_item_id[]" value="{{ $stock->id }}">
                                        </div>
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
                                    <td>
                                        <div class="md-form form-sm my-0">
                                            <textarea class="md-textarea form-control required" name="prop_stock_no[]"
                                                    placeholder="Value..." rows="1"></textarea>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="md-form form-sm my-0">
                                            <input class="form-control form-control-sm required" type="date"
                                                name="date_issued[]">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="md-form form-sm my-0">
                                            <input class="form-control form-control-sm" type="number"
                                                readonly="readonly" value="{{ $stock->amount }}">
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
                <td>
                    <div class="md-form form-sm">
                        <input type="text" id="po-no" class="form-control" readonly
                               value="{{ $poNo }}">
                        <label for="po-no" class="active">
                            <strong>PO No</strong>
                        </label>
                    </div>
                    <div class="md-form form-sm">
                        <input type="date" id="date-po" class="form-control" readonly
                               value="{{ $poDate }}">
                        <label for="date-po" class="active">
                            <strong>Date</strong>
                        </label>
                    </div>
                    <div class="md-form form-sm">
                        <input type="text" id="supplier" class="form-control" readonly
                               value="{{ $supplier }}">
                        <label for="supplier" class="active">
                            <strong>Supplier</strong>
                        </label>
                    </div>
                </td>
            </tr>

            <tr>
                <td width="50%">
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
                        <select id="sig-issued-by" name="sig_issued_by" searchable="Search here.."
                                class="mdb-select crud-select md-form my-0 required">
                            <option value="" disabled selected>
                                Choose an issued by
                            </option>

                            @if (count($signatories) > 0)
                                @foreach ($signatories as $sig)
                                    @if ($sig->module->par->issued_by)
                            <option value="{{ $sig->id }}">
                                {!! $sig->name !!} [{!! $sig->module->par->designation !!}]
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
