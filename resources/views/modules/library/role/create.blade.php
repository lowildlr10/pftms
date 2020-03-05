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
            @if (count($module) > 0)
    <div id="{{ $parentID }}-menu" style="display: none;">
        <hr class="my-1">
        <div class="pl-3">
                @foreach ($module as $accessID => $access)
            <div class="custom-control custom-checkbox ">
                <input type="checkbox" class="custom-control-input" id="{{ $accessID }}"
                       value="{{ $access }}">
                <label class="custom-control-label" for="{{ $accessID }}">
                    {!! $label[$accessID] !!}
                </label>
            </div>
                @endforeach
        </div>
    </div>
            @endif
    <hr class="my-1">
        @endforeach
    @endif

</form>
