<form id="form-create" method="POST" action="{{ url('#') }}"
      class="z-depth-1-half wow animated fadeIn">
    @csrf

    <input type="hidden" name="classification" value="{{ $classification }}">

    <div class="table-responsive">
        <table class="table">

    @if ($classification == 'par')

            <tr>
                <td colspan="2">
                    <div class="form-group">
                        <label>PAR No:</label>
                        <input type="text" class="form-control required" name="inventory_no">
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

                        <tr>
                            <td>
                                <input class="form-control quantity required" type="number" name="quantity[]"
                                       required="required">
                            </td>
                            <td align="center">
                                
                            </td>
                            <td>
                                <p>
                                    <span>
                                        <textarea name="item_description[]" class="form-control required"></textarea>
                                    </span>
                                    <br>
                                    <strong><i class="fas fa-caret-right"></i> S/N:</strong>
                                    <div class="form-group">
                                        <input class="form-control" type="text" name="serial_no[]" 
                                               placeholder="Serial Number...">
                                    </div>
                                </p>
                            </td>
                            <td>
                                <input class="form-control" type="text" name="property_no[]">
                            </td>
                            <td>
                                <input class="form-control required" type="date" name="date_issued[]">
                            </td>
                            <td>
                                <input class="form-control required" type="number">
                            </td>
                        </tr>

                        <tr>
                            <td colspan="6">
                                <a id="add-block-btn" class="btn btn-outline-indigo btn-block waves-effect" 
                                   onclick="$(this).addRow('#item-pr-table')">
                                    <i class="fas fa-plus"></i>
                                    <strong>Add Item</strong>
                                </a>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr>
                <td>
                    <div class="form-group">
                        <label>PO No:</label>
                        <input type="text" class="form-control" readonly="readonly">
                    </div>
                    <div class="form-group">
                        <label>Date:</label>
                        <input type="date" class="form-control" readonly="readonly">
                    </div>
                </td>
                <td>
                    <div class="form-group">
                        <label>Supplier:</label>
                        <input type="text" class="form-control" readonly="readonly">
                    </div>
                </td>
            </tr>

            <tr>
                <td width="50%">
                    <div class="form-group">
                        <label>Received By:</label>
                        <select class="browser-default custom-select required">
                            <option value=""> -- Select received By -- </option>

                            @if (!empty($employees))

                                @foreach ($employees as $employee)

                            <option value="{{ $employee->emp_id }}">
                                {{ $employee->name }} [ {{ $employee->position }} ]
                            </option>

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

                                <option value="{{ $signatory->id }}">
                                    {{ $signatory->name }} [ {{ $signatory->position }} ]
                                </option>

                                @endforeach

                            @endif

                        </select>
                    </div>
                </td>
            </tr>

    @elseif ($classification == 'ris')

    @elseif ($classification == 'ics')

    @endif

        </table>
    </div>
</form> 