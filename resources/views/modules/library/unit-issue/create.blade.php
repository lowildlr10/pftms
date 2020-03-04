<form id="form-store" method="POST" action="{{ route('item-unit-issue-store') }}">
    @csrf

    <div class="md-form">
        <input type="text" id="unit-name" class="form-control required"
               name="unit_name">
        <label for="unit-name">
            Unit of Issue Name <span class="red-text">*</span>
        </label>
    </div>
</form>
