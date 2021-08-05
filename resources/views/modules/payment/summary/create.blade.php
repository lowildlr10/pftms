<form id="form-store" class="wow animated fadeIn d-flex justify-content-center" method="POST"
      action="{{ route('summary-store') }}">
    @csrf
    <div class="card w-responsive">
        <div class="card-body py-1">
            <div class="row">
                <div class="col-md-12">
                    <div class="md-form row">
                        <div class="col-md-12 text-center">
                            <b class="h5">
                                SUMMARY OF LDDAP-ADAs ISSUED AND INVALIDATED ADA ENTRIES
                            </b>
                            <div class="md-form">
                                <select class="mdb-select crud-select md-form required" searchable="Search here.."
                                        name="mds_gsb_id">
                                    <option value="" disabled selected>Choose a MDS account number</option>

                                    @if (count($mdsGSBs) > 0)
                                        @foreach ($mdsGSBs as $mdsGSB)
                                    <option value="{{ $mdsGSB->id }}">
                                        {!! $mdsGSB->sub_account_no !!}
                                    </option>
                                        @endforeach
                                    @endif
                                </select>
                                <label class="mdb-main-label">
                                    <span class="red-text">* </span>
                                    <b>For MDS Account Number</b>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-5">
                    <div class="md-form form-sm">
                        <input type="text" id="department" name="department"
                               class="form-control form-control-sm required" value="DOST-CAR">
                        <label for="department" class="active">
                            <span class="red-text">* </span>
                            <b>Department</b>
                        </label>
                    </div>
                    <div class="md-form form-sm">
                        <input type="text" id="entity-name" name="entity_name"
                               class="form-control form-control-sm required" value="DOST-CAR">
                        <label for="entity-name" class="active">
                            <span class="red-text">* </span>
                            <b>Entity Name</b>
                        </label>
                    </div>
                    <div class="md-form form-sm">
                        <input type="text" id="operating-unit" name="operating_unit"
                               class="form-control form-control-sm required" value="DOST-CAR">
                        <label for="operating-unit" class="active">
                            <span class="red-text">* </span>
                            <b>Operating Unit</b>
                        </label>
                    </div>
                </div>
                <div class="col-md-2"></div>
                <div class="col-md-5">
                    <div class="md-form form-sm">
                        <input type="text" id="fund-cluster" name="fund_cluster"
                               class="form-control form-control-sm required" value="01">
                        <label for="fund-cluster" class="active">
                            <span class="red-text">* </span>
                            <b>Fund Cluster</b>
                        </label>
                    </div>
                    <div class="md-form form-sm">
                        <input type="text" id="sliiae-no" name="sliiae_no"
                               class="form-control form-control-sm required" value="{{ $sliiaeNo }}">
                        <label for="sliiae-no" class="active">
                            <span class="red-text">* </span>
                            <b>SLIIAE No.</b>
                        </label>
                    </div>
                    <div class="md-form form-sm">
                        <input type="date" id="sliiae-date" name="sliiae_date"
                               class="form-control form-control-sm required"
                               value="{{ \Carbon\Carbon::today()->toDateString() }}">
                        <label for="sliiae-date" class="active">
                            <span class="red-text">* </span>
                            <b>Date</b>
                        </label>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-5">
                    <div class="md-form form-sm">
                        <input type="text" id="to" name="to"
                               class="form-control form-control-sm required">
                        <label for="to">
                            <span class="red-text">* </span>
                            <b>To</b>
                        </label>
                    </div>
                    <div class="md-form form-sm">
                        <div class="md-form">
                            <textarea id="bank-name" rows="1" name="bank_name"
                                      class="md-textarea form-control form-control-sm required"
                            >Land Bank of the Philippines&#13;&#10;La Trinidad Branch</textarea>
                            <label for="bank-name" class="active">
                                <span class="red-text">* </span>
                                <b>Name of Bank</b>
                            </label>
                        </div>
                    </div>
                    <div class="md-form form-sm">
                        <div class="md-form">
                            <textarea id="bank-address" rows="1" name="bank_address"
                                      class="md-textarea form-control form-control-sm required"
                                >Km.5, La Trinidad, Benguet</textarea>
                            <label for="bank-address" class="active">
                                <span class="red-text">* </span>
                                <b>Address of Bank</b>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="col-md-7"></div>
            </div>

            <hr>
            <div class="row">
                <div class="col-md-12 px-0 table-responsive border border-dark">
                    <table class="table table-sm table-hover table-bordered">
                        <thead class="text-center">
                            <tr>
                                <th rowspan="3" class="align-middle" width="10%">
                                    <b>LDDAP-ADA No.</b>
                                </th>
                                <th rowspan="3" class="align-middle" width="8%">
                                    <b>Date of Issue</b>
                                </th>
                                <th colspan="5" class="align-middle" width="45%">
                                    <b>Amount</b>
                                </th>
                                <th class="align-middle" width="19%">
                                    <b>For GSB Use Only</b>
                                </th>
                                <th rowspan="3" width="3%"></th>
                            </tr>
                            <tr>
                                <th rowspan="2" class="align-middle" width="12%">
                                    Total
                                </th>
                                <th colspan="4" class="align-middle" width="48%">
                                    Allotment/Object Class
                                </th>
                                <th rowspan="2" class="align-middle" width="19%">
                                    Remarks
                                </th>
                            </tr>
                            <tr>
                                <th class="align-middle" width="9%">
                                    PS
                                </th>
                                <th class="align-middle" width="9%">
                                    MOOE
                                </th>
                                <th class="align-middle" width="9%">
                                    CO
                                </th>
                                <th class="align-middle" width="9%">
                                    FE
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr id="item-row-1" class="item-row">
                                <td>
                                    <div class="md-form form-sm my-0">
                                        <select class="mdb-select required lddap-tokenizer"
                                                name="lddap_id[]"></select>
                                    </div>
                                </td>
                                <td>
                                    <div class="md-form form-sm my-0">
                                        <input type="date" name="date_issue[]"
                                               class="form-control required form-control-sm date-issue"
                                               id="date-issue-0">
                                    </div>
                                </td>
                                <td>
                                    <div class="md-form form-sm my-0">
                                        <input type="number" name="total[]"
                                               class="form-control required form-control-sm total"
                                               id="total-0" min="0"
                                               onkeyup="$(this).computeAll()"
                                               onchange="$(this).computeAll()">
                                    </div>
                                </td>
                                <td>
                                    <div class="md-form form-sm my-0">
                                        <input type="number" name="allotment_ps[]"
                                               class="form-control required form-control-sm allotment-ps"
                                               id="allotment-ps-0" min="0"
                                               onkeyup="$(this).computeAll()"
                                               onchange="$(this).computeAll()">
                                    </div>
                                </td>
                                <td>
                                    <div class="md-form form-sm my-0">
                                        <input type="number" name="allotment_mooe[]"
                                               class="form-control required form-control-sm allotment-mooe"
                                               id="allotment-mooe-0" min="0"
                                               onkeyup="$(this).computeAll()"
                                               onchange="$(this).computeAll()">
                                    </div>
                                </td>
                                <td>
                                    <div class="md-form form-sm my-0">
                                        <input type="number" name="allotment_co[]"
                                               class="form-control required form-control-sm allotment-co"
                                               id="allotment-co-0" min="0"
                                               onkeyup="$(this).computeAll()"
                                               onchange="$(this).computeAll()">
                                    </div>
                                </td>
                                <td>
                                    <div class="md-form form-sm my-0">
                                        <input type="number" name="allotment_fe[]"
                                               class="form-control required form-control-sm allotment-fe"
                                               id="allotment-fe-0" min="0"
                                               onkeyup="$(this).computeAll()"
                                               onchange="$(this).computeAll()">
                                    </div>
                                </td>
                                <td>
                                    <div class="md-form form-sm my-0">
                                        <textarea name="allotment_remarks[]"
                                                  class="md-textarea form-control-sm w-100 py-1"></textarea>
                                    </div>
                                </td>
                                <td>
                                    <a onclick="$(this).deleteRow('#item-row-1');"
                                       class="btn btn-outline-red px-1 py-0">
                                        <i class="fas fa-minus-circle"></i>
                                    </a>
                                </td>
                            </tr>

                            <tr>
                                <td colspan="9">
                                    <a class="btn btn-outline-light-blue btn-sm btn-block z-depth-0"
                                       onclick="$(this).addRow('.item-row');">
                                        + Add Item
                                    </a>
                                </td>
                            </tr>

                            <tr>
                                <td class="text-center">Total</td>
                                <td></td>
                                <td>
                                    <div class="md-form form-sm my-0">
                                        <input type="number" class="form-control form-control-sm"
                                               id="total" value="0.00" readonly>
                                    </div>
                                </td>
                                <td>
                                    <div class="md-form form-sm my-0">
                                        <input type="number" class="form-control form-control-sm"
                                               id="total-ps" value="0.00" readonly>
                                    </div>
                                </td>
                                <td>
                                    <div class="md-form form-sm my-0">
                                        <input type="number" class="form-control form-control-sm"
                                               id="total-mooe" value="0.00" readonly>
                                    </div>
                                </td>
                                <td>
                                    <div class="md-form form-sm my-0">
                                        <input type="number" class="form-control form-control-sm"
                                               id="total-co" value="0.00" readonly>
                                    </div>
                                </td>
                                <td>
                                    <div class="md-form form-sm my-0">
                                        <input type="number" class="form-control form-control-sm"
                                               id="total-fe" value="0.00" readonly>
                                    </div>
                                </td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td rowspan="2" colspan="2" class="text-left">
                                    No. of pcs of LDDAP-ADA: <span id="lddap-no-pcs">1</span>
                                </td>
                                <td colspan="10">
                                    <div class="md-form form-sm">
                                        <input type="number" id="total-amount" name="total_amount"
                                               class="form-control required">
                                        <label for="total-amount">
                                            <span class="red-text">* </span>
                                            <b>Total Amount</b>
                                        </label>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="10">
                                    <div class="md-form form-sm">
                                        <input type="text" id="total-amount-words" name="total_amount_words"
                                               class="form-control required">
                                        <label for="total-amount-words">
                                            <span class="red-text">* </span>
                                            <b>Amount in Words</b>
                                        </label>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <hr class="py-0">
            <div class="row">
                <div class="col-md-6">
                    <div class="md-form">
                        <select class="mdb-select crud-select md-form required" searchable="Search here.."
                                name="cert_correct">
                            <option value="" disabled selected>Choose a signatory</option>

                            @if (count($signatories) > 0)
                                @foreach ($signatories as $sig)
                                    @if (isset($sig->module->summary->cert_correct) && $sig->module->summary->cert_correct)
                            <option value="{{ $sig->id }}">
                                {!! $sig->name !!} [{!! $sig->module->summary->designation !!}]
                            </option>
                                    @endif
                                @endforeach
                            @endif
                        </select>
                        <label class="mdb-main-label">
                            <span class="red-text">* </span>
                            <b>Certified Correct By</b>
                        </label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="md-form">
                        <select class="mdb-select crud-select md-form required" searchable="Search here.."
                                name="approved_by">
                            <option value="" disabled selected>Choose a signatory</option>

                            @if (count($signatories) > 0)
                                @foreach ($signatories as $sig)
                                    @if (isset($sig->module->summary->approved_by) && $sig->module->summary->approved_by)
                            <option value="{{ $sig->id }}">
                                {!! $sig->name !!} [{!! $sig->module->summary->designation !!}]
                            </option>
                                    @endif
                                @endforeach
                            @endif
                        </select>
                        <label class="mdb-main-label">
                            <span class="red-text">* </span>
                            <b>Approved By</b>
                        </label>
                    </div>
                </div>
            </div>

            <hr class="py-0">
            <small>
                TRANSMITTAL INFORMATION
            </small>

            <div class="row">
                <div class="col-md-6">
                    <div class="md-form">
                        <select class="mdb-select crud-select md-form required" searchable="Search here.."
                                name="delivered_by">
                            <option value="" disabled selected>Choose a signatory</option>

                            @if (count($signatories) > 0)
                                @foreach ($signatories as $sig)
                                    @if (isset($sig->module->summary->delivered_by) && $sig->module->summary->delivered_by)
                            <option value="{{ $sig->id }}">
                                {!! $sig->name !!} [{!! $sig->module->summary->designation !!}]
                            </option>
                                    @endif
                                @endforeach
                            @endif
                        </select>
                        <label class="mdb-main-label">
                            <span class="red-text">* </span>
                            <b>Delivered By</b>
                        </label>
                    </div>
                </div>
                <div class="col-md-6"></div>
            </div>

        </div>
    </div>
</form>
