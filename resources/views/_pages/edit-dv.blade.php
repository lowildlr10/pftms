<form id="form-update" method="POST" class="wow animated fadeIn d-flex justify-content-center"
      action="{{ $actionURL }}">
    @csrf
    <input type="hidden" name="module_type" value="{{ $moduleType }}">
    <div class="card w-responsive">
        <div class="card-body py-1">
            <div class="row">
                <div class="col-md-9 border border-bottom-0 border-dark">
                    <div class="md-form row">
                        <div class="col-md-3"></div>
                        <div class="col-md-6 text-center">
                            <strong class="h4">
                                DISBURSEMENT VOUCHER
                            </strong>
                        </div>
                        <div class="col-md-3"></div>
                    </div>
                </div>
                <div class="col-md-3 border border-left-0 border-bottom-0 border-dark">
                    <div class="md-form form-sm">
                        <input type="text" id="fund-cluster" name="fund_cluster"
                               class="form-control" value="{{ $dv->fund_cluster }}">
                        <label for="fund-cluster" class="{{ !empty($dv->fund_cluster) ? 'active': '' }}">
                            <strong>Fund Cluster</strong>
                        </label>
                    </div>
                    <div class="md-form form-sm">
                        <input type="date" id="date-dv" name="date_dv"
                               class="form-control" value="{{ $dv->date_dv }}">
                        <label for="date-dv" class="active">
                            <strong>Date</strong>
                        </label>
                    </div>
                    <div class="md-form form-sm">
                        <input type="text" id="dv-no" name="dv_no"
                               class="form-control" value="{{ $dv->dv_no }}">
                        <label for="dv-no" class="{{ !empty($dv->dv_no) ? 'active': '' }}">
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
                                   name="mds_check" {{ $paymentMode[0] == 1 ? 'checked': '' }}>
                            <label class="form-check-label" for="mds-check">
                                <strong>MDS Check</strong>
                            </label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="checkbox" class="form-check-input" id="commercial-check"
                                   name="commercial_check" {{ $paymentMode[1] == 1 ? 'checked': '' }}>
                            <label class="form-check-label" for="commercial-check">
                                <strong>Commercial Check</strong>
                            </label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="checkbox" class="form-check-input" id="ada"
                                   name="ada" {{ $paymentMode[2] == 1 ? 'checked': '' }}>
                            <label class="form-check-label" for="ada">
                                <strong>ADA</strong>
                            </label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="checkbox" class="form-check-input" id="others-check"
                                   name="others_check" {{ $paymentMode[3] == 1 ? 'checked': '' }}>
                            <label class="form-check-label" for="others-check">
                                <strong>Others (Please specify)</strong>
                            </label>
                        </div>
                        <div class="form-check form-check-inline">
                            <textarea class="md-textarea form-control" rows="1" id="other-payment"
                                      style="{{ $paymentMode[3] == 0 ? 'display: none;': '' }}"
                                      name="other_payment" placeholder="Please specify here..."
                                      >{{ $dv->other_payment }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-2 border border-bottom-0 border-dark text-center">
                    <div class="md-form">
                        Payee<br>
                        <small class="grey-text">(From ORS/BURS)</small>
                    </div>
                </div>
                <div class="col-md-5 border border-left-0 border-bottom-0 border-dark">
                    <div class="md-form">
                        <select id="payee" name="payee" searchable="Search here.."
                                class="mdb-select md-form my-0" disabled>
                            <option class="red-text" value="" disabled selected
                            >Payee</option>

                            @if (count($payees) > 0)
                                @if ($moduleType == 'cashadvance')
                                    @foreach ($payees as $payee)
                            <option value="{{ $payee->emp_id }}" {{ $dv->payee == $payee->emp_id ? 'selected': '' }}
                                >{{ $payee->name }} [ {{ $payee->position }} ]
                            </option>
                                        @if ($dv->payee == $payee->emp_id)
                                            {{ $tinEmpNo = $payee->emp_id }}
                                        @endif
                                    @endforeach
                                @else
                                    @foreach ($payees as $payee)
                            <option value="{{ $payee->id }}" {{ $dv->payee == $payee->id ? 'selected': '' }}
                                >{{ $payee->company_name }}
                            </option>
                                        @if ($dv->payee == $payee->id)
                                            {{ $tinEmpNo = $payee->tin_no }}
                                        @endif
                                    @endforeach
                                @endif
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
                        <input type="text" class="form-control" value="{{ $tinEmpNo }}" readonly>
                        <label for="date-ors-burs" class="active">
                            <strong>TIN/Employee No</strong>
                        </label>
                    </div>
                </div>
                <div class="col-md-2 border border-left-0 border-bottom-0 border-dark">
                    <div class="md-form">
                        <input type="text" class="form-control" value="{{ $dv->serial_no }}" readonly>
                        <label for="date-ors-burs" class="active">
                            <strong>ORS/BURS No</strong>
                        </label>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-2 border border-bottom-0 border-dark text-center">
                    <div class="md-form">
                        Address<br>
                        <small class="grey-text">(From ORS/BURS)</small>
                    </div>
                </div>
                <div class="col-md-10 border border-left-0 border-bottom-0 border-dark">
                    <div class="md-form form-sm my-0">
                        <textarea class="md-textarea form-control" rows="2" placeholder="Write address here..."
                                  readonly>{{ $dv->address }}</textarea>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 border border-bottom-0 border-dark px-0 text-center">
                    <div class="p-2 border-bottom border-dark">
                        <span class="red-text">* </span>
                        <strong>Particulars</strong>
                    </div>
                    <div class="form-group p-0 m-0">
                        <textarea class="md-textarea form-control border border-0 rounded-0 required"
                                  id="particulars" name="particulars" rows="8"
                                  placeholder="Write particulars here...">{{ $dv->particulars }}</textarea>
                    </div>
                </div>
                <div class="col-md-2 border border-left-0 border-bottom-0 border-dark px-0 text-center">
                    <div class="p-2 border-bottom border-dark">
                        <strong>Responsibilty Center</strong>
                    </div>
                    <div class="md-form px-3">
                        <input type="text" class="form-control" value="{{ $dv->responsibility_center }}"
                               readonly>
                    </div>
                </div>
                <div class="col-md-3 border border-left-0 border-bottom-0 border-dark px-0 text-center">
                    <div class="p-2 border-bottom border-dark">
                        <strong>MFO/PAP</strong>
                    </div>
                    <div class="form-group p-0 m-0">
                        <textarea class="md-textarea form-control border border-0 rounded-0"
                                  rows="8" placeholder="Write MFO/PAP here..." readonly
                        >{{ $dv->mfo_pap }}</textarea>
                    </div>
                </div>
                <div class="col-md-3 border border-left-0 border-bottom-0 border-dark px-0 text-center">
                    <div class="p-2 border-bottom border-dark">
                        <strong>AMOUNT</strong>
                    </div>
                    <div class="md-form px-3">
                        <input type="number" class="form-control" value="{{ $dv->amount }}" readonly>
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
                        <input type="number" value="{{ $dv->amount }}"
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
                        <select searchable="Search here.." class="mdb-select md-form my-0" disabled>
                            <option class="red-text" value="" disabled selected
                            >Printed Name, Designation and Signature of Supervisor</option>

                            @if (count($signatories) > 0)
                                @foreach ($signatories as $supervisor)
                                    @if ($supervisor->dv_sign_type == 'supervisor' ||
                                         $supervisor->dv_sign_type == 'agency-head')
                            <option value="{{ $supervisor->id }}"
                                    {{ $dv->sig_certified_1 == $supervisor->id ? 'selected': '' }}
                                >{{ $supervisor->name }} [ {{ $supervisor->position }} ]
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
                        <select searchable="Search here.." class="mdb-select md-form required my-0"
                                id="sig-accounting" name="sig_accounting">
                            <option class="red-text" value="" disabled selected
                            >* Head, Accounting Unit/Authorized Representative</option>

                            @if (count($signatories) > 0)
                                @foreach ($signatories as $accountant)
                                    @if ($accountant->dv_sign_type == 'accountant')
                            <option value="{{ $accountant->id }}"
                                    {{ $dv->sig_accounting == $accountant->id ? 'selected': '' }}
                                >{{ $accountant->name }} [ {{ $accountant->position }} ]
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
                        <input type="date" id="date-accounting" name="date_accounting"
                               class="form-control" value="{{ $dv->date_accounting }}">
                        <label for="date-accounting" class="active">
                            Date:
                        </label>
                    </div>
                </div>
                <div class="col-md-6 border border-left-0 border-dark">
                    <div class="md-form">
                        <select searchable="Search here.." class="mdb-select md-form required my-0"
                                id="sig-agency-head" name="sig_agency_head">
                            <option class="red-text" value="" disabled selected
                            >* Agency Head/Authorized Representative</option>

                            @if (count($signatories) > 0)
                                @foreach ($signatories as $agencyHead)
                                    @if ($agencyHead->dv_sign_type == 'agency-head')
                            <option value="{{ $agencyHead->id }}"
                                    {{ $dv->sig_agency_head == $agencyHead->id ? 'selected': '' }}
                                >{{ $agencyHead->name }} [ {{ $agencyHead->position }} ]
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
                        <input type="date" id="date-agency-head" name="date_agency_head"
                               class="form-control" value="{{ $dv->date_agency_head }}">
                        <label for="date-agency-head" class="active">
                            Date:
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
