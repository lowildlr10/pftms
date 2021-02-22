<form id="form-update" method="POST" action="{{ url('procurement/iar/update/' . $iar->iar_no) }}"
      class="z-depth-1-half wow animated fadeIn">
	@csrf

	<table class="table table-bordered">
		<tr>
			<td width="15%">
				<label>Supplier:</label>
			</td>
			<td width="35%">
				<select class="browser-default custom-select required" disabled="disabled">

					@if (!empty($suppliers))

						@foreach ($suppliers as $supplier)

							@if ($supplier->id == $iar->awarded_to)

					<option value="{{ $supplier->id }}" selected="selected">{{ $supplier->company_name }}</option>

							@else

					<option value="{{ $supplier->id }}">{{ $supplier->company_name }}</option>

							@endif

						@endforeach

					@endif

				</select>
			</td>
			<td width="15%">
				<label>IAR No.:</label>
			</td>
			<td width="35%">
                <input type="text" value="{{ $iar->iar_no }}" class="form-control"
                       disabled="disabled">
			</td>
		</tr>

		<tr>
			<td>
				<label>PO No./Date:</label>
			</td>
			<td>
                <input type="text" value="{{ $iar->date_po }}" class="form-control"
                       disabled="disabled">
			</td>
			<td>
				<label>Date:</label>
			</td>
			<td>
                <input name="date_iar" type="date" value="{{ $iar->date_iar }}"
                       class="form-control z-depth-1">
			</td>
		</tr>

		<tr>
			<td>
				<label>Requisitioning Office/Dept.:</label>
			</td>
			<td>
                <input type="text" value="{{ $iar->division }}" class="form-control"
                       disabled="disabled">
			</td>
			<td>
				<label>Invoice No.:</label>
			</td>
			<td>
                <input name="invoice_no" type="text" value="{{ $iar->invoice_no }}"
                       class="form-control z-depth-1">
			</td>
		</tr>

		<tr>
			<td>
				<label>Responsibility Center Code:</label>
			</td>
			<td>
                <input type="text" value="19 001 03000 14" class="form-control"
                       disabled="disabled">
			</td>
			<td>
				<label>Date:</label>
			</td>
			<td>
                <input name="date_invoice" type="date" value="{{ $iar->date_invoice }}"
                       class="form-control z-depth-1">
			</td>
		</tr>

		<tr>
			<td colspan="4">
				<table class="table table-hover z-depth-1">
                    <thead class="mdb-color white-text">
                        <tr>
                            <th>Stock/Property No.</th>
                            <th>Description</th>
                            <th>Unit</th>
                            <th>Quantity</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (!empty($poItems))
                            @foreach ($poItems as $item)
                        <tr>
                            <td>{{ $item->stock_no }}</td>
                            <td>{{ $item->item_description }}</td>
                            <td>{{ $item->unit }}</td>
                            <td>{{ $item->quantity }}</td>
                        </tr>
                            @endforeach
                        @endif

                        @for ($i = 0; $i < 2; $i++)
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        @endfor

                        <tr>
                            <td align="center" colspan="4"> *** Nothing Follows ***</td>
                        </tr>
                    </tbody>
				</table>
			</td>
		</tr>

		<tr>
			<td colspan="2" width="50%">
				<div class="form-group">
					<label>Inspection Office/Inspection Committee</label>
					<select name="sig_inspection" class="browser-default custom-select z-depth-1 required">
						<option value=""> -- Select Signatory -- </option>
						@if (!empty($signatories))
							@foreach ($signatories as $signatory)
                                @if ($signatory->iar_sign_type == 'inspector')
								    @if ($signatory->id == $iar->sig_inspection)
						<option value="{{ $signatory->id }}" selected="selected">
							{{ $signatory->name }} [ {{ $signatory->position }} ]
						</option>
								    @else
						<option value="{{ $signatory->id }}">
							{{ $signatory->name }} [ {{ $signatory->position }} ]
						</option>
								    @endif
                                @endif
							@endforeach
						@endif
					</select>
				</div>
			</td>
			<td colspan="2" width="50%">
				<div class="form-group">
					<label>Supply and/or Property Custodian</label>
					<select name="sig_supply" class="browser-default custom-select z-depth-1 required">
						<option value=""> -- Select Signatory -- </option>
						@if (!empty($signatories))
							@foreach ($signatories as $signatory)
                                @if ($signatory->iar_sign_type == 'custodian')
								    @if ($signatory->id == $iar->sig_supply)
						<option value="{{ $signatory->id }}" selected="selected">
							{{ $signatory->name }} [ {{ $signatory->position }} ]
						</option>
								    @else
						<option value="{{ $signatory->id }}">
							{{ $signatory->name }} [ {{ $signatory->position }} ]
						</option>
								    @endif
                                @endif
							@endforeach
						@endif
					</select>
				</div>
			</td>
		</tr>

	</table>
</form>
