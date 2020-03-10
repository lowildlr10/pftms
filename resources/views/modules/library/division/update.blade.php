<form id="form-update" method="POST" action="{{ route('emp-division-update', ['id' => $id]) }}">
    @csrf

    <div class="md-form">
        <input type="text" id="division-name" class="form-control required"
               name="division_name" value="{{ $division }}">
        <label for="division-name" class="{{ !empty($division) ? 'active' : '' }}">
            Employee Division Name <span class="red-text">*</span>
        </label>
    </div>
</form>
