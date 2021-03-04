<form id="form-update" method="POST" action="{{ route('industry-sector-update',
                                                      ['id' => $id]) }}">
    @csrf

    <div class="md-form">
        <input type="text" id="industry-sector" class="form-control required"
               name="industry_sector" value="{{ $industrySector }}">
        <label for="industry-sector" class="active">
            Industry/Sector <span class="red-text">*</span>
        </label>
    </div>
</form>
