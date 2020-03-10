<form id="form-update" method="POST" action="{{ route('item-classification-update', ['id' => $id]) }}">
    @csrf

    <div class="md-form">
        <input type="text" id="classification-name" class="form-control required"
               name="classification_name" value="{{ $classification }}">
        <label for="classification-name" class="{{ !empty($classification) ? 'active' : '' }}">
            Item Classification Name <span class="red-text">*</span>
        </label>
    </div>
</form>
