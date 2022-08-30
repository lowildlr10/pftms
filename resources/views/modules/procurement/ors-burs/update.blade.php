<form id="form-update" class="wow animated fadeIn d-flex justify-content-center" method="POST"
      action="{{ route('proc-ors-burs-update', ['id' => $id]) }}">
    @csrf

    <div class="card doc-voucher p-0 w-responsive">
        <div class="card-body py-1">
            <div class="row">
                <div class="col-md-8 border border-bottom-0 border-dark">
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
                                    Funding/Charging Source <span class="red-text">*</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="md-form row">
                        <div class="col-md-3"></div>
                        <div class="col-md-6 text-center">
                            <strong class="h4">
                                OBLIGATION/BUDGET UTILIZATION REQUEST AND STATUS
                                <div class="md-form">
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
                               class="form-control" value="{{ $dateORS }}">
                        <label for="date-ors-burs" class="active">
                            <strong>Date</strong>
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
                                @foreach ($payees as $bid)
                            <option value="{{ $bid->id }}" {{ $bid->id == $payee ? 'selected': '' }}>
                                {{ $bid->company_name }}
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
                    <div class="md-form form-sm">
                        <textarea class="md-textarea form-control required" id="address" name="address"
                                  rows="2" placeholder="Write address here...">{{ $address }}</textarea>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-2 border border-bottom-0 border-dark px-0 text-center">
                    <div class="p-2 border-bottom border-dark">
                        <strong>Responsibilty Center <span class="red-text">*</span></strong>
                    </div>
                    <div class="md-form px-3">
                        <input type="text" id="responsibility_center" name="responsibility_center"
                               class="form-control required" value="{{ $responsibilityCenter }}"
                               required>
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
                        <strong>MFO/PAP <span class="red-text">*</span></strong>
                    </div>
                    <div class="md-form px-2">
                        <select class="mdb-select crud-select md-form required" searchable="Search here.."
                                name="mfo_pap[]" multiple>
                            <option value="" disabled selected>Choose the MFO PAP</option>

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
                        <textarea class="md-textarea form-control border border-0 rounded-0 required"
                                  id="mfo-pap" name="mfo_pap" rows="8" placeholder="Write MFO/PAP here..."
                        >{{ $mfoPAP }}</textarea>
                    </div>
                    --}}
                </div>
                <div class="col-md-2 border border-left-0 border-bottom-0 border-dark px-0 text-center">
                    <div class="p-2 border-bottom border-dark">
                        <strong>UACS Object Code</strong>
                    </div>

                    <button type="button" class="btn btn-link btn-block waves-effect my-2"
                            onclick="$(this).showUacsItems('{{ route('proc-ors-burs-show-uacs-items',
                            ['id' => $id]) }}');">
                        <i class="fas fa-tags"></i> Update UACS Items
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
                        <input type="hidden" name="ors_uacs_id[{{ $itemCtr }}]" value="{{ $item->id }}">
                        <input type="hidden" name="uacs_amount[{{ $itemCtr }}]" value="{{ $item->amount }}">
                            @endforeach
                        @endif

                        @if (count($_uacsItems) > 0 && count($uacsItems) == 0)
                            @foreach ($_uacsItems as $itemCtr => $item)
                        <input type="hidden" name="uacs_description[{{ $itemCtr }}]" value="{{ $item->account_title }}">
                        <input type="hidden" name="uacs_id[{{ $itemCtr }}]" value="{{ $item->id }}">
                        <input type="hidden" name="ors_uacs_id[{{ $itemCtr }}]" value="">
                        <input type="hidden" name="uacs_amount[{{ $itemCtr }}]" value="0">
                            @endforeach
                        @endif
                    </div>


                    {{--
                    <div class="md-form px-2">
                        <select class="mdb-select crud-select md-form" searchable="Search here.."
                                id="sel-uacs-code" name="uacs_object_code[]" multiple>
                            <option value="" disabled selected>Choose the MOOE account titles</option>
                            <option value="">-- None --</option>

                            @if (count($mooeTitles) > 0)
                                @foreach ($mooeTitles as $mooe)
                            <option value="{{ $mooe->id }}"  {{ in_array($mooe->id, $uacsObjectCode) ? 'selected' : '' }}>
                                {!! $mooe->uacs_code !!} : {!! $mooe->account_title !!}
                            </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    --}}
                </div>
                <div class="col-md-2 border border-left-0 border-bottom-0 border-dark px-0 text-center">
                    <div class="p-2 border-bottom border-dark">
                        <strong>AMOUNT <span class="red-text">*</span></strong>
                    </div>
                    <div class="md-form px-3">
                        <input type="number" id="amount" name="amount" placeholder="Total Amount..."
                               class="form-control required" value="{{ $amount }}"
                               data-toggle="tooltip" data-placement="right"
                               title="This should be equals or greater than zero.">
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
                        <label for="date-certified-1" class="active">
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
                        <label for="date-certified-2" class="active">
                            Date:
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
