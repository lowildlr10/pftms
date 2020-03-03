<form id="form-store" method="POST" action="{{ route('procurement-mode-store') }}">
    @csrf

    <div class="md-form">
        <input type="text" id="mode-name" class="form-control required"
               name="mode_name">
        <label for="mode-name">
            Mode of Procurement Name <span class="red-text">*</span>
        </label>
    </div>
</form>
