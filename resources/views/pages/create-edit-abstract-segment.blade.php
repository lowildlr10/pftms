@if (!empty($list))
    @foreach ($list as $abstract)

<table class="table table-bordered z-depth-1 wow animated fadeIn">
    <tr class="header-group">
        <th style="text-align:center;" width="50px">#</th>
        <th style="text-align:center;" width="300px">Item Description</th>
        <th style="text-align:center;" width="100px">Unit</th>
        <th style="text-align:center;" width="100px">ABC (UNIT)</th>

        @if ($bidderCount > 0)
            @for ($key = 0; $key < $bidderCount; $key++)

        <th style="text-align:center;" width="320px">
            <div class="form-group">
                <select class="browser-default custom-select sel-supplier"
                        name="selected_supplier[{{ $groupKey }}][{{ $key }}]">
                    @if (!empty($supplierList))
                        @foreach ($supplierList as $supplierCounter => $bid)
                            @if ($key == $supplierCounter)
                    <option value="{{ $bid->id }}" selected="selected">
                        {{ $bid->company_name }}
                    </option>
                            @else

                    <option value="{{ $bid->id }}">
                        {{ $bid->company_name }}
                    </option>
                            @endif
                        @endforeach
                    @endif

                </select>
            </div>
        </th>

            @endfor
        @endif

        <th style="text-align:center;" width="320px">Awarded To</th>
    </tr>

    @if (!empty($abstract->pr_items) && isset($abstract->pr_items))
        @foreach ($abstract->pr_items as $listCtr => $item)

    <tr>
        <td align="center">
            {{ $listCtr + 1 }}
            <input type="hidden" class="item-id"
                   name="item_id[{{ $groupKey }}][{{ $listCtr }}]"
                   value="{{ $item->item_id }}">
        </td>
        <td>{{ substr($item->item_description, 0, 300) }}...</td>
        <td align="center">{{ $item->unit }}</td>
        <td align="center">{{ $item->est_unit_cost }}</td>

            @if ($bidderCount > 0)
                @for ($key = 0; $key < $bidderCount; $key++)

        <td width="320px">
            <div class="form-group">
                <label>Unit Cost</label>
                <input class=".quantity" type="hidden" value="{{ $item->quantity }}">
                <input type="number" class="form-control unit-cost required"
                       name="unit_cost[{{ $groupKey }}][{{ $listCtr }}][{{ $key }}]" min="0">
            </div>
            <div class="form-group">
                <label>Total Cost</label>
                <input type="number" class="form-control total-cost required"
                       name="total_cost[{{ $groupKey }}][{{ $listCtr }}][{{ $key }}]" min="0">
            </div>
            <div class="form-group">
                <label>Specification</label>
                <textarea class="form-control specification"
                name="specification[{{ $groupKey }}][{{ $listCtr }}][{{ $key }}]"
                style="resize: none;" rows="3"></textarea>
            </div>
            <div class="form-group">
                <label>Remarks</label>
                <textarea class="form-control remarks"
                name="remarks[{{ $groupKey }}][{{ $listCtr }}][{{ $key }}]"
                style="resize: none;" rows="3"></textarea>
            </div>
        </td>

                @endfor
            @endif

        <td>

            @if ($bidderCount > 0)

            <div class="form-group">
                <label>Select a Supplier</label>
                <select class="browser-default custom-select awarded-to"
                        name="awarded_to[{{ $groupKey }}][{{ $listCtr }}]">
                    <option value="">-- No awardee --</option>

                    @if (!empty($supplierList))
                        @foreach ($supplierList as $bidCounter => $bid)
                            @if ($bidCounter < $bidderCount)

                    <option value="{{ $bid->id }}">
                        {{ $bid->company_name }}
                    </option>

                            @endif
                        @endforeach
                    @endif

                </select>
            </div>
            <div class="form-group">
                <label>Document Type</label>
                <select class="browser-default custom-select document-type required"
                        name="document_type[{{ $groupKey }}][{{ $listCtr }}]">
                    <option value="" selected="selected">-- Select a document --</option>
                    <option value="PO">
                        Purchase Order (PO)
                    </option>
                    <option value="JO">
                       Job Order (JO)
                    </option>
                </select>
            </div>
            <div class="form-group">
                <label>Remarks</label>
                <textarea class="form-control awarded-remarks" name="awarded_remarks[{{ $groupKey }}][{{ $listCtr }}]"
                style="resize: none;" rows="3"></textarea>
            </div>

            @else

            <center>
                <strong>N/A</strong>
            </center>

            @endif

        </td>
    </tr>

        @endforeach
    @endif

</table>

    @endforeach
@endif
