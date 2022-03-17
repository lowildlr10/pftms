<form id="form-store" method="POST" action="{{ route('uacs-object-code-store') }}">
    @csrf

    <div class="md-form">
        <select class="mdb-select crud-select md-form required" searchable="Search here.."
                name="classification">
            <option value="" disabled selected>Choose a classification</option>

            @if (count($uacsClassifications) > 0)
                @foreach ($uacsClassifications as $classification)
            <option value="{{ $classification->id }}">
                {!! $classification->classification_name !!}
            </option>
                @endforeach
            @endif
        </select>
        <label class="mdb-main-label">
            UACS Classification <span class="red-text">*</span>
        </label>
    </div>

    <div class="md-form">
        <input type="text" id="account-title-header" class="form-control"
               name="account_title_header">
        <label for="account-title-header">
            Account Title Header
        </label>
    </div>

    <div class="md-form">
        <input type="text" id="account-title" class="form-control required"
               name="account_title">
        <label for="account-title">
            Account Title <span class="red-text">*</span>
        </label>
    </div>

    <div class="md-form">
        <input type="text" id="uacs-code" class="form-control required"
               name="uacs_code">
        <label for="uacs-code">
            UACS Code <span class="red-text">*</span>
        </label>
    </div>

    <div class="md-form mt-5">
        <textarea class="md-textarea form-control" id="description" name="description" rows="5"></textarea>
        <label for="description">
            Description
        </label>
    </div>
</form>
