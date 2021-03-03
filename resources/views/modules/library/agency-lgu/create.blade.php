<form id="form-store" method="POST" action="{{ route('agency-lgu-store') }}">
    @csrf

    <div class="md-form">
        <select class="mdb-select crud-select md-form" searchable="Search here.."
                name="region">
            <option value="" disabled selected>Choose a region</option>
            <option value="">-- None --</option>

            @if (count($regions) > 0)
                @foreach ($regions as $region)
            <option value="{{ $region->id }}">
                {!! $region->region_name !!}
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
                @foreach ($provinces as $province)
            <option value="{{ $province->id }}">
                {!! $province->province_name !!}
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
                @foreach ($municipalities as $municipality)
            <option value="{{ $municipality->id }}">
                {!! $municipality->municipality_name !!}
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
               name="agency_lgu">
        <label for="agency-lgu">
            Agency/LGU <span class="red-text">*</span>
        </label>
    </div>
</form>
