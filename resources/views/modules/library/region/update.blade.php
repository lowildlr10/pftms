<form id="form-update" method="POST" action="{{ route('region-update', ['id' => $id]) }}">
    @csrf

    <div class="md-form">
        <input type="text" id="region-name" class="form-control required"
               name="region_name" value="{{ $region }}">
        <label for="region-name" class="{{ !empty($region) ? 'active' : '' }}">
            Region Name <span class="red-text">*</span>
        </label>
    </div>
</form>
