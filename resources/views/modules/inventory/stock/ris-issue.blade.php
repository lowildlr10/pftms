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
                <td>
                    <div class="form-group">
                        <label>Division:</label>
                        <input type="text" class="form-control" value="{{ $division }}"
                               readonly="readonly">
                    </div>
                    <div class="form-group">
                        <label>Office:</label>
                        <input type="text" class="form-control" value="{{ $office }}"
                               readonly="readonly">
                    </div>
                </td>
                <td>
                    <div class="form-group">
                        <label>RIS No:</label>
                        <input type="text" class="form-control required" name="inventory_no" value="{{ $inventoryNo }}">
                    </div>
                </td>
            </tr>

            <tr>
                <td colspan="2">
                    <table id="item-pr-table" class="table table-bordered z-depth-1">
                        <tr id="pr-item-header">
                            <th colspan="4" width="55%">Requisition</th>
                            <th colspan="2" width="15%">Stock Available</th>
                            <th colspan="2" width="28%">Issue</th>
                            <th colspan="1"></th>
                        </tr>
                        <tr id="pr-item-header-2">
                            <td width="10%">
                                <strong> Stock No. </strong>
                            </td>
                            <td width="5%"><strong> Unit </strong></td>
                            <td width="35%">
                                <strong> Description </strong>
                            </td>
                            <td width="5%">
                                <strong> Quantity </strong>
                            </td>
                            <td width="7.5%">
                                <strong> Yes </strong>
                            </td>
                            <td width="7.5%">
                                <strong> No </strong></td>
                            <td width="10%">
                                <strong> Quantity </strong>
                            </td>
                            <td width="18%">
                                <strong> Remarks </strong>
                            </td>
                            <td width="2%"></td>
                        </tr>

                        @if (!empty($stocks))

                            @foreach ($stocks as $cntStock => $stock)

                                @if ($stock->current_quantity > 0)

                        <tr>
                            <td>
                                <input class="form-control" type="text" name="property_no[]"
                                       value="{{ $stock->stock_no }}">
                                <input type="hidden" name="inventory_id[]" value="{{ $stock->inventory_id }}">
                            </td>
                            <td align="center">
                                <strong>{{ $stock->unit }}</strong>
                            </td>
                            <td>
                                <i class="fas fa-caret-right"></i> {{ $stock->item_description }}
                            </td>
                            <td align="center">
                                <strong>{{ $stock->current_quantity }}</strong>
                            </td>
                            <td align="center">
                                <input type="radio" name="stock_available_{{ $cntStock }}" value="y" checked="checked">
                            </td>
                            <td align="center">
                                <input type="radio" name="stock_available_{{ $cntStock }}" value="n">
                            </td>
                            <td>
                                <input class="form-control quantity required" type="number" name="quantity[]"
                                       min="0" max="{{ $stock->current_quantity }}" required="required">
                            </td>
                            <td>

                                    @if (isset( $stock->issued_remarks))

                                <textarea class="form-control" name="issued_remarks[]" style="resize: none;">{{ $stock->issued_remarks }}</textarea>

                                    @else

                                <textarea class="form-control" name="issued_remarks[]" style="resize: none;"></textarea>

                                    @endif

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
                                <input class="form-control" type="text" value="{{ $stock->stock_no }}" readonly="readonly">
                            </td>
                            <td align="center">
                                <strong>{{ $stock->unit }}</strong>
                            </td>
                            <td>
                                <i class="fas fa-caret-right"></i> {{ $stock->item_description }}
                            </td>
                            <td align="center">
                                <strong>{{ $stock->current_quantity }}</strong>
                            </td>
                            <td align="center">
                                <input type="radio" name="stock_available_{{ $cntStock }}" value="y" checked="checked"
                                       readonly="readonly">
                            </td>
                            <td align="center">
                                <input type="radio" name="stock_available_{{ $cntStock }}" value="n" readonly="readonly">
                            </td>
                            <td>
                                <center><strong class="text-danger">Out of Stock</strong></center>
                            </td>
                            <td>
                                <textarea class="form-control" style="resize: none;" readonly="readonly"></textarea>
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
                <td width="50%">
                    <div class="form-group">
                        <label>Approved By:</label>
                        <select name="approved_by" class="browser-default custom-select required">
                            <option value=""> -- Select Approved by -- </option>

                            @if (!empty($signatories))

                                @foreach ($signatories as $signatory)

                                    @if ($signatory->ris_sign_type == 'approval')

                            <option value="{{ $signatory->id }}">
                                {{ $signatory->name }} [ {{ $signatory->position }} ]
                            </option>

                                    @endif

                                @endforeach

                            @endif

                        </select>
                    </div>
                    <div class="form-group">
                        <label>Received By:</label>
                        <select name="received_by" class="browser-default custom-select required">
                            <option value=""> -- Select received by -- </option>

                            @if (!empty($employees))

                                @foreach ($employees as $employee)

                                    @if (!in_array($employee->emp_id, $issuers))

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
                            <option value=""> -- Select Issued by -- </option>

                            @if (!empty($signatories))

                                @foreach ($signatories as $signatory)

                                    @if ($signatory->ris_sign_type == 'issuer')

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
