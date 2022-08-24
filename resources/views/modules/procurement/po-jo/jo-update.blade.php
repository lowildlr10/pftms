<form id="form-update" class="wow animated fadeIn" method="POST" action="{{ route('po-jo-update', ['id' => $id]) }}">
    @csrf

    <div class="row">
        <div class="col-md-6">
            <div class="md-form">
                <input type="text" id="po-no" class="form-control form-sm"
                       value="{{ $poNo }}" readonly>
                <label for="po-no" class="{{ !empty($poNo) ? 'active' : '' }}">
                    JOB ORDER NO
                </label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="md-form">
                {{--
                <input type="text" id="company-name" class="form-control form-sm"
                       value="{{ $companyName }}" readonly>
                <label for="company-name" class="{{ !empty($companyName) ? 'active' : '' }}">
                    TO
                </label>
                --}}

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
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="md-form">
                <input type="date" id="po-date" class="form-control form-sm required"
                       name="date_po" value="{{ $poDate }}">
                <label for="po-date" class="{{ !empty($poDate) ? 'active' : '' }}">
                    DATE <span class="red-text">*</span>
                </label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="md-form">
                <input type="text" id="company-address" class="form-control form-sm"
                       value="{{ $companyAddress }}" readonly>
                <label for="company-address" class="{{ !empty($companyAddress) ? 'active' : '' }}">
                    ADDRESS
                </label>
            </div>
        </div>
    </div>

    <hr>

    <div class="row">
        <div class="col-md-12">
            <p>
                Sir/Madam:<br><br>
                In connection with the existing regulations, you are hereby authorized to undertake the indicated job/work below:
            </p>
        </div>
    </div>

    <hr>

    <div class="table-responsive-md table-responsive-sm table-responsive-lg text-nowrap my-3">
        <table id="add-pr-table" class="table border" style="width: 100%;">
            <tr>
                <td class="p-0">
                    <table id="item-pr-table" class="table z-depth-1 m-0">
                        <thead class="mdb-color white-text">
                            <tr id="pr-item-header">
                                <th class="text-center" style="vertical-align: middle;" width="8%">
                                    Quantity <span class="red-text">*</span>
                                </th>
                                <th class="text-center" style="vertical-align: middle;" width="11%">
                                    Unit <span class="red-text">*</span>
                                </th>
                                <th class="text-center" style="vertical-align: middle;" width="40%">
                                    Desciption <span class="red-text">*</span>
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
                                    </small>
                                </th>
                                <th class="text-center" style="vertical-align: middle;" width="6%" class="pt-1">
                                    <small>
                                        You can exclude <br>
                                        the item by <br>
                                        selecting 'Yes'
                                    </small>
                                </th>
                            </tr>
                        </thead>

                        <tbody id="row-items">
                        @if (count($poItems) > 0)
                            @php $grandTotal = 0; @endphp

                            @foreach ($poItems as $key => $item)
                            <tr>
                                <td>
                                    <input type="hidden" name="item_id[]" value="{{ $item->id }}" class="item-id">
                                    <div class="md-form">
                                        <input type="number" id="quantity{{ $key }}"
                                               name="quantity[]" class="quantity form-control required"
                                               onkeyup="$(this).computeCost('{{ $key }}', 'unit_cost{{ $key }}')"
                                               onchange="$(this).computeCost('{{ $key }}', 'unit_cost{{ $key }}')"
                                               min="0" placeholder="0" value="{{ $item->quantity }}">
                                    </div>
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
                                <td colspan="4" class="text-center" style="vertical-align: middle;">
                                    <strong class="font-weight-bold">
                                        <em>TOTAL AMOUNT</em>
                                    </strong>
                                </td>
                                <td colspan="4">
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
        <div class="col-md-6"></div>
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

    <hr>

    <div class="row">
        <div class="col-md-12">
            <p class="text-center">
                This order is authorized by the DEPARTMENT OF SCIENCE AND TECHNOLOGY, Cordillera Administrative
                Region under DR. NANCY A. BANTOG, Regional Director in the amount not to exceed
            </p>
            <div class="md-form">
                <input type="text" id="amount-words" class="form-control form-sm required"
                       value="{{ $amountWords }}" name="amount_words">
                <label for="amount-words" class="{{ !empty($amountWords) ? 'active' : '' }}">
                    Amount in Words <span class="red-text">*</span>
                </label>
            </div>
            <p class="text-center">
                The cost of this WORK ORDER will be charged against DOST-CAR after work has been completed.
                <br><br>
                <strong class="font-weight-bold">
                    In case of failure to make the full delivery within time specified above, a penalty of one-tenth (1/10) of one
                    percent for everyday of delay shall be imposed.
                </strong><br><br>
                Please submit your bill together with the original of this JOB/WORK ORDER to expedite payment
            </p>
        </div>
    </div>

    <hr>

    <div class="row mt-4">
        <div class="col-md-12">
            <h6>
                <em>Very truly yours,</em>
            </h6>
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-md-6">
            <div class="md-form">
                <div class="md-form">
                    <select class="mdb-select crud-select md-form required" searchable="Search here.."
                            name="sig_department">
                        <option value="" disabled selected>Choose a signatory</option>

                        @if (count($signatories) > 0)
                            @foreach ($signatories as $sig)
                                @if ($sig->module->jo->requisitioning)
                        <option value="{{ $sig->id }}" {{ $sig->id == $sigDepartment ? 'selected' : '' }}>
                            {!! $sig->name !!} [{!! $sig->module->jo->designation !!}]
                        </option>
                                @endif
                            @endforeach
                        @endif
                    </select>
                    <label class="mdb-main-label">
                        Requisitioning Office/Dept <span class="red-text">*</span>
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
                                @if ($sig->module->jo->approved)
                        <option value="{{ $sig->id }}" {{ $sig->id == $sigApproval ? 'selected' : '' }}>
                            {!! $sig->name !!} [{!! $sig->module->jo->designation !!}]
                        </option>
                                @endif
                            @endforeach
                        @endif
                    </select>
                    <label class="mdb-main-label">
                        APPROVED <span class="red-text">*</span>
                    </label>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="md-form">
                <div class="md-form">
                    <select class="mdb-select crud-select md-form required" searchable="Search here.."
                            name="sig_funds_available">
                        <option value="" disabled selected>Choose a signatory</option>

                        @if (count($signatories) > 0)
                            @foreach ($signatories as $sig)
                                @if ($sig->module->jo->funds_available)
                        <option value="{{ $sig->id }}" {{ $sig->id == $sigFundsAvailable ? 'selected' : '' }}>
                            {!! $sig->name !!} [{!! $sig->module->jo->designation !!}]
                        </option>
                                @endif
                            @endforeach
                        @endif
                    </select>
                    <label class="mdb-main-label">
                        Funds Available <span class="red-text">*</span>
                    </label>
                </div>
            </div>
        </div>
        <div class="col-md-6"></div>
    </div>
</form>
