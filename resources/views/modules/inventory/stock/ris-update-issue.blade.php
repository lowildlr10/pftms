<form id="form-update" class="wow animated fadeIn d-flex justify-content-center" method="POST"
      action="{{ route('stocks-update-issue-item', [
          'invStockID' => $invStockID,
          'classification' => $classification,
      ]) }}">
    @csrf

    <div class="table-responsive">
        <table class="table">
            <tr>
                <td>
                    <div class="md-form form-sm">
                        <input type="text" id="divsion" class="form-control"
                               value="{{ $division }}" readonly>
                        <label for="divsion" class="active">
                            <strong>Division</strong>
                        </label>
                    </div>
                    <div class="md-form form-sm">
                        <input type="text" id="office" class="form-control"
                               value="{{ $office }}" readonly>
                        <label for="office" class="active">
                            <strong>Office</strong>
                        </label>
                    </div>
                </td>
                <td>
                    <div class="md-form form-sm">
                        <input type="text" id="inventory-no" class="form-control"
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
                                <td class="text-center" width="25%">
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
                                <td class="text-center" width="12%"></td>
                            </tr>

                            @if (count($stocks) > 0)
                                @foreach ($stocks as $ctr => $stock)
                            <tr id="row-{{ $ctr + 1 }}">
                                <td>
                                    <div class="md-form form-sm my-0">
                                        <textarea class="md-textarea form-control required" name="prop_stock_no[]"
                                                  placeholder="Value..." rows="1">{{ $stock->prop_stock_no }}</textarea>
                                    </div>
                                    <input type="hidden" name="inv_stock_issue_item_id[]" value="{{ $stock->id }}">
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
                                               name="stock_available[{{ $ctr }}]" value="y"
                                               {{ $stock->stock_available == 'y' ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="avail-y-{{ $ctr }}"></label>
                                    </div>
                                </td>
                                <td align="center">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" class="custom-control-input" id="avail-n-{{ $ctr }}"
                                               name="stock_available[{{ $ctr }}]" value="n"
                                               {{ $stock->stock_available == 'n' ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="avail-n-{{ $ctr }}"></label>
                                    </div>
                                </td>
                                <td>
                                    <div class="md-form form-sm my-0">
                                        <input class="form-control form-control-sm quantity required" type="number"
                                               name="quantity[]" min="0" max="{{ $stock->available_quantity }}"
                                               placeholder="avail: {{ $stock->available_quantity }}"
                                               value="{{ $stock->issued_quantity }}">
                                    </div>
                                </td>
                                <td>
                                    <div class="md-form form-sm my-0">
                                        <textarea class="md-textarea form-control required" name="issued_remarks[]"
                                                  placeholder="Value..." rows="1">{{ $stock->remarks }}</textarea>
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
                            <option value="{{ $sig->id }}" {{ $sig->id == $sigApprovedBy ? 'selected' : '' }}>
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
                <td width="50%">
                    <div class="md-form">
                        <select id="sig-requested-by" name="sig_requested_by" searchable="Search here.."
                                class="mdb-select crud-select md-form my-0 required">
                            <option value="" disabled selected>
                                Choose a requested by
                            </option>

                            @if (count($employees) > 0)
                                @foreach ($employees as $emp)
                            <option value="{{ $emp->id }}" {{ $emp->id == $sigRequestedBy ? 'selected' : '' }}>
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
                            <option value="{{ $sig->id }}" {{ $sig->id == $sigIssuedBy ? 'selected' : '' }}>
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
