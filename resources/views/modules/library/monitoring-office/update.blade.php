<form id="form-update" method="POST" action="{{ route('monitoring-office-update', ['id' => $id]) }}">
    @csrf

    <div class="md-form">
        <select class="mdb-select crud-select md-form" searchable="Search here.."
                name="agency_lgu">
            <option value="" disabled selected>Choose an Agency/LGU</option>
            <option value="">-- None --</option>

            @if (count($agencies) > 0)
                @foreach ($agencies as $agency)
            <option value="{{ $agency->id }}"
                    {{ $agency->id == $agencyLGU ? 'selected' : '' }}>
                {!! $agency->agency_name !!}
            </option>
                @endforeach
            @endif
        </select>
        <label class="mdb-main-label">
            Agency/LGU
        </label>
    </div>

    <div class="md-form">
        <input type="text" id="office-name" class="form-control required"
               name="office_name" value="{{ $officeName }}">
        <label for="office-name" class="active">
            Monitoring Office <span class="red-text">*</span>
        </label>
    </div>
</form>
