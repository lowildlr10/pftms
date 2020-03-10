<form id="form-update" method="POST" action="{{ route('item-unit-issue-update',
                                                      ['id' => $id]) }}">
    @csrf

    <div class="md-form">
        <input type="text" id="unit-name" class="form-control required"
               name="unit_name" value="{{ $unit }}">
        <label for="unit-name" class="{{ !empty($unit) ? 'active' : '' }}">
            Unit of Issue Name <span class="red-text">*</span>
        </label>
    </div>
</form>
