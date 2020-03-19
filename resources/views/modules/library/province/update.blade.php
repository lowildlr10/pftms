<form id="form-update" method="POST" action="{{ route('province-update', ['id' => $id]) }}">
    @csrf

    <div class="md-form">
        <select class="mdb-select md-form required" searchable="Search here.."
                name="region">
            <option value="" disabled selected>
                Choose region
            </option>

            @if (count($regions) > 0)
                @foreach ($regions as $reg)
            <option value="{{ $reg->id }}" {{ ($reg->id == $region) ? 'selected' : '' }}>
                {!! $reg->region_name !!}
            </option>
                @endforeach
            @endif
        </select>
        <label class="mdb-main-label">
            Region <span class="red-text">*</span>
        </label>
    </div>

    <div class="md-form">
        <input type="text" id="province-name" class="form-control required"
               name="province_name" value="{{ $provinceName }}"">
        <label for="province-name" class="{{ !empty($provinceName) ? 'active' : '' }}">
            Province Name <span class="red-text">*</span>
        </label>
    </div>
</form>
