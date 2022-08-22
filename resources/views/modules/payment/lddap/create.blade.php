<form id="form-store" class="wow animated fadeIn d-flex justify-content-center" method="POST"
      action="{{ route('lddap-store') }}">
    @csrf
    <div class="card">
        <div class="card-body py-1">
            <div class="row">
                <div class="col-md-12">
                    <div class="md-form row">
                        <div class="col-md-12 text-center">
                            <strong class="h5">
                                LIST OF DUE AND DEMANDABLE ACCOUNTS PAYABLE - ADVICE TO DEBIT ACCOUNTS (LDDAP-ADA)
                            </strong>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-5">
                    <div class="md-form form-sm">
                        <input type="text" id="department" name="department"
                               class="form-control required" value="DOST-CAR">
                        <label for="department" class="active">
                            <span class="red-text">* </span>
                            <strong>Department</strong>
                        </label>
                    </div>
                    <div class="md-form form-sm">
                        <input type="text" id="entity-name" name="entity_name"
                               class="form-control required" value="DOST-CAR">
                        <label for="entity-name" class="active">
                            <span class="red-text">* </span>
                            <strong>Entity Name</strong>
                        </label>
                    </div>
                    <div class="md-form form-sm">
                        <input type="text" id="operating-unit" name="operating_unit"
                               class="form-control required" value="DOST-CAR">
                        <label for="operating-unit" class="active">
                            <span class="red-text">* </span>
                            <strong>Operating Unit</strong>
                        </label>
                    </div>
                </div>
                <div class="col-md-2"></div>
                <div class="col-md-5">
                    <div class="md-form form-sm">
                        <input type="text" id="nca-no" name="nca_no"
                               class="form-control required" value="NCA-BMB-">
                        <label for="nca-no" class="active">
                            <span class="red-text">* </span>
                            <strong>NCA No.</strong>
                        </label>
                    </div>
                    <div class="md-form form-sm">
                        <input type="text" id="lddap-ada-no" name="lddap_ada_no"
                               class="form-control required" value="{{ $lddapNo }}">
                        <label for="lddap-ada-no" class="active">
                            <span class="red-text">* </span>
                            <strong>LDDAP-ADA No.</strong>
                        </label>
                    </div>
                    <div class="md-form form-sm">
                        <input type="date" id="lddap-date" name="lddap_date"
                               class="form-control required"
                               value="{{ \Carbon\Carbon::today()->toDateString() }}">
                        <label for="lddap-date" class="active">
                            <span class="red-text">* </span>
                            <strong>Date</strong>
                        </label>
                    </div>
                    <div class="md-form form-sm">
                        <input type="text" id="fund-cluster" name="fund_cluster"
                               class="form-control required" value="01">
                        <label for="fund-cluster" class="active">
                            <span class="red-text">* </span>
                            <strong>Fund Cluster</strong>
                        </label>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-7">
                    <div class="form-group form-sm">
                        <label for="mds-gsb-accnt-no my-3" class="active">
                            <span class="red-text">* </span>
                            <strong>MDS-GSB BRANCH/MDS SUB ACCOUNT NO.</strong>
                        </label>
                        <select class="mdb-select required mds-gsb-tokenizer"
                                name="mds_gsb_accnt_no[]" id="mds-gsb-accnt-no"></select>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="md-form form-sm">
                        <input type="text" id="serial-no" name="serial_no"
                               class="form-control required" value="{{ $lddapSerialNo }}">
                        <label for="serial-no" class="active">
                            <span class="red-text">* </span>
                            <strong>Serial No.</strong>
                        </label>
                    </div>
                </div>
            </div>


            <div class="row mt-3">
                <div class="col-md-12 border border-bottom-0 border-dark px-0 text-center">
                    <div class="p-2 border-bottom border-dark">
                        <strong>LIST OF DUE AND DEMANDABLE ACCOUNTS PAYABLE (LDDAP)</strong>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 border border-bottom-0 border-dark px-0 table-responsive">
                    <table class="table table-sm table-hover table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center" width="20%">
                                    <small>
                                        CREDITOR NAME
                                    </small>
                                </th>
                                <th class="text-center" width="15%">
                                    <small>
                                        CREDITOR PREFERRED SERVICING BANKS/SAVINGS/CURRENT ACCOUNT NO.
                                    </small>
                                </th>
                                <th class="text-center" width="14%">
                                    <small>
                                        Obligation Request and Status No.
                                    </small>
                                </th>
                                <th class="text-center" width="13%">
                                    <small>
                                        ALLOTMENT CLASS per (UACS)
                                    </small>
                                </th>
                                <th class="text-center" width="9%">
                                    <small>
                                        GROSS AMOUNT (PHP)
                                    </small>
                                </th>
                                <th class="text-center"  width="9%">
                                    <small>
                                        WITHHOLDING TAX (PHP)
                                    </small>
                                </th>
                                <th class="text-center"  width="9%">
                                    <small>
                                        NET AMOUNT (PHP)
                                    </small>
                                </th>
                                <th class="text-center"  width="9%">
                                    <small>
                                        REMARKS
                                    </small>
                                </th>
                                <th width="2%"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <strong>I. Current Year A/Ps</strong>
                                </td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>
                                    <strong>FOR MDS-GSB USE ONLY</strong>
                                </td>
                                <td></td>
                            </tr>
                            <tr id="current-row-1" class="current-row">
                                <td>
                                    <div class="md-form form-sm my-0">
                                        <textarea class="md-textarea required form-control-sm w-100 py-1 current-creditor-name"
                                                  name="current_creditor_name[]" placeholder=" Value..."
                                                  ></textarea>
                                    </div>
                                </td>
                                <td>
                                    <div class="md-form form-sm my-0">
                                        <textarea name="current_creditor_acc_no[]" placeholder=" Value..."
                                                  class="md-textarea required form-control-sm w-100 py-1"></textarea>
                                    </div>
                                </td>
                                <td>
                                    <div class="md-form my-0">
                                        <select class="mdb-select required ors-tokenizer" multiple="multiple"
                                                name="current_ors_no[0][]"></select>
                                    </div>
                                </td>
                                <td>
                                    <div class="md-form form-sm my-0">
                                        <select class="mdb-select required allot-class-tokenizer" multiple="multiple"
                                                name="current_allot_class_uacs[0][]"></select>
                                    </div>
                                </td>
                                <td>
                                    <div class="md-form form-sm my-0">
                                        <input type="number" placeholder=" Value..." name="current_gross_amount[]"
                                               class="form-control required form-control-sm current-gross-amount"
                                               id="current-gross-amount-0"
                                               onkeyup="$(this).computeGrossTotal('current')"
                                               onchange="$(this).computeGrossTotal('current')"
                                               onclick="$(this).showCalc('#current-gross-amount-0', 'current')">
                                    </div>
                                </td>
                                <td>
                                    <div class="md-form form-sm my-0">
                                        <input type="number" placeholder=" Value..." name="current_withold_tax[]"
                                               class="form-control required form-control-sm current-withold-tax"
                                               onkeyup="$(this).computeWithholdingTaxTotal('current')"
                                               onchange="$(this).computeWithholdingTaxTotal('current')">
                                    </div>
                                </td>
                                <td>
                                    <div class="md-form form-sm my-0">
                                        <input type="number" placeholder=" Value..." name="current_net_amount[]"
                                               class="form-control required form-control-sm current-net-amount"
                                               onkeyup="$(this).computeNetAmountTotal('current')"
                                               onchange="$(this).computeNetAmountTotal('current')">
                                    </div>
                                </td>
                                <td>
                                    <div class="md-form form-sm my-0">
                                        <textarea name="current_remarks[]" placeholder=" Value..."
                                                  class="md-textarea form-control-sm w-100 py-1"></textarea>
                                    </div>
                                </td>
                                <td>
                                    <a onclick="$(this).deleteRow('#current-row-1');"
                                       class="btn btn-outline-red px-1 py-0">
                                        <i class="fas fa-minus-circle"></i>
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="9">
                                    <a class="btn btn-outline-light-blue btn-sm btn-block z-depth-0"
                                       onclick="$(this).addRow('.current-row', 'current');">
                                        + Add Item
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>Sub-total</strong>
                                </td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>
                                    <div class="md-form form-sm my-0">
                                        <input type="number" class="form-control form-control-sm"
                                               id="current-total-gross" value="0.00" readonly>
                                    </div>
                                </td>
                                <td>
                                    <div class="md-form form-sm my-0">
                                        <input type="number" class="form-control form-control-sm"
                                               id="current-total-withholdingtax" value="0.00" readonly>
                                    </div>
                                </td>
                                <td>
                                    <div class="md-form form-sm my-0">
                                        <input type="number" class="form-control form-control-sm"
                                               id="current-total-netamount" value="0.00" readonly>
                                    </div>
                                </td>
                                <td></td>
                                <td></td>
                            </tr>

                            <tr>
                                <td colspan="9" class="py-1 grey">

                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <strong>II. Prior Year A/Ps</strong>
                                </td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr id="prior-row-0" class="prior-row" hidden></tr>
                            <tr>
                                <td colspan="9">
                                    <a class="btn btn-outline-light-blue btn-sm btn-block z-depth-0"
                                       onclick="$(this).addRow('.prior-row', 'prior');">
                                        + Add Item
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>Sub-total</strong>
                                </td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>
                                    <div class="md-form form-sm my-0">
                                        <input type="number" class="form-control form-control-sm"
                                               id="prior-total-gross" value="0.00" readonly>
                                    </div>
                                </td>
                                <td>
                                    <div class="md-form form-sm my-0">
                                        <input type="number" class="form-control form-control-sm"
                                               id="prior-total-withholdingtax" value="0.00" readonly>
                                    </div>
                                </td>
                                <td>
                                    <div class="md-form form-sm my-0">
                                        <input type="number" class="form-control form-control-sm"
                                               id="prior-total-netamount" value="0.00" readonly>
                                    </div>
                                </td>
                                <td></td>
                                <td></td>
                            </tr>

                            <tr>
                                <td>
                                    <strong>TOTAL</strong>
                                </td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>
                                    <div class="md-form form-sm my-0">
                                        <input type="number" class="form-control form-control-sm"
                                               id="total-gross-amount" value="0.00" readonly>
                                    </div>
                                </td>
                                <td>
                                    <div class="md-form form-sm my-0">
                                        <input type="number" class="form-control form-control-sm"
                                               id="total-withholding-tax" value="0.00" readonly>
                                    </div>
                                </td>
                                <td>
                                    <div class="md-form form-sm my-0">
                                        <input type="number" class="form-control form-control-sm"
                                               id="total-net-amount" value="0.00" readonly>
                                    </div>
                                </td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 border border-dark">
                    <div class="md-form">
                        <small>
                            <span class="red-text">* </span>
                            I hereby warant that the above List of Due and Demmandable A/Ps
                            was prepared in accordance with existing budgeting, accounting and
                            auditing rules and regulations. <br><br>
                            Certified Correct:
                        </small>
                        <select id="sig-cert-correct" name="sig_cert_correct" searchable="Search here.."
                                searchable="Search here.." class="mdb-select md-form required my-0 crud-select">
                            <option value="" disabled selected
                            > Certified Correct</option>
                            <option value=""> -- None --</option>

                            @if (count($signatories) > 0)
                                @foreach ($signatories as $sig)
                                    @if ($sig->module->lddap->cert_correct)
                            <option value="{{ $sig->id }}">
                                {!! $sig->name !!} [{!! $sig->module->lddap->designation !!}]
                            </option>
                                    @endif
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
                <div class="col-md-6 border border-left-0 border-dark">
                    <div class="md-form">
                        <small>
                            <span class="red-text">* </span>
                            I hereby assume full responsibility for the veracity and accuracy of the
                            listed claims, and the authencity of the supporting documents as submitted
                            by the claimants. <br><br>
                            Approved:
                        </small>
                        <select id="sig-approval-1" name="sig_approval_1" searchable="Search here.."
                                searchable="Search here.." class="mdb-select md-form required my-0 crud-select">
                            <option value="" disabled selected
                            > Approval 1</option>
                            <option value=""> -- None --</option>

                            @if (count($signatories) > 0)
                                @foreach ($signatories as $sig)
                                    @if ($sig->module->lddap->approval)
                            <option value="{{ $sig->id }}">
                                {!! $sig->name !!} [{!! $sig->module->lddap->designation !!}]
                            </option>
                                    @endif
                                @endforeach
                            @endif
                        </select>
                        <select id="sig-approval-2" name="sig_approval_2" searchable="Search here.."
                                searchable="Search here.." class="mdb-select md-form required my-0 crud-select">
                            <option value="" disabled selected
                            > Approval 2</option>
                            <option value=""> -- None --</option>

                            @if (count($signatories) > 0)
                                @foreach ($signatories as $sig)
                                    @if ($sig->module->lddap->approval)
                            <option value="{{ $sig->id }}">
                                {!! $sig->name !!} [{!! $sig->module->lddap->designation !!}]
                            </option>
                                    @endif
                                @endforeach
                            @endif
                        </select>
                        <select id="sig-approval-3" name="sig_approval_3" searchable="Search here.."
                                searchable="Search here.." class="mdb-select md-form required my-0 crud-select">
                            <option value="" disabled selected
                            > Approval 3</option>
                            <option value=""> -- None --</option>

                            @if (count($signatories) > 0)
                                @foreach ($signatories as $sig)
                                    @if ($sig->module->lddap->approval)
                            <option value="{{ $sig->id }}">
                                {!! $sig->name !!} [{!! $sig->module->lddap->designation !!}]
                            </option>
                                    @endif
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 border border-top-0 border-dark">
                    <div class="md-form">
                        <small>
                            <strong>To: MDS-GSB of the Agency</strong><br>
                            Please debit MDS Sub-Account Number: <br>
                            Please credit the accounts of the above listed creditors to cover payment of accounts payable<br><br>

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="md-form form-sm mt-0">
                                        <input type="text" id="total-amount-words" name="total_amount_words"
                                               class="form-control form-control-sm required">
                                        <label for="total-amount-words">
                                            <span class="red-text">*</span> TOTAL AMOUNT IN WORDS
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="md-form form-sm mt-0">
                                        <input type="number" id="total-amount" name="total_amount"
                                               class="form-control form-control-sm required"
                                               value="0.00">
                                        <label for="total-amount" class="active">
                                            <span class="red-text">* </span>
                                            TOTAL AMOUNT
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <strong class="text-center">
                                Agency Authorized Signatories
                            </strong>
                        </small>

                        <div class="row">
                            <div class="col-md-3">
                                <select id="sig-agency-auth-1" name="sig_agency_auth_1" searchable="Search here.."
                                        searchable="Search here.." class="mdb-select md-form required my-0 crud-select">
                                    <option value="" disabled selected
                                    > Signatory 1</option>
                                    <option value=""> -- None --</option>

                                    @if (count($signatories) > 0)
                                        @foreach ($signatories as $sig)
                                            @if ($sig->module->lddap->agency_auth)
                                    <option value="{{ $sig->id }}">
                                        {!! $sig->name !!} [{!! $sig->module->lddap->designation !!}]
                                    </option>
                                            @endif
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select id="sig-agency-auth-2" name="sig_agency_auth_2" searchable="Search here.."
                                        searchable="Search here.." class="mdb-select md-form required my-0 crud-select">
                                    <option value="" disabled selected
                                    > Signatory 2</option>
                                    <option value=""> -- None --</option>

                                    @if (count($signatories) > 0)
                                        @foreach ($signatories as $sig)
                                            @if ($sig->module->lddap->agency_auth)
                                    <option value="{{ $sig->id }}">
                                        {!! $sig->name !!} [{!! $sig->module->lddap->designation !!}]
                                    </option>
                                            @endif
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select id="sig-agency-auth-3" name="sig_agency_auth_3" searchable="Search here.."
                                        searchable="Search here.." class="mdb-select md-form required my-0 crud-select">
                                    <option value="" disabled selected
                                    > Signatory 3</option>
                                    <option value=""> -- None --</option>

                                    @if (count($signatories) > 0)
                                        @foreach ($signatories as $sig)
                                            @if ($sig->module->lddap->agency_auth)
                                    <option value="{{ $sig->id }}">
                                        {!! $sig->name !!} [{!! $sig->module->lddap->designation !!}]
                                    </option>
                                            @endif
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select id="sig-agency-auth-4" name="sig_agency_auth_4" searchable="Search here.."
                                        searchable="Search here.." class="mdb-select md-form required my-0 crud-select">
                                    <option value="" disabled selected
                                    > Signatory 4</option>
                                    <option value=""> -- None --</option>

                                    @if (count($signatories) > 0)
                                        @foreach ($signatories as $sig)
                                            @if ($sig->module->lddap->agency_auth)
                                    <option value="{{ $sig->id }}">
                                        {!! $sig->name !!} [{!! $sig->module->lddap->designation !!}]
                                    </option>
                                            @endif
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 text-center">
                                <small>
                                    <i>(Erasures shall invalidate this document)</i>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</form>
