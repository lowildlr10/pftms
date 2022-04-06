<form id="form-update" class="wow animated fadeIn d-flex justify-content-center" method="POST"
      action="{{ route('proc-dv-update', ['id' => $id]) }}">
    @csrf

    <div class="card doc-voucher p-0 w-responsive">
        <div class="card-body py-1">
            <div class="row">
                <div class="col-md-9 border border-bottom-0 border-dark">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="md-form">
                                <select class="mdb-select crud-select md-form" searchable="Search here.."
                                        name="funding_source">
                                    <option value="" disabled selected>Choose a funding/charging</option>
                                    <option value="">-- None --</option>

                                    @if (count($projects) > 0)
                                        @foreach ($projects as $fund)
                                    <option value="{{ $fund->id }}" {{ $fund->id == $project ? 'selected' : '' }}>
                                        {!! $fund->project_title !!}
                                    </option>
                                        @endforeach
                                    @endif
                                </select>
                                <label class="mdb-main-label">
                                    Funding/Charging Soruce <span class="red-text">*</span>
                                </label>
                            </div>
                        </div>
                    </div>
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
                               class="form-control" value="{{ !empty($fundCluster) ? $fundCluster: '01' }}">
                        <label for="fund-cluster" class="active">
                            <strong>Fund Cluster</strong>
                        </label>
                    </div>
                    <div class="md-form form-sm">
                        <input type="date" id="date-dv" name="date_dv"
                               class="form-control required" value="{{ $dvDate }}">
                        <label for="date-dv" class="active">
                            <strong>Date <span class="red-text">*</span></strong>
                        </label>
                    </div>
                    <div class="md-form form-sm">
                        <input type="text" id="dv-no" name="dv_no"
                               class="form-control" value="{{ $dvNo }}">
                        <label for="dv-no" class="{{ !empty($dvNo) ? 'active': '' }}">
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
                                   name="mode_payment[]" {{ $paymentMode1 == 1 ? 'checked': '' }}
                                   value="mds">
                            <label class="form-check-label" for="mds-check">
                                <strong>MDS Check</strong>
                            </label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="checkbox" class="form-check-input" id="commercial-check"
                                   name="mode_payment[]" {{ $paymentMode2 == 1 ? 'checked': '' }}
                                   value="commercial">
                            <label class="form-check-label" for="commercial-check">
                                <strong>Commercial Check</strong>
                            </label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="checkbox" class="form-check-input" id="ada"
                                   name="mode_payment[]" {{ $paymentMode3 == 1 ? 'checked': '' }}
                                   value="ada">
                            <label class="form-check-label" for="ada">
                                <strong>ADA</strong>
                            </label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="checkbox" class="form-check-input" id="others-check"
                                   name="mode_payment[]" {{ $paymentMode4 == 1 ? 'checked': '' }}
                                   value="others">
                            <label class="form-check-label" for="others-check">
                                <strong>Others (Please specify)</strong>
                            </label>
                        </div>
                        <div class="form-check form-check-inline">
                            <textarea class="md-textarea form-control" rows="1" id="other-payment"
                                      style="{{ $paymentMode4 == 0 ? 'display: none;': '' }}"
                                      name="other_payment" placeholder="Please specify here..."
                                      >{{ $otherPayment }}</textarea>
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
                                class="mdb-select md-form my-0 crud-select" disabled>
                            <option class="red-text" value="" disabled selected
                            >Payee</option>

                            @if (count($payees) > 0)
                                @foreach ($payees as $pay)
                            <option value="{{ $pay->id }}" {{ $pay->id == $payee ? 'selected': '' }}>
                                {{ $pay->company_name }}
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
                        <input type="text" class="form-control" value="{{ $tinNo }}" readonly>
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
                        Address<br>
                        <small class="grey-text">(From ORS/BURS)</small>
                    </div>
                </div>
                <div class="col-md-10 border border-left-0 border-bottom-0 border-dark">
                    <div class="md-form form-sm">
                        <textarea class="md-textarea form-control" rows="2" placeholder="Write address here..."
                                  readonly>{{ $address }}</textarea>
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
                                  placeholder="Write particulars here...">{{ $particulars }}</textarea>
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
                        >{{ $responsibilityCenter }}</textarea>
                    </div>
                </div>
                <div class="col-md-3 border border-left-0 border-bottom-0 border-dark px-0 text-center">
                    <div class="p-2 border-bottom border-dark">
                        <strong>MFO/PAP</strong>
                    </div>
                    <div class="md-form px-2">
                        <select class="mdb-select crud-select md-form" searchable="Search here.."
                                name="mfo_pap[]" multiple>
                            <option value="" disabled selected>Choose the MFO PAP</option>
                            <option value="">-- None --</option>

                            @if (count($mfoPAPs) > 0)
                                @foreach ($mfoPAPs as $pap)
                            <option value="{{ $pap->id }}" {{ in_array($pap->id, $mfoPAP) ? 'selected' : '' }}>
                                {!! $pap->code !!} : {!! $pap->description !!}
                            </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    {{--
                    <div class="form-group p-0 m-0">
                        <textarea class="md-textarea form-control border border-0 rounded-0"
                                  rows="8" placeholder="Write MFO/PAP here..." name="mfo_pap"
                        >{{ $mfoPAP }}</textarea>
                    </div>
                    --}}
                </div>
                <div class="col-md-3 border border-left-0 border-bottom-0 border-dark px-0 text-center">
                    <div class="p-2 border-bottom border-dark">
                        <strong>AMOUNT <span class="red-text">*</span></strong>
                    </div>
                    <div class="md-form px-3">
                        <input type="number" id="amount" name="amount" placeholder="Total Amount..."
                               class="form-control required" value="{{ $amount }}">
                        <label for="amount" class="active px-3">
                            Total Amount <span class="red-text">*</span>
                        </label>
                    </div>
                    <div class="md-form px-3">
                        <input type="number" id="prior_year" name="prior_year" placeholder="Prior year (Optional)..."
                               class="form-control" value="{{ $priorYear }}">
                        <label for="prior_year" class="active px-3">Prior Year</label>
                    </div>
                    <div class="md-form px-3">
                        <input type="number" id="continuing" name="continuing" placeholder="Continuing (Optional)..."
                               class="form-control" value="{{ $continuing }}">
                        <label for="continuing" class="active px-3">Continuing</label>
                    </div>
                    <div class="md-form px-3">
                        <input type="number" id="current" name="current" placeholder="Current (Optional)..."
                               class="form-control" value="{{ $current }}">
                        <label for="current" class="active px-3">Current</label>
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
                        <select searchable="Search here.." class="mdb-select md-form my-0 crud-select" disabled>
                            <option class="red-text" value="" disabled selected
                            >Printed Name, Designation and Signature of Supervisor</option>

                            @if (count($signatories) > 0)
                                @foreach ($signatories as $sig)
                                    @if ($sig->module->dv->supervisor)
                            <option value="{{ $sig->id }}" {{ $sig->id == $sigCertified ? 'selected' : '' }}>
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
                <div class="col-md-5 border border-bottom-0 border-dark px-0 text-center">
                    <div class="p-2 border-bottom border-dark">
                        <strong>Account Title</strong>
                    </div>
                </div>
                <div class="col-md-3 border border-left-0 border-bottom-0 border-dark px-0 text-center">
                    <div class="p-2 border-bottom border-dark">
                        <strong>UACS Code</strong>
                    </div>

                    <button type="button" class="btn btn-link btn-block waves-effect my-2"
                            onclick="$(this).showUacsItems('{{ route('proc-dv-show-uacs-items',
                            ['id' => $id]) }}');">
                        <i class="fas fa-tags"></i> <b>Add/Update UACS Items</b>
                    </button>

                    <div class="md-form p-0">
                        <textarea id="uacs-code-display" rows="8" class="md-textarea w-75 p-0" style="overflow: auto;"
                                  placeholder="Selected UACS Object Codes" readonly>{!! $uacsDisplay !!}</textarea>
                    </div>

                    <input type="hidden" id="uacs-code" name="uacs_object_code">
                    <div id="uacs-items-segment" style="display: none;">
                        @if (count($uacsItems) > 0)
                            @foreach ($uacsItems as $itemCtr => $item)
                        <input type="hidden" name="uacs_description[{{ $itemCtr }}]" value="{{ $item->description }}">
                        <input type="hidden" name="uacs_id[{{ $itemCtr }}]" value="{{ $item->uacs_id }}">
                        <input type="hidden" name="dv_uacs_id[{{ $itemCtr }}]" value="{{ $item->id }}">
                        <input type="hidden" name="uacs_amount[{{ $itemCtr }}]" value="{{ $item->amount }}">
                            @endforeach
                        @endif

                        @if (count($_uacsItems) > 0 && count($uacsItems) == 0)
                            @foreach ($_uacsItems as $itemCtr => $item)
                        <input type="hidden" name="uacs_description[{{ $itemCtr }}]" value="{{ $item->account_title }}">
                        <input type="hidden" name="uacs_id[{{ $itemCtr }}]" value="{{ $item->id }}">
                        <input type="hidden" name="dv_uacs_id[{{ $itemCtr }}]" value="">
                        <input type="hidden" name="uacs_amount[{{ $itemCtr }}]" value="0">
                            @endforeach
                        @endif
                    </div>
                </div>
                <div class="col-md-2 border border-left-0 border-bottom-0 border-dark px-0 text-center">
                    <div class="p-2 border-bottom border-dark">
                        <strong>Debit</strong>
                    </div>
                </div>
                <div class="col-md-2 border border-left-0 border-bottom-0 border-dark px-0 text-center">
                    <div class="p-2 border-bottom border-dark">
                        <strong>Credit</strong>
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
                            <option value="{{ $sig->id }}" {{ $sig->id == $sigAccounting ? 'selected' : '' }}>
                                {!! $sig->name !!} [{!! $sig->module->dv->designation !!}]
                            </option>
                                    @endif
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="md-form my-0">
                        <input type="date" id="date-accounting" name="date_accounting"
                               class="form-control" value="{{ $dateAccounting }}">
                        <label for="date-accounting mt-3" class="active">
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
                            <option value="{{ $sig->id }}" {{ $sig->id == $sigAgencyHead ? 'selected' : '' }}>
                                {!! $sig->name !!} [{!! $sig->module->dv->designation !!}]
                            </option>
                                    @endif
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="md-form my-0">
                        <input type="date" id="date-agency-head" name="date_agency_head"
                               class="form-control" value="{{ $dateAgencyHead }}">
                        <label for="date-agency-head" class="active">
                            Date:
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
