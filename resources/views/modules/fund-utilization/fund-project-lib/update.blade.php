<form id="form-update" class="wow animated fadeIn d-flex justify-content-center" method="POST"
      action="{{ route('fund-project-lib-update', ['id' => $id]) }}">
    @csrf
    <div class="card w-responsive">
        <div class="card-body">
            <h4>Project</h4>
            <hr>
            <div class="row">
                <div class="col-md-6">
                    <div class="md-form form-sm">
                        <select class="mdb-select crud-select md-form required" searchable="Search here.."
                                name="project">
                            <option value="" disabled selected>Choose a project</option>

                            @if (count($projects) > 0)
                                @foreach ($projects as $project)
                            <option {{ $project->id == $budget->project_id ? 'selected' : '' }}
                                    value="{{ $project->id }}">
                                {!! $project->project_title !!}
                            </option>
                                @endforeach
                            @endif
                        </select>
                        <label class="mdb-main-label">
                            <span class="red-text">* </span>
                            <b>Project</b>
                        </label>
                    </div>
                </div>

                <div class="col-md-6"></div>
            </div>
            <br>

            <h4>Proposed Budget</h4>
            <hr>
            <div class="row">
                <div class="col-md-6">
                    <div class="md-form form-sm">
                        <input type="number" id="approved-budget" name="approved_budget"
                               class="form-control form-control-sm required"
                               onkeyup="$(this).totalBudgetIsValid();"
                               onchange="$(this).totalBudgetIsValid();"
                               value="{{ $budget->approved_budget }}">
                        <label for="approved-budget" class="active">
                            <span class="red-text">* </span>
                            <b>Budget</b>
                        </label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="md-form form-sm">
                        <input type="number" id="remaining-budget" material-tooltip-main"
                               data-toggle="tooltip" data-placement="right" value="0.00"
                               readonly class="form-control form-control-sm"
                               title="This should be equals or greater than zero."
                               value="{{ $remainingBudget }}">
                        <label for="remaining-budget" class="active">
                            <b>Remaining Budget</b>
                        </label>
                    </div>
                </div>
            </div><br>

            <h4>Line-Items</h4>
            <hr>
            <div class="col-md-12 px-0 table-responsive">
                <table class="table table-sm table-hover table-bordered">
                    <thead class="text-center">
                        <tr>
                            <th class="align-middle" width="33%">
                                <b>
                                    <span class="red-text">* </span> Allotment Name
                                </b>
                            </th>
                            <th class="align-middle" width="33%">
                                <b>
                                    <span class="red-text">* </span> Allotment Class
                                </b>
                            </th>
                            <th class="align-middle" width="31%">
                                <b>
                                    <span class="red-text">* </span> Allotted Budget
                                </b>
                            </th>
                            <th width="3%"></th>
                        </tr>
                    </thead>
                    <tbody class="sortable">
                        @if (count($allotments))
                            @foreach ($allotments as $ctr => $item)
                        <tr id="item-row-{{ $ctr + 1 }}" class="item-row">
                            <td>
                                <div class="md-form form-sm my-0">
                                    <input type="hidden" name="allotment_id[{{ $ctr }}]" value="{{ $item->id }}">
                                    <input type="text" placeholder=" Value..." name="allotment_name[{{ $ctr }}]"
                                            class="form-control required form-control-sm allotment-name py-1"
                                            id="allotment-name-{{ $ctr + 1 }}" value="{{ $item->allotment_name }}">
                                </div>
                            </td>
                            <td>
                                <div class="md-form my-0">
                                    <select class="mdb-select form-control-sm required allot-class-tokenizer"
                                            name="allot_class[{{ $ctr }}]">
                                        @foreach ($allotmentClassifications as $class)
                                        <option {{ $class->id == $item->allotment_class ? 'selected' : '' }}
                                                value="{{ $class->id }}">
                                            {{ $class->class_name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </td>
                            <td>
                                <div class="md-form form-sm my-0">
                                    <input type="number" placeholder=" Value..." name="allotted_budget[{{ $ctr }}]"
                                            class="form-control required form-control-sm allotted-budget py-1"
                                            id="allotted-budget-{{ $ctr + 1 }}" min="0"
                                            onkeyup="$(this).totalBudgetIsValid();"
                                            onchange="$(this).totalBudgetIsValid();"
                                            value="{{ $item->allotment_cost }}">
                                </div>
                            </td>
                            <td class="align-middle">
                                <a onclick="$(this).deleteRow('#item-row-{{ $ctr + 1 }}');"
                                    class="btn btn-outline-red px-1 py-0">
                                    <i class="fas fa-minus-circle"></i>
                                </a>
                            </td>
                            <td class="align-middle">
                                <a href="#" class="grey-text">
                                    <i class="fas fa-ellipsis-v"></i>
                                </a>
                            </td>
                        </tr>
                            @endforeach
                        @endif

                        <tr class="exclude-sortable">
                            <td colspan="12">
                                <a class="btn btn-outline-blue btn-sm btn-block z-depth-0"
                                   onclick="$(this).addRow('.item-row', 'header');">
                                    + Insert Header
                                </a>
                            </td>
                        </tr>

                        <tr class="exclude-sortable">
                            <td colspan="12">
                                <a class="btn btn-outline-light-blue btn-sm btn-block z-depth-0"
                                   onclick="$(this).addRow('.item-row', 'item');">
                                    + Add Item
                                </a>
                            </td>
                        </tr>

                        <tr class="exclude-sortable">
                            <td colspan="12">
                                <a class="btn btn-outline-dark btn-sm btn-block z-depth-0"
                                   onclick="$(this).addRow('.item-row', 'header-break');">
                                    + Add Break Header Group
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div><br>

            <h4>Signatories</h4>
            <hr>
            <div class="row">
                <div class="col-md-6">
                    <div class="md-form">
                        <select class="mdb-select crud-select md-form required" searchable="Search here.."
                                name="submitted_by">
                            <option value="" disabled selected>Choose a signatory</option>

                            @if (count($users) > 0)
                                @foreach ($users as $user)
                            <option value="{{ $user->id }}" {{ $user->id == $budget->sig_submitted_by ? 'selected' : '' }}>
                                {!! $user->firstname !!} {!! $user->lastname !!} [{!! $user->position !!}]
                            </option>
                                @endforeach
                            @endif
                        </select>
                        <label class="mdb-main-label">
                            <span class="red-text">* </span>
                            <b>Submitted By</b>
                        </label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="md-form">
                        <select class="mdb-select crud-select md-form required" searchable="Search here.."
                                name="approved_by">
                            <option value="" disabled selected>Choose a signatory</option>

                            @if (count($signatories) > 0)
                                @foreach ($signatories as $sig)
                                    @if (isset($sig->module->summary->delivered_by) && $sig->module->summary->delivered_by)
                            <option value="{{ $sig->id }}" {{ $sig->id == $budget->sig_approved_by ? 'selected' : '' }}>
                                {!! $sig->name !!} [{!! $sig->module->summary->designation !!}]
                            </option>
                                    @endif
                                @endforeach
                            @endif
                        </select>
                        <label class="mdb-main-label">
                            <span class="red-text">* </span>
                            <b>Approved by</b>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
