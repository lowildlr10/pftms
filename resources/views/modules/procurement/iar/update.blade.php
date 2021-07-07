<form id="form-update" class="wow animated fadeIn" method="POST" action="{{ route('iar-update', ['id' => $id]) }}">
    @csrf

    <div class="row">
        <div class="col-md-6">
            <div class="md-form">
                <input type="text" id="supplier" class="form-control form-sm"
                       value="{{ $awardee }}" readonly>
                <label for="supplier" class="{{ !empty($awardee) ? 'active' : '' }}">
                    Supplier
                </label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="md-form">
                <input type="text" id="iar-no" class="form-control form-sm"
                       value="{{ $iarNo }}" readonly>
                <label for="iar-no" class="{{ !empty($iarNo) ? 'active' : '' }}">
                    IAR No
                </label>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="md-form">
                <input type="text" id="po-no" class="form-control form-sm"
                       value="{{ $poDate }}" readonly>
                <label for="po-no" class="{{ !empty($poDate) ? 'active' : '' }}">
                    PO No./Date
                </label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="md-form">
                <input type="date" id="date-iar" class="form-control form-sm required required"
                       value="{{ $iarDate }}" name="date_iar">
                <label for="date-iar" class="active">
                    Date <span class="red-text">*</span>
                </label>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="md-form">
                <input type="text" id="po-no" class="form-control form-sm"
                       value="{{ $division }}" readonly>
                <label for="po-no" class="{{ !empty($division) ? 'active' : '' }}">
                    Requisitioning Office/Dept.
                </label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="md-form">
                <input type="text" id="invoice-no" class="form-control form-sm"
                       value="{{ $invoiceNo }}" name="invoice_no">
                <label for="invoice-no" class="{{ !empty($invoiceNo) ? 'active' : '' }}">
                    Invoice No
                </label>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="md-form">
                <input type="text" id="responsiblity-no" class="form-control form-sm"
                       value="19 001 03000 14" readonly>
                <label for="responsiblity-no" class="active">
                    Responsibility Center Code
                </label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="md-form">
                <input type="date" id="date-invoice" class="form-control form-sm"
                       value="{{ $invoiceDate }}" name="date_invoice">
                <label for="date-invoice" class="active">
                    Date
                </label>
            </div>
        </div>
    </div>

    <div class="table-responsive-md table-responsive-sm table-responsive-lg my-1">
        <table id="add-pr-table" class="table border" style="width: 100%;">
            <thead class="mdb-color white-text">
                <tr>
                    <th width="15%" class="text-center">
                        Stock/Property No.
                    </th>
                    <th width="52%" class="text-center">
                        Description
                    </th>
                    <th width="10%" class="text-center">
                        Unit
                    </th>
                    <th width="13%" class="text-center">
                        Quantity
                    </th>
                    <th width="10%" class="text-center">
                        Excluded? <span class="red-text">*</span>
                    </th>
                </tr>
            </thead>

            <tbody>
                @if (count($poItem) > 0)
                    @foreach ($poItem as $item)
                <tr>
                    <td>
                        {{ $item->stock_no }}
                    </td>
                    <td>
                        {{ $item->item_description }}
                    </td>
                    <td class="text-center">
                        {{ $item->unitissue['unit_name'] }}
                    </td>
                    <td class="text-center">
                        {{ $item->quantity }}
                    </td>
                    <td>
                        <div class="md-form">
                            <input type="hidden" name="po_item_id[]" value="{{ $item->id }}">
                            <select class="mdb-select crud-select md-form required"
                                    name="iar_excluded[]">
                                <option value="y" {{ $item->iar_excluded == 'y' ? 'selected' : ''}}>
                                    Yes
                                </option>
                                <option value="n" {{ $item->iar_excluded == 'n' ? 'selected' : ''}}>
                                    No
                                </option>
                            </select>
                        </div>
                    </td>
                </tr>
                    @endforeach
                @endif

                <tr>
                    <td class="text-center" colspan="5">
                        *** Nothing Follows ***
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="row mt-5">
        <div class="col-md-6">
            <div class="md-form">
                <select class="mdb-select crud-select md-form required" searchable="Search here.."
                        name="sig_inspection">
                    <option value="" disabled selected>Choose a signatory</option>

                    @if (count($signatories) > 0)
                        @foreach ($signatories as $sig)
                            @if ($sig->module->iar->inspection)
                    <option value="{{ $sig->id }}" {{ $sig->id == $sigInspection ? 'selected' : '' }}>
                        {!! $sig->name !!} [{!! $sig->module->iar->designation !!}]
                    </option>
                            @endif
                        @endforeach
                    @endif
                </select>
                <label class="mdb-main-label">
                    Inspection Officer/Inspection Committee <span class="red-text">*</span>
                </label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="md-form">
                <select class="mdb-select crud-select md-form required" searchable="Search here.."
                        name="sig_prop_supply">
                    <option value="" disabled selected>Choose a signatory</option>

                    @if (count($signatories) > 0)
                        @foreach ($signatories as $sig)
                            @if ($sig->module->iar->prop_supply)
                    <option value="{{ $sig->id }}" {{ $sig->id == $sigSupply ? 'selected' : '' }}>
                        {!! $sig->name !!} [{!! $sig->module->iar->designation !!}]
                    </option>
                            @endif
                        @endforeach
                    @endif
                </select>
                <label class="mdb-main-label">
                    Supply and/or Property Custodian <span class="red-text">*</span>
                </label>
            </div>
        </div>
    </div>
</form>
