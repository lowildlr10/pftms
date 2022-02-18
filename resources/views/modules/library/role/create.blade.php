<form id="form-store" method="POST" action="{{ route('emp-role-store') }}">
    @csrf

    <div class="md-form">
        <input type="text" id="role" class="form-control required"
               name="role">
        <label for="role">
            Employee Role Name <span class="red-text">*</span>
        </label>
    </div>

    <span class="d-block text-center">
        <strong class="text-black-50">* MODULE ACCESS *</strong>
        <input type="hidden" name="module_access" id="json-access">
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
        <div class="pl-3">
            @if (count($module) > 0)
            <div class="custom-control custom-checkbox ">
                <input type="checkbox" class="custom-control-input" id="sel-{{ $parentID }}">
                <label class="custom-control-label" for="sel-{{ $parentID }}">
                    <small><em>-- Select all action --</em></small>
                </label>
            </div>
            @endif

            <div class="custom-control custom-checkbox ">
                <input type="checkbox" class="custom-control-input" id="allowed-{{ $parentID }}"
                       disabled>
                <label class="custom-control-label" for="allowed-{{ $parentID }}">
                    Allowed (View/Read)
                </label>
            </div>

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

    <div class="md-form">
        <select class="mdb-select md-form required" searchable="Search here.."
                name="is_developer">
            <option value="" disabled>Choose a value</option>
            <option value="y">Yes</option>
            <option value="n" selected>No</option>
        </select>
        <label class="mdb-main-label">
            Is a Developer Role? <span class="red-text">*</span>
        </label>
    </div>

    <div class="md-form">
        <select class="mdb-select md-form required" searchable="Search here.."
                name="is_administrator">
            <option value="" disabled>Choose a value</option>
            <option value="y">Yes</option>
            <option value="n" selected>No</option>
        </select>
        <label class="mdb-main-label">
            Is an Administrator Role? <span class="red-text">*</span>
        </label>
    </div>

    <div class="md-form">
        <select class="mdb-select md-form required" searchable="Search here.."
                name="is_rd">
            <option value="" disabled>Choose a value</option>
            <option value="y">Yes</option>
            <option value="n" selected>No</option>
        </select>
        <label class="mdb-main-label">
            Is a Regional Director Role? <span class="red-text">*</span>
        </label>
    </div>

    <div class="md-form">
        <select class="mdb-select md-form required" searchable="Search here.."
                name="is_ard">
            <option value="" disabled>Choose a value</option>
            <option value="y">Yes</option>
            <option value="n" selected>No</option>
        </select>
        <label class="mdb-main-label">
            Is an Assistant Regional Director Role? <span class="red-text">*</span>
        </label>
    </div>

    <div class="md-form">
        <select class="mdb-select md-form required" searchable="Search here.."
                name="is_pstd">
            <option value="" disabled>Choose a value</option>
            <option value="y">Yes</option>
            <option value="n" selected>No</option>
        </select>
        <label class="mdb-main-label">
            Is a Provincial Science & Technology Director Role? <span class="red-text">*</span>
        </label>
    </div>

    <div class="md-form">
        <select class="mdb-select md-form required" searchable="Search here.."
                name="is_planning">
            <option value="" disabled>Choose a value</option>
            <option value="y">Yes</option>
            <option value="n" selected>No</option>
        </select>
        <label class="mdb-main-label">
            Is a Planning Role? <span class="red-text">*</span>
        </label>
    </div>

    <div class="md-form">
        <select class="mdb-select md-form required" searchable="Search here.."
                name="is_project_staff">
            <option value="" disabled>Choose a value</option>
            <option value="y">Yes</option>
            <option value="n" selected>No</option>
        </select>
        <label class="mdb-main-label">
            Is a Project Staff Role? <span class="red-text">*</span>
        </label>
    </div>

    <div class="md-form">
        <select class="mdb-select md-form required" searchable="Search here.."
                name="is_accountant">
            <option value="" disabled>Choose a value</option>
            <option value="y">Yes</option>
            <option value="n" selected>No</option>
        </select>
        <label class="mdb-main-label">
            Is an Accountant Role? <span class="red-text">*</span>
        </label>
    </div>

    <div class="md-form">
        <select class="mdb-select md-form required" searchable="Search here.."
                name="is_budget">
            <option value="" disabled>Choose a value</option>
            <option value="y">Yes</option>
            <option value="n" selected>No</option>
        </select>
        <label class="mdb-main-label">
            Is a Budget Role? <span class="red-text">*</span>
        </label>
    </div>

    <div class="md-form">
        <select class="mdb-select md-form required" searchable="Search here.."
                name="is_cashier">
            <option value="" disabled>Choose a value</option>
            <option value="y">Yes</option>
            <option value="n" selected>No</option>
        </select>
        <label class="mdb-main-label">
            Is a Cashier Role? <span class="red-text">*</span>
        </label>
    </div>

    <div class="md-form">
        <select class="mdb-select md-form required" searchable="Search here.."
                name="is_property_supply">
            <option value="" disabled>Choose a value</option>
            <option value="y">Yes</option>
            <option value="n" selected>No</option>
        </select>
        <label class="mdb-main-label">
            Is a Property & Supply Role? <span class="red-text">*</span>
        </label>
    </div>

    <div class="md-form">
        <select class="mdb-select md-form required" searchable="Search here.."
                name="is_ordinary">
            <option value="" disabled>Choose a value</option>
            <option value="y">Yes</option>
            <option value="n" selected>No</option>
        </select>
        <label class="mdb-main-label">
            Is an Ordinary Role? <span class="red-text">*</span>
        </label>
    </div>
</form>
