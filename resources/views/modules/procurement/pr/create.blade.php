<form id="form-store" class="wow animated fadeIn" method="POST" action="{{ route('pr-store') }}">
    @csrf

    <div class="row">
        <div class="col-md-4">
            <div class="md-form">
                <input type="text" id="office" class="form-control required"
                       name="office" value="DOST-CAR">
                <label for="office" class="active">
                    Office/Section <span class="red-text">*</span>
                </label>
            </div>
        </div>
        <div class="col-md-4">
            <div class="md-form">
                <input type="text" value="Auto Generated" class="form-control"
                       id="pr_no" readonly>
                <label for="pr_no" class="active">
                    PR Number
                </label>
            </div>
        </div>
        <div class="col-md-4">
            <div class="md-form">
                <input type="date" id="date-pr" class="form-control required"
                       name="date_pr">
                <label for="date-pr" class="active">
                    Date <span class="red-text">*</span>
                </label>
            </div>
        </div>
    </div>

    <div class="table-responsive-md table-responsive-sm table-responsive-lg text-nowrap my-1">
        <table id="add-pr-table" class="table my-1" style="width: 100%;">
            <tr>
                <td colspan="6" class="p-0">
                    <table id="item-pr-table" class="table z-depth-1 m-0">
                        <thead class="mdb-color white-text">
                            <tr id="pr-item-header">
                                <th class="text-center" width="11%">
                                    Unit <span class="red-text">*</span>
                                </th>
                                <th class="text-center" width="45%">
                                    Item Description <span class="red-text">*</span>
                                </th>
                                <th class="text-center" width="10%">
                                    Quantity <span class="red-text">*</span>
                                </th>
                                <th class="text-center" width="17%">
                                    Unit Cost <span class="red-text">*</span>
                                </th>
                                <th class="text-center" width="17%">
                                    Total Cost
                                </th>
                                <th></th>
                            </tr>
                        </thead>

                        <tbody id="row-items">
                            <tr id="row-0">
                                <td>
                                    <input type="hidden" name="item_id[]">
                                    <div class="md-form my-0 py-0">
                                        <select class="mdb-select crud-select md-form required" searchable="Search here.."
                                                name="unit[]" id="unit0">
                                            @if (count($unitIssues) > 0)
                                                @foreach ($unitIssues as $unit)
                                            <option value="{{ $unit->id }}">
                                                {!! $unit->unit_name !!}
                                            </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <div class="md-form my-0 py-0">
                                        <textarea class="md-textarea form-control required" placeholder="Item description..."
                                                  name="item_description[]" rows="3"></textarea>
                                    </div>
                                <td>
                                    <div class="md-form">
                                        <input type="number" id="quantity0" name="quantity[]" class="form-control required"
                                               onkeyup="$(this).computeCost('{{ $itemNo }}', 'unit_cost{{ $itemNo }}')"
                                               onchange="$(this).computeCost('{{ $itemNo }}', 'unit_cost{{ $itemNo }}')"
                                               min="0" placeholder="0">
                                    </div>
                                </td>
                                <td>
                                    <div class="md-form ">
                                        <input type="number" id="unit_cost0" name="unit_cost[]" class="form-control required"
                                            onkeyup="$(this).computeCost('{{ $itemNo }}', 'unit_cost{{ $itemNo }}')"
                                            onchange="$(this).computeCost('{{ $itemNo }}', 'unit_cost{{ $itemNo }}')"
                                            min="0" placeholder="0.00">
                                    </div>
                                </td>
                                <td>
                                    <div class="md-form ">
                                        <input id="total_cost0" type="number" name="total_cost[]" class="form-control required"
                                            placeholder="0.00" readonly>
                                    </div>
                                </td>
                                <td>
                                    <a class="btn btn-sm btn-outline-red btn-rounded p-1 px-2 mt-3 waves-effect material-tooltip-main"
                                       data-toggle="tooltip" data-placement="left" title="Delete Item"
                                            onclick="$(this).deleteRow('item-pr-table', 'row-0')">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    <a id="add-block-btn" class="btn btn-md btn-block btn-outline-mdb-color
                                 waves-effect mt-0 mb-3 py-4"
       onclick="$(this).addRow('#item-pr-table')">
        <i class="fas fa-plus"></i>
        <strong>Add Item</strong>
    </a>

    <div class="row">
        <div class="col-md-12">
            <div class="md-form">
                <select class="mdb-select crud-select md-form" searchable="Search here.."
                        name="project">
                    <option value="" disabled selected>Choose a funding/charging</option>
                    <option value="">-- None --</option>

                    @if (count($fundingSources) > 0)
                        @foreach ($fundingSources as $fund)
                    <option value="{{ $fund->id }}">
                        {!! $fund->project_title !!}
                    </option>
                        @endforeach
                    @endif
                </select>
                <label class="mdb-main-label">
                    Funding/Charging Soruce
                </label>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="md-form">
                <textarea id="purpose" class="md-textarea form-control required"
                          name="purpose" rows="3"></textarea>
                <label for="purpose">Purpose <span class="red-text">*</span></label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="md-form">
                <textarea id="remarks" class="md-textarea form-control"
                          name="remarks" rows="3"></textarea>
                <label for="remarks">Remarks</label>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="md-form">
                <select class="mdb-select crud-select md-form required" searchable="Search here.."
                        name="requested_by">
                    <option value="" disabled selected>Choose a requestor</option>

                    @if (count($users) > 0)
                        @foreach ($users as $emp)
                    <option value="{{ $emp->id }}">
                        {!! $emp->firstname !!} {!! $emp->lastname !!} [{!! $emp->position !!}]
                    </option>
                        @endforeach
                    @endif
                </select>
                <label class="mdb-main-label">
                    Requestor <span class="red-text">*</span>
                </label>
            </div>

            <div class="md-form">
                <select class="mdb-select crud-select md-form required" searchable="Search here.."
                        name="division">
                    <option value="" disabled selected>Choose a division</option>

                    @if (count($divisions) > 0)
                        @foreach ($divisions as $div)
                    <option value="{{ $div->id }}">
                        {!! $div->division_name !!}
                    </option>
                        @endforeach
                    @endif
                </select>
                <label class="mdb-main-label">
                    Division <span class="red-text">*</span>
                </label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="md-form">
                <select class="mdb-select crud-select md-form required" searchable="Search here.."
                        name="approved_by">
                    <option value="" disabled selected>Choose a approval</option>

                    @if (count($signatories) > 0)
                        @foreach ($signatories as $sig)
                            @if ($sig->module->pr->approval)
                    <option value="{{ $sig->id }}">
                        {!! $sig->name !!} [{!! $sig->module->pr->designation !!}]
                    </option>
                            @endif
                        @endforeach
                    @endif
                </select>
                <label class="mdb-main-label">
                    Approved By <span class="red-text">*</span>
                </label>
            </div>

            <div class="md-form">
                <select class="mdb-select crud-select md-form required" searchable="Search here.."
                        name="recommended_by">
                    <option value="" disabled selected>Choose a recommended by</option>

                    @if (count($signatories) > 0)
                        @foreach ($signatories as $sig)
                            @if ($sig->module->pr->recommended_by)
                    <option value="{{ $sig->id }}">
                        {!! $sig->name !!} [{!! $sig->module->pr->designation !!}]
                    </option>
                            @endif
                        @endforeach
                    @endif
                </select>
                <label class="mdb-main-label">
                    Recommended By <span class="red-text">*</span>
                </label>
            </div>
        </div>
    </div>

    <input id="item-count" value="{{ $itemNo }}" type="hidden">
</form>
