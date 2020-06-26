<form id="form-store" class="wow animated fadeIn d-flex justify-content-center" method="POST"
      action="{{ route('stocks-store-iar', ['poID' => $poID]) }}">
    @csrf

    <div class="table-responsive table-container">
        <table class="table table-hover z-depth-1">
            <tr>
                <th width="3%">#</th>
                <th width="37%">Description</th>
                <th width="10%">Unit</th>
                <th width="10%">Unit Cost</th>
                <th width="20%">
                    Inventory Classification <span class="red-text">*</span>
                </th>
                <th width="20%">
                    Item Classification <span class="red-text">*</span>
                </th>
            </tr>

            @if (count($items) > 0)
                @foreach ($items as $listCtr => $item)
            <tr>
                <td class="table-border-left">
                    <input type="hidden" name="po_item_ids[]" value="{{ $item->id }}">
                    {{ $listCtr + 1 }}
                </td>
                <td class="table-border-left"><i class="fas fa-caret-right"></i> {{ $item->item_description }}</td>
                <td class="table-border-left">{{ $item->unit }}</td>
                <td class="table-border-left">P {{ number_format($item->unit_cost, 2) }}</td>
                <td class="table-border-left">
                    <div class="md-form my-0">
                        <select class="mdb-select crud-select md-form my-0 required" searchable="Search here.."
                                name="inventory_classifications[]">
                            <option value="" disabled selected>Choose an inventory classification</option>

                            @if (count($inventoryClassifications) > 0)
                                @foreach ($inventoryClassifications as $class)
                            <option value="{{ $class->id }}">
                                {{ $class->classification_name }}
                            </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </td>
                <td class="table-border-left">
                    <div class="md-form my-0">
                        <select class="mdb-select crud-select md-form my-0 required" searchable="Search here.."
                                name="item_classifications[]">
                            <option value="" disabled selected>Choose an item classification</option>

                            @if (count($itemClassifications) > 0)
                                @foreach ($itemClassifications as $class)
                            <option value="{{ $class->id }}">
                                {{ $class->classification_name }}
                            </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </td>
            </tr>
                @endforeach
            @endif

        </table>
    </div>
</form>
