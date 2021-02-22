<form id="form-store" method="POST" action="{{ url('cadv-reim-liquidation/ors-burs/store') }}"
      class="wow animated fadeIn">
	@csrf

	@php $address = ""; @endphp

	<table class="table table-bordered">
		<tr>
			<td>
				<label>Document Type:</label>
			</td>
			<td>
				<select name="document_type" class="browser-default custom-select required">
					<option value=""> -- Select document type -- </option>
                    <option value="ors">Obligation & Status Request (ORS)</option>
                    <option value="burs">Budget Utilization & Status Request (BURS)</option>
				</select>
			</td>
		</tr>
		
		<tr>
			<td width="20%">
				<label>Serial Number:</label>
			</td>
			<td width="80%">
				<input type="text" name="serial_no" class="form-control" placeholder="Serial number...">
			</td>
		</tr>

		<tr>
			<td>
				<label>Date:</label>
			</td>
			<td>
				<input type="date" name="date_ors_burs" class="form-control" placeholder="ORS/BURS Date...">
			</td>
		</tr>

		<tr>
			<td>
				<label>Payee:</label>
			</td>
			<td>
				<select name="payee" class="browser-default custom-select required">

					@if (!empty($payee))

						@foreach ($payee as $emp)

							@if ($emp->emp_id == Auth::user()->emp_id)

					<option value="{{ $emp->emp_id }}" selected="selected">{{ $emp->name }}</option>

							@else

					<option value="{{ $emp->emp_id }}">{{ $emp->name }}</option>		

							@endif

						@endforeach

					@endif
					
				</select>
			</td>
		</tr>

		<tr>
			<td>
				<label>Office:</label>
			</td>
			<td>
				<input type="text" name="office" class="form-control" placeholder="Office...">
			</td>
		</tr>

		<tr>
			<td>
				<label>Address:</label>
			</td>
			<td>
				<input type="text" name="address" class="form-control" placeholder="Address...">
			</td>
		</tr>

		<tr>
			<td colspan="2">
				<table class="table table-bordered">
					<tr>
						<th>Responsibility Center</th>
						<th>Particulars</th>
						<th>MFO/PAP</th>
						<th>UACS Object Code</th>
						<th>Amount</th>
					</tr>

					<tr>
						<td  width="10%">
							
							<input type="text" name="responsibility_center" class="form-control" placeholder="Responsibility Center...">
						</td>
						<td  width="40%">
							<textarea class="form-control required" name="particulars" style="resize: none;" rows="5"  placeholder="Particulars..."></textarea>
						</td>
						<td width="15%">
							<textarea class="form-control" name="mfo_pap" style="resize: none;" rows="5"  placeholder="MFO PAP..."></textarea>
						</td>
						<td width="20%">
							<input type="text" name="uacs_object_code" class="form-control" placeholder="UACS Object Code...">
						</td>
						<td width="15%">
							<input type="number" name="amount" min="0" class="form-control required">
						</td>
					</tr>
				</table>
			</td>
		</tr>

		<tr>
			<td width="50%">
				<p align="left">
					<strong>A. </strong><br>
					<strong>Certified: </strong>Charges to appropriation/alloment Certified: Allotment <br>
					available and obligated necessary, lawful and under my direct <br>
					supervision; for the purpose/adjustment necessary as and <br>
					supporting documents valid, proper and legal. <br><br>
					Signature: _________________________________________ <br><br>

					<div class="form-group">
						<label>Printed Name:</label>
						<select name="sig_certified_1" class="browser-default custom-select required">
							<option value=""> -- Select sigantory -- </option>

							@if (!empty($signatories))

								@foreach ($signatories as $signatory)

                                    @if ($signatory->ors_burs_sign_type == 'approval')

							<option value="{{ $signatory->id }}">
								{{ $signatory->name }} [ {{ $signatory->position }} ]
							</option>

                                    @endif

								@endforeach

							@endif

						</select>
					</div>

					<br>

					<div class="form-group">
						<label>Date:</label>
						<input type="date" name="date_certified_1" class="form-control">
					</div>
				</p>
			</td>
			<td width="50%">
				<p align="left">
					<strong>B. </strong><br>
					<strong>Certified: </strong>Allotment available and obligated necessary, lawful <br>
					and under my direct supervision; for the purpose/adjustment <br>
					necessary as and supporting documents valid, proper and legal. <br>
					indicated above. <br><br>
					Signature: _________________________________________ <br><br>

					<div class="form-group">
						<label>Printed Name:</label>
						<select name="sig_certified_2" class="browser-default custom-select required">
							<option value=""> -- Select sigantory -- </option>

							@if (!empty($signatories))

								@foreach ($signatories as $signatory)

                                    @if ($signatory->ors_burs_sign_type == 'budget')

							<option value="{{ $signatory->id }}">
								{{ $signatory->name }} [ {{ $signatory->position }} ]
							</option>

                                    @endif

								@endforeach

							@endif

						</select>
					</div>

					<br>

					<div class="form-group">
						<label>Date:</label>
						<input type="date" name="date_certified_2" class="form-control">
					</div>
				</p>
			</td>
		</tr>
	</table>
</form>