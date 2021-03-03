<form id="form-update" method="POST" action="{{ route('agency-lgu-update', ['id' => $id]) }}">
    @csrf

    <div class="md-form">
        <select class="mdb-select crud-select md-form" searchable="Search here.."
                name="region">
            <option value="" disabled selected>Choose a region</option>
            <option value="">-- None --</option>

            @if (count($regions) > 0)
                @foreach ($regions as $_region)
            <option value="{{ $_region->id }}"
                    {{ $_region->id == $region ? 'selected' : '' }}>
                {!! $_region->region_name !!}
            </option>
                @endforeach
            @endif
        </select>
        <label class="mdb-main-label">
            Region
        </label>
    </div>

    <div class="md-form">
        <select class="mdb-select crud-select md-form" searchable="Search here.."
                name="province">
            <option value="" disabled selected>Choose a province</option>
            <option value="">-- None --</option>

            @if (count($provinces) > 0)
                @foreach ($provinces as $_province)
            <option value="{{ $_province->id }}"
                    {{ $_province->id == $province ? 'selected' : '' }}>
                {!! $_province->province_name !!}
            </option>
                @endforeach
            @endif
        </select>
        <label class="mdb-main-label">
            Province
        </label>
    </div>

    <div class="md-form">
        <select class="mdb-select crud-select md-form" searchable="Search here.."
                name="municipality">
            <option value="" disabled selected>Choose a muncipality</option>
            <option value="">-- None --</option>

            @if (count($municipalities) > 0)
                @foreach ($municipalities as $_municipality)
            <option value="{{ $_municipality->id }}"
                    {{ $_municipality->id == $municipality ? 'selected' : '' }}>
                {!! $_municipality->municipality_name !!}
            </option>
                @endforeach
            @endif
        </select>
        <label class="mdb-main-label">
            Municipality
        </label>
    </div>

    <div class="md-form">
        <input type="text" id="agency-lgu" class="form-control required"
               name="agency_lgu" value="{{ $agencyName }}">
        <label for="agency-lgu" class="active">
            Agency/LGU <span class="red-text">*</span>
        </label>
    </div>
</form>
