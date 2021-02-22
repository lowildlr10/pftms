
<div class="table-responsive">

	@if (!empty($list))

		@foreach ($list as $abstract)

	<table class="table table-bordered table-hover">
		<tr>
			<th>Group No: {{ $abstract->group_no }}</th>
		</tr>
		<tr>
			<td>
				<table class="table table-bordered">
					<tr>
						<th style="text-align:center;" width="50px">#</th>
						<th style="text-align:center;" width="300px">Item Description</th>
						<th style="text-align:center;" width="150px">Unit</th>
						<th style="text-align:center;" width="150px">ABC (UNIT)</th>

						@if (!empty($abstract->suppliers) && isset($abstract->suppliers))

							@foreach ($abstract->suppliers as $key => $supplier)

						<th style="text-align:center;" width="320px" colspan="2">
							<p align="center">
								{{ $supplier->company_name }} <br>
								[Unit Cost | Total Cost]
							</p>
						</th>

							@endforeach

						@endif

						<th style="text-align:center;" width="320px">Awarded To</th>
					</tr>

					

					@if (!empty($abstract->pr_items) && isset($abstract->pr_items))

						@foreach ($abstract->pr_items as $listCtr => $item)

					<tr>
						<td rowspan="2" align="center">{{ $listCtr + 1 }}</td>
						<td rowspan="2">{{ substr($item->item_description, 0, 150) }}...</td>
						<td rowspan="2" align="center">{{ $item->unit }}</td>
						<td rowspan="2" align="center">{{ $item->est_unit_cost }}</td>

							@if (!empty($item->abstract_items) && isset($item->abstract_items))

								@foreach ($item->abstract_items as $abs)

						<td width="125px">
							<p align="center">
								<strong>&#8369;{{ number_format($abs->unit_cost, 2) }}</strong><br>
							</p>
						</td>
						<td width="125px">
							<p align="center">
								<strong>&#8369;{{ number_format($abs->total_cost, 2) }}</strong><br>
							</p>
						</td>

								@endforeach

							@endif

						<td rowspan="2">
							<p align="center">
								<strong>{{ $item->company_name }}</strong><br>

								@if (!empty($abs->remarks))

								({{ $item->awarded_remarks }})

								@endif
							</p>
						</td>
					</tr>
					<tr>

							@if (!empty($item->abstract_items) && isset($item->abstract_items))

								@foreach ($item->abstract_items as $abs)

						<td colspan="2">
							
							@if (!empty($abs->remarks))

							<center>({{ $abs->remarks }})</center>

							@endif
                            
						</td>

								@endforeach

							@endif
							
					</tr>

						@endforeach

					@endif	

				</table>
			</td>
		</tr>
	</table>

		@endforeach

	@endif

</div>