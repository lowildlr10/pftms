<form id="form-store" class="wow animated fadeIn d-flex justify-content-center" method="POST"
      action="{{ route('stocks-store-issue-item', [
          'invStockID' => $invStockID,
          'invStockItemID' => $invStockItemID,
          'classification' => $classification,
          'type' => $type
      ]) }}">
    @csrf

    <div class="table-responsive">
        <table class="table">
            <tr>
                <td colspan="2">
                    <div class="form-group">
                        <label>PAR No:</label>
                        <input type="text" class="form-control required" name="inventory_no" value="{{ $inventoryNo }}">
                    </div>
                </td>
            </tr>

            <tr>
                <td colspan="2">
                    <table id="item-pr-table" class="table table-bordered z-depth-1">
                        <tr id="pr-item-header">
                            <th width="8%">Quantity</th>
                            <th width="7%">Unit</th>
                            <th width="45%">Description</th>
                            <th width="15%">Property Number</th>
                            <th width="14%">Date Acquired</th>
                            <th width="9%">Amount</th>
                            <th width="2%"></th>
                        </tr>

                        @if (count($stocks) > 0)
                            @foreach ($stocks as $stock)
                                @if ($stock->available_quantity > 0)
                        <tr>
                            <td>
                                <input class="form-control quantity required" type="number" name="quantity[]"
                                       min="0" max="{{ $stock->available_quantity }}" required="required">
                                <input type="hidden" name="inventory_id[]" value="{{ $stock->inventory_id }}">
                            </td>
                            <td align="center">
                                <strong>{{ $stock->unit }}</strong>
                            </td>
                            <td>
                                <p>
                                    <i class="fas fa-caret-right"></i> {{ $stock->item_description }}
                                    <br><br>
                                    <strong><i class="fas fa-caret-right"></i> S/N:</strong>

                                    @if (isset($stock->serial_no))

                                    <input class="form-control" type="text" name="serial_no[]"
                                           value="{{ $stock->serial_no }}" placeholder="Serial Number...">

                                    @else

                                    <input class="form-control" type="text" name="serial_no[]"
                                           value="" placeholder="Serial Number...">

                                    @endif

                                </p>
                            </td>
                            <td>
                                <input class="form-control" type="text" name="property_no[]"
                                       value="{{ $stock->property_no }}">
                            </td>
                            <td>

                                    @if (isset($stock->serial_no))

                                <input class="form-control required" type="date" name="date_issued[]"
                                       value="{{ $stock->date_issued }}">

                                    @else

                                <input class="form-control required" type="date" name="date_issued[]"
                                       value="">

                                    @endif

                            </td>
                            <td>
                                <input class="form-control required" type="number"
                                       readonly="readonly" value="{{ $stock->total_cost }}">
                            </td>
                            <td>
                                <button type="button" class="btn btn-outline-red waves-effect btn-sm btn-block"
                                        onclick="">
                                    <i class="fas fa-minus-circle"></i> Remove
                                </button>
                            </td>
                        </tr>

                                @else

                        <tr>
                            <td>
                                <center><strong class="text-danger">Out of Stock</strong></center>
                            </td>
                            <td align="center">
                                <strong>{{ $stock->unit }}</strong>
                            </td>
                            <td>
                                <i class="fas fa-caret-right"></i> {{ $stock->item_description }}
                            </td>
                            <td>
                                <input class="form-control" type="text" readonly="readonly"
                                       value="{{ $stock->property_no }}">
                            </td>
                            <td>
                                <input class="form-control" type="date" readonly="readonly">
                            </td>
                            <td>
                                <input class="form-control" type="number"
                                       readonly="readonly" value="{{ $stock->total_cost }}">
                            </td>
                            <td></td>
                        </tr>

                                @endif

                            @endforeach

                        @endif

                    </table>
                </td>
            </tr>

            <tr>
                <td>
                    <div class="form-group">
                        <label>PO No:</label>
                        <input type="text" class="form-control" value="{{ $poNo }}" readonly="readonly">
                    </div>
                    <div class="form-group">
                        <label>Date:</label>
                        <input type="date" class="form-control" value="{{ $poDate }}" readonly="readonly">
                    </div>
                </td>
                <td>
                    <div class="form-group">
                        <label>Supplier:</label>
                        <input type="text" class="form-control" value="{{ $supplier }}" readonly="readonly">
                    </div>
                </td>
            </tr>

            <tr>
                <td width="50%">
                    <div class="form-group">
                        <label>Received By:</label>
                        <select name="received_by" class="browser-default custom-select required">
                            <option value=""> -- Select received By -- </option>

                            @if (!empty($employees))

                                @foreach ($employees as $employee)

                                    @if (!in_array($employee->emp_id , $issuers))

                            <option value="{{ $employee->emp_id }}">
                                {{ $employee->name }} [ {{ $employee->position }} ]
                            </option>

                                    @endif

                                @endforeach

                            @endif

                        </select>
                    </div>
                </td>
                <td width="50%">
                    <div class="form-group">
                        <label>Issued By:</label>
                        <select name="issued_by" class="browser-default custom-select required">
                            <option value=""> -- Select issued by -- </option>

                            @if (!empty($signatories))

                                @foreach ($signatories as $signatory)

                                    @if ($signatory->par_sign_type == 'issuer')

                            <option value="{{ $signatory->id }}">
                                {{ $signatory->name }} [ {{ $signatory->position }} ]
                            </option>

                                    @endif

                                @endforeach

                            @endif

                        </select>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</form>
