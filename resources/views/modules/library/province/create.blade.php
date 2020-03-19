<form id="form-store" method="POST" action="{{ route('province-store') }}">
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
        <input type="text" id="province-name" class="form-control required"
               name="province_name">
        <label for="province-name">
            Province Name <span class="red-text">*</span>
        </label>
    </div>
</form>
