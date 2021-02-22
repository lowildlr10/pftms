<form id="form-update" method="POST" class="wow animated fadeIn d-flex justify-content-center"
      action="{{ $actionURL }}">
    @csrf
    <div class="card w-responsive">
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
                            <input type="text" id="period-covered" name="period_covered"
                                   class="form-control w-100 required" value="{{ $dat->period_covered }}">
                            <label for="period-covered" style="padding-left: 15px;"
                                   class="{{ !empty($dat->period_covered) ? 'active': '' }}">
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
                               class="form-control" value="{{ $dat->serial_no }}">
                        <label for="serial-no" class="{{ !empty($dat->serial_no) ? 'active': '' }}">
                            <strong>Serial Number</strong>
                        </label>
                    </div>
                    <div class="md-form form-sm">
                        <input type="date" id="date-liquidation" name="date_liquidation"
                               class="form-control required" value="{{ $dat->date_liquidation }}">
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
                            class="form-control required"
                            value="{{ empty($dat->entity_name) ?
                                      'Department of Science and Technology': $dat->entity_name }}">
                        <label for="entity-name" class="active">
                            <span class="red-text">* </span>
                            <strong>Entity Name</strong>
                        </label>
                    </div>
                    <div class="md-form form-sm">
                        <input type="text" id="fund-cluster" name="fund_cluster" class="form-control"
                               value="{{ empty($dat->fund_cluster) ? '01': $dat->fund_cluster }}">
                        <label for="fund-cluster" class="active">
                            <strong>Fund Cluster</strong>
                        </label>
                    </div>
                </div>
                <div class="col-md-4 border border-top-0 border-left-0 border-bottom-0 border-dark">
                    <div class="md-form form-sm">
                        <input type="text" id="responsibility-center" name="responsibility_center"
                               class="form-control required"
                               value="{{ empty($dat->responsibility_center) ?
                                         '19 001 03000 14': $dat->responsibility_center }}">
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
                                  rows="8" placeholder="Write particulars here...">{{ $dat->particulars }}</textarea>
                    </div>
                </div>
                <div class="col-md-4 border border-left-0 border-bottom-0 border-dark px-0 text-center">
                    <div class="p-2 border-bottom border-dark">
                        <span class="red-text">* </span>
                        <strong>AMOUNT</strong>
                    </div>
                    <div class="md-form px-3">
                        <input type="number" id="amount" name="amount" placeholder="Enter a value..."
                    class="form-control required" value="{{ $dat->amount }}">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8 border border-bottom-0 border-dark p-2">
                    TOTAL AMOUNT SPENT
                </div>
                <div class="col-md-4 border border-left-0 border-bottom-0 border-dark p-2">
                    <div class="md-form my-0">
                        <input type="number" id="total-amount" name="total_amount"
                               placeholder="Enter a value..." class="form-control required"
                               value="{{ $dat->total_amount }}">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8 border border-bottom-0 border-dark py-2 pl-0">
                    <div class="md-form input-group my-0">
                        <div class="input-group-prepend">
                            <span class="input-group-text md-addon pl-2" id="or-no">
                                AMOUNT OF CASH ADVANCE PER DV NO.
                            </span>
                        </div>
                        <input type="text" class="form-control" id="dv-no" name="dv_no"
                               placeholder="..." value="{{ $dat->dv_no }}" readonly>
                        <div class="input-group-prepend">
                            <span class="input-group-text md-addon" id="dtd">DTD:</span>
                        </div>
                        <input type="date" class="form-control" id="dtd" name="dtd"
                               placeholder="..." value="{{ $dat->dv_dtd }}" readonly>
                    </div>
                </div>
                <div class="col-md-4 border border-left-0 border-bottom-0 border-dark p-2">
                    <div class="md-form my-0">
                        <input type="number" id="amount-cash-adv" name="amount_cash_adv"
                               placeholder="Enter a value..." class="form-control required"
                               value="{{ $dat->amount_cash_adv }}">
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
                               placeholder="..." value="{{ $dat->or_no }}">
                        <div class="input-group-prepend">
                            <span class="input-group-text md-addon" id="dtd">DTD:</span>
                        </div>
                        <input type="date" class="form-control" id="or-dtd" name="or_dtd"
                               placeholder="..." value="{{ $dat->or_dtd }}">
                    </div>
                </div>
                <div class="col-md-4 border border-left-0 border-bottom-0 border-dark p-2">
                    <div class="md-form my-0">
                        <input type="number" id="amount-refunded" name="amount_refunded"
                               placeholder="Enter a value..." class="form-control required"
                               value="{{ $dat->amount_refunded }}">
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
                               placeholder="Enter a value..." class="form-control required"
                               value="{{ $dat->amount_reimbursed }}">
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
                        <select id="sig-claimant" name="sig_claimant" class="mdb-select md-form my-0 required">
                            <option value="" disabled selected
                            > Claimant</option>

                            @if (count($claimants) > 0)
                                @foreach ($claimants as $claimant)
                            <option value="{{ $claimant->emp_id }}"
                                    {{ ($claimant->emp_id == $dat->sig_claimant) ? 'selected': '' }}
                                >{{ $claimant->name }}
                            </option>
                                @endforeach
                            @else
                            <option value="" disabled>
                                No data...
                            </option>
                            @endif
                        </select>
                    </div>
                    <div class="md-form">
                        <div class="md-form my-0">
                            <input type="date" id="date-claimant" name="date_claimant"
                                   class="form-control" value="{{ $dat->date_claimant }}">
                            <label for="date-claimant" class="active">
                                Date:
                            </label>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 border border-left-0 border-dark">
                    <div class="md-form">
                        <small>
                            <span class="red-text">* </span>
                            [ B ] Certified: Purpose of travel/cash advance duly accomplished
                        </small>
                        <select id="sig-supervisor" name="sig_supervisor" class="mdb-select md-form my-0 required">
                            <option value="" disabled selected
                            > Immediate Supervisor</option>

                            @if (count($signatories) > 0)
                                @foreach ($signatories as $supervisor)
                                    @if ($supervisor->liquidation_sign_type == 'supervisor')
                            <option value="{{ $supervisor->id }}"
                                    {{ ($supervisor->id == $dat->sig_supervisor) ? 'selected': '' }}
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
                    <div class="md-form">
                        <div class="md-form my-0">
                            <input type="date" id="date-supervisor" name="date_supervisor"
                                   class="form-control" value="{{ $dat->date_supervisor }}">
                            <label for="date-supervisor" class="active">
                                Date:
                            </label>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 border border-left-0 border-dark">
                    <div class="md-form">
                        <small>
                            <span class="red-text">* </span>
                            [ C ] Certified: Supporting documents complete and proper
                        </small>
                        <select id="sig-accounting" name="sig_accounting" class="mdb-select md-form my-0 required">
                            <option value="" disabled selected
                            > Head, Accounting Division Unit</option>

                            @if (count($signatories) > 0)
                                @foreach ($signatories as $accountant)
                                    @if ($accountant->liquidation_sign_type == 'accountant')
                            <option value="{{ $accountant->id }}"
                                    {{ ($accountant->id == $dat->sig_accounting) ? 'selected': '' }}
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
                    <div class="md-form">
                        <div class="md-form my-0">
                            <input type="text" id="jev-no" name="jev_no"
                                   class="form-control" value="{{ $dat->jev_no }}">
                            <label for="jev-no" class="{{ !empty($dat->jev_no) ? 'active': '' }}">
                                JEV No
                            </label>
                        </div>
                    </div>
                    <div class="md-form">
                        <div class="md-form my-0">
                            <input type="date" id="date-accounting" name="date_accounting"
                                   class="form-control" value="{{ $dat->date_accounting }}">
                            <label for="date-accounting" class="active">
                                Date:
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
