<form id="form-store" method="POST" action="{{ route('region-store') }}">
    @csrf

    <div class="md-form">
        <input type="text" id="region-name" class="form-control required"
               name="region_name">
        <label for="region-name">
            Region Name <span class="red-text">*</span>
        </label>
    </div>
</form>
