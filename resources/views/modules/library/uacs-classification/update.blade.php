<form id="form-update" method="POST" action="{{ route('uacs-classification-update',
                                                      ['id' => $id]) }}">
    @csrf

    <div class="md-form">
        <input type="text" id="classification-name" class="form-control required"
               name="classification_name" value="{{ $classificationName }}">
        <label for="classification-name" class="{{ !empty($classificationName) ? 'active' : '' }}">
            Classification Name <span class="red-text">*</span>
        </label>
    </div>
</form>
