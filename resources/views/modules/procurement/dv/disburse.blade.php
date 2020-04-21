<form id="form-obligate" class="wow animated fadeIn" method="POST"
      action="{{ route('proc-ors-burs-obligate', ['id' => $id]) }}">
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
