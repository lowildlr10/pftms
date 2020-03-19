<form id="form-store" method="POST" action="{{ route('funding-source-store') }}">
    @csrf

    <div class="md-form">
        <input type="text" id="reference-code" class="form-control"
               name="reference_code">
        <label for="reference-code">Reference Code</label>
    </div>

    <div class="md-form">
        <input type="text" id="source-name" class="form-control required"
               name="source_name">
        <label for="source-name">
            Funding Source Name <span class="red-text">*</span>
        </label>
    </div>
</form>