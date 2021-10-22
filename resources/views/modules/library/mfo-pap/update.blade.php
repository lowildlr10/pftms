<form id="form-update" method="POST" action="{{ route('mfo-pap-update', ['id' => $id]) }}">
    @csrf

    <div class="md-form">
        <input type="text" id="code" class="form-control required"
               name="code" value="{{ $code }}">
        <label for="code" class="active">
            Code <span class="red-text">*</span>
        </label>
    </div>
    <div class="md-form">
        <input type="text" id="description" class="form-control required"
               name="description" value="{{ $description }}">
        <label for="description" class="active">
            Description <span class="red-text">*</span>
        </label>
    </div>
</form>
