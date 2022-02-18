<form id="form-update" class="wow animated fadeIn" method="POST"
      action="{{ route('fund-project-lib-update', ['id' => $id]) }}">
    @csrf
    <div class="card">
        <div class="card-body">
            <h4>Project</h4>
            <hr>
            <div class="row">
                <div class="col-md-6">
                    <div class="md-form form-sm">
                        <select class="mdb-select crud-select md-form required" searchable="Search here.."
                                name="project" id="project">
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
                            <th class="align-middle" width="300px">
                                <b>
                                    <span class="red-text">* </span> Allotment Name
                                </b>
                            </th>
                            <th class="align-middle" width="150px">
                                <b>
                                    <span class="red-text">* </span> UACS Code
                                </b>
                            </th>
                            <th class="align-middle" width="150px">
                                <b>
                                    <span class="red-text">* </span> Allotment Class
                                </b>
                            </th>
                            <th class="align-middle" width="250px">
                                <b>
                                    <span class="red-text">* </span>
                                    {{ \App\Models\AgencyLGU::find($implementingAgency)->agency_name }}
                                </b>
                            </th>

                            @foreach ($coimplementors as $coimpHeadCtr => $coimplementor)
                            <th id="coimplementor-{{ $coimpHeadCtr }}" class="align-middle coimplementor" width="250px">
                                <b id="coimplementor-name-{{ $coimpHeadCtr }}">
                                    <span class="red-text">* </span>
                                    {{ \App\Models\AgencyLGU::find($coimplementor['comimplementing_agency_lgu'])->agency_name }}
                                </b>
                                <input id="coimplementor-id-{{ $coimpHeadCtr }}" type="hidden"
                                       value="{{ $coimplementor['comimplementing_agency_lgu'] }}">
                            </th>
                            @endforeach

                            <th class="align-middle" width="5px"></th>
                            <th width="1px"></th>
                        </tr>
                    </thead>
                    <tbody id="item-row-container" class="sortable">
                        @if (count($groupedAllotments) > 0)
                            @foreach ($groupedAllotments as $ctr => $item)
                                @if (is_int($ctr))
                        <tr id="item-row-{{ $itemCounter }}" class="item-row">
                            <td>
                                <div class="md-form form-sm my-0">
                                    <input type="hidden" name="allotment_id[{{ $itemCounter }}]" value="{{ $item->id }}">
                                    <input name="row_type[{{ $itemCounter }}]" type="hidden" value="item">
                                    <input type="text" placeholder=" Value..." name="allotment_name[{{ $itemCounter }}]"
                                            class="form-control required form-control-sm allotment-name py-1"
                                            id="allotment-name-{{ $itemCounter }}" value="{{ $item->allotment_name }}">
                                </div>
                            </td>
                            <td>
                                <div class="md-form my-0">
                                    <select class="mdb-select required uacs-class-tokenizer"
                                            name="uacs_code[{{ $itemCounter }}]">
                                        @foreach ($uacsCodes as $uacs)
                                        <option {{ $uacs->id == $item->uacs_id ? 'selected' : '' }}
                                                value="{{ $uacs->id }}">
                                            {{ $uacs->uacs_code }} : {{ $uacs->account_title }}
                                        </option>
                                        @endforeach
                                    </select>
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

                            @foreach (unserialize($item->coimplementers) as $coimpCtr => $coimplementor)
                            <td>
                                <div class="md-form form-sm my-0">
                                    <input type="hidden" name="coimplementor_id[{{ $itemCounter }}][{{ $coimpCtr }}]"
                                           value="{{ $coimplementor['id'] }}">
                                    <input type="number" placeholder=" Value..."
                                        name="coimplementor_budget[{{ $itemCounter }}][{{ $coimpCtr }}]"
                                        class="form-control required form-control-sm coimplementor-budget allotted-budget py-1"
                                        id="coimplementor-budget-{{ $itemCounter }}-{{ $coimpCtr }}" min="0"
                                        value="{{ $coimplementor['coimplementor_budget'] }}"
                                        onkeyup="$(this).totalBudgetIsValid();"
                                        onchange="$(this).totalBudgetIsValid();">
                                </div>
                            </td>
                            @endforeach

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
                                    <input type="hidden"name="allot_class[{{ $itemCounter }}]">
                                    <input type="hidden"name="allotted_budget[{{ $itemCounter }}]">
                                    <input type="text" placeholder="Header Value..." name="allotment_name[{{ $itemCounter }}]"
                                           class="form-control required form-control-sm allotment-name py-1 font-weight-bold"
                                           value="{{ str_replace('-', ' ', $ctr) }}"
                                           id="allotment-name-header-{{ $itemCounter }}">

                                    @foreach ($coimplementors as $coimpCtr => $coimplementor)
                                    <input type="hidden" name="coimplementor_id[{{ $itemCounter }}][{{ $coimpCtr }}]">
                                    <input type="hidden" name="coimplementor_budget[{{ $itemCounter }}][{{ $coimpCtr }}]">
                                    @endforeach
                                </div>
                            </td>
                            <td colspan="{{ count($coimplementors) + 3 }}"></td>
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
                                    <input name="row_type[{{ $itemCounter }}]" type="hidden" value="item">
                                    <input type="hidden" name="allotment_id[{{ $itemCounter }}]"
                                           value="{{ $itm->id }}">
                                    <input type="text" placeholder=" Value..."
                                           name="allotment_name[{{ $itemCounter }}]"
                                           class="form-control required form-control-sm allotment-name py-1"
                                           id="allotment-name-{{ $itemCounter }}"
                                           value="{{ explode('::', $itm->allotment_name)[1] }}">
                                </div>
                            </td>
                            <td>
                                <div class="md-form my-0">
                                    <select class="mdb-select required uacs-class-tokenizer"
                                            name="uacs_code[{{ $itemCounter }}]">
                                        @foreach ($uacsCodes as $uacs)
                                        <option {{ $uacs->id == $itm->uacs_id ? 'selected' : '' }}
                                                value="{{ $uacs->id }}">
                                            {{ $uacs->uacs_code }} : {{ $uacs->account_title }}
                                        </option>
                                        @endforeach
                                    </select>
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

                            @foreach (unserialize($itm->coimplementers) as $coimpCtr => $coimplementor)
                            <td>
                                <div class="md-form form-sm my-0">
                                    <input type="hidden" name="coimplementor_id[{{ $itemCounter }}][{{ $coimpCtr }}]"
                                           value="{{ $coimplementor['id'] }}">
                                    <input type="number" placeholder=" Value..."
                                        name="coimplementor_budget[{{ $itemCounter }}][{{ $coimpCtr }}]"
                                        class="form-control required form-control-sm coimplementor-budget allotted-budget py-1"
                                        id="coimplementor-budget-{{ $itemCounter }}-{{ $coimpCtr }}" min="0"
                                        value="{{ $coimplementor['coimplementor_budget'] }}"
                                        onkeyup="$(this).totalBudgetIsValid();"
                                        onchange="$(this).totalBudgetIsValid();">
                                </div>
                            </td>
                            @endforeach

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
                            <td colspan="{{ count($coimplementors) + 4 }}">
                                <hr>
                                <div class="md-form form-sm my-0">
                                    <input name="row_type[{{ $itemCounter }}]" type="hidden" value="header-break">
                                    <input type="hidden" name="allotment_id[{{ $itemCounter }}]">
                                    <input type="hidden"name="allot_class[{{ $itemCounter }}]">
                                    <input type="hidden"name="allotted_budget[{{ $itemCounter }}]">
                                    <input type="hidden" name="allotment_name[{{ $itemCounter }}]"
                                           id="allotment-name-{{ $itemCounter }}">

                                    @foreach ($coimplementors as $coimpCtr => $coimplementor)
                                    <input type="hidden" name="coimplementor_id[{{ $itemCounter }}][{{ $coimpCtr }}]">
                                    <input type="hidden" name="coimplementor_budget[{{ $itemCounter }}][{{ $coimpCtr }}]">
                                    @endforeach
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
                                <a class="btn btn-outline-primary btn-sm btn-block z-depth-0"
                                   onclick="$(this).addRow('.item-row', 'header-break');">
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
                                    @if (isset($sig->module->lib->approved_by) && $sig->module->lib->approved_by)
                            <option value="{{ $sig->id }}" {{ $sig->id == $budget->sig_approved_by ? 'selected' : '' }}>
                                {!! $sig->name !!} [{!! $sig->module->lib->designation !!}]
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

<script>
    @foreach ($projects as $project)
        coimplementors = [];

        @if ($project->comimplementing_agency_lgus)
            @foreach (unserialize($project->comimplementing_agency_lgus) as $coimplement)
            coimplementors.push({
                'id': `{!! $coimplement['comimplementing_agency_lgu'] !!}`,
                'coimplementor_name': `{!! \App\Models\AgencyLGU::find($coimplement['comimplementing_agency_lgu'])->agency_name !!}`,
                'coimplementor_budget': {!! $coimplement['coimplementing_project_cost'] !!}

            });
            @endforeach
        @endif

        projects.push({
            'id': `{!! $project->id !!}`,
            'project_title': `{!! $project->project_title !!}`,
            'project_cost':  `{!! $project->project_cost !!}`,
            'implementor_id': `{!! $project->implementing_agency !!}`,
            'implementor_name': `{!! $project->implementing_agency ?
                \App\Models\AgencyLGU::find($project->implementing_agency)->agency_name :
                "" !!}`,
            'implementor_budget': {!! $project->implementing_project_cost !!},
            'coimplementors': coimplementors,
        });
    @endforeach
</script>
