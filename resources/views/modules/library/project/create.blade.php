<form id="form-store" method="POST" action="{{ route('project-store') }}">
    @csrf

    <h5>Project Details</h5>
    <hr>
    <label for="directory" class="py-0 w-100">
        <div class="md-form py-0">
            <b>Folder Name</b>
            <select id="directory" class="form-control-sm directory-tokenizer"
                    name="directory[]" style="width: 100%;" multiple>
                @if (isset($directories['directory']) && count($directories['directory']) > 0)
                    @foreach ($directories['directory'] as $dirCtr => $dir)
                <option disabled>Directory {{ $dirCtr + 1 }}: {{ $dir }}</option>
                    @endforeach
                @endif

                @if (isset($directories['items']) && count($directories['items']) > 0)
                    @foreach ($directories['items'] as $item)
                <option value="{{ $item }}">{{ $item }}</option>
                    @endforeach
                @endif
            </select>
        </div>
    </label>

    <hr class="py-0">

    <div class="md-form">
        <input type="text" id="project-title" class="form-control required"
               name="project_title">
        <label for="project-title">
            Project Title <span class="red-text">*</span>
        </label>
    </div>

    <label>
        Industry/Sector <em><small>(Dynamic)</small></em> <span class="red-text">*</span>
    </label>
    <div class="md-form mt-0">
        <select class="mdb-select form-control-sm industry-tokenizer required"
                name="industry_sector"></select>
    </div>

    <label for="project-site" class="py-0 w-100">
        <div class="md-form py-0 my-0">
            Project Site <span class="red-text">*</span>
            <select id="project-site" class="form-control-sm proj-site-tokenizer required"
                    name="project_site[]" style="width: 100%;" multiple>
                @if (count($projectSites) > 0)
                    @foreach ($projectSites as $site)
                <option value="{{ $site->id }}">
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
                name="implementing_agency"></select>
    </div>

    <div class="md-form my-5">
        <input type="number" id="implementing-project-cost" class="form-control required"
               name="implementing_project_cost" value="0.00"
               onchange="$(this).computeTotalProjectCost();"
               onkeyup="$(this).computeTotalProjectCost();">
        <label for="implementing-project-cost" class="active">
            Project Cost (Implementing Agency/LGU) <span class="red-text">*</span>
        </label>
    </div>

    <div class="custom-control custom-checkbox">
        <input type="checkbox" class="custom-control-input" id="coimplementing-agency"
               name="with_coimplementing_agency">
        <label class="custom-control-label font-weight-bold" for="coimplementing-agency">
            With Co-implementing Agencies/LGUs?
        </label>
    </div>

    <div id="coimplementing-agency-menu" style="display: none;">
        <hr class="my-1">
        <div class="pl-3 mb-4">
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
                           onchange="$(this).computeTotalProjectCost();"
                           onkeyup="$(this).computeTotalProjectCost();">
                    <label for="coimplementing-project-cost" class="active">
                        Project Cost (Co-implementing Agency/LGU) <span class="red-text">*</span>
                    </label>
                </div>

                <a href="#" class="btn btn-outline-red btn-sm btn-block"
                   onclick="$(this).deleteRow('#coimplementing-form-group-0');">
                    Delete
                </a>
            </div>

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
                <option value="{{ $unit->id }}">
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
            </select>
        </div>
    </label>

    <div class="md-form mt-5">
        <input type="number" id="project-cost" class="form-control required"
               name="project_cost" value="0.00">
        <label for="project-cost" class="active">
            Total Project Cost <span class="red-text">*</span>
        </label>
    </div>

    <div class="md-form">
        <input type="text" id="project-leader" class="form-control"
               name="project_leader">
        <label for="project-leader">
            Project Coordinator/Leader
        </label>
    </div>

    <br>

    <h5>Project Duration</h5>
    <hr>
    <div class="md-form">
        <input type="date" id="date-from" class="form-control required"
               name="date_from">
        <label for="date-from" class="active">
            From <span class="red-text">*</span>
        </label>
    </div>

    <div class="md-form">
        <input type="date" id="date-to" class="form-control required"
               name="date_to">
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
            <option value="{{ $group->id }}">
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
            <option value="" disabled selected>Choose a type</option>
            <option value="saa">Special Project</option>
            <option value="mooe">Regular MOOE</option>
            <option value="lgia">LGIA</option>
            <option value="setup">SETUP</option>
        </select>
        <label class="mdb-main-label">
            Type/Fund Source <span class="red-text">*</span>
        </label>
    </div><br>
</form>
