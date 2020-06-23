<form id="form-liquidate" class="wow animated fadeIn" method="POST"
      action="{{ route('ca-lr-liquidate', ['id' => $id]) }}">
    @csrf

    <div class="row">
        <div class="col-md-12">
            <div class="md-form">
                <input type="text" id="serial_no" class="form-control required"
                       name="serial_no" value="{{ $serialNo }}">
                <label for="serial_no" class="{{ !empty($serialNo) ? 'active' : '' }}">
                    Serial Number <span class="red-text">*</span>
                </label>
            </div>
        </div>
    </div>
</form>
