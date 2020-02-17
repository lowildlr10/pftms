
<div class="table-responsive">
	<table class="table table-hover">

	<tr style="background: #01163c; color: #fff;">
		<th width="5%" align="center">#</th>
		<th width="7%" align="center">Qnty</th>
		<th width="12%" align="center">Unit</th>
		<th width="46%" align="center">Item Description</th>

		@if ($toggle == 'po')

		<th width="15%" align="center">Estimate Unit Cost</th>
		<th width="15%" align="center">Estimate Total Cost</th>

		@else

		<th width="15%" align="center">Unit Cost</th>
		<th width="15%" align="center">Total Cost</th>

		@endif

	<tr>

	@if (!empty($prItems))

	@php $totalCost = 0; @endphp

	@foreach ($prItems as $key => $item)

	<tr>
		<td>{{ $key + 1 }}</td>
		<td>{{ $item->quantity }}</td>
		<td>{{ $item->unit }}</td>
		<td>{{ $item->item_description }}</td>

		@if ($toggle == 'po')

			@if ($countPO > 0)

		<td>{{ number_format($item->unit_cost, 2) }}</td>
		<td>{{ number_format($item->total_cost, 2) }}</td>

			@php $totalCost += $item->total_cost; @endphp

			@else

		<td>{{ number_format($item->est_unit_cost, 2) }}</td>
		<td>{{ number_format($item->est_total_cost, 2) }}</td>	

			@php $totalCost += $item->est_total_cost; @endphp

			@endif

		@else

		<td><strong>{{ number_format($item->est_unit_cost, 2) }}</strong></td>
		<td><strong>{{ number_format($item->est_total_cost, 2) }}</strong></td>

		@php $totalCost += $item->est_total_cost; @endphp

		@endif

	</tr>

	@endforeach

	@endif

	<tr><td colspan="6"><center>*** Nothing Follows ***</center></td></tr>

	@for ($i = 0; $i < 2; $i++)

	<tr><td colspan="6"></td></tr>

	@endfor

	<tr style="background: #3f5371f0; color: #fff;">
		<td colspan="5"><strong>Grand Total Cost:</strong></td>
		<td><strong>{{ number_format($totalCost, 2) }}</strong></td>
	</tr>

	</table>
</div>