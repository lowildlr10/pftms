<form id="form-create" class="wow animated fadeIn" method="POST" action="{{ url('procurement/pr/save') }}">
    @csrf

    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label>Office/Section<span class="red-text">*</span></label>

                @if ($toggle == 'create')
                <input type="text" name="office" value="DOST-CAR" class="form-control z-depth-1">
                @elseif ($toggle == 'edit')
                <input type="text" name="office" value="{{ $pr->office }}" class="form-control z-depth-1">
                @endif
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>PR Number</label>

                @if ($toggle == 'create')
                <input name="pr_no" type="text" value="Auto Generated" class="form-control" disabled="disabled">
                @elseif ($toggle == 'edit')
                <input name="pr_no" type="text" value="{{ $pr->pr_no }}" class="form-control" disabled="disabled">
                @endif
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Date<span class="red-text">*</span></label>

                @if ($toggle == 'create')
                <input type="date" name="date_pr" class="form-control z-depth-1 required">
                @elseif ($toggle == 'edit')
                <input type="date" name="date_pr" class="form-control z-depth-1 required" value="{{ $pr->date_pr }}">
                @endif
            </div>
        </div>
    </div>

    <div class="table-responsive-md table-responsive-sm table-responsive-lg text-nowrap my-3">
        <table id="add-pr-table" class="table" style="width: 100%;">
            <tr>
                <td colspan="6" class="p-0">
                    <table id="item-pr-table" class="table table-bordered table-hover z-depth-1 m-0">
                        <thead class="mdb-color white-text">
                            <tr id="pr-item-header">
                                <th class="hidden-xs" width="5%">Stock/Propery No.</th>
                                <th width="11%">Unit</th>
                                <th width="40%">Item Description</th>
                                <th width="10%">Quantity</th>
                                <th width="17%">Unit Cost</th>
                                <th width="17%">Total Cost</th>
                                <th></th>
                            </tr>
                        </thead>

                        <tbody id="row-items">
                        @if ($toggle == 'create')
                            <tr id="row-0">
                                <td class="hidden-xs"><input type="hidden" name="id0" id="id0"></td>
                                <td>
                                    <select name="unit[]" class="browser-default custom-select required">
                                        @if (!empty($unitIssue))

                                        @foreach ($unitIssue as $unit)

                                        <option value="{{ $unit->id }}">{{ $unit->unit }}</option>

                                        @endforeach

                                        @endif
                                    </select>
                                </td>
                                <td><textarea name="item_description[]" class="form-control required"></textarea></td>
                                <td>
                                    <input id="quantity0" type="number" name="quantity[]" class="form-control required"
                                           onkeyup="$(this).computeCost('{{ $itemNo }}', 'unit_cost{{ $itemNo }}')"
                                           onchange="$(this).computeCost('{{ $itemNo }}', 'unit_cost{{ $itemNo }}')"
                                           min="0">
                                </td>
                                <td>
                                    <input id="unit_cost0" type="number" name="unit_cost[]" class="form-control required"
                                           onkeyup="$(this).computeCost('{{ $itemNo }}', 'unit_cost{{ $itemNo }}')"
                                           onchange="$(this).computeCost('{{ $itemNo }}', 'unit_cost{{ $itemNo }}')"
                                           min="0">
                                </td>
                                <td>
                                    <input id="total_cost0" type="number" name="total_cost[]" class="form-control required"
                                           disabled="disabled">
                                </td>
                                <td>
                                    <a  class="btn btn-outline-red waves-effect btn-sm btn-block"
                                            onclick="$(this).deleteRow('item-pr-table', 'id0')">
                                        <i class="fas fa-minus-circle"></i> <span class="hidden-xs">Remove</span>
                                    </a>
                                </td>
                            </tr>
                        @elseif ($toggle == 'edit')
                            @if (!empty($prItems))
                                @foreach ($prItems as $key => $item)
                            <tr id="row-{{$key}}">
                                <td class="hidden-xs">
                                    <input type="hidden" name="id{{ $key }}" id="id{{ $key }}">
                                    <input type="hidden" name="item_id[]" value="{{ $item->item_id }}">
                                </td>
                                <td>
                                    <select name="unit[]" class="browser-default custom-select required">
                                        @if (!empty($unitIssue))
                                            @foreach ($unitIssue as $unit)
                                                @if ($unit->id == $item->unit_issue)
                                        <option value="{{ $unit->id }}" selected="selected">
                                            {{ $unit->unit }}
                                        </option>
                                                @else
                                        <option value="{{ $unit->id }}">
                                            {{ $unit->unit }}
                                        </option>
                                                @endif
                                            @endforeach
                                        @endif

                                    </select>
                                </td>
                                <td>
                                    <textarea name="item_description[]" class="form-control required">{{ $item->item_description }}</textarea>
                                </td>
                                <td>
                                    <input id="quantity{{ $key }}" type="number" name="quantity[]" class="form-control required"
                                           onkeyup="$(this).computeCost('{{ $key }}', 'unit_cost{{ $key }}')"
                                           onchange="$(this).computeCost('{{ $key }}', 'unit_cost{{ $key }}')"
                                           min="0" value="{{ $item->quantity }}">
                                </td>
                                <td>
                                    <input id="unit_cost{{ $key }}" type="number" name="unit_cost[]" class="form-control required"
                                           onkeyup="$(this).computeCost('{{ $key }}', 'unit_cost{{ $key }}')"
                                           onchange="$(this).computeCost('{{ $key }}', 'unit_cost{{ $key }}')"
                                           min="0" value="{{ $item->est_unit_cost }}">
                                </td>
                                <td>
                                    <input id="total_cost{{ $key }}" type="number" name="total_cost[]" class="form-control required"
                                           disabled="disabled" value="{{ $item->est_total_cost }}">
                                </td>
                                <td>
                                    @if ($pr->status < 5)
                                    <a class="btn btn-outline-red waves-effect btn-sm btn-block"
                                       onclick="$(this).deleteRow('item-pr-table', 'row-{{$key}}')">
                                        <i class="fas fa-minus-circle"></i> <span class="hidden-xs">Remove</span>
                                    </a>
                                    @endif
                                </td>
                            </tr>
                                    @php  $itemNo++ @endphp
                                @endforeach
                            @endif
                        @endif
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    @if ($pr->status < 5)
    <a id="add-block-btn" class="btn btn-md btn-block btn-outline-indigo waves-effect mt-0 mb-3"
       onclick="$(this).addRow('#item-pr-table')">
        <i class="fas fa-plus"></i>
        <strong>Add Item</strong>
    </a>
    @endif

    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label>Charge to:</label>
                <select name="project" class="browser-default custom-select z-depth-1">
                    <option value=""> --Select a project-- </option>

                    @if (!empty($projects))
                        @foreach ($projects as $project)
                            @if ($toggle == 'create')
                    <option value="{{ $project->id }}">
                        {{ $project->project }}
                    </option>
                            @elseif ($toggle == 'edit')
                                @if ($project->id == $pr->project_id)
                    <option value="{{ $project->id }}" selected="selected">
                        {{ $project->project }}
                    </option>
                                @else
                    <option value="{{ $project->id }}">
                        {{ $project->project }}
                    </option>
                                @endif
                            @endif
                        @endforeach
                    @endif
                </select>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Purpose:</label>

                @if ($toggle == 'create')
                <textarea name="purpose" rows="5" class="form-control z-depth-1 required"></textarea>
                @elseif ($toggle == 'edit')
                <textarea name="purpose" rows="5" class="form-control z-depth-1 required">{{ $pr->purpose }}</textarea>
                @endif
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>Remarks:</label>

                @if ($toggle == 'create')
                <textarea name="remarks" rows="5" class="form-control z-depth-1"></textarea>
                @elseif ($toggle == 'edit')
                <textarea name="remarks" rows="5" class="form-control z-depth-1">{{ $pr->remarks }}</textarea>
                @endif
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Requested by:</label>
                <select name="requested_by" class="browser-default custom-select z-depth-1 required">
                    <option value="">  -- Select requestor -- </option>

                    @if (!empty($requestedBy))
                        @foreach ($requestedBy as $emp)
                            @if ($toggle == 'create')
                    <option value="{{ $emp->emp_id }}">
                        {{ $emp->firstname.' '.$emp->lastname }} [ {{ $emp->position }} ]
                    </option>
                            @elseif ($toggle == 'edit')
                                @if ($emp->emp_id == $pr->requested_by)
                    <option value="{{ $emp->emp_id }}" selected="selected">
                        {{ $emp->firstname.' '.$emp->lastname }} [ {{ $emp->position }} ]
                    </option>
                                @else
                    <option value="{{ $emp->emp_id }}">
                        {{ $emp->firstname.' '.$emp->lastname }} [ {{ $emp->position }} ]
                    </option>
                                @endif
                            @endif
                        @endforeach
                    @endif

                </select>
            </div>
            <div class="form-group">
                <label>Division:</label>
                <select name="division" class="browser-default custom-select z-depth-1 required">
                    <option value=""> --Select a Division-- </option>

                    @if (!empty($divisions))
                        @foreach ($divisions as $division)
                            @if ($toggle == 'create')
                    <option value="{{ $division->id }}">
                        {{ $division->division }}
                    </option>
                            @elseif ($toggle == 'edit')
                                @if ($division->id == $pr->pr_division_id)
                    <option value="{{ $division->id }}" selected="selected">
                        {{ $division->division }}
                    </option>
                                @else
                    <option value="{{ $division->id }}">
                        {{ $division->division }}
                    </option>
                                @endif
                            @endif
                        @endforeach
                    @endif
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>Approved by:</label>
                <select name="approved_by" class="browser-default custom-select z-depth-1 required">
                    <option value="">  -- Select approval -- </option>

                    @if (!empty($approvedBy))
                        @foreach ($approvedBy as $app)
                            @if ($app->pr_sign_type == 'approval')
                                @if ($toggle == 'create')
                    <option value="{{ $app->id }}">
                        {{ $app->name }} [ {{ $app->position }} ]
                    </option>
                                @elseif ($toggle == 'edit')
                                    @if ($app->id == $pr->approved_by)
                    <option value="{{ $app->id }}" selected="selected">
                        {{ $app->name }} [ {{ $app->position }} ]
                    </option>
                                    @else
                    <option value="{{ $app->id }}">
                        {{ $app->name }} [ {{ $app->position }} ]
                    </option>
                                    @endif
                                @endif
                            @endif
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="form-group">
                <label>Recommended By:</label>
                <select name="recommended_by" class="browser-default custom-select z-depth-1 required">
                    <option value="">  -- Select recommended by -- </option>

                    @if (!empty($approvedBy))
                        @foreach ($approvedBy as $app)
                            @if ($app->pr_sign_type == 'recommended-by')
                                @if ($toggle == 'create')
                    <option value="{{ $app->id }}">
                        {{ $app->name }} [ {{ $app->position }} ]
                    </option>
                                @elseif ($toggle == 'edit')
                                    @if ($app->id == $pr->recommended_by)
                    <option value="{{ $app->id }}" selected="selected">
                        {{ $app->name }} [ {{ $app->position }} ]
                    </option>
                                    @else
                    <option value="{{ $app->id }}">
                        {{ $app->name }} [ {{ $app->position }} ]
                    </option>
                                    @endif
                                @endif
                            @endif
                        @endforeach
                    @endif
                </select>
            </div>
        </div>
    </div>

    <div class="row">

    </div>

    <input id="item-count" value="{{ $itemNo }}" type="hidden">
</form>
