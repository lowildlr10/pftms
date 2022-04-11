<form id="form-update" method="POST" action="{{ route('project-update', ['id' => $id]) }}">
    @csrf

    <h5>Project Details</h5>
    <hr>
    <label for="directory" class="py-0 w-100"">
        <div class="md-form py-0">
            <b>Folder Name</b>
                {{ $directory ? "($directory)" : '' }}
            </small>
            <select id="directory" class="form-control-sm directory-tokenizer"
                    name="directory[]" style="width: 100%;" multiple>
                @if (isset($directories['directory']) && count($directories['directory']) > 0)
                    @foreach ($directories['directory'] as $dirCtr => $dir)
                <option disabled>Directory {{ $dirCtr + 1 }}: {{ $dir }}</option>
                    @endforeach
                @endif

                @if (isset($directories['items']) && count($directories['items']) > 0)
                    @foreach ($directories['items'] as $item)
                        @if ($directory && count(explode(' / ', $directory)))
                            @php $isEquals = false; @endphp

                            @foreach (explode(' / ', $directory) as $itm)
                                @if ($itm == $item)
                                    @php $isEquals = true; @endphp
                                @endif
                            @endforeach

                <option value="{{ $item }}" {{ $isEquals ? 'selected' : '' }}>{{ $item }}</option>
                        @else
                <option value="{{ $item }}">{{ $item }}</option>
                        @endif
                    @endforeach
                @endif
            </select>
        </div>
    </label>

    <hr class="py-0">

    <div class="md-form">
        <input type="text" id="project-title" class="form-control required"
               name="project_title" value="{{ $projectTitle }}">
        <label for="project-title" class="active">
            Project Title <span class="red-text">*</span>
        </label>
    </div>

    <label>
        Industry/Sector <em><small>(Dynamic)</small></em> <span class="red-text">*</span>
    </label>
    <div class="md-form mt-0">
        <select class="mdb-select form-control-sm industry-tokenizer required"
                name="industry_sector">
            <option value="" disabled selected>Choose an Industry/Sector</option>

            @if (count($industries) > 0)
                @foreach ($industries as $industry)
                    @if ($industry->id == $industrySector)
            <option value="{{ $industry->id }}" selected>
                {!! $industry->sector_name !!}
            </option>
                    @endif
                @endforeach
            @endif
        </select>
    </div>

    <label for="project-site" class="py-0 w-100">
        <div class="md-form py-0 my-0">
            Project Site <span class="red-text">*</span>
            <select id="project-site" class="form-control-sm proj-site-tokenizer required"
                    name="project_site[]" style="width: 100%;" multiple>
                @if (count($projectSites) > 0)
                    @foreach ($projectSites as $site)
                <option value="{{ $site->id }}" {{ in_array($site->id, $projectSite) ? 'selected' : '' }}>
                    {!! $site->name !!}
                </option>
                    @endforeach
                @endif
            </select>
        </div>
    </label>

    <label class="mt-3">
        Implementing Agency <em><small>(Dynamic)</small></em> <span class="red-text">*</span>
    </label>
    <div class="md-form mt-0">
        <select class="mdb-select form-control-sm agency-tokenizer required"
                name="implementing_agency">
            @if (count($agencies) > 0)
                @foreach ($agencies as $agency)
                    @if ($agency->id == $implementingAgency)
            <option value="{{ $agency->id }}" selected>
                {!! $agency->agency_name !!}
            </option>
                    @endif
                @endforeach
            @endif
        </select>
    </div>

    <div class="md-form my-5">
        <input type="number" id="implementing-project-cost" class="form-control required"
               name="implementing_project_cost" value="{{ $implementingBudget }}"
               onkeyup="$(this).computeTotalProjectCost();"
               onchange="$(this).computeTotalProjectCost();">
        <label for="implementing-project-cost" class="active">
            Project Cost (Implementing Agency/LGU) <span class="red-text">*</span>
        </label>
    </div>

    <div class="custom-control custom-checkbox">
        <input type="checkbox" class="custom-control-input" id="coimplementing-agency"
               name="with_coimplementing_agency" {{ $coimplementingCount > 0 ? 'checked' : '' }}>
        <label class="custom-control-label font-weight-bold" for="coimplementing-agency">
            With Co-implementing Agencies/LGUs?
        </label>
    </div>

    <div id="coimplementing-agency-menu" style="{{ $coimplementingCount > 0 ? '' : 'display: none;' }}">
        <hr class="my-1">
        <div class="pl-3 mb-4">
            @if ($coimplementingCount > 0)
                @foreach ($comimplementingAgencyLGUs as $coimplementCtr => $coimplementor)
            <div class="coimplementing-form-group border rounded p-3"
                 id="coimplementing-form-group-{{ $coimplementCtr }}">
                <div class="md-form">
                    <em><small>(Dynamic)</small></em>
                    <select class="mdb-select form-control-sm coimp-agencies-tokenizer coimplementing-agency-lgus"
                            name="comimplementing_agency_lgus[]">
                        @if (count($agencies) > 0)
                            @foreach ($agencies as $agency)
                                @if ($agency->id == $coimplementor['comimplementing_agency_lgu'])
                        <option value="{{ $agency->id }}" selected>
                            {!! $agency->agency_name !!}
                        </option>
                                @endif
                            @endforeach
                        @endif
                    </select>
                </div>

                <div class="md-form mt-3">
                    <input type="number" class="form-control coimplementing-project-cost"
                           name="coimplementing_project_costs[]"
                           value="{{ $coimplementor['coimplementing_project_cost'] }}"
                           onkeyup="$(this).computeTotalProjectCost();"
                           onchange="$(this).computeTotalProjectCost();">
                    <label for="coimplementing-project-cost" class="active">
                        Project Cost (Co-implementing Agency/LGU) <span class="red-text">*</span>
                    </label>
                </div>

                <a href="#" class="btn btn-outline-red btn-sm btn-block"
                   onclick="$(this).deleteRow('#coimplementing-form-group-{{ $coimplementCtr }}');">
                    Delete
                </a>
            </div>
                @endforeach
            @else
            <div class="coimplementing-form-group border rounded p-3"
                 id="coimplementing-form-group-0">
                <div class="md-form">
                    <em><small>(Dynamic)</small></em>
                    <select class="mdb-select form-control-sm coimp-agencies-tokenizer coimplementing-agency-lgus"
                            name="comimplementing_agency_lgus[]"></select>
                </div>

                <div class="md-form mt-3">
                    <input type="number" class="form-control coimplementing-project-cost"
                           name="coimplementing_project_costs[]"
                           onkeyup="$(this).computeTotalProjectCost();"
                           onchange="$(this).computeTotalProjectCost();">
                    <label for="coimplementing-project-cost" class="active">
                        Project Cost (Co-implementing Agency/LGU) <span class="red-text">*</span>
                    </label>
                </div>

                <a href="#" class="btn btn-outline-red btn-sm btn-block"
                   onclick="$(this).deleteRow('#coimplementing-form-group-0');">
                    Delete
                </a>
            </div>
            @endif

            <a id="btn-add-coimplementing" class="btn btn-outline-info btn-sm btn-block mt-2"
               onclick="$(this).addRow('.coimplementing-form-group')">
                Add Coimplementing Agency/LGU
            </a>
        </div>
    </div>
    <hr class="my-1"><br>

    <label for="proponent-units" class="py-0 w-100">
        <div class="md-form py-0 my-0">
            Proponent Units/PSTCs <span class="red-text">*</span>
            <select id="proponent-units" class="form-control-sm proponent-tokenizer required"
                    name="proponent_units[]" style="width: 100%;" multiple>
                @if (count($empUnits) > 0)
                    @foreach ($empUnits as $unit)
                <option value="{{ $unit->id }}" {{ in_array($unit->id, $proponentUnits) ? 'selected' : '' }}>
                    {!! $unit->unit_name !!}
                </option>
                    @endforeach
                @endif
            </select>
        </div>
    </label>

    <label for="monitoring-office" class="py-0 mt-3 w-100">
        <div class="md-form py-0 my-0">
            Monitoring Office <em><small>(Dynamic)</small></em> <span class="red-text">*</span>
            <select id="monitoring-office" class="form-control-sm monitoring-tokenizer required"
                    name="monitoring_office[]" style="width: 100%;" multiple>
                @if (count($monitoringOffices) > 0)
                    @foreach ($monitoringOffices as $office)
                        @if (in_array($office->id, $monitoringOffice))
                <option value="{{ $office->id }}" selected>
                    {!! $office->office_name !!}
                </option>
                        @endif
                    @endforeach
                @endif
            </select>
        </div>
    </label>

    <div class="md-form mt-5">
        <input type="number" id="project-cost" class="form-control required"
               name="project_cost" value="{{ $projectCost }}">
        <label for="project-title" class="active">
            Total Project Cost <span class="red-text">*</span>
        </label>
    </div>

    <div class="md-form">
        <input type="text" id="project-leader" class="form-control"
               name="project_leader" value="{{ $projectLeader }}">
        <label for="project-leader" class="{{ $projectLeader ? 'active' : '' }}">
            Project Coordinator/Leader
        </label>
    </div>

    <br>

    <h5>Project Duration</h5>
    <hr>
    <div class="md-form">
        <input type="date" id="date-from" class="form-control required"
               name="date_from" value="{{ $dateFrom }}">
        <label for="date-from" class="active">
            From <span class="red-text">*</span>
        </label>
    </div>

    <div class="md-form">
        <input type="date" id="date-to" class="form-control required"
               name="date_to" value="{{ $dateTo }}">
        <label for="date-to" class="active">
            To <span class="red-text">*</span>
        </label>
    </div><br>

    <h5>Project Team Members</h5>
    <hr>
    <div class="md-form">
        <select class="mdb-select crud-select md-form" searchable="Search here.."
                name="access_group[]" multiple>
            <option value="" disabled selected>Choose the groups that can access</option>
            <option value="">-- None --</option>

            @if (count($empGroups) > 0)
                @foreach ($empGroups as $group)
            <option value="{{ $group->id }}"  {{ in_array($group->id, $accessGroup) ? 'selected' : '' }}>
                {!! $group->group_name !!}
            </option>
                @endforeach
            @endif
        </select>
        <label class="mdb-main-label">
            Project Team Members
        </label>
    </div>

    <br>

    <h5>Type/Fund Source</h5>
    <hr>
    <div class="md-form">
        <select class="mdb-select crud-select md-form required" searchable="Search here.."
                name="project_type">
            <option value="" disabled selected>Choose a project type</option>
            <option value="saa" {{ $projectType == 'saa' ? 'selected' : '' }}>Special Project</option>
            <option value="mooe" {{ $projectType == 'mooe' ? 'selected' : '' }}>Regular MOOE</option>
            <option value="lgia" {{ $projectType == 'lgia' ? 'selected' : '' }}>LGIA</option>
            <option value="setup" {{ $projectType == 'setup' ? 'selected' : '' }}>SETUP</option>
        </select>
        <label class="mdb-main-label">
            Type/Fund Source <span class="red-text">*</span>
        </label>
    </div><br>
</form>
