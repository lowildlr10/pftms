<form id="form-update" method="POST" action="{{ route('funding-source-update', ['id' => $id]) }}">
    @csrf

    <div class="md-form">
        <input type="text" id="reference-code" class="form-control"
               name="reference_code" value="{{ $referenceCode }}">
        <label for="reference-code" class="{{ !empty($referenceCode) ? 'active' : '' }}">
            Reference Code
        </label>
    </div>

    <div class="md-form">
        <input type="text" id="source-name" class="form-control required"
               name="source_name" value="{{ $funding }}">
        <label for="source-name" class="{{ !empty($funding) ? 'active' : '' }}">
            Funding Source Name <span class="red-text">*</span>
        </label>
    </div>
</form>
