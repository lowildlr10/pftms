<form id="form-store" method="POST" action="{{ route('project-store') }}">
    @csrf

    <h5>Project Details</h5>
    <hr>
    <div class="md-form">
        <input type="text" id="project-title" class="form-control required"
               name="project_title">
        <label for="project-title">
            Project Title <span class="red-text">*</span>
        </label>
    </div>

    <div class="md-form">
        <select class="mdb-select crud-select md-form required" searchable="Search here.."
                name="industry_sector">
            <option value="" disabled selected>Choose an Industry/Sector</option>

            @if (count($industries) > 0)
                @foreach ($industries as $industry)
            <option value="{{ $industry->id }}">
                {!! $industry->sector_name !!}
            </option>
                @endforeach
            @endif
        </select>
        <label class="mdb-main-label">
            Industry/Sector <span class="red-text">*</span>
        </label>
    </div>

    <div class="md-form">
        <select class="mdb-select crud-select md-form required" searchable="Search here.."
                name="project_site">
            <option value="" disabled selected>Choose a project site</option>

            @if (count($municipalities) > 0)
                @foreach ($municipalities as $municipality)
            <option value="{{ $municipality->id }}">
                {!! $municipality->municipality_name !!}
            </option>
                @endforeach
            @endif
        </select>
        <label class="mdb-main-label">
            Project Site <span class="red-text">*</span>
        </label>
    </div>

    <div class="md-form">
        <select class="mdb-select crud-select md-form required" searchable="Search here.."
                name="implementing_agency">
            <option value="" disabled selected>Choose an Implementing Agency</option>

            @if (count($agencies) > 0)
                @foreach ($agencies as $agency)
            <option value="{{ $agency->id }}">
                {!! $agency->agency_name !!}
            </option>
                @endforeach
            @endif
        </select>
        <label class="mdb-main-label">
            Implementing Agency <span class="red-text">*</span>
        </label>
    </div>

    <div class="md-form">
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
                    <select class="mdb-select form-control-sm agencies-tokenizer coimplementing-agency-lgus"
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

    <div class="md-form">

        <select class="mdb-select crud-select md-form required" searchable="Search here.."
                name="proponent_units[]" multiple>
            <option value="" disabled selected>Choose a Proponent Units/PSTCs</option>

            @if (count($empUnits) > 0)
                @foreach ($empUnits as $unit)
            <option value="{{ $unit->id }}">
                {!! $unit->unit_name !!}
            </option>
                @endforeach
            @endif
        </select>
        <label class="mdb-main-label">
            Proponent Units/PSTCs <span class="red-text">*</span>
        </label>
    </div>

    <div class="md-form">
        <select class="mdb-select crud-select md-form required" searchable="Search here.."
                name="monitoring_office[]" multiple>
            <option value="" disabled selected>Choose a Monitoring Offices</option>

            @if (count($monitoringOffices) > 0)
                @foreach ($monitoringOffices as $office)
            <option value="{{ $office->id }}">
                {!! $office->office_name !!}
            </option>
                @endforeach
            @endif
        </select>
        <label class="mdb-main-label">
            Monitoring Office <span class="red-text">*</span>
        </label>
    </div>

    <div class="md-form">
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
            Project Leader
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

    <h5>Others</h5>
    <hr>
    <div class="md-form">
        <select class="mdb-select crud-select md-form required" searchable="Search here.."
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
            Access Groups
        </label>
    </div><br>
</form>
