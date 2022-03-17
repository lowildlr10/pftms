<form id="form-update" method="POST" action="{{ route('uacs-object-code-update',
                                                     ['id' => $id]) }}">
    @csrf

    <div class="md-form">
        <select class="mdb-select crud-select md-form required" searchable="Search here.."
                name="classification">
            <option value="" disabled selected>Choose a classification</option>

            @if (count($uacsClassifications) > 0)
                @foreach ($uacsClassifications as $classification)
            <option value="{{ $classification->id }}" {{ $classification->id == $uacsClassification ? 'selected' : '' }}>
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
               name="account_title_header" value="{{ $accountTitleHeader }}">
        <label for="account-title-header" class="{{ !empty($accountTitleHeader) ? 'active' : '' }}">
            Account Title Header
        </label>
    </div>

    <div class="md-form">
        <input type="text" id="account-title" class="form-control required"
               name="account_title" value="{{ $accountTitle }}">
        <label for="account-title" class="{{ !empty($accountTitle) ? 'active' : '' }}">
            Account Title <span class="red-text">*</span>
        </label>
    </div>

    <div class="md-form">
        <input type="text" id="uacs-code" class="form-control required"
               name="uacs_code" value="{{ $uacsCode }}">
        <label for="uacs-code" class="{{ !empty($uacsCode) ? 'active' : '' }}">
            UACS Code <span class="red-text">*</span>
        </label>
    </div>

    <div class="md-form mt-5">
        <textarea class="md-textarea form-control" id="description" name="description" rows="5"
        >{{ $description }}</textarea>
        <label for="description" class="{{ !empty($description) ? 'active' : '' }}">
            Description
        </label>
    </div>
</form>
