<form id="form-edit" method="POST" class="wow animated fadeIn d-flex justify-content-center"
      action="{{ $actionURL }}">
    @csrf
    <div class="card w-responsive">
        <div class="card-body py-1">
            <div class="row">
                <div class="col-md-12">
                    <div class="md-form row">
                        <div class="col-md-12 text-center">
                            <strong class="h5">
                                LIST OF DUE AND DEMANDABLE ACCOUNTS PAYABLE - ADVICE TO DEVIT ACCOUNTS (LDDAP-ADA)
                            </strong>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-5">
                    <div class="md-form form-sm">
                        <input type="text" id="department" name="department"
                               class="form-control required" value="{{ $lddap->department }}">
                        <label for="department" class="{{ !empty($lddap->department) ? 'active' : '' }}">
                            <span class="red-text">* </span>
                            <strong>Department</strong>
                        </label>
                    </div>
                    <div class="md-form form-sm">
                        <input type="text" id="entity-name" name="entity_name"
                               class="form-control required" value="{{ $lddap->entity_name }}">
                        <label for="entity-name" class="{{ !empty($lddap->entity_name) ? 'active' : '' }}">
                            <span class="red-text">* </span>
                            <strong>Entity Name</strong>
                        </label>
                    </div>
                    <div class="md-form form-sm">
                        <input type="text" id="operating-unit" name="operating_unit"
                               class="form-control required" value="{{ $lddap->operating_unit }}">
                        <label for="operating-unit" class="{{ !empty($lddap->operating_unit) ? 'active' : '' }}">
                            <span class="red-text">* </span>
                            <strong>Operating Unit</strong>
                        </label>
                    </div>
                    <div class="md-form form-sm">
                        <select id="dv-id" name="dv_id" searchable="Search here.."
                                class="mdb-select md-form my-0">
                            <option value="" disabled selected
                            > DV Document</option>
                            <option value="">None</option>

                            @if (count($dvDocs) > 0)
                                @foreach ($dvDocs as $dv)
                            <option value="{{ $dv->id }}" {{ ($lddap->dv_id == $dv->id) ? 'selected' : '' }}
                                >DV No: {{ $dv->dv_no }} ( {{ $dv->particulars }} )
                            </option>
                                @endforeach
                            @else
                            <option value="" disabled>
                                No data...
                            </option>
                            @endif
                        </select>
                    </div>
                </div>
                <div class="col-md-2"></div>
                <div class="col-md-5">
                    <div class="md-form form-sm">
                        <input type="text" id="nca-no" name="nca_no"
                               class="form-control required"
                               value="{{ $lddap->nca_no }}">
                        <label for="nca-no" class="{{ !empty($lddap->nca_no) ? 'active' : '' }}">
                            <span class="red-text">* </span>
                            <strong>NCA No.</strong>
                        </label>
                    </div>
                    <div class="md-form form-sm">
                        <input type="text" id="lddap-ada-no" name="lddap_ada_no"
                               class="form-control required" value="{{ $lddap->lddap_ada_no }}">
                        <label for="lddap-ada-no" class="{{ !empty($lddap->lddap_ada_no) ? 'active' : '' }}">
                            <span class="red-text">* </span>
                            <strong>LDDAP-ADA No.</strong>
                        </label>
                    </div>
                    <div class="md-form form-sm">
                        <input type="date" id="lddap-date" name="lddap_date"
                               class="form-control required" value="{{ $lddap->lddap_date }}">
                        <label for="lddap-date" class="{{ !empty($lddap->lddap_date) ? 'active' : '' }}">
                            <span class="red-text">* </span>
                            <strong>Date</strong>
                        </label>
                    </div>
                    <div class="md-form form-sm">
                        <input type="text" id="fund-cluster" name="fund_cluster"
                               class="form-control required" value="{{ $lddap->fund_cluster }}">
                        <label for="fund-cluster" class="{{ !empty($lddap->fund_cluster) ? 'active' : '' }}">
                            <span class="red-text">* </span>
                            <strong>Fund Cluster</strong>
                        </label>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="md-form form-sm">
                        <input type="text" id="mds-gsb-accnt-no" name="mds_gsb_accnt_no"
                               class="form-control required" value="{{ $lddap->mds_gsb_accnt_no }}">
                        <label for="mds-gsb-accnt-no" class="{{ !empty($lddap->mds_gsb_accnt_no) ? 'active' : '' }}">
                            <span class="red-text">* </span>
                            <strong>MDS-GSB BRANCH/MDS SUB ACCOUNT NO.</strong>
                        </label>
                    </div>
                </div>
            </div>


            <div class="row">
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
                                        REAMARKS
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

                            @if (count($currentItems) > 0)
                                @foreach ($currentItems as $ctrCurrent => $item)
                            <tr id="current-row-{{ $ctrCurrent + 1 }}" class="current-row">
                                <td>
                                    <div class="md-form form-sm my-0">
                                        <textarea class="md-textarea form-control required form-control-sm py-2"
                                                  name="current_creditor_name[]"
                                                  placeholder=" Value..." rows="1"
                                            >{{ $item->creditor_name }}</textarea>
                                    </div>
                                </td>
                                <td>
                                    <div class="md-form form-sm my-0">
                                        <input type="text" class="form-control required form-control-sm"
                                               placeholder=" Value..." name="current_creditor_acc_no[]"
                                               value="{{ $item->creditor_acc_no }}">
                                    </div>
                                </td>
                                <td>
                                    <div class="md-form form-sm my-0">
                                        <input type="text" class="form-control required form-control-sm"
                                               placeholder=" Value..." name="current_ors_no[]"
                                               value="{{ $item->ors_no }}">
                                    </div>
                                </td>
                                <td>
                                    <div class="md-form form-sm my-0">
                                        <input type="text" class="form-control required form-control-sm"
                                               placeholder=" Value..." name="current_allot_class_uacs[]"
                                               value="{{ $item->allot_class_uacs }}">
                                    </div>
                                </td>
                                <td>
                                    <div class="md-form form-sm my-0">
                                        <input type="number" placeholder=" Value..." name="current_gross_amount[]"
                                               class="form-control required form-control-sm current-gross-amount"
                                               onkeyup="$(this).computeGrossTotal('current')"
                                               onchange="$(this).computeGrossTotal('current')"
                                               value="{{ $item->gross_amount }}">
                                    </div>
                                </td>
                                <td>
                                    <div class="md-form form-sm my-0">
                                        <input type="number" placeholder=" Value..." name="current_withold_tax[]"
                                               class="form-control required form-control-sm current-withold-tax"
                                               onkeyup="$(this).computeWithholdingTaxTotal('current')"
                                               onchange="$(this).computeWithholdingTaxTotal('current')"
                                               value="{{ $item->withold_tax }}">
                                    </div>
                                </td>
                                <td>
                                    <div class="md-form form-sm my-0">
                                        <input type="number" placeholder=" Value..." name="current_net_amount[]"
                                               class="form-control required form-control-sm current-net-amount"
                                               onkeyup="$(this).computeNetAmountTotal('current')"
                                               onchange="$(this).computeNetAmountTotal('current')"
                                               value="{{ $item->net_amount }}">
                                    </div>
                                </td>
                                <td>
                                    <div class="md-form form-sm my-0">
                                        <textarea class="md-textarea form-control required form-control-sm py-2"
                                                  name="current_remarks[]"
                                                  placeholder=" Value..." rows="1"
                                            >{{ $item->remarks }}</textarea>
                                    </div>
                                </td>
                                <td>
                                    <a onclick="$(this).deleteRow('#current-row-{{ $ctrCurrent + 1 }}');"
                                       class="btn btn-outline-red px-1 py-0">
                                        <i class="fas fa-minus-circle"></i>
                                    </a>
                                </td>
                            </tr>

                            @php
                            $currentGross += $item->gross_amount;
                            $currentWithholding += $item->withold_tax;
                            $currentNet += $item->net_amount;
                            $totalGross += $item->gross_amount;
                            $totalWithholding += $item->withold_tax;
                            $totalNet += $item->net_amount;
                            @endphp

                                @endforeach
                            @endif

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
                                               id="current-total-gross"
                                               value="{{ $currentGross }}" readonly>
                                    </div>
                                </td>
                                <td>
                                    <div class="md-form form-sm my-0">
                                        <input type="number" class="form-control form-control-sm"
                                               id="current-total-withholdingtax"
                                               value="{{ $currentWithholding }}" readonly>
                                    </div>
                                </td>
                                <td>
                                    <div class="md-form form-sm my-0">
                                        <input type="number" class="form-control form-control-sm"
                                               id="current-total-netamount"
                                               value="{{ $currentNet }}" readonly>
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

                            @if (count($priorItems) > 0)
                                @foreach ($priorItems as $ctrPrior => $item)
                            <tr id="prior-row-{{ $ctrPrior + 1 }}" class="prior-row">
                                <td>
                                    <div class="md-form form-sm my-0">
                                        <textarea class="md-textarea form-control required form-control-sm py-2"
                                                  name="prior_creditor_name[]"
                                                  placeholder=" Value..." rows="1"
                                            >{{ $item->creditor_name }}</textarea>
                                    </div>
                                </td>
                                <td>
                                    <div class="md-form form-sm my-0">
                                        <input type="text" class="form-control required form-control-sm"
                                               placeholder=" Value..." name="prior_creditor_acc_no[]"
                                               value="{{ $item->creditor_acc_no }}">
                                    </div>
                                </td>
                                <td>
                                    <div class="md-form form-sm my-0">
                                        <input type="text" class="form-control required form-control-sm"
                                               placeholder=" Value..." name="prior_ors_no[]"
                                               value="{{ $item->ors_no }}">
                                    </div>
                                </td>
                                <td>
                                    <div class="md-form form-sm my-0">
                                        <input type="text" class="form-control required form-control-sm"
                                               placeholder=" Value..." name="prior_allot_class_uacs[]"
                                               value="{{ $item->allot_class_uacs }}">
                                    </div>
                                </td>
                                <td>
                                    <div class="md-form form-sm my-0">
                                        <input type="number" placeholder=" Value..." name="prior_gross_amount[]"
                                               class="form-control required form-control-sm prior-gross-amount"
                                               onkeyup="$(this).computeGrossTotal('prior')"
                                               onchange="$(this).computeGrossTotal('prior')"
                                               value="{{ $item->gross_amount }}">
                                    </div>
                                </td>
                                <td>
                                    <div class="md-form form-sm my-0">
                                        <input type="number" placeholder=" Value..." name="prior_withold_tax[]"
                                               class="form-control required form-control-sm prior-withold-tax"
                                               onkeyup="$(this).computeWithholdingTaxTotal('prior')"
                                               onchange="$(this).computeWithholdingTaxTotal('prior')"
                                               value="{{ $item->withold_tax }}">
                                    </div>
                                </td>
                                <td>
                                    <div class="md-form form-sm my-0">
                                        <input type="number" placeholder=" Value..." name="prior_net_amount[]"
                                               class="form-control required form-control-sm prior-net-amount"
                                               onkeyup="$(this).computeNetAmountTotal('prior')"
                                               onchange="$(this).computeNetAmountTotal('prior')"
                                               value="{{ $item->net_amount }}">
                                    </div>
                                </td>
                                <td>
                                    <div class="md-form form-sm my-0">
                                        <textarea class="md-textarea form-control required form-control-sm py-2"
                                                  name="prior_remarks[]"
                                                  placeholder=" Value..." rows="1"
                                            >{{ $item->remarks }}</textarea>
                                    </div>
                                </td>
                                <td>
                                    <a onclick="$(this).deleteRow('#prior-row-{{ $ctrPrior + 1 }}');"
                                       class="btn btn-outline-red px-1 py-0">
                                        <i class="fas fa-minus-circle"></i>
                                    </a>
                                </td>
                            </tr>

                            @php
                            $priorGross += $item->gross_amount;
                            $priorWithholding += $item->withold_tax;
                            $priorNet += $item->net_amount;
                            $totalGross += $item->gross_amount;
                            $totalWithholding += $item->withold_tax;
                            $totalNet += $item->net_amount;
                            @endphp

                                @endforeach
                            @endif

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
                                               id="prior-total-gross"
                                               value="{{ $priorGross }}" readonly>
                                    </div>
                                </td>
                                <td>
                                    <div class="md-form form-sm my-0">
                                        <input type="number" class="form-control form-control-sm"
                                               id="prior-total-withholdingtax"
                                               value="{{ $priorWithholding }}" readonly>
                                    </div>
                                </td>
                                <td>
                                    <div class="md-form form-sm my-0">
                                        <input type="number" class="form-control form-control-sm"
                                               id="prior-total-netamount"
                                               value="{{ $priorNet }}" readonly>
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
                                               id="total-gross-amount"
                                               value="{{ $totalGross }}" readonly>
                                    </div>
                                </td>
                                <td>
                                    <div class="md-form form-sm my-0">
                                        <input type="number" class="form-control form-control-sm"
                                               id="total-withholding-tax"
                                               value="{{ $totalWithholding }}" readonly>
                                    </div>
                                </td>
                                <td>
                                    <div class="md-form form-sm my-0">
                                        <input type="number" class="form-control form-control-sm"
                                               id="total-net-amount"
                                               value="{{ $totalNet }}" readonly>
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
                                class="mdb-select md-form my-0">
                            <option value="" disabled selected
                            > Certified Correct</option>

                            @if (count($signatories) > 0)
                                @foreach ($signatories as $sig)
                                    @if ($sig->lddap_sign_type == 'cert_correct')
                            <option value="{{ $sig->id }}" {{ ($lddap->sig_cert_correct == $sig->id) ? 'selected' : '' }}
                                >{{ $sig->name }}
                            </option>
                                    @endif
                                @endforeach
                            @else
                            <option value="" disabled>
                                No data...
                            </option>
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
                                class="mdb-select md-form my-0">
                            <option value="" disabled selected
                            > Approval 1</option>

                            @if (count($signatories) > 0)
                                @foreach ($signatories as $sig)
                                    @if ($sig->lddap_sign_type == 'approval')
                            <option value="{{ $sig->id }}" {{ ($lddap->sig_approval_1 == $sig->id) ? 'selected' : '' }}
                                >{{ $sig->name }} [ {{ $sig->position }} ]
                            </option>
                                    @endif
                                @endforeach
                            @else
                            <option value="" disabled>
                                No data...
                            </option>
                            @endif
                        </select>
                        <select id="sig-approval-2" name="sig_approval_2" searchable="Search here.."
                                class="mdb-select md-form my-0">
                            <option value="" disabled selected
                            > Approval 2</option>

                            @if (count($signatories) > 0)
                                @foreach ($signatories as $sig)
                                    @if ($sig->lddap_sign_type == 'approval')
                            <option value="{{ $sig->id }}" {{ ($lddap->sig_approval_2 == $sig->id) ? 'selected' : '' }}
                                >{{ $sig->name }} [ {{ $sig->position }} ]
                            </option>
                                    @endif
                                @endforeach
                            @else
                            <option value="" disabled>
                                No data...
                            </option>
                            @endif
                        </select>
                        <select id="sig-approval-3" name="sig_approval_3" searchable="Search here.."
                                class="mdb-select md-form my-0">
                            <option value="" disabled selected
                            > Approval 3</option>

                            @if (count($signatories) > 0)
                                @foreach ($signatories as $sig)
                                    @if ($sig->lddap_sign_type == 'approval')
                            <option value="{{ $sig->id }}" {{ ($lddap->sig_approval_3 == $sig->id) ? 'selected' : '' }}
                                >{{ $sig->name }} [ {{ $sig->position }} ]
                            </option>
                                    @endif
                                @endforeach
                            @else
                            <option value="" disabled>
                                No data...
                            </option>
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
                                               class="form-control form-control-sm required"
                                               value="{{ $lddap->total_amount_words }}">
                                        <label for="total-amount-words" class="{{ !empty($lddap->total_amount_words) ? 'active' : '' }}">
                                            <span class="red-text">*</span> TOTAL AMOUNT IN WORDS
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="md-form form-sm mt-0">
                                        <input type="number" id="total-amount" name="total_amount"
                                               class="form-control form-control-sm required"
                                               value="{{ $lddap->total_amount }}">
                                        <label for="total-amount" class="{{ !empty($lddap->total_amount) ? 'active' : '' }}">
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
                                        class="mdb-select md-form my-0">
                                    <option value="" disabled selected
                                    > Signatory 1</option>

                                    @if (count($signatories) > 0)
                                        @foreach ($signatories as $sig)
                                            @if ($sig->lddap_sign_type == 'agency_authorized')
                                    <option value="{{ $sig->id }}" {{ ($lddap->sig_agency_auth_1 == $sig->id) ? 'selected' : '' }}
                                        >{{ $sig->name }} [ {{ $sig->position }} ]
                                    </option>
                                            @endif
                                        @endforeach
                                    @else
                                    <option value="" disabled>
                                        No data...
                                    </option>
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select id="sig-agency-auth-2" name="sig_agency_auth_2" searchable="Search here.."
                                        class="mdb-select md-form my-0">
                                    <option value="" disabled selected
                                    > Signatory 2</option>

                                    @if (count($signatories) > 0)
                                        @foreach ($signatories as $sig)
                                            @if ($sig->lddap_sign_type == 'agency_authorized')
                                    <option value="{{ $sig->id }}" {{ ($lddap->sig_agency_auth_2 == $sig->id) ? 'selected' : '' }}
                                        >{{ $sig->name }} [ {{ $sig->position }} ]
                                    </option>
                                            @endif
                                        @endforeach
                                    @else
                                    <option value="" disabled>
                                        No data...
                                    </option>
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select id="sig-agency-auth-3" name="sig_agency_auth_3" searchable="Search here.."
                                        class="mdb-select md-form my-0">
                                    <option value="" disabled selected
                                    > Signatory 3</option>

                                    @if (count($signatories) > 0)
                                        @foreach ($signatories as $sig)
                                            @if ($sig->lddap_sign_type == 'agency_authorized')
                                    <option value="{{ $sig->id }}" {{ ($lddap->sig_agency_auth_3 == $sig->id) ? 'selected' : '' }}
                                        >{{ $sig->name }} [ {{ $sig->position }} ]
                                    </option>
                                            @endif
                                        @endforeach
                                    @else
                                    <option value="" disabled>
                                        No data...
                                    </option>
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select id="sig-agency-auth-4" name="sig_agency_auth_4" searchable="Search here.."
                                        class="mdb-select md-form my-0">
                                    <option value="" disabled selected
                                    > Signatory 4</option>

                                    @if (count($signatories) > 0)
                                        @foreach ($signatories as $sig)
                                            @if ($sig->lddap_sign_type == 'agency_authorized')
                                    <option value="{{ $sig->id }}" {{ ($lddap->sig_agency_auth_4 == $sig->id) ? 'selected' : '' }}
                                        >{{ $sig->name }} [ {{ $sig->position }} ]
                                    </option>
                                            @endif
                                        @endforeach
                                    @else
                                    <option value="" disabled>
                                        No data...
                                    </option>
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
