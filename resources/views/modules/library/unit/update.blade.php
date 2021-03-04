<form id="form-update" method="POST" action="{{ route('emp-unit-update',
                                                      ['id' => $id]) }}">
    @csrf

    <div class="md-form">
        <select class="mdb-select crud-select md-form required" searchable="Search here.."
                name="division">
            <option value="" disabled selected>Choose a division</option>

            @if (count($divisions) > 0)
                @foreach ($divisions as $div)
            <option value="{{ $div->id }}" {{ $div->id == $division ? 'selected' : '' }}>
                {!! $div->division_name !!}
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
               name="unit_name" value="{{ $unitName }}">
        <label for="unit-name" class="active">
            Unit Name <span class="red-text">*</span>
        </label>
    </div>
</form>
