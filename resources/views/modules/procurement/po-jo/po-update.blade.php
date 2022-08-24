<form id="form-update" class="wow animated fadeIn" method="POST" action="{{ route('po-jo-update', ['id' => $id]) }}">
    @csrf

    <div class="row">
        <div class="col-md-6">
            <div class="md-form">
                <select class="mdb-select crud-select md-form colorful-select dropdown-dark required"
                        data-stop-refresh="true" searchable="Search here.." name="awarded_to">
                    @foreach ($awardees as $award)
                    <option value="{{ $award->id }}" {{ $award->id == $awardedTo ? 'selected' : '' }}>
                        {{ $award->company_name }}
                    </option>
                    @endforeach
                </select>
                <label class="mdb-main-label active">
                    Supplier <span class="red-text">*</span>
                </label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="md-form">
                <input type="text" id="po-no" class="form-control form-sm"
                       value="{{ $poNo }}" readonly>
                <label for="po-no" class="{{ !empty($poNo) ? 'active' : '' }}">
                    P.O. No.
                </label>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="md-form">
                <input type="text" id="company-address" class="form-control form-sm"
                       value="{{ $companyAddress }}" readonly>
                <label for="company-address" class="{{ !empty($companyAddress) ? 'active' : '' }}">
                    Address
                </label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="md-form">
                <input type="date" id="po-date" class="form-control form-sm required"
                       name="date_po" value="{{ $poDate }}">
                <label for="po-date" class="{{ !empty($poDate) ? 'active' : '' }}">
                    Date <span class="red-text">*</span>
                </label>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="md-form">
                <input type="text" id="company-tin" class="form-control form-sm"
                       value="{{ $companyTinNo }}" readonly>
                <label for="company-tin" class="{{ !empty($companyTinNo) ? 'active' : '' }}">
                    TIN
                </label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="md-form">
                <input type="text" id="mode-procurement" class="form-control form-sm"
                       value="{{ $modeProcurement }}" readonly>
                <label for="mode-procurement" class="{{ !empty($modeProcurement) ? 'active' : '' }}">
                    Mode of Procurement
                </label>
            </div>
        </div>
    </div>

    <hr>

    <div class="row">
        <div class="col-md-12">
            <p>
                Gentlemen:<br><br>
                Please furnish this Office the following articles subject to the terms and conditions contained herein:
            </p>
        </div>
    </div>

    <hr>

    <div class="row">
        <div class="col-md-6">
            <div class="md-form">
                <input type="text" id="place-delivery" class="form-control form-sm required"
                       value="{{ $placeDelivery }}" name="place_delivery">
                <label for="place-delivery" class="{{ !empty($placeDelivery) ? 'active' : '' }}">
                    Place of Delivery <span class="red-text">*</span>
                </label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="md-form">
                <input type="text" id="delivery-term" class="form-control form-sm required"
                       value="{{ $deliveryTerm }}" name="delivery_term">
                <label for="delivery-term" class="{{ !empty($deliveryTerm) ? 'active' : '' }}">
                    Delivery Term <span class="red-text">*</span>
                </label>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="md-form">
                <input type="text" id="date-delivery" class="form-control form-sm required"
                       value="{{ $dateDelivery }}" name="date_delivery">
                <label for="date-delivery" class="{{ !empty($dateDelivery) ? 'active' : '' }}">
                    Date of Delivery <span class="red-text">*</span>
                </label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="md-form">
                <input type="text" id="payment-term" class="form-control form-sm required"
                       value="{{ $paymentTerm }}" name="payment_term">
                <label for="payment-term" class="{{ !empty($paymentTerm) ? 'active' : '' }}">
                    Payment Term <span class="red-text">*</span>
                </label>
            </div>
        </div>
    </div>

    <div class="table-responsive-md table-responsive-sm table-responsive-lg text-nowrap my-3">
        <table id="add-pr-table" class="table border" style="width: 100%;">
            <tr>
                <td class="p-0">
                    <table id="item-pr-table" class="table z-depth-1 m-0">
                        <thead class="mdb-color white-text">
                            <tr id="pr-item-header">
                                <th class="text-center" style="vertical-align: middle;" width="5%">
                                    Stock/Property No.
                                </th>
                                <th class="text-center" style="vertical-align: middle;" width="11%">
                                    Unit <span class="red-text">*</span>
                                </th>
                                <th class="text-center" style="vertical-align: middle;" width="35%">
                                    Desciption <span class="red-text">*</span>
                                </th>
                                <th class="text-center" style="vertical-align: middle;" width="8%">
                                    Quantity <span class="red-text">*</span>
                                </th>
                                <th class="text-center" style="vertical-align: middle;" width="13%">
                                    Unit Cost <span class="red-text">*</span>
                                </th>
                                <th class="text-center" style="vertical-align: middle;" width="13%">
                                    Amount <span class="red-text">*</span>
                                </th>
                                <th class="text-center" style="vertical-align: middle;" width="9%" class="pt-1">
                                    <small>
                                        You can move the item to other <br>
                                        PO/JO by selecting different <br>
                                        PO/JO number.
                                    </small> <span class="red-text">*</span>
                                </th>
                                <th class="text-center" style="vertical-align: middle;" width="6%" class="pt-1">
                                    <small>
                                        You can exclude <br>
                                        the item by <br>
                                        selecting 'Yes'
                                    </small> <span class="red-text">*</span>
                                </th>
                            </tr>
                        </thead>

                        <tbody id="row-items">
                        @if (count($poItems) > 0)
                            @php $grandTotal = 0; @endphp

                            @foreach ($poItems as $key => $item)
                            <tr>
                                <td class="hidden-xs">
                                    <input type="hidden" name="item_id[]" value="{{ $item->id }}" class="item-id">
                                </td>
                                <td>
                                    <div class="md-form my-0 py-0">
                                        <select class="mdb-select crud-select md-form required" searchable="Search here.."
                                                name="unit[]">
                                            @if (count($unitIssues) > 0)
                                                @foreach ($unitIssues as $unit)
                                            <option value="{{ $unit->id }}" {{ $unit->id == $item->unit_issue ? 'selected' : '' }}>
                                                {!! $unit->unit_name !!}
                                            </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <div class="md-form my-0 py-0">
                                        <textarea class="md-textarea form-control required" placeholder="Item description..."
                                                  name="item_description[]" rows="1">{{ $item->item_description }}</textarea>
                                    </div>
                                </td>
                                <td>
                                    <div class="md-form">
                                        <input type="number" id="quantity{{ $key }}"
                                               name="quantity[]" class="quantity form-control required"
                                               onkeyup="$(this).computeCost('{{ $key }}', 'unit_cost{{ $key }}')"
                                               onchange="$(this).computeCost('{{ $key }}', 'unit_cost{{ $key }}')"
                                               min="0" placeholder="0" value="{{ $item->quantity }}">
                                    </div>
                                </td>
                                <td>
                                    <div class="md-form ">
                                        <input type="number" id="unit_cost{{ $key }}"
                                               name="unit_cost[]" class="unit-cost form-control required"
                                               onkeyup="$(this).computeCost('{{ $key }}', 'unit_cost{{ $key }}')"
                                               onchange="$(this).computeCost('{{ $key }}', 'unit_cost{{ $key }}')"
                                               min="0" placeholder="0.00" value="{{ $item->unit_cost }}">
                                    </div>
                                </td>
                                <td>
                                    <div class="md-form ">
                                        <input id="total_cost{{ $key }}" type="number" name="total_cost[]"
                                               class="total-cost form-control required" placeholder="0.00"
                                               value="{{ $item->total_cost }}" readonly>
                                    </div>
                                </td>
                                <td>
                                    <div class="md-form my-0 py-0">
                                        <select class="mdb-select crud-select md-form required" searchable="Search here.."
                                                name="po_jo_no[]">
                                            @if ($poNumbers->count() > 0)
                                                @foreach ($poNumbers as $docNumber)
                                            <option value="{{ $docNumber->po_no }}" {!! ($docNumber->po_no == $poNo) ? 'selected' : '' !!}>
                                                {{ $docNumber->po_no }}
                                            </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <div class="md-form my-0 py-0">
                                        <select class="exclude mdb-select crud-select md-form required" searchable="Search here.."
                                                name="exclude[]" onchange="$(this).computeCost('{{ $key }}', 'unit_cost{{ $key }}')">
                                            <option value="y" {{ $item->excluded == 'y'? 'selected' : '' }}>Yes</option>
                                            <option value="n" {{ $item->excluded == 'n'? 'selected' : '' }}>No</option>
                                        </select>
                                    </div>
                                </td>
                            </tr>
                                @php $grandTotal += ($item->excluded == 'n') ? $item->total_cost : 0; @endphp
                            @endforeach
                        @endif
                            <tr>
                                <td align="center" colspan="8">*** Nothing Follows ***</td>
                            </tr>
                            <tr>
                                <td colspan="2">(Total Amount in Words)</td>
                                <td colspan="3">
                                    <div class="md-form">
                                        <input type="text" id="amount-words" class="form-control form-sm required"
                                               value="{{ $amountWords }}" name="amount_words">
                                        <label for="amount-words" class="{{ !empty($amountWords) ? 'active' : '' }}">
                                            Amount in Words <span class="red-text">*</span>
                                        </label>
                                    </div>
                                </td>
                                <td colspan="3">
                                    <div class="md-form">
                                        <input type="number" id="grand-total" class="form-control form-sm required"
                                               value="{{ $grandTotal }}" name="grand_total">
                                        <label for="grand-total" class="active">
                                            Grand Total <span class="red-text">*</span>
                                        </label>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    <div class="row mt-5">
        <div class="col-md-6">
            <div class="md-form">
                <div class="md-form">
                    <select class="mdb-select crud-select md-form required" searchable="Search here.."
                            name="sig_funds_available">
                        <option value="" disabled selected>Choose a signatory</option>

                        @if (count($signatories) > 0)
                            @foreach ($signatories as $sig)
                                @if ($sig->module->po->funds_available)
                        <option value="{{ $sig->id }}" {{ $sig->id == $sigFundsAvailable ? 'selected' : '' }}>
                            {!! $sig->name !!} [{!! $sig->module->po->designation !!}]
                        </option>
                                @endif
                            @endforeach
                        @endif
                    </select>
                    <label class="mdb-main-label">
                        Chief Accountant/ Head of Accounting Division/Unit <span class="red-text">*</span>
                    </label>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="md-form">
                <div class="md-form">
                    <select class="mdb-select crud-select md-form required" searchable="Search here.."
                            name="sig_approval">
                        <option value="" disabled selected>Choose a signatory</option>

                        @if (count($signatories) > 0)
                            @foreach ($signatories as $sig)
                                @if ($sig->module->po->approved)
                        <option value="{{ $sig->id }}" {{ $sig->id == $sigApproval ? 'selected' : '' }}>
                            {!! $sig->name !!} [{!! $sig->module->po->designation !!}]
                        </option>
                                @endif
                            @endforeach
                        @endif
                    </select>
                    <label class="mdb-main-label">
                        Regional Director or Authorized Representative <span class="red-text">*</span>
                    </label>
                </div>
            </div>
        </div>
    </div>
</form>
