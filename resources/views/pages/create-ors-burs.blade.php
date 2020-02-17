<form id="form-store" method="POST" class="wow animated fadeIn d-flex justify-content-center"
      action="{{ $actionURL }}">
    @csrf
    <input type="hidden" name="module_type" value="{{ $moduleType }}">
    <div class="card w-responsive">
        <div class="card-body py-1">
            <div class="row">
                <div class="col-md-8 border border-bottom-0 border-dark">
                    <div class="md-form row">
                        <div class="col-md-3"></div>
                        <div class="col-md-6 text-center">
                            <strong class="h4">
                                OBLIGATION/BUDGET UTILIZATION REQUEST AND STATUS
                                <div class="md-form">
                                    <select id="document-type" name="document_type" searchable="Search here.."
                                            class="mdb-select md-form my-0 required">
                                        <option class="red-text" value="" disabled selected
                                        >* Document Type</option>
                                        <option value="ORS">OBLIGATION REQUEST AND STATUS (ORS)</option>
                                        <option value="BURS">BUDGET UTILIZATION REQUEST AND STATUS (BURS)</option>
                                    </select>
                                </div>

                                @if ($moduleType == 'cashadvance')
                                <div class="md-form">
                                    <select id="transaction-type" name="transaction_type" searchable="Search here.."
                                            class="mdb-select md-form my-0 required">
                                        <option class="red-text" value="" disabled selected
                                        >* Transaction Type</option>
                                        <option value="cash_advance">Cash Advance</option>
                                        <option value="reimbursement">Reimbursement</option>
                                        <option value="others">Others</option>
                                    </select>
                                </div>
                                @endif
                            </strong>
                        </div>
                        <div class="col-md-3"></div>
                    </div>
                </div>
                <div class="col-md-4 border border-left-0 border-bottom-0 border-dark">
                    <div class="md-form form-sm">
                        <input type="text" id="serial-no" name="serial_no"
                            class="form-control">
                        <label for="serial-no">
                            <strong>Serial Number</strong>
                        </label>
                    </div>
                    <div class="md-form form-sm">
                        <input type="date" id="date-ors-burs" name="date_ors_burs"
                            class="form-control">
                        <label for="date-ors-burs" class="active">
                            <strong>Date</strong>
                        </label>
                    </div>
                    <div class="md-form form-sm">
                        <input type="text" id="fund-cluster" name="fund_cluster"
                               class="form-control">
                        <label for="fund-cluster">
                            <strong>Fund Cluster</strong>
                        </label>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-2 border border-bottom-0 border-dark text-center">
                    <div class="md-form">
                        <span class="red-text">* </span>
                        Payee
                    </div>
                </div>
                <div class="col-md-10 border border-left-0 border-bottom-0 border-dark">
                    <div class="md-form form-sm">
                        <select id="payee" name="payee" searchable="Search here.."
                                class="mdb-select md-form my-0 required">
                            <option class="red-text" value="" disabled selected
                            >Payee</option>

                            @if (count($payees) > 0)
                                @if ($moduleType == 'cashadvance')
                                    @foreach ($payees as $payee)
                            <option value="{{ $payee->emp_id }}"
                                >{{ $payee->name }} [ {{ $payee->position }} ]
                            </option>
                                    @endforeach
                                @else
                            <option value="{{ $payee->id }}"
                                >{{ $payee->company_name }}
                            </option>
                                @endif
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
                <div class="col-md-2 border border-bottom-0 border-dark text-center">
                    <div class="md-form">
                        @if ($moduleType == 'cashadvance')
                        <span class="red-text">* </span>
                        @endif
                        Office
                    </div>
                </div>
                <div class="col-md-10 border border-left-0 border-bottom-0 border-dark">
                    <div class="md-form form-sm">
                        <input type="text" id="office" name="office"
                               class="form-control {{ $moduleType == 'cashadvance' ? 'required': '' }}"
                               placeholder="Enter office here...">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-2 border border-bottom-0 border-dark text-center">
                    <div class="md-form">
                        <span class="red-text">* </span>
                        Address
                    </div>
                </div>
                <div class="col-md-10 border border-left-0 border-bottom-0 border-dark">
                    <div class="md-form form-sm my-0">
                        <textarea class="md-textarea form-control required" id="address" name="address"
                                  rows="2" placeholder="Write address here..."></textarea>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-2 border border-bottom-0 border-dark px-0 text-center">
                    <div class="p-2 border-bottom border-dark">
                        <span class="red-text">* </span>
                        <strong>Responsibilty Center</strong>
                    </div>
                    <div class="md-form px-3">
                        <input type="text" id="responsibility_center" name="responsibility_center"
                               class="form-control required" value="19 001 03000 14" required>
                    </div>
                </div>
                <div class="col-md-4 border border-left-0 border-bottom-0 border-dark px-0 text-center">
                    <div class="p-2 border-bottom border-dark">
                        <span class="red-text">* </span>
                        <strong>PARTICULARS</strong>
                    </div>
                    <div class="form-group p-0 m-0">
                        <textarea class="md-textarea form-control border border-0 rounded-0 required"
                                  id="particulars" name="particulars" rows="8"
                                  placeholder="Write particulars here..."></textarea>
                    </div>
                </div>
                <div class="col-md-2 border border-left-0 border-bottom-0 border-dark px-0 text-center">
                    <div class="p-2 border-bottom border-dark">
                        <span class="red-text">* </span>
                        <strong>MFO/PAP</strong>
                    </div>
                    <div class="form-group p-0 m-0">
                        <textarea class="md-textarea form-control border border-0 rounded-0 required"
                                  id="mfo_pap" name="mfo_pap" rows="8" placeholder="Write MFO/PAP here..."
                        >3-Regional Office&#13;&#10;A.III.c.1&#13;&#10;A.III.b.1&#13;&#10;A.III.c.2</textarea>
                    </div>
                </div>
                <div class="col-md-2 border border-left-0 border-bottom-0 border-dark px-0 text-center">
                    <div class="p-2 border-bottom border-dark">
                        <strong>UACS Object Code</strong>
                    </div>
                    <div class="form-group p-0 m-0">
                        <textarea class="md-textarea form-control border border-0 rounded-0"
                                  id="uacs_object_code" name="uacs_object_code" rows="8"
                                  placeholder="Write UACS Object Code here..."></textarea>
                    </div>
                </div>
                <div class="col-md-2 border border-left-0 border-bottom-0 border-dark px-0 text-center">
                    <div class="p-2 border-bottom border-dark">
                        <span class="red-text">* </span>
                        <strong>AMOUNT</strong>
                    </div>
                    <div class="md-form px-3">
                        <input type="number" id="amount" name="amount" placeholder="Enter a value..."
                               class="form-control required">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8 border border-bottom-0 border-dark px-0 text-center">
                    <div class="md-form">
                        <strong>TOTAL</strong>
                    </div>
                </div>
                <div class="col-md-4 border border-left-0 border-bottom-0 border-dark px-0 text-center">
                    <div class="md-form px-3">
                        <input type="number" id="total" name="total"
                               class="form-control" readonly>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 border border-dark">
                    <div class="md-form">
                        <small>
                            <span class="red-text">* </span>
                            [ A ] Certified: Charges to appropriation/alloment necessary, lawful and under
                            my direct supervision; and supporting documents valid, proper and legal.
                        </small>
                        <select id="sig-agency-head" name="sig_agency_head" searchable="Search here.."
                                class="mdb-select md-form my-0 required">
                            <option value="" disabled selected
                            > Head, Requesting Office/Authorized Representative</option>

                            @if (count($signatories) > 0)
                                @foreach ($signatories as $approval)
                                    @if ($approval->ors_burs_sign_type == 'approval')
                            <option value="{{ $approval->id }}"
                                >{{ $approval->name }} [ {{ $approval->position }} ]
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
                    <div class="md-form my-0">
                        <input type="date" id="date-certified-1" name="date_certified_1"
                               class="form-control">
                        <label for="date-certified-1" class="active">
                            Date:
                        </label>
                    </div>
                </div>
                <div class="col-md-6 border border-left-0 border-dark">
                    <div class="md-form">
                        <small>
                            <span class="red-text">* </span>
                            [ B ] Certified: Allotment available and obligated for the purpose/adjustment
                            necessary as indicated above.
                        </small>
                        <select id="sig-budget" name="sig_budget" searchable="Search here.."
                                class="mdb-select md-form my-0 required">
                            <option value="" disabled selected
                            > Head, Budget Division/Unit/Authorized Representative</option>

                            @if (count($signatories) > 0)
                                @foreach ($signatories as $budget)
                                    @if ($budget->ors_burs_sign_type == 'budget')
                            <option value="{{ $budget->id }}"
                                >{{ $budget->name }} [ {{ $budget->position }} ]
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
                    <div class="md-form my-0">
                        <input type="date" id="date-certified-2" name="date_certified_2"
                               class="form-control">
                        <label for="date-certified-2" class="active">
                            Date:
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
