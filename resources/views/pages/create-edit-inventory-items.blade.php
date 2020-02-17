<form id="form-create-inventory" method="POST" action="{{ url('inventory/stocks/store/'.$poNo) }}"
      enctype="multipart/form-data" class="z-depth-1-half">
    @csrf
    <input type="hidden" id="type" name="type" value="create">

    <div class="table-responsive table-container">
        <table class="table table-hover z-depth-1">
            <tr>
                <th width="3%">#</th>
                <th width="37%">Description</th>
                <th width="10%">Unit</th>
                <th width="10%">Unit Cost</th>
                <th width="15%">Inventory Classification</th>
                <th width="15%">Item Category</th>
                <th width="10%">Group No</th>
            </tr>

            @if (!empty($items))
                @foreach ($items as $listCtr => $item)
            <tr>
                <td class="table-border-left">
                    <input type="hidden" name="inventory_id[]" value="{{ $item->inventory_id }}">
                    <input type="hidden" name="item_id[]" value="{{ $item->item_id }}">
                    {{ $listCtr + 1 }}
                </td>
                <td class="table-border-left"><i class="fas fa-caret-right"></i> {{ $item->item_description }}</td>
                <td class="table-border-left" align="center">{{ $item->unit }}</td>
                <td class="table-border-left" align="right">{{ number_format($item->unit_cost, 2) }}</td>
                <td class="table-border-left">
                    <select class="browser-default custom-select required" name="inventory_classification[]">
                        <option value=""> -- Select Inventory Classification -- </option>

                        @if (!empty($inventoryClassification))
                            @foreach ($inventoryClassification as $classification)
                                @if ($classification->id == $item->inventory_class_id)
                        <option value="{{ $classification->id }}" selected="selected">{{ $classification->classification }}</option>
                                @else
                        <option value="{{ $classification->id }}">{{ $classification->classification }}</option>
                                @endif
                            @endforeach
                        @endif
                    </select>
                </td>
                <td class="table-border-left">
                    <select class="browser-default custom-select" name="item_classification[]">
                        <option value="0"> -- Select Item Category -- </option>

                        @if (!empty($itemClassification))
                            @foreach ($itemClassification as $category)
                                @if ($category->id == $item->item_class_id)
                        <option value="{{ $category->id }}" selected="selected">{{ $category->classification }}</option>
                                @else
                        <option value="{{ $category->id }}">{{ $category->classification }}</option>
                                @endif
                            @endforeach
                        @endif
                    </select>
                </td>
                <td class="table-border-left">
                    <select class="browser-default custom-select required" name="group_no[]">
                        <option value=""> -- </option>

                        @for ($i = 0; $i <= 20; $i++)
                            @if ($i == $item->group_no)
                        <option value="{{ $i }}" selected="selected">{{ $i }}</option>
                            @else
                        <option value="{{ $i }}">{{ $i }}</option>
                            @endif
                        @endfor
                    </select>
                </td>
            </tr>
                @endforeach
            @endif

        </table>
    </div>
</form>
