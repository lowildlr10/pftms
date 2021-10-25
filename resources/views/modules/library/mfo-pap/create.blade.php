<form id="form-store" method="POST" action="{{ route('mfo-pap-store') }}">
    @csrf

    <div class="md-form">
        <input type="text" id="code" class="form-control required"
               name="code">
        <label for="code">
            Code <span class="red-text">*</span>
        </label>
    </div>
    <div class="md-form">
        <input type="text" id="description" class="form-control required"
               name="description">
        <label for="description">
            Description <span class="red-text">*</span>
        </label>
    </div>
</form>
