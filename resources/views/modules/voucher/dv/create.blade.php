<form id="form-store" class="wow animated fadeIn d-flex justify-content-center" method="POST"
      action="{{ route('ca-dv-store') }}">
    @csrf

    <div class="card w-responsive">
        <div class="card-body py-1">
            <div class="row">
                <div class="col-md-9 border border-bottom-0 border-dark">
                    <div class="md-form row">
                        <div class="col-md-3"></div>
                        <div class="col-md-6 text-center">
                            <strong class="h4">
                                DISBURSEMENT VOUCHER
                                <div class="md-form text-left">
                                    <select id="ors-id" name="ors_id" searchable="Search here.."
                                            class="mdb-select crud-select md-form my-0">
                                        <option class="red-text" value="" disabled selected>
                                            Choose a ORS/BURS document
                                        </option>
                                        <option value="">-- None --</option>

                                        @foreach ($orsList as $orsDat)
                                        <option class="red-text" value="{{ $orsDat->id }}" {{ $orsID == $orsDat->id ? 'selected' : '' }}>
                                            Serial no. {{ $orsDat->serial_no }} : {{ $orsDat->particulars }}
                                        </option>
                                        @endforeach
                                    </select>
                                    <label class="mdb-main-label">
                                        ORS/BURS Document <span class="red-text">*</span>
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
                <div class="col-md-3 border border-left-0 border-bottom-0 border-dark">
                    <div class="md-form form-sm">
                        <input type="text" id="fund-cluster" name="fund_cluster"
                               class="form-control" value="01">
                        <label for="fund-cluster" class="active">
                            <strong>Fund Cluster</strong>
                        </label>
                    </div>
                    <div class="md-form form-sm">
                        <input type="date" id="date-dv" name="date_dv"
                               class="form-control required">
                        <label for="date-dv" class="py-3 active">
                            <strong>Date <span class="red-text">*</span></strong>
                        </label>
                    </div>
                    <div class="md-form form-sm">
                        <input type="text" id="dv-no" name="dv_no"
                               class="form-control">
                        <label for="dv-no">
                            <strong>DV Number</strong>
                        </label>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-2 border border-bottom-0 border-dark text-center">
                    <div class="md-form">
                        Mode of Payment<br>
                        <small class="grey-text">(Select by clicking the checkbox)</small>
                    </div>
                </div>
                <div class="col-md-10 border border-left-0 border-bottom-0 border-dark text-center">
                    <div class="md-form form-sm my-3">
                        <div class="form-check form-check-inline">
                            <input type="checkbox" class="form-check-input" id="mds-check"
                                   name="mode_payment[]"
                                   value="mds">
                            <label class="form-check-label" for="mds-check">
                                <strong>MDS Check</strong>
                            </label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="checkbox" class="form-check-input" id="commercial-check"
                                   name="mode_payment[]"
                                   value="commercial">
                            <label class="form-check-label" for="commercial-check">
                                <strong>Commercial Check</strong>
                            </label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="checkbox" class="form-check-input" id="ada"
                                   name="mode_payment[]"
                                   value="ada">
                            <label class="form-check-label" for="ada">
                                <strong>ADA</strong>
                            </label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="checkbox" class="form-check-input" id="others-check"
                                   name="mode_payment[]"
                                   value="others">
                            <label class="form-check-label" for="others-check">
                                <strong>Others (Please specify)</strong>
                            </label>
                        </div>
                        <div class="form-check form-check-inline">
                            <textarea class="md-textarea form-control" rows="1" id="other-payment"
                                      name="other_payment" placeholder="Please specify here..."
                                      ></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-2 border border-bottom-0 border-dark text-center">
                    <div class="md-form">
                        Payee <span class="red-text">*</span><br>
                        <small class="grey-text">(From ORS/BURS)</small>
                    </div>
                </div>
                <div class="col-md-5 border border-left-0 border-bottom-0 border-dark">
                    <div class="md-form">
                        <select id="payee" name="payee" searchable="Search here.."
                                class="mdb-select md-form my-0 crud-select required">
                            <option class="red-text" value="" disabled selected
                            >Payee</option>

                            @if (count($payees) > 0)
                                @foreach ($payees as $emp)
                            <option value="{{ $emp->id }}" {{ $emp->id == $payee ? 'selected': '' }}>
                                {{ $emp->firstname }} {{ $emp->lastname }}
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
                <div class="col-md-3 border border-left-0 border-bottom-0 border-dark">
                    <div class="md-form">
                        <input type="text" class="form-control" value="{{ $empID }}" readonly>
                        <label for="date-ors-burs" class="active">
                            <strong>TIN/Employee No</strong>
                        </label>
                    </div>
                </div>
                <div class="col-md-2 border border-left-0 border-bottom-0 border-dark">
                    <div class="md-form">
                        <input type="text" class="form-control" value="{{ $serialNo }}" readonly>
                        <label for="date-ors-burs" class="active">
                            <strong>ORS/BURS No</strong>
                        </label>
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
                        <textarea class="md-textarea form-control required" rows="2" placeholder="Write address here..."
                        >{{ $address }}</textarea>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 border border-bottom-0 border-dark px-0 text-center">
                    <div class="p-2 border-bottom border-dark">
                        <strong>Particulars <span class="red-text">*</span></strong>
                    </div>
                    <div class="form-group p-0 m-0">
                        <textarea class="md-textarea form-control border border-0 rounded-0 required"
                                  id="particulars" name="particulars" rows="8"
                                  placeholder="Write particulars here..."></textarea>
                    </div>
                </div>
                <div class="col-md-2 border border-left-0 border-bottom-0 border-dark px-0 text-center">
                    <div class="p-2 border-bottom border-dark">
                        <strong>Responsibilty Center</strong>
                    </div>
                    <div class="form-group p-0 m-0">
                        <textarea class="md-textarea form-control border border-0 rounded-0"
                                  rows="8" placeholder="Write Responsibilty Center here..."
                                  name="responsibility_center"
                        ></textarea>
                    </div>
                </div>
                <div class="col-md-3 border border-left-0 border-bottom-0 border-dark px-0 text-center">
                    <div class="p-2 border-bottom border-dark">
                        <strong>MFO/PAP</strong>
                    </div>
                    <div class="form-group p-0 m-0">
                        <textarea class="md-textarea form-control border border-0 rounded-0"
                                  rows="8" placeholder="Write MFO/PAP here..." name="mfo_pap"
                        ></textarea>
                    </div>
                </div>
                <div class="col-md-3 border border-left-0 border-bottom-0 border-dark px-0 text-center">
                    <div class="p-2 border-bottom border-dark">
                        <strong>AMOUNT <span class="red-text">*</span></strong>
                    </div>
                    <div class="md-form px-3">
                        <input type="number" id="amount" name="amount" class="form-control required"
                               value="{{ $amount }}">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-9 border border-bottom-0 border-dark px-0 text-center">
                    <div class="md-form">
                        <strong>Amount Due</strong>
                    </div>
                </div>
                <div class="col-md-3 border border-left-0 border-bottom-0 border-dark px-0 text-center">
                    <div class="md-form px-3">
                        <input type="number" id="total-amount" value="{{ $amount }}"
                               class="form-control" readonly>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 border border-bottom-0 border-dark">
                    <div class="md-form">
                        <small>
                            [ A ] Certified: Expenses/Cash Advance necessary, lawful and
                            incurred under my direct supervision.
                        </small>
                        <select searchable="Search here.." class="mdb-select md-form my-0 crud-select required"
                                id="sig-certified" name="sig_certified">
                            <option class="red-text" value="" disabled selected
                            >* Printed Name, Designation and Signature of Supervisor</option>

                            @if (count($signatories) > 0)
                                @foreach ($signatories as $sig)
                                    @if ($sig->module->dv->supervisor)
                            <option value="{{ $sig->id }}" {{ $sig->id == $sigCert1 ? 'selected' : '' }}>
                                {!! $sig->name !!} [{!! $sig->module->dv->designation !!}]
                            </option>
                                    @endif
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 border border-bottom-0 border-dark grey lighten-2">
                    <div class="md-form">
                        <small>
                            [ B ] Accounting Entry
                        </small>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 border border-bottom-0 border-dark grey lighten-2">
                    <div class="md-form">
                        <small>
                            [ C ] Certified
                        </small>
                    </div>
                </div>
                <div class="col-md-6 border border-left-0 border-bottom-0 border-dark grey lighten-2">
                    <div class="md-form">
                        <small>
                            [ D ] Approved for Payment
                        </small>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 border border-dark">
                    <div class="md-form">
                        <select searchable="Search here.." class="mdb-select md-form required my-0 crud-select"
                                id="sig-accounting" name="sig_accounting">
                            <option class="red-text" value="" disabled selected
                            >* Head, Accounting Unit/Authorized Representative</option>

                            @if (count($signatories) > 0)
                                @foreach ($signatories as $sig)
                                    @if ($sig->module->dv->accounting)
                            <option value="{{ $sig->id }}">
                                {!! $sig->name !!} [{!! $sig->module->dv->designation !!}]
                            </option>
                                    @endif
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="md-form my-0">
                        <input type="date" id="date-accounting" name="date_accounting"
                               class="form-control">
                        <label for="date-accounting mt-3" class="active mt-3">
                            Date:
                        </label>
                    </div>
                </div>
                <div class="col-md-6 border border-left-0 border-dark">
                    <div class="md-form">
                        <select searchable="Search here.." class="mdb-select md-form required my-0 crud-select"
                                id="sig-agency-head" name="sig_agency_head">
                            <option class="red-text" value="" disabled selected
                            >* Agency Head/Authorized Representative</option>

                            @if (count($signatories) > 0)
                                @foreach ($signatories as $sig)
                                    @if ($sig->module->dv->agency_head)
                            <option value="{{ $sig->id }}">
                                {!! $sig->name !!} [{!! $sig->module->dv->designation !!}]
                            </option>
                                    @endif
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="md-form my-0">
                        <input type="date" id="date-agency-head" name="date_agency_head"
                               class="form-control">
                        <label for="date-agency-head" class="active mt-3">
                            Date:
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>