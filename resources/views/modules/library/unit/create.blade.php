<form id="form-store" method="POST" action="{{ route('emp-unit-store') }}">
    @csrf

    <div class="md-form">
        <select class="mdb-select crud-select md-form required" searchable="Search here.."
                name="division">
            <option value="" disabled selected>Choose a division</option>

            @if (count($divisions) > 0)
                @foreach ($divisions as $divsion)
            <option value="{{ $divsion->id }}">
                {!! $divsion->division_name !!}
            </option>
                @endforeach
            @endif
        </select>
        <label class="mdb-main-label">
            Division <span class="red-text">*</span>
        </label>
    </div>

    <div class="md-form">
        <input type="text" id="unit-name" class="form-control required"
               name="unit_name">
        <label for="unit-name">
            Unit Name <span class="red-text">*</span>
        </label>
    </div>
</form>
