<form id="form-create" class="wow animated fadeIn" method="POST"
      action="{{ url('procurement/po-jo/update/' . $jo->po_no) }}">
	@csrf

    <div class="row border">
        <div class="col-md-6">
            <div class="form-group p-2">
                <label>Job Order No:</label>
                <input name="po_no" id="po-no" class="form-control z-depth-1 required"
                       type="text" value="{{ $jo->po_no }}">
            </div>
            <div class="form-group p-2">
                <label>Date:</label>
                <input name="date_po" class="form-control z-depth-1 required"
                       type="date" value="{{ $jo->date_po }}">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group p-2">
                <label>To:</label>
                <input class="form-control required" type="text" value="{{ $jo->company_name }}"
                       disabled="disabled">
            </div>
            <div class="form-group p-2">
                <label>Address:</label>
                <textarea style="resize: none;" class="form-control"
                          disabled="disabled">{{ $jo->address }}</textarea>
            </div>
        </div>
        <div class="col-md-12 border">
            <p class="p-2">
                Sir/Madam: <br>
                In connection with the existing regulations, you are
                hereby authorized to undertake the indicated job/work below:
            </p>
        </div>
    </div>

    <div class="table-responsive-md table-responsive-sm table-responsive-lg text-nowrap my-3">
        <table id="add-pr-table" class="table" style="width: 100%;">
            <tr>
                <td colspan="4" class="rgba-blue-grey-light p-0">
                    <table id="item-pr-table" class="table table-bordered table-hover z-depth-1 m-0">
                        <thead class="mdb-color white-text">
                            <tr>
                                <th align="center" width="10%">Quantity</th>
                                <th align="center" width="8%">Unit</th>
                                <th align="center" width="45%">Job/Work Desctiption</th>
                                <th align="center" width="10%">Amount</th>
                                <th align="center" width="9%" class="pt-1">
                                    <small class="form-text">
                                        <p class="mb-0">
                                            You can move the item to other <br>
                                            PO/JO by selecting different PO/JO number.
                                        </p>
                                    </small>
                                </th>
                                <th align="center" width="6%" class="pt-1">
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

                        <tbody>
                            @if (!empty($joItems))
                                @php $grandTotal = 0; @endphp

                                @foreach ($joItems as $key => $item)
                            <tr>
                                <td>
                                    <input type="hidden" name="item_id[]" value="{{ $item->item_id }}">
                                    <input id="quantity{{ $key }}" type="number" name="quantity[]" class="form-control required"
                                        min="0" value="{{ $item->quantity }}">
                                </td>
                                <td>
                                    <select name="unit[]" class="browser-default custom-select required">
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
                                    <textarea name="item_description[]" class="form-control required" rows="4"
                                              style="resize: none;">{{ $item->item_description }}</textarea>
                                </td>
                                <td>
                                    <input id="total_cost{{ $key }}" type="number" name="total_cost[]" class="form-control required"
                                        onkeyup="$(this).computeCost('{{ $key }}', 'unit_cost{{ $key }}', 0)"
                                        onchange="$(this).computeCost('{{ $key }}', 'unit_cost{{ $key }}', 0)"
                                        value="{{ $item->total_cost }}">
                                </td>
                                <td>
                                    <select name="po_jo_no[]" class="browser-default custom-select required">
                                        @if ($poJoNumbers->count() > 0)
                                            @foreach ($poJoNumbers as $docNumber)
                                        <option value="{{ $docNumber->po_no }}" {!! ($jo->po_no == $docNumber->po_no) ? 'selected' : '' !!}>
                                            {{ $docNumber->po_no }}
                                        </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </td>
                                <td>
                                    <select name="exclude[]" class="browser-default custom-select required">

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
                                @php $grandTotal += $item->total_cost; @endphp
                                @endforeach
                            <tr>
                                <td colspan="5"><center> *** Nothing Follows *** </center></td>
                            </tr>
                            @endif

                            <tr>
                                <td colspan="3"><center><label>Total Amount</label></center></td>
                                <td colspan="3">
                                    <input name="grand_total" class="form-control required" type="number" value="{{ $grandTotal }}">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    <div class="row border">
        <div class="col-md-6">
            <div class="form-group p-2">
                <label>Place of Delivery:</label>
                <input name="place_delivery" class="form-control z-depth-1 required"
                       type="text" value="{{ $jo->place_delivery }}">
            </div>
        </div>
        <div class="col-md-6"></div>
    </div>

    <div class="row border">
        <div class="col-md-6">
            <div class="form-group p-2">
                <label>Date of Delivery:</label>
                <input name="date_delivery" class="form-control z-depth-1 required"
                       type="text" value="{{ $jo->date_delivery }}">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group p-2">
                <label>Payment Term:</label>
                <input name="payment_term" class="form-control z-depth-1 required"
                       type="text" value="{{ $jo->payment_term }}">
            </div>
        </div>
    </div>

    <div class="row border">
        <div class="col-md-12">
            <p align="center" class="p-2">
                This order is authorized by the DEPARTMENT OF SCIENCE AND TECHNOLOGY, Cordillera Administrative Region <br>
                under DR. NANCY A. BANTOG, Regional Director in the amount not to exceed <br><br>
                <input name="amount_words" class="form-control z-depth-1 required"
                    type="text" value="{{ $jo->amount_words }}"
                    placeholder="Enter grand total amount in words..."><br>
                <strong>
                    <em>
                        In case of failure to make the full delivery within time specified above, a penalty
                        of one-tenth (1/10) of one <br>
                        percent for everyday of delay shall be imposed.
                    </em>
                </strong>
                <br>
                lease submit your bill together with the original of this JOB/WORK ORDER to expedite payment.
            </p>
            <p>Very truly yours,</p>
        </div>
    </div>

    <div class="row border">
        <div class="col-md-6">
            <div class="form-group p-2">
                <label>Requisitioning Office/Dept.:</label>
                <select class="browser-default custom-select z-depth-1 required"
                        name="sig_department">
                    <option value=""> -- Select a signatory -- </option>

                    @if (!empty($signatories))
                        @foreach ($signatories as $signatory)
                            @if ($signatory->po_jo_sign_type == 'requisitioning')
                                @if ($signatory->id == $jo->sig_department)
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
            <div class="form-group p-2">
                <label>Funds Available:</label>
                <select class="browser-default custom-select z-depth-1 required"
                        name="sig_funds_available">
                    <option value=""> -- Select a signatory -- </option>

                    @if (!empty($signatories))
                        @foreach ($signatories as $signatory)
                            @if ($signatory->po_jo_sign_type == 'accountant')
                                @if ($signatory->id == $jo->sig_funds_available)
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
        <div class="col-md-6">
            <div class="form-group p-2">
                <label>Approved:</label>
                <select class="browser-default custom-select z-depth-1 required"
                        name="sig_approval">
                    <option value=""> -- Select a signatory -- </option>

                    @if (!empty($signatories))
                        @foreach ($signatories as $signatory)
                            @if ($signatory->po_jo_sign_type == 'approval')
                                @if ($signatory->id == $jo->sig_approval)
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

    <div class="row border">

    </div>

    <input type="hidden" name="type" value="jo">
	<input type="hidden" name="pr_id" value="{{ $jo->pr_id }}">
</form>
