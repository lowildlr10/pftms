<form id="form-update" class="wow animated fadeIn d-flex justify-content-center" method="POST"
      action="{{ route('fund-project-lib-update-realignment', ['id' => $id]) }}">
    @csrf
    <div class="card w-responsive">
        <div class="card-body">
            <h4>Realignment Date</h4>
            <hr>
            <div class="row">
                <div class="col-md-6">
                    <div class="md-form form-sm">
                        <input type="date" id="date_realignment" name="date_realignment"
                               class="form-control form-control-sm required"
                               value="{{ $dateRealignment }}">
                        <label for="date_realignment" class="active">
                            <span class="red-text">* </span>
                            <b>Date</b>
                        </label>
                    </div>
                </div>
                <div class="col-md-6"></div>
            </div><br>

            <h4>Proposed Budget</h4>
            <hr>
            <div class="row">
                <div class="col-md-6">
                    <div class="md-form form-sm">
                        <input type="number" id="approved-budget" name="approved_budget"
                               class="form-control form-control-sm required"
                               onkeyup="$(this).totalBudgetIsValid();"
                               onchange="$(this).totalBudgetIsValid();"
                               value="{{ $approvedBudget }}">
                        <label for="approved-budget" class="active">
                            <span class="red-text">* </span>
                            <b>Budget</b>
                        </label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="md-form form-sm">
                        <input type="number" id="remaining-budget" material-tooltip-main"
                               data-toggle="tooltip" data-placement="right"
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
                            <th class="align-middle" width="30%">
                                <b>
                                    <span class="red-text">* </span> Allotment Name
                                </b>
                            </th>
                            <th class="align-middle" width="25%">
                                <b>
                                    <span class="red-text">* </span> Allotment Class
                                </b>
                            </th>
                            <th class="align-middle" width="16%">
                                <b>
                                    <span class="red-text">* </span> Proposed Realignment
                                </b>
                            </th>
                            <th class="align-middle" width="26%">
                                <b>
                                    <span class="red-text">* </span> Justification
                                </b>
                            </th>
                            <th width="3%"></th>
                        </tr>
                    </thead>
                    <tbody class="sortable">
                        @if (count($groupedAllotments) > 0)
                            @foreach ($groupedAllotments as $ctr => $item)
                                @if (is_int($ctr))
                        <tr id="item-row-{{ $itemCounter }}" class="item-row">
                            <td>
                                <div class="md-form form-sm my-0">
                                    <input type="hidden" name="allotment_id[{{ $itemCounter }}]" value="{{ $item->id }}">
                                    <input type="hidden" name="allotment_realign_id[{{ $itemCounter }}]" value="{{ $item->r_allot_id }}">
                                    <input name="row_type[{{ $itemCounter }}]" type="hidden" value="item">
                                    <input type="text" placeholder=" Value..." name="allotment_name[{{ $itemCounter }}]"
                                            class="form-control required form-control-sm allotment-name py-1"
                                            id="allotment-name-{{ $itemCounter }}" value="{{ $item->allotment_name }}">
                                </div>
                            </td>
                            <td>
                                <div class="md-form my-0">
                                    <select class="mdb-select form-control-sm required allot-class-tokenizer"
                                            name="allot_class[{{ $itemCounter }}]">
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
                                    <input type="number" placeholder=" Value..." name="allotted_budget[{{ $itemCounter }}]"
                                            class="form-control required form-control-sm allotted-budget py-1"
                                            id="allotted-budget-{{ $itemCounter }}" min="0"
                                            onkeyup="$(this).totalBudgetIsValid();"
                                            onchange="$(this).totalBudgetIsValid();"
                                            value="{{ $item->allotment_cost }}">
                                </div>
                            </td>
                            <td>
                                <div class="md-form form-sm my-0">
                                    <textarea id="justification-{{ $itemCounter }}"
                                              class="md-textarea form-control"
                                              name="justification[{{ $itemCounter }}]" rows="2"
                                              placeholder="Justification">{{ $item->justification }}</textarea>
                                </div>
                            </td>
                            <td class="align-middle">
                                <a onclick="$(this).deleteRow('#item-row-{{ $itemCounter }}');"
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
                                    @php $itemCounter++ @endphp
                                @else
                        <tr id="header-row-{{ $itemCounter }}" class="item-row">
                            <td>
                                <div class="md-form form-sm my-0">
                                    <input name="row_type[{{ $itemCounter }}]" type="hidden" value="header">
                                    <input type="hidden" name="allotment_id[{{ $itemCounter }}]">
                                    <input type="hidden" name="allotment_realign_id[{{ $itemCounter }}]">
                                    <input type="hidden"name="allot_class[{{ $itemCounter }}]">
                                    <input type="hidden"name="allotted_budget[{{ $itemCounter }}]">
                                    <input type="hidden"name="justification[{{ $itemCounter }}]">
                                    <input type="text" placeholder="Header Value..." name="allotment_name[{{ $itemCounter }}]"
                                           class="form-control required form-control-sm allotment-name py-1 font-weight-bold"
                                           value="{{ str_replace('-', ' ', $ctr) }}"
                                           id="allotment-name-header-{{ $itemCounter }}">
                                </div>
                            </td>
                            <td colspan="3"></td>
                            <td class="align-middle">
                                <a onclick="$(this).deleteRow('#header-row-{{ $itemCounter }}');"
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
                                    @php $itemCounter++ @endphp

                                    @foreach ($item as $itmCtr => $itm)
                        <tr id="item-row-{{ $itemCounter }}" class="item-row">
                            <td>
                                <div class="md-form form-sm my-0">
                                    <input type="hidden" name="allotment_id[{{ $itemCounter }}]" value="{{ $itm->id }}">
                                    <input type="hidden" name="allotment_realign_id[{{ $itemCounter }}]" value="{{ $itm->r_allot_id }}">
                                    <input name="row_type[{{ $itemCounter }}]" type="hidden" value="item">
                                    <input type="text" placeholder=" Value..." name="allotment_name[{{ $itemCounter }}]"
                                            class="form-control required form-control-sm allotment-name py-1"
                                            id="allotment-name-{{ $itemCounter }}"
                                            value="{{ explode('::', $itm->allotment_name)[1] }}">
                                </div>
                            </td>
                            <td>
                                <div class="md-form my-0">
                                    <select class="mdb-select form-control-sm required allot-class-tokenizer"
                                            name="allot_class[{{ $itemCounter }}]">
                                        @foreach ($allotmentClassifications as $class)
                                        <option {{ $class->id == $itm->allotment_class ? 'selected' : '' }}
                                                value="{{ $class->id }}">
                                            {{ $class->class_name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </td>
                            <td>
                                <div class="md-form form-sm my-0">
                                    <input type="number" placeholder=" Value..." name="allotted_budget[{{ $itemCounter }}]"
                                            class="form-control required form-control-sm allotted-budget py-1"
                                            id="allotted-budget-{{ $itemCounter }}" min="0"
                                            onkeyup="$(this).totalBudgetIsValid();"
                                            onchange="$(this).totalBudgetIsValid();"
                                            value="{{ $itm->allotment_cost }}">
                                </div>
                            </td>
                            <td>
                                <div class="md-form form-sm my-0">
                                    <textarea id="justification-{{ $itemCounter }}" class="md-textarea form-control"
                                              name="justification[{{ $itemCounter }}]" rows="2"
                                              placeholder="Justification">{{ $itm->justification }}</textarea>
                                </div>
                            </td>
                            <td class="align-middle">
                                <a onclick="$(this).deleteRow('#item-row-{{ $itemCounter }}');"
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
                                    @php $itemCounter++ @endphp
                                    @endforeach

                        <tr id="headerbreak-row-{{ $itemCounter }}" class="item-row">
                            <td colspan="4">
                                <hr>
                                <div class="md-form form-sm my-0">
                                    <input name="row_type[{{ $itemCounter }}]" type="hidden" value="header-break">
                                    <input type="hidden" name="allotment_id[{{ $itemCounter }}]">
                                    <input type="hidden" name="allotment_realign_id[{{ $itemCounter }}]">
                                    <input type="hidden"name="allot_class[{{ $itemCounter }}]">
                                    <input type="hidden"name="allotted_budget[{{ $itemCounter }}]">
                                    <input type="hidden"name="justification[{{ $itemCounter }}]">
                                    <input type="hidden" name="allotment_name[{{ $itemCounter }}]"
                                           id="allotment-name-headerbreak-{{ $itemCounter }}">
                                </div>
                            </td>
                            <td class="align-middle">
                                <a onclick="$(this).deleteRow('#headerbreak-row-{{ $itemCounter }}');"
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
                                    @php $itemCounter++ @endphp
                                @endif
                            @endforeach
                        @endif

                        <tr class="exclude-sortable">
                            <td colspan="12">
                                <a class="btn btn-outline-indigo btn-sm btn-block z-depth-0"
                                   onclick="$(this).addRow('.item-row', 'header', true);">
                                    + Insert Header
                                </a>
                            </td>
                        </tr>

                        <tr class="exclude-sortable">
                            <td colspan="12">
                                <a class="btn btn-outline-light-blue btn-sm btn-block z-depth-0"
                                   onclick="$(this).addRow('.item-row', 'item', true);">
                                    + Add Item
                                </a>
                            </td>
                        </tr>

                        <tr class="exclude-sortable">
                            <td colspan="12">
                                <a class="btn btn-outline-primary btn-sm btn-block z-depth-0"
                                   onclick="$(this).addRow('.item-row', 'header-break', true);">
                                    + Add Group Break
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
                            <option value="{{ $user->id }}" {{ $user->id == $budgetData->sig_submitted_by ? 'selected' : '' }}>
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
                            <option value="{{ $sig->id }}" {{ $sig->id == $budgetData->sig_approved_by ? 'selected' : '' }}>
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
