<form id="form-store" class="wow animated fadeIn" method="POST"
      action="{{ route('report-raod-store') }}">
    @csrf

    <div class="card">
        <div class="card-body">
            <h4 class="text-center">
                REGISTRY OF ALLOTMENTS, OBLIGATIONS AND DISBURSEMENTS<br>
                PERSONNEL SERVICES/MAINTENANCE AND OTHER OPERATING EXPENSES
            </h4>
            <hr>
            <div class="row">
                <div class="offset-md-4 col-md-4">
                    <label for="period-ending" class="active">
                        <span class="red-text">* </span>
                        <b>For the Period Ending</b>
                    </label>
                    <div class="form-group">
                        <input type="month" id="period-ending" name="period_ending"
                               class="form-control required">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-5">
                    <div class="md-form form-sm">
                        <input type="text" id="entity-name" name="entity_name"
                               class="form-control required" value="Department of Science and Technology-Cordillera Administrative Region">
                        <label for="entity-name" class="active">
                            <span class="red-text">* </span>
                            <strong>Entity Name</strong>
                        </label>
                    </div>
                </div>
                <div class="col-md-5 offset-md-2">
                    <div class="md-form form-sm">
                        <select class="mdb-select crud-select sm-form required" searchable="Search here.."
                                name="mfo_pap[]" id="mfo-pap" multiple>
                            <option value="" disabled selected>Choose the MFO PAP</option>

                            @if (count($mfoPAPs) > 0)
                                @foreach ($mfoPAPs as $pap)
                            <option value="{{ $pap->id }}">
                                {!! $pap->code !!} : {!! $pap->description !!}
                            </option>
                                @endforeach
                            @endif
                        </select>
                        <label for="mfo-pap" class="active">
                            <span class="red-text">* </span>
                            <b>MFO/PAP</b>
                        </label>
                    </div>
                    {{--
                    <div class="md-form form-sm">
                        <input type="text" id="mfo-pap" name="mfo_pap"
                               class="form-control required" value="3/A.III.c.1/A.III.c.2/A.III.b.1">
                        <label for="mfo-pap" class="active">
                            <span class="red-text">* </span>
                            <strong>MFO/PAP</strong>
                        </label>
                    </div>
                    --}}
                </div>
            </div>
            <div class="row">
                <div class="col-md-5">
                    <div class="md-form form-sm">
                        <input type="text" id="fund-cluster" name="fund_cluster"
                               class="form-control required" value="01-101-101/01-101-102">
                        <label for="fund-cluster" class="active">
                            <span class="red-text">* </span>
                            <strong>Fund Cluster</strong>
                        </label>
                    </div>
                </div>
                <div class="col-md-5 offset-md-2">
                    <div class="md-form form-sm">
                        <input type="text" id="sheet-no" name="sheet_no"
                               class="form-control required" value="1">
                        <label for="sheet-no" class="active">
                            <span class="red-text">* </span>
                            <strong>Sheet No.</strong>
                        </label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-5">
                    <div class="md-form form-sm">
                        <input type="text" id="legal-basis" name="legal_basis"
                               class="form-control required" value="RA 11518">
                        <label for="legal-basis" class="active">
                            <span class="red-text">* </span>
                            <strong>Legal Basis</strong>
                        </label>
                    </div>
                </div>
                <div class="col-md-5 offset-md-2">
                    <em>Current /Cont Allotment</em>
                </div>
            </div>
            <div class="row">
                <div id="voucher-table-section" class="col-md-12 border px-0 table-responsive">
                    <table class="table table-sm table-hover table-bordered m-0">
                        <thead>
                            <tr>
                                <th class="text-center" rowspan="2" width="7%">
                                    <small>
                                        Date Received
                                    </small>
                                </th>
                                <th class="text-center" rowspan="2" width="7%">
                                    <small>
                                        Date Obligated
                                    </small>
                                </th>
                                <th class="text-center" rowspan="2" width="7%">
                                    <small>
                                        Date Released
                                    </small>
                                </th>
                                <th class="text-center" colspan="3" width="21%">
                                    <small>
                                        Reference
                                    </small>
                                </th>
                                <th class="text-center" rowspan="2" width="8%">
                                    <small>
                                        UACS Object Code/Expenditure
                                    </small>
                                </th>
                                <th class="text-center" rowspan="2" width="7%">
                                    <small>
                                        Allotments
                                    </small>
                                </th>
                                <th class="text-center" rowspan="2" width="7%">
                                    <small>
                                        Obligations
                                    </small>
                                </th>
                                <th class="text-center" rowspan="2" width="7%">
                                    <small>
                                        Unobligated Allotments
                                    </small>
                                </th>
                                <th class="text-center" rowspan="2" width="7%">
                                    <small>
                                        Disbursements
                                    </small>
                                </th>
                                <th class="text-center" colspan="2" width="14%">
                                    <small>
                                        Unpaid Obligations
                                    </small>
                                </th>
                                <th width="10%"></th>
                                <th width="2%"></th>
                            </tr>
                            <tr>
                                <th class="text-center" width="7%">
                                    <small>
                                        Payee
                                    </small>
                                </th>
                                <th class="text-center" width="14%">
                                    <small>
                                        Particulars
                                    </small>
                                </th>
                                <th class="text-center" width="7%">
                                    <small>
                                        Serial Number
                                    </small>
                                </th>
                                <th class="text-center" width="7%">
                                    <small>
                                        Due and Demandable
                                    </small>
                                </th>
                                <th class="text-center" width="7%">
                                    <small>
                                        Not Yet Due and Demandable
                                    </small>
                                </th>
                                <th class="text-center" width="10%">
                                    <small>
                                        Is Excluded?
                                    </small>
                                </th>
                                <th width="2%"></th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" id="toggle" value="store">
</form>
