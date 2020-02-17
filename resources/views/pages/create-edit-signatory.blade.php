<form id="form-create-update" method="POST" action="{{ url('libraries/store/signatory') }}">
    @csrf
    <input type="hidden" name="key" value="{{ $key }}">

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Employee:</label>
                <select class="browser-default custom-select z-depth-1 required" name="employee">
                    <option value=""> -- Select employee -- </option>

                    @if (!empty($employees))

                        @foreach ($employees as $employee)

                            @if ($employee->emp_id == $empID)

                    <option value="{{ $employee->emp_id }}" selected="selected">
                        {{ $employee->firstname }} {{ $employee->lastname }}
                    </option>

                            @else

                    <option value="{{ $employee->emp_id }}">
                        {{ $employee->firstname }} {{ $employee->lastname }}
                    </option>

                            @endif

                        @endforeach

                    @endif

                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>Active:</label>
                <select class="browser-default custom-select z-depth-1 required" name="active">
                    <option value="y" <?php echo $active == 'y' ? ' selected="selected"' : 'n' ?>>
                        Yes
                    </option>
                    <option value="n" <?php echo $active == 'n' ? ' selected="selected"' : 'y' ?>>
                        No
                    </option>
                </select>
            </div>
        </div>

        <div class="col-md-12">
            <div class="form-group">
                <label>Position:</label>
                <input type="text" name="position" class="form-control z-depth-1 required"
                       placeholder="Enter position..." value="{{ $position }}">
            </div>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-md-12 text-center mdb-color white-text p-2 rounded z-depth-1-half">
            <h4>ROLES</h4>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-md-12">
            <div class="form-check mb-2 p-0">
                <input id="p-req" type="checkbox" class="form-check-input" name="p_req" value="y"
                       <?php echo $pReq == 'y' ? ' checked="checked"' : '' ?>>
                <label class="form-check-label" for="p-req">
                    <strong>Purchase Request</strong>
                </label>
            </div>
            <div class="form-group white rounded">
                <label>Signatory Type:</label>
                <select class="browser-default custom-select z-depth-1 <?php echo $pReq == 'y' ? 'required' : '' ?>"
                        id="p-req-sign" name="pr_sign_type"
                        <?php echo $pReq == 'y' ? '' : ' disabled="disabled"' ?>>
                    <option value=""
                            <?php echo $prSignType == '' ? ' selected="selected"' : '' ?>>
                        -- Select signatory type --
                    </option>
                    <option value="approval"
                            <?php echo $prSignType == 'approval' ? ' selected="selected"' : '' ?>>
                        Approval
                    </option>
                    <option value="within-app"
                            <?php echo $prSignType == 'within-app' ? ' selected="selected"' : '' ?>>
                        Within APP
                    </option>
                    <option value="funds-available"
                            <?php echo $prSignType == 'funds-available' ? ' selected="selected"' : '' ?>>
                        Funds Available
                    </option>
                    <option value="recommended-by"
                            <?php echo $prSignType == 'recommended-by' ? ' selected="selected"' : '' ?>>
                        Recommended By
                    </option>
                </select>
            </div>
            <hr>
            <div class="form-check mb-2 p-0">
                <input id="rfq" type="checkbox" class="form-check-input" name="rfq" value="y"
                       <?php echo $rfq == 'y' ? ' checked="checked"' : '' ?>>
                <label class="form-check-label" for="rfq">
                    <strong>Request for Quotation</strong>
                </label>
            </div>
            <hr>
            <div class="form-check mb-2 p-0">
                <input id="abs" type="checkbox" class="form-check-input" name="abs" value="y"
                       <?php echo $abs == 'y' ? ' checked="checked"' : '' ?>>
                <label class="form-check-label" for="abs">
                    <strong>Abstract</strong>
                </label>
            </div>
            <div class="form-group white rounded">
                <label>Signatory Type:</label>
                <select class="browser-default custom-select z-depth-1 <?php echo $abs == 'y' ? 'required' : '' ?>"
                        id="abs-sign" name="abstract_sign_type"
                        <?php echo $abs == 'y' ? '' : ' disabled="disabled"' ?>>
                    <option value=""
                            <?php echo $abstractSignType == '' ? ' selected="selected"' : '' ?>>
                        -- Select signatory type --</option>
                    <option value="chairperson"
                            <?php echo $abstractSignType == 'chairperson' ? ' selected="selected"' : '' ?>>
                        Chairperson
                    </option>
                    <option value="vice-chairperson"
                            <?php echo $abstractSignType == 'vice-chairperson' ? ' selected="selected"' : '' ?>>
                        Vice Chairperson
                    </option>
                    <option value="member"
                            <?php echo $abstractSignType == 'member' ? ' selected="selected"' : '' ?>>
                        Member
                    </option>
                </select>
            </div>
            <hr>
            <div class="form-check mb-2 p-0">
                <input id="po-jo" type="checkbox" class="form-check-input" name="po_jo" value="y"
                       <?php echo $poJo == 'y' ? ' checked="checked"' : '' ?>>
                <label class="form-check-label" for="po-jo">
                    <strong>Purchase/Job Order</strong>
                </label>
            </div>
            <div class="form-group white rounded">
                <label>Signatory Type:</label>
                <select class="browser-default custom-select z-depth-1 <?php echo $poJo == 'y' ? 'required' : '' ?>"
                        id="po-jo-sign" name="po_jo_sign_type"
                        <?php echo $poJo == 'y' ? '' : ' disabled="disabled"' ?>>
                    <option value=""
                            <?php echo $poJoSignType == '' ? ' selected="selected"' : '' ?>>
                        -- Select signatory type --
                    </option>
                    <option value="accountant"
                            <?php echo $poJoSignType == 'accountant' ? ' selected="selected"' : '' ?>>
                        Chief Accountant/ Head of Accounting Division/Unit/Funds Available
                    </option>
                    <option value="requisitioning"
                            <?php echo $poJoSignType == 'requisitioning' ? ' selected="selected"' : '' ?>>
                        Requisitioning Office/Dept
                    </option>
                    <option value="approval"
                            <?php echo $poJoSignType == 'approval' ? ' selected="selected"' : '' ?>>
                        Very Truly Yours/Approved
                    </option>
                </select>
            </div>
            <hr>
            <div class="form-check mb-2 p-0">
                <input id="ors" type="checkbox" class="form-check-input" name="ors" value="y"
                       <?php echo $ors == 'y' ? ' checked="checked"' : '' ?>>
                <label class="form-check-label" for="ors">
                    <strong>Obligation/Budget Utilization & Request Status</strong>
                </label>
            </div>
            <div class="form-group white rounded">
                <label>Signatory Type:</label>
                <select class="browser-default custom-select z-depth-1 <?php echo $ors == 'y' ? 'required' : '' ?>"
                        id="ors-sign" name="ors_burs_sign_type"
                        <?php echo $ors == 'y' ? '' : ' disabled="disabled"' ?>>
                    <option value=""
                            <?php echo $orsBursSignType == '' ? ' selected="selected"' : '' ?>>
                        -- Select signatory type --
                    </option>
                    <option value="approval"
                            <?php echo $orsBursSignType == 'approval' ? ' selected="selected"' : '' ?>>
                        Approval
                    </option>
                    <option value="budget"
                            <?php echo $orsBursSignType == 'budget' ? ' selected="selected"' : '' ?>>
                        Funds Available
                    </option>
                </select>
            </div>
            <hr>
            <div class="form-check mb-2 p-0">
                <input id="iar" type="checkbox" class="form-check-input" name="iar" value="y"
                       <?php echo $iar == 'y' ? ' checked="checked"' : '' ?>>
                <label class="form-check-label" for="iar">
                    <strong>Inspection & Acceptance Report</strong>
                </label>
            </div>
            <div class="form-group white rounded">
                <label>Signatory Type:</label>
                <select class="browser-default custom-select z-depth-1 <?php echo $iar == 'y' ? 'required' : '' ?>"
                        id="iar-sign" name="iar_sign_type"
                        <?php echo $iar == 'y' ? '' : ' disabled="disabled"' ?>>
                    <option value=""
                        <?php echo $iarSignType == '' ? ' selected="selected"' : '' ?>>
                        -- Select signatory type --
                    </option>
                    <option value="inspector"
                        <?php echo $iarSignType == 'inspector' ? ' selected="selected"' : '' ?>>
                        Inspection Office/Inspection Committee
                    </option>
                    <option value="custodian"
                        <?php echo $iarSignType == 'custodian' ? ' selected="selected"' : '' ?>>
                        Supply and/or Property Custodian
                    </option>
                </select>
            </div>
            <hr>
            <div class="form-check mb-2 p-0">
                <input id="dv" type="checkbox" class="form-check-input" name="dv" value="y"
                       <?php echo $dv == 'y' ? ' checked="checked"' : '' ?>>
                <label class="form-check-label" for="dv">
                    <strong>Disbursement Voucher</strong>
                </label>
            </div>
            <div class="form-group white rounded">
                <label>Signatory Type:</label>
                <select class="browser-default custom-select z-depth-1 <?php echo $dv == 'y' ? 'required' : '' ?>"
                        id="dv-sign" name="dv_sign_type"
                        <?php echo $dv == 'y' ? '' : ' disabled="disabled"' ?>>
                    <option value=""
                            <?php echo $dvSignType == '' ? ' selected="selected"' : '' ?>>
                        -- Select signatory type --
                    </option>
                    <option value="supervisor"
                            <?php echo $dvSignType == 'supervisor' ? ' selected="selected"' : '' ?>>
                        Printed Name, Designation and Signature of Supervisor
                    </option>
                    <option value="accountant"
                            <?php echo $dvSignType == 'accountant' ? ' selected="selected"' : '' ?>>
                        Head, Accounting Unit/Authorized Representative
                    </option>
                    <option value="agency-head"
                            <?php echo $dvSignType == 'agency-head' ? ' selected="selected"' : '' ?>>
                        Agency Head/Authorized Representative
                    </option>
                </select>
            </div>
            <hr>
            <div class="form-check mb-2 p-0">
                <input id="ris" type="checkbox" class="form-check-input" name="ris" value="y"
                       <?php echo $ris == 'y' ? ' checked="checked"' : '' ?>>
                <label class="form-check-label" for="ris">
                    <strong>Requisition & Issue Slip</strong>
                </label>
            </div>
            <div class="form-group white rounded">
                <label>Signatory Type:</label>
                <select class="browser-default custom-select z-depth-1 <?php echo $ris == 'y' ? 'required' : '' ?>"
                        id="ris-sign" name="ris_sign_type"
                        <?php echo $ris == 'y' ? '' : ' disabled="disabled"' ?>>
                    <option value=""
                            <?php echo $risSignType == '' ? ' selected="selected"' : '' ?>>
                        -- Select signatory type --
                    </option>
                    <option value="approval"
                            <?php echo $risSignType == 'approval' ? ' selected="selected"' : '' ?>>
                        Approved By
                    </option>
                    <option value="issuer"
                            <?php echo $risSignType == 'issuer' ? ' selected="selected"' : '' ?>>
                        Issued By
                    </option>
                </select>
            </div>
            <hr>
            <div class="form-check mb-2 p-0">
                <input id="par" type="checkbox" class="form-check-input" name="par" value="y"
                       <?php echo $par == 'y' ? ' checked="checked"' : '' ?>>
                <label class="form-check-label" for="par">
                    <strong>Property Acknowledgement Receipt</strong>
                </label>
            </div>
            <div class="form-group white rounded">
                <label>Signatory Type:</label>
                <select class="browser-default custom-select z-depth-1 <?php echo $par == 'y' ? 'required' : '' ?>"
                        id="par-sign" name="par_sign_type"
                        <?php echo $par == 'y' ? '' : ' disabled="disabled"' ?>>
                    <option value=""
                            <?php echo $parSignType == '' ? ' selected="selected"' : '' ?>>
                        -- Select signatory type --
                    </option>
                    <option value="issuer"
                            <?php echo $parSignType == 'issuer' ? ' selected="selected"' : '' ?>>
                        Issued By
                    </option>
                </select>
            </div>
            <hr>
            <div class="form-check mb-2 p-0">
                <input id="ics" type="checkbox" class="form-check-input" name="ics" value="y"
                       <?php echo $ics == 'y' ? ' checked="checked"' : '' ?>>
                <label class="form-check-label" for="ics">
                    <strong>Inventory Custodian Slip</strong>
                </label>
            </div>
            <div class="form-group white rounded">
                <label>Signatory Type:</label>
                <select class="browser-default custom-select z-depth-1 <?php echo $ics == 'y' ? 'required' : '' ?>"
                        id="ics-sign" name="ics_sign_type"
                        <?php echo $ics == 'y' ? '' : ' disabled="disabled"' ?>>
                    <option value=""
                            <?php echo $icsSignType == '' ? ' selected="selected"' : '' ?>>
                        -- Select signatory type --
                    </option>
                    <option value="issuer"
                            <?php echo $icsSignType == 'issuer' ? ' selected="selected"' : '' ?>>
                        Received From
                    </option>
                </select>
            </div>
            <hr>
            <div class="form-check mb-2 p-0">
                <input id="liquidation" type="checkbox" class="form-check-input" name="liquidation" value="y"
                       <?php echo $liquidation == 'y' ? ' checked="checked"' : '' ?>>
                <label class="form-check-label" for="liquidation">
                    <strong>Liquidation Report</strong>
                </label>
            </div>
            <div class="form-group white rounded">
                <label>Signatory Type:</label>
                <select class="browser-default custom-select z-depth-1 <?php echo $liquidation == 'y' ? 'required' : '' ?>"
                        id="liquidation-sign" name="liquidation_sign_type"
                        <?php echo $liquidation == 'y' ? '' : ' disabled="disabled"' ?>>
                    <option value=""
                            <?php echo $liquidationSignType == '' ? ' selected="selected"' : '' ?>>
                        -- Select signatory type --
                    </option>
                    <option value="supervisor"
                            <?php echo $liquidationSignType == 'supervisor' ? ' selected="selected"' : '' ?>>
                        Immediate Supervisor
                    </option>
                    <option value="accountant"
                            <?php echo $liquidationSignType == 'accountant' ? ' selected="selected"' : '' ?>>
                        Head, Accounting Division Unit
                    </option>
                </select>
            </div>
            <hr>
            <div class="form-check mb-2 p-0">
                <input id="lddap" type="checkbox" class="form-check-input" name="lddap" value="y"
                       <?php echo $lddap == 'y' ? ' checked="checked"' : '' ?>>
                <label class="form-check-label" for="lddap">
                    <strong>List of Due and Demandable Accounts Payable</strong>
                </label>
            </div>
            <div class="form-group white rounded">
                <label>Signatory Type:</label>
                <select class="browser-default custom-select z-depth-1 <?php echo $lddap == 'y' ? 'required' : '' ?>"
                        id="lddap-sign" name="lddap_sign_type"
                        <?php echo $lddap == 'y' ? '' : ' disabled="disabled"' ?>>
                    <option value=""
                            <?php echo $lddapSignType == '' ? ' selected="selected"' : '' ?>>
                        -- Select signatory type --
                    </option>
                    <option value="cert_correct"
                            <?php echo $lddapSignType == 'cert_correct' ? ' selected="selected"' : '' ?>>
                        Certified Correct
                    </option>
                    <option value="approval"
                            <?php echo $lddapSignType == 'approval' ? ' selected="selected"' : '' ?>>
                        Approval
                    </option>
                    <option value="agency_authorized"
                            <?php echo $lddapSignType == 'agency_authorized' ? ' selected="selected"' : '' ?>>
                        Agency Authorized Signatories
                    </option>
                </select>
            </div>
        </div>
    </div>
    <hr>
    <div class="text-center mt-4">
        <button type="button" id="btn-create-update" type="submit" onclick="$(this).createUpdate();"
                class="btn waves-effect btn-block"></button>
    </div>
</form>
