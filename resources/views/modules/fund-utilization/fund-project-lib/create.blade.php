<form id="form-store" class="wow animated fadeIn" method="POST"
      action="{{ route('fund-project-lib-store') }}">
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
                            <option value="{{ $project->id }}">
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
            </div><br>

            <h4>Proposed Budget</h4>
            <hr>
            <div class="row">
                <div class="col-md-6">
                    <div class="md-form form-sm">
                        <input type="number" id="approved-budget" name="approved_budget"
                               class="form-control form-control-sm required"
                               onkeyup="$(this).totalBudgetIsValid();"
                               onchange="$(this).totalBudgetIsValid();">
                        <label for="approved-budget">
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
                               title="This should be equals or greater than zero.">
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
                            <th id="implementor" class="align-middle" width="250px">
                                <b id="implementor-name">-</b>
                            </th>
                            <th class="align-middle" width="5px"></th>
                            <th width="1px"></th>
                        </tr>
                    </thead>
                    <tbody id="item-row-container" class="sortable" style="display: none;">
                        <tr id="item-row-0" class="item-row"></tr>

                        <tr class="exclude-sortable">
                            <td colspan="12">
                                <a class="btn btn-outline-indigo btn-sm btn-block z-depth-0"
                                   onclick="$(this).addRow('.item-row', 'header');">
                                    + Add Header Group
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
                            <option value="{{ $user->id }}">
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
                            <option value="{{ $sig->id }}">
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

<script type="text/javascript">
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
