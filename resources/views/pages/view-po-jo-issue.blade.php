<form id="form-po-jo-issue" method="POST" class="wow animated fadeIn"
      action="{{ url('procurement/po-jo/issue/' . $key . '?type=' . $type) }}">
    @csrf

    <label>Responsible Person</label>
    <div class="md-form ml-0 mr-0 mb-5 mt-0">
        <select class="browser-default custom-select z-depth-1 required" name="issued_to">
            <option value=""> -- Select responsible person -- </option>

            @if ($issuedTo)
                @foreach ($issuedTo as $emp)
            <option value="{{ $emp->emp_id }}">
                {{ $emp->firstname }} {{ $emp->lastname }}
            </option>
                @endforeach
            @endif

        </select>
    </div>

    <div class="form-group shadow-textarea">
        <label>Remarks</label>
        <textarea name="remarks" class="form-control z-depth-1" 
                  rows="3" placeholder="Remarks..."></textarea>
    </div>

    <div class="text-center mt-4">
        <button type="button" class="btn btn-orange waves-effect btn-block" 
                onclick="$(this).issue();">
            <i class="fas fa-paper-plane"></i> Issue
        </button>
    </div>
</form>