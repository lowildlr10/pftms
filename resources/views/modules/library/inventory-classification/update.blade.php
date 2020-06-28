<form id="form-update" method="POST" action="{{ route('inventory-classification-update', ['id' => $id]) }}">
    @csrf

    <div class="md-form">
        <input type="text" id="classification-name" class="form-control required"
               name="classification_name" value="{{ $classification }}">
        <label for="classification-name" class="{{ !empty($classification) ? 'active' : '' }}">
            inventory Classification Name <span class="red-text">*</span>
        </label>
    </div>

    <div class="md-form">
        <input type="text" id="abbrv" class="form-control required"
               name="abbrv" value="{{ $abbrv }}">
        <label for="abbrv" class="{{ !empty($abbrv) ? 'active' : '' }}">
            Abbreviation <span class="red-text">*</span>
        </label>
    </div>
</form>
