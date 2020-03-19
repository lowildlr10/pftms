<form id="form-store" method="POST" action="{{ route('signatory-store') }}">
    @csrf

    <div class="md-form">
        <select class="mdb-select md-form required" searchable="Search here.."
                name="emp_id">
            <option value="" disabled selected>Choose employee *</option>

            @if (count($employees) > 0)
                @foreach ($employees as $emp)
            <option value="{{ $emp->id }}">
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
            <option value="" disabled selected>Choose active status</option>
            <option value="y">Yes</option>
            <option value="n">No</option>
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
               value="{{ $parentID }}">
        <label class="custom-control-label font-weight-bold" for="{{ $parentID }}">
            {!! $label[$parentID] !!}
        </label>
    </div>

    <div id="{{ $parentID }}-menu" style="display: none;">
        <hr class="my-1">
        <div class="pl-3 mb-4">
            <div class="md-form mt-3">
                <input type="text" id="{{ $parentID }}_designation" class="form-control"
                       name="{{ $parentID }}_designation">
                <label for="{{ $parentID }}_designation">
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
                       value="{{ $access }}">
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
