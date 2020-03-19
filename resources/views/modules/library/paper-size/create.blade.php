<form id="form-store" method="POST" action="{{ route('paper-size-store') }}">
    @csrf

    <div class="md-form">
        <input type="text" id="paper-type" class="form-control required"
               name="paper_type">
        <label for="paper-type">
            Paper Type <span class="red-text">*</span>
        </label>
    </div>

    <div class="md-form">
        <select class="mdb-select md-form required" searchable="Search here.."
                name="unit">
            <option value="" disabled selected>Choose a unit</option>
            <option value="mm">mm</option>
            <option value="cm">cm</option>
            <option value="in">inch</option>
        </select>
        <label class="mdb-main-label">
            Unit <span class="red-text">*</span>
        </label>
    </div>

    <div class="md-form">
        <input type="number" id="width" class="form-control required"
               name="width">
        <label for="width">
            Width <span class="red-text">*</span>
        </label>
    </div>

    <div class="md-form">
        <input type="number" id="height" class="form-control required"
               name="height">
        <label for="height">
            Height <span class="red-text">*</span>
        </label>
    </div>
</form>
