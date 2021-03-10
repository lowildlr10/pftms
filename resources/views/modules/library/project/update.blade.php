<form id="form-update" method="POST" action="{{ route('project-update', ['id' => $id]) }}">
    @csrf

    <h5>Project Details</h5>
    <hr>
    <div class="md-form">
        <input type="text" id="project-title" class="form-control required"
               name="project_title" value="{{ $projectTitle }}">
        <label for="project-title" class="active">
            Project Title <span class="red-text">*</span>
        </label>
    </div>

    <div class="md-form">
        <select class="mdb-select crud-select md-form required" searchable="Search here.."
                name="industry_sector">
            <option value="" disabled selected>Choose an Industry/Sector</option>

            @if (count($industries) > 0)
                @foreach ($industries as $industry)
            <option value="{{ $industry->id }}"
                    {{ $industry->id == $industrySector ? 'selected' : '' }}>
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
            <option value="{{ $municipality->id }}"
                    {{ $municipality->id == $projectSite ? 'selected' : '' }}>
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
            <option value="{{ $agency->id }}" {{ $agency->id == $implementingAgency ? 'selected' : '' }}>
                {!! $agency->agency_name !!}
            </option>
                @endforeach
            @endif
        </select>
        <label class="mdb-main-label">
            Implementing Agency <span class="red-text">*</span>
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
                    <select class="mdb-select form-control-sm agencies-tokenizer coimplementing-agency-lgus"
                            name="comimplementing_agency_lgus[]">
                        @if (count($agencies) > 0)
                            @foreach ($agencies as $agency)
                        <option {{ $agency->id == $coimplementor['comimplementing_agency_lgu'] ? 'selected' : '' }}
                                value="{{ $agency->id }}" >
                            {!! $agency->agency_name !!}
                        </option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div class="md-form mt-3">
                    <input type="number" class="form-control coimplementing-project-cost"
                           name="coimplementing_project_costs[]"
                           value="{{ $coimplementor['coimplementing_project_cost'] }}">
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
            @endif

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
            <option value="{{ $unit->id }}" {{ in_array($unit->id, $proponentUnits) ? 'selected' : '' }}>
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
                name="monitoring_office">
            <option value="" disabled selected>Choose a Monitoring Office</option>

            @if (count($monitoringOffices) > 0)
                @foreach ($monitoringOffices as $office)
            <option value="{{ $office->id }}" {{ $office->id == $monitoringOffice ? 'selected' : '' }}>
                {!! $office->agency_name !!}
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
               name="project_cost" value="{{ $projectCost }}">
        <label for="project-title" class="active">
            Project Cost <span class="red-text">*</span>
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
</form>
