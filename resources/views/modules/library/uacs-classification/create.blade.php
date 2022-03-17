<form id="form-store" method="POST" action="{{ route('uacs-classification-store') }}">
    @csrf

    <div class="md-form">
        <input type="text" id="classification-name" class="form-control required"
               name="classification_name">
        <label for="classification-name">
            Classification Name <span class="red-text">*</span>
        </label>
    </div>
</form>
