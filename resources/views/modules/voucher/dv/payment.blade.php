<form id="form-payment" class="wow animated fadeIn" method="POST"
      action="{{ route('ca-dv-payment', ['id' => $id]) }}">
    @csrf

    <div class="row">
        <div class="col-md-12">
            <div class="md-form">
                <input type="text" id="dv-no" class="form-control required"
                       name="dv_no" value="{{ $dvNo }}">
                <label for="dv-no" class="{{ !empty($dvNo) ? 'active' : '' }}">
                    DV Number <span class="red-text">*</span>
                </label>
            </div>
        </div>
    </div>
</form>
