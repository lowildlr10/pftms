<form id="form-update" class="wow animated fadeIn d-flex justify-content-center" method="POST"
      action="{{ route('stocks-update-iar', ['poID' => $poID]) }}">
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
                    <input type="hidden" name="inv_item_ids[]" value="{{ $item->id }}">
                    {{ $listCtr + 1 }}
                </td>
                <td class="table-border-left"><i class="fas fa-caret-right"></i> {{ $item->description }}</td>
                <td class="table-border-left">{{ $item->unit }}</td>
                <td class="table-border-left">P {{ number_format($item->amount, 2) }}</td>
                <td class="table-border-left">
                    <div class="md-form my-0">
                        <select class="mdb-select crud-select md-form my-0 required" searchable="Search here.."
                                name="inventory_classifications[]">
                            <option value="" disabled selected>Choose an inventory classification</option>

                            @if (count($inventoryClassifications) > 0)
                                @foreach ($inventoryClassifications as $class)
                            <option {{ $class->id == $item->inventory_classification ? 'selected' : '' }}
                                    value="{{ $class->id }}">
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
                            <option {{ $class->id == $item->item_classification ? 'selected' : '' }}
                                    value="{{ $class->id }}">
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
