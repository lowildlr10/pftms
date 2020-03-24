<form id="form-issue" class="wow animated fadeIn" method="POST" action="{{ route('rfq-issue', ['id' => $id]) }}">
    @csrf

    <div class="row">
        <div class="col-md-12">
            <div class="md-form">
                <select class="mdb-select crud-select md-form required" searchable="Search here.."
                        name="issued_to">
                    <option value="" disabled selected>Choose an issuee</option>
                    <option value="">-- None --</option>

                    @if (!empty($users))
                        @foreach ($users as $emp)
                    <option value="{{ $emp->id }}">
                        {{ $emp->firstname }} {{ $emp->lastname }}
                    </option>
                        @endforeach
                    @endif
                </select>
                <label class="mdb-main-label">
                    Responsible Person <span class="red-text">*</span>
                </label>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="md-form">
                <textarea id="remarks" class="md-textarea form-control"
                          name="remarks" rows="3"></textarea>
                <label for="remarks">
                    Remarks
                </label>
            </div>
        </div>
    </div>
</form>
