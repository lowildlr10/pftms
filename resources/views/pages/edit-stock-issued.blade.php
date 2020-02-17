<form id="form-update" method="POST" action="{{ url('inventory/stocks/update/' . $inventoryNo) }}"
      class="z-depth-1-half">
    @csrf

    <input type="hidden" name="classification" value="{{ $classification }}">
    <input type="hidden" name="type" value="{{ $type }}">
    <input type="hidden" name="received_by" value="{{ $receivedBy }}">

    <div class="table-responsive">
        <table class="table">

            @if ($classification == 'par')

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
                            <th width="47%">Description</th>
                            <th width="15%">Property Number</th>
                            <th width="14%">Date Acquired</th>
                            <th width="9%">Amount</th>
                        </tr>

                        @if (!empty($stocks))
                            @foreach ($stocks as $stockKey => $stock)
                        <tr>
                            <td>
                                <input class="form-control quantity required" type="number" name="quantity[]"
                                       min="0" max="{{ $stock->current_quantity }}" required="required"
                                       value="{{ $stock->quantity }}">
                                <input type="hidden" name="inventory_id[]" value="{{ $stock->inventory_id }}">
                            </td>
                            <td align="center">
                                <strong>{{ $stock->unit }}</strong>
                            </td>
                            <td>
                                <p class="mb-0">
                                    <i class="fas fa-caret-right"></i> {{ $stock->item_description }}
                                    <br><br>
                                    <strong><i class="fas fa-caret-right"></i> S/N:</strong>
                                    <div class="form-group">
                                        <input class="form-control" type="text" name="serial_no[]"
                                               id="serial-no-{{ $stockKey + 1 }}"
                                               value="{{ $stock->serial_no }}"
                                               placeholder="{{' Serial number (Separate with "/" if quantity is greater than 1)' }}">
                                    </div>
                                    <!--
                                    <div>
                                        <a class="btn btn-info btn-block" onclick="$(this).saveLabel('{{ $stock->inventory_id }}',
                                                                                                     '{{ $receivedBy }}',
                                                                                                     $('#serial-no-{{ $stockKey + 1 }}'))">
                                            <i class="fas fa-barcode"></i> Generate Label
                                        </a>
                                    </div>
                                    -->
                                </p>
                            </td>
                            <td>
                                <input class="form-control" type="text" name="property_no[]"
                                       value="{{ $stock->property_no }}">
                            </td>
                            <td>
                                <input class="form-control required" type="date" name="date_issued[]"
                                       value="{{ $stock->date_issued }}">
                            </td>
                            <td>
                                <input class="form-control required" type="number"
                                       readonly="readonly" value="{{ $stock->total_cost }}">
                            </td>
                        </tr>
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
                        <select class="browser-default custom-select required" disabled="disabled">
                            <option value=""> -- Select received By -- </option>

                            @if (!empty($employees))

                                @foreach ($employees as $employee)

                            <option value="{{ $employee->emp_id }}">
                                {{ $employee->name }} [ {{ $employee->position }} ]
                            </option>

                                @endforeach

                            @endif

                            @if (!empty($employees))

                                @foreach ($employees as $employee)

                                    @if ($employee->emp_id == $receivedBy)

                            <option value="{{ $employee->emp_id }}" selected="selected">
                                {{ $employee->name }} [ {{ $employee->position }} ]
                            </option>

                                    @else

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
                            <option value=""> -- Select received From -- </option>

                            @if (!empty($signatories))

                                @foreach ($signatories as $signatory)

                                    @if ($signatory->par_sign_type == 'issuer')

                                        @if ($signatory->id == $issuedBy)

                            <option value="{{ $signatory->id }}" selected="selected">
                                {{ $signatory->name }} [ {{ $signatory->position }} ]
                            </option>

                                        @else

                            <option value="{{ $signatory->id }}">
                                {{ $signatory->name }} [ {{ $signatory->position }} ]
                            </option>

                                        @endif

                                    @endif

                                @endforeach

                            @endif

                        </select>
                    </div>
                </td>
            </tr>

            @elseif($classification == 'ris')

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
                            <th colspan="4" width="55%">Requisistion</th>
                            <th colspan="2" width="15%">Stock Available</th>
                            <th colspan="2" width="28%">Issue</th>
                        </tr>
                        <tr id="pr-item-header-2">
                            <td width="10%">
                                <strong> Stock No. </strong>
                            </td>
                            <td width="5%"><strong> Unit </strong></td>
                            <td width="37%">
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
                        </tr>

                        @if (!empty($stocks))

                            @foreach ($stocks as $cntStock => $stock)

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

                                @if ($stock->stock_available == 'y')

                                <input type="radio" name="stock_available_{{ $cntStock }}" value="y" checked="checked">

                                @else

                                <input type="radio" name="stock_available_{{ $cntStock }}" value="y">

                                @endif

                            </td>
                            <td align="center">

                                 @if ($stock->stock_available == 'n')

                                <input type="radio" name="stock_available_{{ $cntStock }}" value="n" checked="checked">

                                @else

                                <input type="radio" name="stock_available_{{ $cntStock }}" value="n">

                                @endif

                            </td>
                            <td>
                                <input class="form-control quantity required" type="number" name="quantity[]"
                                       min="0" max="{{ $stock->current_quantity }}" required="required"
                                       value="{{ $stock->quantity }}">
                            </td>
                            <td>
                                <textarea class="form-control" name="issued_remarks[]" style="resize: none;">{{ $stock->issued_remarks }}</textarea>
                            </td>
                        </tr>

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

                                        @if ($signatory->id == $approvedBy)

                            <option value="{{ $signatory->id }}" selected="selected">
                                {{ $signatory->name }} [ {{ $signatory->position }} ]
                            </option>

                                        @else

                            <option value="{{ $signatory->id }}">
                                {{ $signatory->name }} [ {{ $signatory->position }} ]
                            </option>

                                        @endif

                                    @endif

                                @endforeach

                            @endif

                        </select>
                    </div>
                    <div class="form-group">
                        <label>Received By:</label>
                        <select class="browser-default custom-select required" disabled="disabled">
                            <option value=""> -- Select received by -- </option>

                            @if (!empty($employees))

                                @foreach ($employees as $employee)

                                    @if ($employee->emp_id == $receivedBy)

                            <option value="{{ $employee->emp_id }}" selected="selected">
                                {{ $employee->name }} [ {{ $employee->position }} ]
                            </option>

                                    @else

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

                                        @if ($signatory->id == $issuedBy)

                            <option value="{{ $signatory->id }}" selected="selected">
                                {{ $signatory->name }} [ {{ $signatory->position }} ]
                            </option>

                                        @else

                            <option value="{{ $signatory->id }}">
                                {{ $signatory->name }} [ {{ $signatory->position }} ]
                            </option>

                                        @endif

                                    @endif

                                @endforeach

                            @endif

                        </select>
                    </div>
                </td>
            </tr>

            @elseif ($classification == 'ics')

            <tr>
                <td colspan="2">
                    <div class="form-group">
                        <label>ICS No:</label>
                        <input type="text" class="form-control required" name="inventory_no" value="{{ $inventoryNo }}">
                    </div>
                </td>
            </tr>

            <tr>
                <td colspan="2">
                    <table id="item-pr-table" class="table table-bordered z-depth-1">
                        <tr id="pr-item-header">
                            <th width="6%">Quantity</th>
                            <th width="7%">Unit</th>
                            <th width="9%">Unit Cost</th>
                            <th width="9%">Total Cost</th>
                            <th width="30%">Description</th>
                            <th width="12%">Date Acquired</th>
                            <th width="13%">Inventory Item Number</th>
                            <th width="14%">Estimated Useful Life</th>
                        </tr>

                        @if (!empty($stocks))

                            @foreach ($stocks as $stockKey => $stock)

                        <tr>
                            <td>
                                <input class="form-control quantity required" type="number" name="quantity[]"
                                       min="0" max="{{ $stock->current_quantity }}" required="required"
                                       value="{{ $stock->quantity }}">
                                <input type="hidden" name="inventory_id[]" value="{{ $stock->inventory_id }}">
                            </td>
                            <td align="center">
                                <strong>{{ $stock->unit }}</strong>
                            </td>
                            <td>
                                <input class="form-control required" type="number"
                                       readonly="readonly" value="{{ $stock->unit_cost }}">
                            </td>
                            <td>
                                <input class="form-control required" type="number"
                                       readonly="readonly" value="{{ $stock->total_cost }}">
                            </td>
                            <td>
                                <p>
                                    <i class="fas fa-caret-right"></i> {{ $stock->item_description }}
                                    <br><br>
                                    <strong><i class="fas fa-caret-right"></i> S/N:</strong>
                                    <div class="form-group">
                                        <input class="form-control" type="text" name="serial_no[]"
                                               id="serial-no-{{ $stockKey + 1 }}"
                                               value="{{ $stock->serial_no }}"
                                               placeholder="{{' Serial number (Separate with "/" if quantity is greater than 1)' }}">
                                    </div>
                                    <!--
                                    <div>
                                        <a class="btn btn-info btn-block" onclick="$(this).saveLabel('{{ $stock->inventory_id }}',
                                                                                                     '{{ $receivedBy }}',
                                                                                                     $('#serial-no-{{ $stockKey + 1 }}'))">
                                            <i class="fas fa-barcode"></i> Generate Label
                                        </a>
                                    </div>
                                    -->
                                </p>
                            </td>
                            <td>
                                <input class="form-control required" type="date" name="date_issued[]"
                                       value="{{ $stock->date_issued }}">

                            </td>
                            <td>
                                <input class="form-control" type="text" name="property_no[]"
                                       value="{{ $stock->property_no }}">
                            </td>
                            <td>
                                <input class="form-control" type="text" name="est_useful_life[]"
                                       value="{{ $stock->est_useful_life }}">
                            </td>
                        </tr>

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
                        <label>Received From:</label>
                        <select name="issued_by" class="browser-default custom-select required">
                            <option value=""> -- Select received From -- </option>

                            @if (!empty($signatories))

                                @foreach ($signatories as $signatory)

                                    @if ($signatory->ics_sign_type == 'issuer')

                                        @if ($signatory->id == $issuedBy)

                            <option value="{{ $signatory->id }}" selected="selected">
                                {{ $signatory->name }} [ {{ $signatory->position }} ]
                            </option>

                                        @else

                            <option value="{{ $signatory->id }}">
                                {{ $signatory->name }} [ {{ $signatory->position }} ]
                            </option>

                                        @endif

                                    @endif

                                @endforeach

                            @endif

                        </select>
                    </div>
                </td>
                <td width="50%">
                    <div class="form-group">
                        <label>Received By:</label>
                        <select class="browser-default custom-select required" disabled="disabled">
                            <option value=""> -- Select received By -- </option>

                            @if (!empty($employees))

                                @foreach ($employees as $employee)

                                    @if ($employee->emp_id == $receivedBy)

                            <option value="{{ $employee->emp_id }}" selected="selected">
                                {{ $employee->name }} [ {{ $employee->position }} ]
                            </option>

                                    @else

                            <option value="{{ $employee->emp_id }}">
                                {{ $employee->name }} [ {{ $employee->position }} ]
                            </option>

                                    @endif

                                @endforeach

                            @endif

                        </select>
                    </div>
                </td>
            </tr>

            @endif

        </table>
    </div>
</form>
