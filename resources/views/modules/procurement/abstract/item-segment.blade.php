@if (count($abstractItems) > 0)
    @foreach ($abstractItems as $ctrItem => $abstract)
        @if (isset($abstract->pr_item_count) && $abstract->pr_item_count)

<ul class="nav nav-tabs mdb-color lighten-5" role="tablist">
            @if ($abstract->pr_item_count < 10)
    <li class="nav-item">
        <a class="nav-link active font-weight-bold" id="tab-1-{{ $abstract->pr_item_count }}"
           data-toggle="tab"
           href="#page-1-{{ $abstract->pr_item_count }}"
           role="tab" aria-controls="home"
           aria-selected="true">
            #{{ $currentFirstItemNo }} to #{{ $abstract->pr_item_count }}
        </a>
    </li>
                @php
                    $pages[] = (object) [
                        'first' => 1,
                        'last' => $abstract->pr_item_count
                    ]
                @endphp
            @elseif ($abstract->pr_item_count >= 10  && $totalItemCount == 0)
                @php $totalItemCount = $abstract->pr_item_count @endphp
            @endif

            @if ($abstract->pr_item_count >= 10)
                @for ($ctr = 1; $ctr <= $abstract->pr_item_count; $ctr++)
                    @if ($counter == 1)
                        @php $currentFirstItemNo = $ctr; @endphp
                    @endif

                    @if ($totalItemCount >= 10 && $counter == 10)
                        @php
                            $currentLastItemNo = $currentFirstItemNo + 9;
                            $pages[] = (object) [
                                'first' => $currentFirstItemNo,
                                'last' => $currentLastItemNo
                            ];
                            $totalItemCount -= $counter;
                        @endphp
    <li class="nav-item">
        <a class="nav-link font-weight-bold {{ count($pages) == 1 ? 'active' : '' }}"
           id="tab-{{ $currentFirstItemNo }}-{{ $currentLastItemNo }}"
           data-toggle="tab"
           href="#page-{{ $currentFirstItemNo }}-{{ $currentLastItemNo }}"
           role="tab"
           aria-selected="true">
            #{{ $currentFirstItemNo }} to #{{ $currentLastItemNo }}
        </a>
    </li>
                        @php $counter = 0; @endphp
                    @elseif ($totalItemCount < 10)
                        @php
                            $currentLastItemNo = $abstract->pr_item_count;
                            $pages[] = (object) [
                                'first' => $currentFirstItemNo,
                                'last' => $currentLastItemNo
                            ];
                        @endphp
    <li class="nav-item">
        <a class="nav-link font-weight-bold" id="tab-{{ $currentFirstItemNo }}-{{ $currentLastItemNo }}"
        data-toggle="tab"
        href="#page-{{ $currentFirstItemNo }}-{{ $currentLastItemNo }}"
        role="tab"
        aria-selected="true">
            #{{ $currentFirstItemNo }} to #{{ $currentLastItemNo }}
        </a>
    </li>
                        @php break; @endphp
                    @endif

                    @php
                        $counter++;
                    @endphp
                @endfor
            @endif
</ul>
        @endif


<table class="table table-bordered z-depth-1 wow animated fadeIn p-0 m-0">
    <thead class="header-group">
        <tr>
            @if ($bidderCount > 0)
                @for ($key = 0; $key < $bidderCount; $key++)
            <th width="320px">
                <div class="form-group">
                    <select class="mdb-select md-form sel-supplier sel-supplier-{{ $groupKey }} required"
                            id="sel-bidder-count-{{ $groupKey }}-{{ $key }}"
                            onchange="$(this).setSupplierHeaderName($(this), '.bid-head-{{ $key }}',
                                                                    $(this).find(':selected').text());"
                            name="selected_supplier[{{ $groupKey }}][{{ $key }}]"
                            searchable="Search here..">
                        <option value="" disabled selected>Choose a supplier</option>
                        @if (!empty($supplierList))
                            @foreach ($supplierList as $supplierCounter => $bid)
                        <option value="{{ $bid->id }}" {{ $key == $supplierCounter ? 'selected' : '' }}>
                            Supplier #{{ $key + 1 }} : {{ $bid->company_name }}
                        </option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </th>
                @endfor
            @endif
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="{{ $bidderCount ? $bidderCount : 1 }}" class="p-0 m-0">
                <div class="tab-content p-0">
                    @foreach ($pages as $pageCtr => $page)
                    <div class="tab-pane fade {{ $pageCtr == 0 ? 'show active' : '' }}"
                         id="page-{{ $page->first }}-{{ $page->last }}" role="tabpanel">
                        <table class="table p-0 m-0">
                            <thead>
                                <tr>
                                    <th class="text-center font-weight-bold" width="50px">#</th>
                                    <th class="text-center font-weight-bold" width="300px">Item Description</th>
                                    <th class="text-center font-weight-bold" width="100px">Unit</th>
                                    <th class="text-center font-weight-bold" width="100px">ABC (UNIT)</th>
                                    @if ($bidderCount > 0)
                                        @for ($key = 0; $key < $bidderCount; $key++)
                                    <th class="text-center font-weight-bold" width="320px">
                                            @foreach ($supplierList as $supplierCounter => $bid)
                                                @if ($key == $supplierCounter)
                                        <span class="bid-head-{{ $key }}">
                                            Supplier #{{ $key + 1 }} : {{ $bid->company_name }}
                                        </span>
                                                @endif
                                            @endforeach
                                    </th>
                                        @endfor
                                    <th class="text-center font-weight-bold" width="320px">Awarded To</th>
                                    @endif
                                </tr>
                            </thead>

                            <tbody class="table-data">
                                @if (!empty($abstract->pr_items) && isset($abstract->pr_items))
                                    @foreach ($abstract->pr_items as $listCtr => $item)
                                        @if ($page->last >= ($listCtr + 1) && $page->first <= ($listCtr + 1))
                                <tr>
                                    <td align="center">
                                        {{ $listCtr + 1 }}
                                        <input type="hidden" class="item-id"
                                            value="{{ $item->item_id }}">
                                    </td>
                                    <td>
                                        {{ (strlen($item->item_description) > 150) ?
                                            substr($item->item_description, 0, 150).'...' : $item->item_description  }}
                                        ({{ $item->quantity }} {{ $item->quantity > 1 ? 'pcs.' : 'pc.' }})
                                    </td>
                                    <td align="center">{{ $item->unit_name }}</td>
                                    <td align="center">{{ $item->est_unit_cost }}</td>
                                            @if ($bidderCount > 0)
                                                @for ($key = 0; $key < $bidderCount; $key++)
                                    <td width="320px">
                                        <div class="md-form form-sm">
                                            <input class="quantity" type="hidden" value="{{ $item->quantity }}">
                                            <input type="number" class="form-control form-control-sm unit-cost required"
                                                id="unit_cost-{{ $groupKey }}-{{ $listCtr }}-{{ $key }}" min="0">
                                            <label for="unit_cost-{{ $groupKey }}-{{ $listCtr }}-{{ $key }}" class="active">
                                                Unit Cost <span class="red-text">*</span>
                                            </label>
                                        </div>
                                        <div class="md-form form-sm">
                                            <input type="number" class="form-control form-control-sm total-cost required"
                                                id="total_cost-{{ $groupKey }}-{{ $listCtr }}-{{ $key }}" min="0" >
                                            <label for="total_cost-{{ $groupKey }}-{{ $listCtr }}-{{ $key }}" class="active">
                                                Total Cost <span class="red-text">*</span>
                                            </label>
                                        </div>
                                        <div class="md-form form-sm">
                                            <textarea class="md-textarea form-control form-control-sm specification"
                                                    id="specification-{{ $groupKey }}-{{ $listCtr }}-{{ $key }}"
                                                    style="resize: none;" rows="3"></textarea>
                                            <label for="specification-{{ $groupKey }}-{{ $listCtr }}-{{ $key }}" class="active">
                                                Specifications
                                            </label>
                                        </div>
                                        <div class="md-form form-sm">
                                            <textarea class="md-textarea form-control form-control-sm remarks"
                                                    id="remarks-{{ $groupKey }}-{{ $listCtr }}-{{ $key }}"
                                                    style="resize: none;" rows="3"></textarea>
                                            <label for="remarks-{{ $groupKey }}-{{ $listCtr }}-{{ $key }}" class="active">
                                                Remarks
                                            </label>
                                        </div>
                                    </td>
                                                @endfor
                                            @endif
                                            @if ($bidderCount > 0)
                                    <td>
                                        <div class="form-group">
                                            <label class="mdb-main-label">
                                                Awarded To
                                            </label>
                                            <select class="browser-default custom-select awarded-to" searchable="Search here..">
                                                <option value="" disabled selected>Choose an awardee</option>
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
                                            <label class="mdb-main-label">
                                                Document Type <span class="red-text">*</span>
                                            </label>
                                            <select class="browser-default custom-select document-type"
                                                    searchable="Search here..">
                                                <option value="po">
                                                    Purchase Order (PO)
                                                </option>
                                                <option value="jo">
                                                Job Order (JO)
                                                </option>
                                            </select>
                                        </div>
                                        <div class="md-form form-sm">
                                            <textarea class="md-textarea form-control form-control-sm awarded-remarks"
                                                    id="awarded_remarks-{{ $groupKey }}-{{ $listCtr }}"
                                                    style="resize: none;" rows="3"></textarea>
                                            <label for="awarded_remarks-{{ $groupKey }}-{{ $listCtr }}" class="active">
                                                Remarks
                                            </label>
                                        </div>
                                    </td>
                                            @endif
                                </tr>
                                        @endif
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                    @endforeach

                </div>
            </td>
        </tr>
    </tbody>
</table>
    @endforeach
@endif
