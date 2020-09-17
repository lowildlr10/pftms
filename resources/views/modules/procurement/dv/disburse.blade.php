<form id="form-disburse" class="wow animated fadeIn" method="POST"
      action="{{ route('proc-dv-disburse', ['id' => $id]) }}">
    @csrf

    <div class="row">
        <div class="col-md-12">
            <div class="md-form">
                <input type="text" id="dv-no" class="form-control {{ !$readOnly ? 'required' : ''}}"
                       name="dv_no" value="{{ $dvNo }}" {{ $readOnly ? 'readonly' : ''}}>
                <label for="dv-no" class="{{ !empty($dvNo) ? 'active' : '' }}">
                    DV Number @if (!$readOnly)<span class="red-text">*</span>@endif
                </label>
            </div>
        </div>
    </div>
</form>
