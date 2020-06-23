<form id="form-update" class="wow animated fadeIn d-flex justify-content-center" method="POST"
      action="{{ route('ca-ors-burs-update', ['id' => $id]) }}">
    @csrf

    <div class="card w-responsive">
        <div class="card-body py-1">
            <div class="row">
                <div class="col-md-8 border border-bottom-0 border-dark">
                    <div class="md-form row">
                        <div class="col-md-3"></div>
                        <div class="col-md-6 text-center">
                            <strong class="h4">
                                OBLIGATION/BUDGET UTILIZATION REQUEST AND STATUS
                                <div class="md-form text-left">
                                    <select id="document-type" name="document_type" searchable="Search here.."
                                            class="mdb-select crud-select md-form my-0 required">
                                        <option class="red-text" value="" disabled selected>
                                            Choose a document type
                                        </option>
                                        <option value="ors" {{ $documentType == 'ors' ? 'selected': '' }}
                                            >OBLIGATION REQUEST AND STATUS (ORS)
                                        </option>
                                        <option value="burs" {{ $documentType == 'burs' ? 'selected': '' }}
                                            >BUDGET UTILIZATION REQUEST AND STATUS (BURS)
                                        </option>
                                    </select>
                                    <label class="mdb-main-label">
                                        Document Type <span class="red-text">*</span>
                                    </label>
                                </div>

                                <div class="md-form text-left">
                                    <select id="transaction-type" name="transaction_type" searchable="Search here.."
                                            class="mdb-select crud-select md-form my-0 required">
                                        <option class="red-text" value="" disabled selected>
                                            Choose a transaction type</option>
                                        <option value="cash_advance" {{ $transactionType == 'cash_advance' ? 'selected': '' }}>
                                            Cash Advance
                                        </option>
                                        <option value="reimbursement" {{ $transactionType == 'reimbursement' ? 'selected': '' }}>
                                            Reimbursement
                                        </option>
                                        <option value="others" {{ $transactionType == 'others' ? 'selected': '' }}>
                                            Others
                                        </option>
                                    </select>
                                    <label class="mdb-main-label">
                                        Transaction Type <span class="red-text">*</span>
                                    </label>
                                </div>
                            </strong>
                        </div>
                        <div class="col-md-3"></div>
                    </div>
                </div>
                <div class="col-md-4 border border-left-0 border-bottom-0 border-dark">
                    <div class="md-form form-sm">
                        <input type="text" id="serial-no" name="serial_no"
                               class="form-control" value="{{ $serialNo }}"
                               {{ !$isObligated ? ' readonly' : '' }}>
                        <label for="serial-no" class="{{ !empty($serialNo) ? 'active': '' }}">
                            <strong>Serial Number</strong>
                        </label>
                    </div>
                    <div class="md-form form-sm">
                        <input type="date" id="date-ors-burs" name="date_ors_burs"
                               class="form-control required" value="{{ $dateORS }}">
                        <label for="date-ors-burs" class="active mt-3">
                            <strong>Date <span class="red-text">*</span></strong>
                        </label>
                    </div>
                    <div class="md-form form-sm">
                        <input type="text" id="fund-cluster" name="fund_cluster"
                               class="form-control" value="{{ $fundCluster }}">
                        <label for="fund-cluster" class="{{ !empty($fundCluster) ? 'active': '' }}">
                            <strong>Fund Cluster</strong>
                        </label>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-2 border border-bottom-0 border-dark text-center">
                    <div class="md-form">
                        Payee
                    </div>
                </div>
                <div class="col-md-10 border border-left-0 border-bottom-0 border-dark">
                    <div class="md-form form-sm">
                        <select id="payee" name="payee" searchable="Search here.."
                                class="mdb-select crud-select md-form my-0 required" disabled>
                            <option class="red-text" value="" disabled selected>Payee</option>
                            @if (count($payees) > 0)
                                @foreach ($payees as $emp)
                            <option value="{{ $emp->id }}" {{ $emp->id == $payee ? 'selected': '' }}>
                                {{ $emp->firstname }} {{ $emp->lastname }}
                            </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-2 border border-bottom-0 border-dark text-center">
                    <div class="md-form">
                        Office
                    </div>
                </div>
                <div class="col-md-10 border border-left-0 border-bottom-0 border-dark">
                    <div class="md-form form-sm">
                        <input type="text" id="office" name="office" value="{{ $office }}"
                               class="form-control"
                               placeholder="Enter office here...">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-2 border border-bottom-0 border-dark text-center">
                    <div class="md-form">
                        Address <span class="red-text">*</span>
                    </div>
                </div>
                <div class="col-md-10 border border-left-0 border-bottom-0 border-dark">
                    <div class="md-form form-sm my-0">
                        <textarea class="md-textarea form-control required" id="address" name="address"
                                  rows="2" placeholder="Write address here...">{{ $address }}</textarea>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-2 border border-bottom-0 border-dark px-0 text-center">
                    <div class="p-2 border-bottom border-dark">
                        <strong>Responsibilty Center</strong>
                    </div>
                    <div class="form-group p-0 m-0">
                        <textarea class="md-textarea form-control border border-0 rounded-0"
                                  id="responsibility_center" name="responsibility_center" rows="8"
                                  placeholder="Write responsibility center here...">{{ $responsibilityCenter }}</textarea>
                    </div>
                </div>
                <div class="col-md-4 border border-left-0 border-bottom-0 border-dark px-0 text-center">
                    <div class="p-2 border-bottom border-dark">
                        <strong>PARTICULARS <span class="red-text">*</span></strong>
                    </div>
                    <div class="form-group p-0 m-0">
                        <textarea class="md-textarea form-control border border-0 rounded-0 required"
                                  id="particulars" name="particulars" rows="8"
                                  placeholder="Write particulars here...">{{ $particulars }}</textarea>
                    </div>
                </div>
                <div class="col-md-2 border border-left-0 border-bottom-0 border-dark px-0 text-center">
                    <div class="p-2 border-bottom border-dark">
                        <strong>MFO/PAP</strong>
                    </div>
                    <div class="form-group p-0 m-0">
                        <textarea class="md-textarea form-control border border-0 rounded-0"
                                  id="mfo-pap" name="mfo_pap" rows="8" placeholder="Write MFO/PAP here..."
                        >{{ $mfoPAP }}</textarea>
                    </div>
                </div>
                <div class="col-md-2 border border-left-0 border-bottom-0 border-dark px-0 text-center">
                    <div class="p-2 border-bottom border-dark">
                        <strong>UACS Object Code</strong>
                    </div>
                    <div class="form-group p-0 m-0">
                        <textarea class="md-textarea form-control border border-0 rounded-0"
                                  id="uacs-object-code" name="uacs_object_code" rows="8"
                                  placeholder="Write UACS Object Code here...">{{ $uacsObjectCode }}</textarea>
                    </div>
                </div>
                <div class="col-md-2 border border-left-0 border-bottom-0 border-dark px-0 text-center">
                    <div class="p-2 border-bottom border-dark">
                        <strong>AMOUNT <span class="red-text">*</span></strong>
                    </div>
                    <div class="md-form px-3">
                        <input type="number" id="amount" name="amount" placeholder="Enter a value..."
                               class="form-control required" value="{{ $amount }}">
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
                        <input type="number" id="total" name="total" value="{{ $amount }}"
                               class="form-control" readonly>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 border border-dark">
                    <small>
                        [ A ] Certified: Charges to appropriation/alloment necessary, lawful and under
                        my direct supervision; and supporting documents valid, proper and legal.
                    </small>

                    <div class="md-form">
                        <select id="sig-certified-1" name="sig_certified_1" searchable="Search here.."
                                class="mdb-select crud-select md-form my-0 required">
                            <option value="" disabled selected>
                                Choose a head, requesting office/authorized representative
                            </option>

                            @if (count($signatories) > 0)
                                @foreach ($signatories as $sig)
                                    @if ($sig->module->ors->approval)
                            <option value="{{ $sig->id }}" {{ $sig->id == $sigCertified1 ? 'selected' : '' }}>
                                {!! $sig->name !!} [{!! $sig->module->ors->designation !!}]
                            </option>
                                    @endif
                                @endforeach
                            @endif
                        </select>
                        <label class="mdb-main-label">
                            Head, Requesting Office/Authorized Representative <span class="red-text">*</span>
                        </label>
                    </div>
                    <div class="md-form">
                        <input type="date" id="date-certified-1" name="date_certified_1"
                               class="form-control" value="{{ $dateCertified1 }}">
                        <label for="date-certified-1" class="active mt-3">
                            Date:
                        </label>
                    </div>
                </div>
                <div class="col-md-6 border border-left-0 border-dark">
                    <small>
                        [ B ] Certified: Allotment available and obligated for the purpose/adjustment
                        necessary as indicated above.
                    </small>

                    <div class="md-form">
                        <select id="sig-certified-2" name="sig_certified_2" searchable="Search here.."
                                class="mdb-select crud-select md-form my-0 required">
                            <option value="" disabled selected>
                                Choose a head, budget division/unit/authorized representative
                            </option>

                            @if (count($signatories) > 0)
                                @foreach ($signatories as $sig)
                                    @if ($sig->module->ors->funds_available)
                            <option value="{{ $sig->id }}" {{ $sig->id == $sigCertified2 ? 'selected' : '' }}>
                                {!! $sig->name !!} [{!! $sig->module->ors->designation !!}]
                            </option>
                                    @endif
                                @endforeach
                            @endif
                        </select>
                        <label class="mdb-main-label">
                            Head, Budget Division/Unit/Authorized Representative <span class="red-text">*</span>
                        </label>
                    </div>
                    <div class="md-form">
                        <input type="date" id="date-certified-2" name="date_certified_2"
                               class="form-control" value="{{ $dateCertified2 }}">
                        <label for="date-certified-2" class="active mt-3">
                            Date:
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
