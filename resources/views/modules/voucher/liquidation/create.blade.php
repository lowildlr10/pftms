<form id="form-store" class="wow animated fadeIn d-flex justify-content-center" method="POST"
      action="{{ route('ca-lr-store') }}">
    @csrf
    <div class="card doc-voucher p-0">
        <div class="card-body py-1">
            <div class="row">
                <div class="col-md-8 border border-bottom-0 border-dark">
                    <div class="md-form row">
                        <div class="col-md-3"></div>
                        <div class="col-md-6 text-center">
                            <strong class="h4">
                                LIQUIDATION REPORT
                            </strong>
                        </div>
                        <div class="col-md-3"></div>
                    </div>

                    <div class="md-form form-sm row">
                        <div class="col-md-4"></div>
                        <div class="col-md-4">
                            <input type="text" id="period-cover" name="period_cover"
                                class="form-control w-100 required">
                            <label for="period-cover" style="padding-left: 15px;">
                                <span class="red-text">* </span>
                                <strong>Period Cover</strong>
                            </label>
                        </div>
                        <div class="col-md-4"></div>
                    </div>
                </div>
                <div class="col-md-4 border border-left-0 border-dark">
                    <div class="md-form form-sm">
                        <input type="text" id="serial-no" name="serial_no"
                            class="form-control">
                        <label for="serial-no">
                            <strong>Serial Number</strong>
                        </label>
                    </div>
                    <div class="md-form form-sm">
                        <input type="date" id="date-liquidation" name="date_liquidation"
                            class="form-control required">
                        <label for="date-liquidation" class="active">
                            <span class="red-text">* </span>
                            <strong>Date</strong>
                        </label>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8 border border-top-0 border-bottom-0 border-dark">
                    <div class="md-form form-sm">
                        <input type="text" id="entity-name" name="entity_name"
                            class="form-control required" value="Department of Science and Technology">
                        <label for="entity-name" class="active">
                            <span class="red-text">* </span>
                            <strong>Entity Name</strong>
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
                <div class="col-md-4 border border-top-0 border-left-0 border-bottom-0 border-dark">
                    <div class="md-form form-sm">
                        <input type="text" id="responsibility-center" name="responsibility_center"
                               class="form-control required" value="19 001 03000 14">
                        <label for="responsibility-center" class="active">
                            <span class="red-text">* </span>
                            <strong>Responsibility Center Code</strong>
                        </label>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8 border border-bottom-0 border-dark px-0 text-center">
                    <div class="p-2 border-bottom border-dark">
                        <span class="red-text">* </span>
                        <strong>PARTICULARS</strong>
                    </div>
                    <div class="form-group p-0 m-0">
                        <textarea class="form-control border border-0 rounded-0 required" id="particulars" name="particulars"
                                  rows="8" placeholder="Write particulars here..."
                                  >{{ isset($particulars) ? $particulars : 'To liquidate...' }}</textarea>
                    </div>
                </div>
                <div class="col-md-4 border border-left-0 border-bottom-0 border-dark px-0 text-center">
                    <div class="p-2 border-bottom border-dark">
                        <span class="red-text">* </span>
                        <strong>AMOUNT</strong>
                    </div>
                    <div class="md-form px-3">
                        <input type="number" id="amount" name="amount" placeholder="Enter a value..."
                               class="form-control required" value="{{ $amount }}">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8 border border-bottom-0 border-dark">
                    <div class="md-form">
                        TOTAL AMOUNT SPENT
                    </div>
                </div>
                <div class="col-md-4 border border-left-0 border-bottom-0 border-dark p-2">
                    <div class="md-form my-0">
                        <input type="number" id="total-amount" name="total_amount"
                               placeholder="Enter a value..." class="form-control required">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8 border border-bottom-0 border-dark py-2 pl-0">
                    <div class="my-0">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="md-form my-0 mx-2">
                                    <div class="md-label">
                                        AMOUNT OF CASH ADVANCE PER DV NO. <span class="red-text">* </span>
                                    </div>
                                    <div class="md-form my-0">
                                        <select id="dv-id" name="dv_id" searchable="Search here.."
                                                class="mdb-select crud-select md-form my-0 required">
                                            <option value="" disabled selected
                                            > Choose a DV document</option>

                                            @if (count($dvList) > 0)
                                                @foreach ($dvList as $dv)
                                            <option value="{{ $dv->id }}" {{ $dvID == $dv->id ? 'selected' : '' }}>
                                                {{ $dv->dv_no && $dv->dv_no != '.' ?
                                                   "DV Number: $dv->dv_no ($dv->particulars)" :
                                                   $dv->particulars }}
                                            </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 my-0 md-form input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text md-addon" id="dtd">DTD:</span>
                                </div>
                                <input type="date" class="form-control" id="dv-dtd" name="dv_dtd"
                                       value="{{ $dvDate }}">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 border border-left-0 border-bottom-0 border-dark p-2">
                    <div class="md-form my-0">
                        <input type="number" id="amount-cash-adv" name="amount_cash_adv"
                               placeholder="Enter a value..." class="form-control required">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8 border border-bottom-0 border-dark py-2 pl-0">
                    <div class="md-form input-group my-0">
                        <div class="input-group-prepend">
                            <span class="input-group-text md-addon pl-2" id="or-no">
                                AMOUNT REFUNDED PER OR NO.
                            </span>
                        </div>
                        <input type="text" class="form-control" id="or-no" name="or_no"
                               placeholder="...">
                        <div class="input-group-prepend">
                            <span class="input-group-text md-addon" id="dtd">DTD:</span>
                        </div>
                        <input type="date" class="form-control" id="or-dtd" name="or_dtd">
                    </div>
                </div>
                <div class="col-md-4 border border-left-0 border-bottom-0 border-dark p-2">
                    <div class="md-form my-0">
                        <input type="number" id="amount-refunded" name="amount_refunded"
                               placeholder="Enter a value..." class="form-control required">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8 border border-bottom-0 border-dark p-2">
                    AMOUNT TO BE REIMBURSED
                </div>
                <div class="col-md-4 border border-left-0 border-bottom-0 border-dark p-2">
                    <div class="md-form my-0">
                        <input type="number" id="amount-reimbursed" name="amount_reimbursed"
                               placeholder="Enter a value..." class="form-control required">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 border border-dark">
                    <div class="md-form">
                        <small>
                            <span class="red-text">* </span>
                            [ A ] Certified: Correctness of the above date
                        </small>
                        <select name="sig_claimant" id="claimant" class="claimant-tokenizer">
                            @if (isset($claimant))
                                @foreach ($claimants as $_claimants)
                                    @foreach ($_claimants as $claim)
                                        @if ($claim->id == $claimant)
                            <option value="{{ $claim->id }}" selected>{{ $claim->payee_name }}</option>
                                        @endif
                                    @endforeach
                                @endforeach
                            @endif
                        </select>

                        {{--
                        <select id="sig-claimant" name="sig_claimant" searchable="Search here.."
                                class="mdb-select crud-select md-form my-0 required">
                            <option value="" disabled selected
                            > Claimant</option>

                            @if (count($claimants) > 0)
                                @foreach ($claimants as $claim)
                            <option value="{{ $claim->id }}" {{ $claimant == $claim->id ? 'selected' : '' }}>
                                {{ $claim->firstname }} {{ $claim->lastname }}
                            </option>
                                @endforeach
                            @else
                            <option value="" disabled>
                                No data...
                            </option>
                            @endif
                        </select>
                        --}}
                    </div>
                    <div class="md-form my-0">
                        <input type="date" id="date-claimant" name="date_claimant"
                            class="form-control">
                        <label for="date-claimant" class="active">
                            Date:
                        </label>
                    </div>
                </div>
                <div class="col-md-4 border border-left-0 border-dark">
                    <div class="md-form">
                        <small>
                            <span class="red-text">* </span>
                            [ B ] Certified: Purpose of travel/cash advance duly accomplished
                        </small>
                        <select id="sig-supervisor" name="sig_supervisor" searchable="Search here.."
                                class="mdb-select crud-select md-form my-0 required">
                            <option value="" disabled selected
                            > Immediate Supervisor</option>

                            @if (count($signatories) > 0)
                                @foreach ($signatories as $sig)
                                    @if ($sig->module->lr->immediate_sup)
                            <option value="{{ $sig->id }}">
                                {!! $sig->name !!} [{!! $sig->module->lr->designation !!}]
                            </option>
                                    @endif
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="md-form my-0">
                        <input type="date" id="date-supervisor" name="date_supervisor"
                               class="form-control">
                        <label for="date-supervisor" class="active">
                            Date:
                        </label>
                    </div>
                </div>
                <div class="col-md-4 border border-left-0 border-dark">
                    <div class="md-form">
                        <small>
                            <span class="red-text">* </span>
                            [ C ] Certified: Supporting documents complete and proper
                        </small>
                        <select id="sig-accounting" name="sig_accounting" searchable="Search here.."
                                class="mdb-select crud-select md-form my-0 required">
                            <option value="" disabled selected
                            > Head, Accounting Division Unit</option>

                            @if (count($signatories) > 0)
                                @foreach ($signatories as $sig)
                                    @if ($sig->module->lr->accounting)
                            <option value="{{ $sig->id }}">
                                {!! $sig->name !!} [{!! $sig->module->lr->designation !!}]
                            </option>
                                    @endif
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="md-form">
                        <div class="md-form my-0">
                            <input type="text" id="jev-no" name="jev_no"
                                class="form-control">
                            <label for="jev-no">JEV No</label>
                        </div>
                    </div>
                    <div class="md-form my-0">
                        <input type="date" id="date-accounting" name="date_accounting"
                            class="form-control">
                        <label for="date-accounting" class="active">
                            Date:
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
