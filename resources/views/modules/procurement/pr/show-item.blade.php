<div class="table-responsive">
    <table class="table">
        <thead>
            <th>#</th>
            <th>Unit</th>
            <th>Item Description</th>
            <th>Quantity</th>
            <th>Unit Cost</th>
            <th>Total Cost</th>
            <th>Awarded To</th>
        </thead>

        <tbody>
            @if (!empty($prItems))
                @foreach ($prItems as $itemCtr => $item)
            <tr>
                <td>{{ $itemCtr + 1 }}</td>
                <td>{{ $item->unit_issue }}</td>
                <td>{{ $item->item_description }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ $item->est_unit_cost }}</td>
                <td>{{ $item->est_total_cost }}</td>
                <td>{{ $item->awarded_to }}</td>
            </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>
