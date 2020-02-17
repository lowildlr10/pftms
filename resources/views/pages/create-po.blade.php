<form id="form-create" class="wow animated fadeIn" method="POST"
      action="{{ url('procurement/po-jo/update/' . $po->po_no) }}">
	@csrf

    <div class="row">
        <div class="col-md-6 border">
            <div class="form-group p-2">
                <label>Supplier</label>
                <input class="form-control" type="text" value="{{ $po->company_name }}"
                       disabled="disabled">
            </div>
        </div>
        <div class="col-md-6 border">
            <div class="form-group p-2">
                <label>P.O. Number</label>
                <input name="po_no" id="po-no" class="form-control z-depth-1 required"
                       type="text" value="{{ $po->po_no }}">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 border">
            <div class="form-group p-2">
                <label>Address:</label>
                <textarea class="form-control" style="resize: none;"
                          disabled="disabled">{{ $po->address }}</textarea>
            </div>
        </div>
        <div class="col-md-6 border">
            <div class="form-group p-2">
                <label>Date:</label>
                <input name="date_po" id="date-po" class="form-control z-depth-1 required"
                       type="date" value="{{ $po->date_po }}">
            </div>
        </div>
    </div>

    <div class="row border">
        <div class="col-md-6">
            <div class="form-group p-2">
                <label>TIN:</label>
                <input class="form-control" type="text" value="{{ $po->tin }}"
                       disabled="disabled">
            </div>
        </div>
        <div class="col-md-6"></div>
    </div>

    <div class="row border">
        <div class="col-md-12">
            <p class="p-2">
                Gentlemen: <br>
                Please furnish this Office the following articles subject to the terms and conditions contained herein:
            </p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 border">
            <div class="form-group p-2">
                <label>Place of Delivery:</label>
                <input name="place_delivery" id="place-delivery" class="form-control z-depth-1 required"
                       type="text" value="{{ $po->place_delivery }}">
            </div>
        </div>
        <div class="col-md-6 border">
            <div class="form-group p-2">
                <label>Delivery Term:</label>
                <input name="delivery_term" id="delivery-term" class="form-control z-depth-1 required"
                       type="text" value="{{ $po->delivery_term }}">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 border">
            <div class="form-group p-2">
                <label>Date of Delivery:</label>
                <input name="date_delivery" id="date-delivery" class="form-control z-depth-1 required"
                       type="text" value="{{ $po->date_delivery }}">
            </div>
        </div>
        <div class="col-md-6 border">
            <div class="form-group p-2">
                <label>Payment Term:</label>
                <input name="payment_term" id="payment-term" class="form-control z-depth-1 required"
                       type="text" value="{{ $po->payment_term }}">
            </div>
        </div>
    </div>

    <div class="table-responsive-md table-responsive-sm table-responsive-lg text-nowrap my-3">
        <table id="add-pr-table" class="table" style="width: 100%;">
            <tr>
                <td class="p-0">
                    <table id="item-pr-table" class="table table-bordered table-hover z-depth-1 m-0">
                        <thead class="mdb-color white-text">
                            <tr id="pr-item-header">
                                <th class="hidden-xs" width="5%">Stock/Property No.</th>
                                <th width="11%">Unit</th>
                                <th width="35%">Desciption</th>
                                <th width="8%">Quantity</th>
                                <th width="13%">Unit Cost</th>
                                <th width="13%">Amount</th>
                                <th width="9%" class="pt-1">
                                    <small class="form-text">
                                        <p class="mb-0">
                                            You can move the item to other <br>
                                            PO/JO by selecting different PO/JO number.
                                        </p>
                                    </small>
                                </th>
                                <th width="6%" class="pt-1">
                                    <small class="form-text">
                                        <p class="mb-0">
                                            You can exclude <br>
                                            the item by <br>
                                            selecting 'Yes'
                                        </p>
                                    </small>
                                </th>
                            </tr>
                        </thead>

                        <tbody id="row-items">
                        @if (!empty($poItems))
                            @php $grandTotal = 0; @endphp

                            @foreach ($poItems as $key => $item)
                            <tr>
                                <td class="hidden-xs">
                                    <input type="hidden" name="item_id[]" value="{{ $item->item_id }}" class="item-id">
                                </td>
                                <td>
                                    <select name="unit[]" class="unit browser-default custom-select required">
                                        @if (!empty($unitIssue))
                                            @foreach ($unitIssue as $unit)
                                                @if ($unit->id == $item->unit_issue)
                                        <option value="{{ $unit->id }}" selected="selected">{{ $unit->unit }}</option>
                                                @endif
                                            @endforeach
                                        @endif
                                    </select>
                                </td>
                                <td>
                                    <textarea name="item_description[]" style="resize: none;"
                                            class="item_description form-control required">{{ $item->item_description }}</textarea>
                                </td>
                                <td>
                                    <input id="quantity{{ $key }}" type="number" name="quantity[]" class="quantity form-control required"
                                        onkeyup="$(this).computeCost('{{ $key }}', 'unit_cost{{ $key }}')"
                                        onchange="$(this).computeCost('{{ $key }}', 'unit_cost{{ $key }}')"
                                        min="0" value="{{ $item->quantity }}">
                                </td>
                                <td>
                                    <input id="unit_cost{{ $key }}" type="number" name="unit_cost[]" class="unit-cost form-control required"
                                           onkeyup="$(this).computeCost('{{ $key }}', 'unit_cost{{ $key }}')"
                                           onchange="$(this).computeCost('{{ $key }}', 'unit_cost{{ $key }}')"
                                           min="0" value="{{ $item->unit_cost }}">
                                </td>
                                <td>
                                    <input id="total_cost{{ $key }}" type="number" name="total_cost[]" class="total-cost form-control required"
                                           disabled="disabled" value="{{ $item->total_cost }}">
                                </td>
                                <td>
                                    <select name="po_jo_no[]" class="browser-default custom-select required">
                                        @if ($poJoNumbers->count() > 0)
                                            @foreach ($poJoNumbers as $docNumber)
                                        <option value="{{ $docNumber->po_no }}" {!! ($po->po_no == $docNumber->po_no) ? 'selected' : '' !!}>
                                            {{ $docNumber->po_no }}
                                        </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </td>
                                <td>
                                    <select name="exclude[]" class="exclude browser-default custom-select required"
                                            onchange="$(this).computeCost('{{ $key }}', 'unit_cost{{ $key }}')">
                                        @if (isset($item->excluded))
                                            @if ($item->excluded == 'y' && $item->excluded != 'n')
                                        <option value="y" selected="selected">Yes</option>
                                        <option value="n">No</option>
                                            @else if ($item->excluded != 'y' && $item->excluded == 'n')
                                        <option value="y">Yes</option>
                                        <option value="n" selected="selected">No</option>
                                            @endif
                                        @else
                                        <option value="y">Yes</option>
                                        <option value="n" selected="selected">No</option>
                                        @endif
                                    </select>
                                </td>
                            </tr>
                                @php $grandTotal += ($item->excluded == 'n') ? $item->total_cost : 0; @endphp
                            @endforeach
                        @endif
                            <tr>
                                <td colspan="8"><center>*** Nothing Follows ***</center></td>
                            </tr>
                            <tr>
                                <td colspan="2">(Total Amount in Words)</td>
                                <td colspan="3">
                                    <input id="amount-words" name="amount_words" class="form-control required"
                                        type="text" value="{{ $po->amount_words }}"
                                        placeholder="Input amount in words...">
                                </td>
                                <td colspan="3">
                                    <input id="grand-total" name="grand_total" class="form-control required"
                                        type="number" value="{{ $grandTotal }}">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>

    </div>

    <div class="row border">
        <div class="col-md-12">
            <p class="p-2">
                In case of failure to make the full delivery within time specified above,
                a penalty of one-tenth (1/10) of one percent for every delay shall be imposed.
            </p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 border">
            <div class="form-group p-2">
                <label>Chief Accountant/ Head of Accounting Division/Unit:</label>
                <select id="sig-funds-available" class="browser-default custom-select z-depth-1 required"
                        name="sig_funds_available">
					<option value=""> -- Select a signatory -- </option>

					@if (!empty($signatories))
						@foreach ($signatories as $signatory)
                            @if ($signatory->po_jo_sign_type == 'accountant')
							    @if ($signatory->id == $po->sig_funds_available)
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
        </div>
        <div class="col-md-6 border">
            <div class="form-group p-2">
                <label>Very Truly Yours:</label>
                <select id="sig-approval" class="browser-default custom-select z-depth-1 required"
                        name="sig_approval">
                    <option value=""> -- Select a sigantory -- </option>

					@if (!empty($signatories))
						@foreach ($signatories as $signatory)
                            @if ($signatory->po_jo_sign_type == 'approval')
							    @if ($signatory->id == $po->sig_approval)
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
        </div>
    </div>

    <input type="hidden" name="type" value="po">
    <input type="hidden" name="pr_id" value="{{ $po->pr_id }}">
</form>
