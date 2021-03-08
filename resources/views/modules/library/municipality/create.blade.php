<form id="form-store" method="POST" action="{{ route('municipality-store') }}">
    @csrf

    <div class="md-form">
        <select class="mdb-select md-form required" searchable="Search here.."
                name="region">
            <option value="" disabled selected>
                Choose region
            </option>

            @if (count($regions) > 0)
                @foreach ($regions as $reg)
            <option value="{{ $reg->id }}">{!! $reg->region_name !!}</option>
                @endforeach
            @endif
        </select>
        <label class="mdb-main-label">
            Region <span class="red-text">*</span>
        </label>
    </div>

    <div class="md-form">
        <select class="mdb-select md-form required" searchable="Search here.."
                name="province">
            <option value="" disabled selected>
                Choose province
            </option>

            @if (count($provinces) > 0)
                @foreach ($provinces as $province)
            <option value="{{ $province->id }}">{!! $province->province_name !!}</option>
                @endforeach
            @endif
        </select>
        <label class="mdb-main-label">
            Province <span class="red-text">*</span>
        </label>
    </div>

    <div class="md-form">
        <input type="text" id="municipality-name" class="form-control required"
               name="municipality_name">
        <label for="municipality-name">
            City/Municipality Name <span class="red-text">*</span>
        </label>
    </div>
</form>
