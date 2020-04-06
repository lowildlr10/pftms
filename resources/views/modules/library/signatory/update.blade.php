<form id="form-update" method="POST" action="{{ route('signatory-update', ['id' => $id]) }}">
    @csrf

    <div class="md-form">
        <select class="mdb-select md-form required" searchable="Search here.."
                name="emp_id">
            <option value="" disabled selected>Choose employee</option>

            @if (count($employees) > 0)
                @foreach ($employees as $emp)
            <option value="{{ $emp->id }}" {{ $emp->id == $empID ? 'selected' : '' }}>
                {!! $emp->name !!}
            </option>
                @endforeach
            @endif
        </select>
        <label class="mdb-main-label">
            Employee <span class="red-text">*</span>
        </label>
    </div>

    <div class="md-form">
        <select class="mdb-select md-form required" searchable="Search here.."
                name="is_active">
            <option value="" disabled selected>Choose active status *</option>
            <option value="y" {{ $isActive == 'y' ? 'selected' : '' }}>Yes</option>
            <option value="n" {{ $isActive == 'n' ? 'selected' : '' }}>No</option>
        </select>
        <label class="mdb-main-label">
            Active Status <span class="red-text">*</span>
        </label>
    </div>

    <span class="d-block text-center">
        <strong class="text-black-50">* DOCUMENTS *</strong>
        <input type="hidden" name="module" id="json-access">
    </span>
    <hr class="my-2">

    @if (count($modules) > 0)
        @foreach ($modules as $parentID => $module)
    <div class="custom-control custom-checkbox">
        <input type="checkbox" class="custom-control-input" id="{{ $parentID }}"
               value="{{ $parentID }}"
               {{ (isset($moduleAccess->{$parentID}->is_allowed) &&
                  ($moduleAccess->{$parentID}->is_allowed) ? 'checked':'') }}>
        <label class="custom-control-label font-weight-bold" for="{{ $parentID }}">
            {!! $label[$parentID] !!}
        </label>
    </div>

    <div id="{{ $parentID }}-menu"
         style="display:{{
            (isset($moduleAccess->{$parentID}->is_allowed) &&
            ($moduleAccess->{$parentID}->is_allowed) ? 'block':'none')
        }};">
        <hr class="my-1">
        <div class="pl-3">
            <div class="md-form mt-3">
                <input type="text" id="{{ $parentID }}_designation"
                       class="form-control {{ isset($moduleAccess->{$parentID}->is_allowed) &&
                              ($moduleAccess->{$parentID}->is_allowed) ? 'required':'' }}"
                       name="{{ $parentID }}_designation"
                       value="{{ isset($moduleAccess->{$parentID}->designation) ?
                                 $moduleAccess->{$parentID}->designation : NULL }}">
                <label for="{{ $parentID }}_designation"
                       class="{{ !empty($moduleAccess->{$parentID}->designation) ?
                                  'active' : '' }}">
                    Insert designation <span class="red-text">*</span>
                </label>
            </div>

            @if (count($module) > 0)
            <div class="custom-control custom-checkbox ">
                <input type="checkbox" class="custom-control-input" id="sel-{{ $parentID }}">
                <label class="custom-control-label" for="sel-{{ $parentID }}">
                    <small><em>-- Select all type --</em></small>
                </label>
            </div>
            @endif

            @if (count($module) > 0)
                @foreach ($module as $accessID => $access)
            <div class="custom-control custom-checkbox ">
                <input type="checkbox" class="custom-control-input" id="{{ $accessID }}"
                       value="{{ $access }}"
                       {{ (isset($moduleAccess->{$parentID}->{$access}) &&
                          ($moduleAccess->{$parentID}->{$access}) ? 'checked':'') }}>
                <label class="custom-control-label" for="{{ $accessID }}">
                    {!! $label[$accessID] !!}
                </label>
            </div>
                @endforeach
            @endif
        </div>
    </div>
    <hr class="my-1">
        @endforeach
    @endif

</form>
