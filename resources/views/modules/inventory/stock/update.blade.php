<form id="form-update" class="wow animated fadeIn d-flex justify-content-center" method="POST"
      action="{{ route('stocks-update', [
          'id' => $id,
      ]) }}">
    @csrf

    <div class="table-responsive">
        <table class="table">
            <tr>
                <td>
                    <div class="md-form form-sm">
                        <input type="text" id="fund-cluster" name="fund_cluster"
                               class="form-control" value="{{ $fundCluster }}">
                        <label for="fund-cluster" class="{{ !empty($fundCluster) ? 'active' : '' }}">
                            Fund Cluster
                        </label>
                    </div>
                    <div class="md-form form-sm">
                        <select id="division" name="division" searchable="Search here.."
                                class="mdb-select crud-select md-form my-0">
                            <option value="" disabled selected>
                                Choose an division
                            </option>
                            <option value="">-- None --</option>

                            @if (count($divisions) > 0)
                                @foreach ($divisions as $div)
                            <option value="{{ $div->id }}" {{ $div->id == $division ? 'selected' : '' }}>
                                {!! $div->division_name !!}
                            </option>
                                @endforeach
                            @endif
                        </select>
                        <label class="mdb-main-label">
                            Division
                        </label>
                    </div>
                    <div class="md-form form-sm">
                        <input type="text" id="office" name="office" class="form-control"
                               value="{{ $office }}">
                        <label for="office" class="{{ !empty($office) ? 'active' : '' }}">
                            Office
                        </label>
                    </div>
                </td>
                <td>
                    <div class="md-form form-sm">
                        <input type="text" id="inventory-no" class="form-control required"
                               name="inventory_no" value="{{ $inventoryNo }}">
                        <label for="inventory-no" class="{{ !empty($inventoryNo) ? 'active' : '' }}">
                            {{ strtoupper($classification) }} No <span class="red-text">*</span>
                        </label>
                    </div>
                    <div class="md-form form-sm">
                        <input type="text" id="entity-name" class="form-control"
                               name="entity_name" value="{{ $entityName }}">
                        <label for="entity-name" class="{{ !empty($entityName) ? 'active' : '' }}">
                            Entity Name
                        </label>
                    </div>
                </td>
            </tr>

            <tr>
                <td colspan="2">
                    <div class="col-md-12 px-0 table-responsive border">
                        <table class="table table-hover" id="item-table">
                            <thead class="mdb-color white-text">
                                <tr>
                                    <td class="text-center" width="15%">
                                        Unit <span class="red-text">* </span>
                                    </td>
                                    <td class="text-center" width="43%">
                                        Description <span class="red-text">* </span>
                                    </td>
                                    <td class="text-center" width="8%">
                                        Quantity <span class="red-text">* </span>
                                    </td>
                                    <td class="text-center" width="12%">
                                        Amount <span class="red-text">* </span>
                                    </td>
                                    <td class="text-center" width="20%">
                                        Item Classification
                                    </td>
                                    <td class="text-center" width="2%"></td>
                                </tr>
                            </thead>

                            <tbody id="row-items">
                                @if (count($items) > 0)
                                    @foreach ($items as $ctr => $item)
                                <tr id="row-{{ $ctr }}">
                                    <td align="center">
                                        <div class="md-form form-sm my-0">
                                            <select id="unit" name="unit[]" searchable="Search here.."
                                                    class="browser-default custom-select my-0 required">

                                                @if (count($unitIssues) > 0)
                                                    @foreach ($unitIssues as $unit)
                                                <option value="{{ $unit->id }}" {{ $unit->id == $item->unit_issue ? 'selected' : '' }}>
                                                    {!! $unit->unit_name !!}
                                                </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="md-form form-sm my-0">
                                            <textarea class="md-textarea form-control py-0 required" name="description[]"
                                                    placeholder="Item description..." rows="3">{{ $item->description }}</textarea>
                                        </div>
                                    </td>
                                    <td align="center">
                                        <div class="md-form form-sm my-0">
                                            <input class="form-control form-control-sm required"
                                                   name="quantity[]" type="number" value="{{ $item->quantity }}">
                                        </div>
                                    </td>
                                    <td align="center">
                                        <div class="md-form form-sm my-0">
                                            <input class="form-control form-control-sm required"
                                                   name="amount[]" type="number" value="{{ $item->amount }}">
                                        </div>
                                    </td>
                                    <td align="center">
                                        <div class="md-form form-sm my-0">
                                            <select id="item-classification" name="item_classification[]" searchable="Search here.."
                                                    class="browser-default custom-select my-0">
                                                <option value="">-- None --</option>

                                                @if (count($itemClassifications) > 0)
                                                    @foreach ($itemClassifications as $class)
                                                <option value="{{ $class->id }}" {{ $class->id == $item->item_classification ? 'selected' : '' }}>
                                                    {!! $class->classification_name !!}
                                                </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </td>
                                    <td>
                                        <input type="hidden" name="inv_stock_item_id[]" value="{{ $item->id }}">
                                        <!--
                                        <a onclick="$(this).deleteRow('#row-{{ $ctr }}');"
                                        class="btn btn-outline-red px-1 py-0">
                                            <i class="fas fa-minus-circle"></i>
                                        </a> -->
                                    </td>
                                </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <!--
                    <a id="add-block-btn" class="btn btn-md btn-block btn-outline-mdb-color
                          waves-effect mt-0 mb-3 py-4" onclick="$(this).addRow('#item-table')">
                        <i class="fas fa-plus"></i>
                        <strong>Add Item</strong>
                    </a> -->
                </td>
            </tr>

            <tr>
                <td colspan="2">
                    <div class="md-form form-sm my-0">
                        <textarea class="md-textarea form-control required" id="purpose"
                                  name="purpose" rows="2">{{ $purpose }}</textarea>
                        <label for="purpose" class="{{ !empty($purpose) ? 'active' : '' }}">
                            <strong>> Purpose  <span class="red-text">* </span></strong>
                        </label>
                    </div>
                </td>
            </tr>

            <tr>
                <td>
                    <div class="md-form form-sm">
                        <input type="text" id="po-no" name="po_no" class="form-control"
                               value="{{ $poNo }}">
                        <label for="po-no" class="{{ !empty($poNo) ? 'active' : '' }}">
                            <strong>PO No</strong>
                        </label>
                    </div>
                    <div class="md-form form-sm">
                        <input type="date" id="date-po" name="date_po" class="form-control"
                               value="{{ $datePO }}">
                        <label for="date-po" class="mt-3">
                            <strong>Date</strong>
                        </label>
                    </div>
                    <div class="md-form form-sm">
                        <select id="supplier" name="supplier" searchable="Search here.."
                                class="mdb-select crud-select md-form my-0">
                            <option value="" disabled selected>
                                Choose an supplier
                            </option>
                            <option value="">-- None --</option>

                            @if (count($suppliers) > 0)
                                @foreach ($suppliers as $bid)
                            <option value="{{ $bid->id }}" {{ $bid->id == $supplier ? 'selected' : '' }}>
                                {!! $bid->company_name !!}
                            </option>
                                @endforeach
                            @endif
                        </select>
                        <label class="mdb-main-label">
                            Supplier
                        </label>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <input id="item-count" value="1" type="hidden">
</form>
