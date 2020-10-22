<form id="form-store" method="POST" action="{{ route('funding-source-store') }}">
    @csrf

    <div class="md-form">
        <input type="text" id="source-name" class="form-control required"
               name="source_name">
        <label for="source-name">
            Funding Source Name <span class="red-text">*</span>
        </label>
    </div>
</form>
