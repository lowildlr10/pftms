<form id="form-store" method="POST" action="{{ route('emp-division-store') }}">
    @csrf

    <div class="md-form">
        <input type="text" id="division-name" class="form-control required"
               name="division_name">
        <label for="division-name">
            Employee Division Name <span class="red-text">*</span>
        </label>
    </div>
</form>
