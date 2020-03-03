<form id="form-update" method="POST" action="{{ route('procurement-mode-update', ['id' => $id]) }}">
    @csrf

    <div class="md-form">
        <input type="text" id="mode-name" class="form-control required"
               name="mode_name" value="{{ $mode }}">
        <label for="mode-name" class="{{ !empty($mode) ? 'active' : '' }}">
            Mode of Procurement Name <span class="red-text">*</span>
        </label>
    </div>
</form>
