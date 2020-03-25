<form id="form-receive" class="wow animated fadeIn" method="POST" action="{{ route('rfq-receive', ['id' => $id]) }}">
    @csrf

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
