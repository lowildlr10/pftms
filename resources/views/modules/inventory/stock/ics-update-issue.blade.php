<form id="form-update" class="wow animated fadeIn d-flex justify-content-center" method="POST"
      action="{{ route('stocks-update-issue-item', [
          'invStockID' => $invStockID,
          'classification' => $classification,
      ]) }}">
    @csrf

    <div class="table-responsive">
        <table class="table">
            <tr>
                <td colspan="2">
                    <div class="md-form form-sm">
                        <input type="text" id="inventory-no" class="form-control required"
                               name="inventory_no" value="{{ $inventoryNo }}" readonly>
                        <label for="inventory-no" class="active">
                            <strong>ICS No</strong>
                        </label>
                    </div>
                </td>
            </tr>

            <tr>
                <td colspan="2">
                    <div class="col-md-12 px-0 table-responsive border">
                        <table class="table table-hover">
                            <tr>
                                <th class="text-center" width="6%">
                                    Quantity <span class="red-text">* </span>
                                </th>
                                <th class="text-center" width="7%">
                                    Unit
                                </th>
                                <th class="text-center" width="9%">
                                    Unit Cost
                                </th>
                                <th class="text-center" width="9%">
                                    Total Cost
                                </th>
                                <th class="text-center" width="23%">
                                    Description
                                </th>
                                <th class="text-center" width="12%">
                                    Date Acquired <span class="red-text">* </span>
                                </th>
                                <th class="text-center" width="10%">
                                    Inventory Item Number <span class="red-text">* </span>
                                </th>
                                <th class="text-center" width="12%">
                                    Estimated Useful Life <span class="red-text">* </span>
                                </th>
                                <th class="text-center" width="12%">

                                </th>
                            </tr>

                            @if (count($stocks) > 0)
                                @foreach ($stocks as $ctr => $stock)
                            <tr id="row-{{ $ctr + 1 }}">
                                <td>
                                    <div class="md-form form-sm my-0">
                                        <input class="form-control required form-control-sm quantity required" type="number"
                                               name="quantity[]" placeholder="avail: {{ $stock->available_quantity }}"
                                               min="0" max="{{ $stock->available_quantity }}"
                                               value="{{ $stock->quantity }}">
                                        <input type="hidden" name="inv_stock_issue_item_id[]" value="{{ $stock->id }}">
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
                                        <input class="form-control form-control-sm" type="number"
                                               readonly="readonly" value="{{ $stock->amount / $stock->quantity }}">
                                    </div>
                                </td>
                                <td>
                                    <div class="md-form form-sm my-0">
                                        <input class="form-control form-control-sm" type="number"
                                               readonly="readonly" value="{{ $stock->amount }}">
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
                                        <input class="form-control form-control-sm required" type="date"
                                               name="date_issued[]" value="{{ $stock->date_issued }}">
                                    </div>
                                </td>
                                <td>
                                    <div class="md-form form-sm my-0">
                                        <textarea class="md-textarea form-control required" name="prop_stock_no[]"
                                                  placeholder="Value..." rows="1">{{ $stock->prop_stock_no }}</textarea>
                                    </div>
                                </td>
                                <td>
                                    <div class="md-form form-sm my-0">
                                        <input class="form-control form-control-sm required" type="text"
                                               name="est_useful_life[]" placeholder="Value..."
                                               value="{{ $stock->est_useful_life }}">
                                    </div>
                                </td>
                                <td>
                                    <div class="md-form">
                                        <select name="deleted[]" searchable="Search here.."
                                                class="mdb-select crud-select md-form my-0 required">
                                            <option value="" disabled selected>
                                                Choose a delete option
                                            </option>

                                            <option value="y">Yes</option>
                                            <option value="n" selected>No</option>
                                        </select>
                                        <label class="mdb-main-label">
                                            Delete? <span class="red-text">*</span>
                                        </label>
                                    </div>

                                    <div class="md-form">
                                        <select name="excluded[]" searchable="Search here.."
                                                class="mdb-select crud-select md-form my-0 required">
                                            <option value="" disabled selected>
                                                Choose an exclude option
                                            </option>

                                            <option value="y" {{ $stock->excluded == 'y' ? 'selected' : '' }}>
                                                Yes
                                            </option>
                                            <option value="n" {{ $stock->excluded == 'n' ? 'selected' : '' }}>
                                                No
                                            </option>
                                        </select>
                                        <label class="mdb-main-label">
                                            Exclude? <span class="red-text">*</span>
                                        </label>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        @endif

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
                        <select id="sig-received-from" name="sig_received_from" searchable="Search here.."
                                class="mdb-select crud-select md-form my-0 required">
                            <option value="" disabled selected>
                                Choose a received from
                            </option>

                            @if (count($signatories) > 0)
                                @foreach ($signatories as $sig)
                                    @if ($sig->module->ics->received_from)
                            <option value="{{ $sig->id }}" {{ $sig->id == $sigReceivedFrom ? 'selected' : '' }}>
                                {!! $sig->name !!} [{!! $sig->module->ics->designation !!}]
                            </option>
                                    @endif
                                @endforeach
                            @endif
                        </select>
                        <label class="mdb-main-label">
                            Received From <span class="red-text">*</span>
                        </label>
                    </div>
                </td>
                <td width="50%">
                    <div class="md-form">
                        <select id="sig-received-by" name="sig_received_by" searchable="Search here.."
                                class="mdb-select crud-select md-form my-0 required">
                            <option value="" disabled selected>
                                Choose a received by
                            </option>

                            @if (count($employees) > 0)
                                @foreach ($employees as $emp)
                            <option value="{{ $emp->id }}" {{ $emp->id == $sigReceivedBy ? 'selected' : '' }}>
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
            </tr>
        </table>
    </div>
</form>
