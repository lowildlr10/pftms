<form id="form-update" method="POST" action="{{ route('paper-size-update', ['id' => $id]) }}">
    @csrf

    <div class="md-form">
        <input type="text" id="paper-type" class="form-control required"
               name="paper_type" value="{{ $paperType }}">
        <label for="paper-type" class="{{ !empty($paperType) ? 'active' : '' }}">
            Paper Type <span class="red-text">*</span>
        </label>
    </div>

    <div class="md-form">
        <select class="mdb-select md-form required" searchable="Search here.."
                name="unit">
            <option value="" disabled selected>Choose a unit</option>
            <option value="mm" {{ $unit == 'mm' ? 'selected' : '' }}>mm</option>
            <option value="cm" {{ $unit == 'cm' ? 'selected' : '' }}>cm</option>
            <option value="in" {{ $unit == 'in' ? 'selected' : '' }}>inch</option>
        </select>
        <label class="mdb-main-label">
            Unit <span class="red-text">*</span>
        </label>
    </div>

    <div class="md-form">
        <input type="number" id="width" class="form-control required"
               name="width" value="{{ $width }}">
        <label for="width" class="{{ !empty($width) ? 'active' : '' }}">
            Width <span class="red-text">*</span>
        </label>
    </div>

    <div class="md-form">
        <input type="number" id="height" class="form-control required"
               name="height" value="{{ $height }}">
        <label for="height" class="{{ !empty($height) ? 'active' : '' }}">
            Height <span class="red-text">*</span>
        </label>
    </div>
</form>
