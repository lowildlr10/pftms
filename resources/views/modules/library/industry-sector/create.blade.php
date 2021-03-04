<form id="form-store" method="POST" action="{{ route('industry-sector-store') }}">
    @csrf

    <div class="md-form">
        <input type="text" id="industry-sector" class="form-control required"
               name="industry_sector">
        <label for="industry-sector">
            Industry/Sector <span class="red-text">*</span>
        </label>
    </div>
</form>
