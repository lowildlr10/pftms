<form id="form-store" method="POST" action="{{ route('inventory-classification-store') }}">
    @csrf

    <div class="md-form">
        <input type="text" id="classification-name" class="form-control required"
               name="classification_name">
        <label for="classification-name">
            Inventory Classification Name <span class="red-text">*</span>
        </label>
    </div>

    <div class="md-form">
        <input type="text" id="abbrv" class="form-control required"
               name="abbrv">
        <label for="abbrv">
            Abbreviation <span class="red-text">*</span>
        </label>
    </div>
</form>
