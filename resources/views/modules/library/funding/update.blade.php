<form id="form-update" method="POST" action="{{ route('funding-source-update', ['id' => $id]) }}">
    @csrf

    <div class="md-form">
        <input type="text" id="source-name" class="form-control required"
               name="source_name" value="{{ $funding }}">
        <label for="source-name" class="{{ !empty($funding) ? 'active' : '' }}">
            Funding Source Name <span class="red-text">*</span>
        </label>
    </div>
</form>
