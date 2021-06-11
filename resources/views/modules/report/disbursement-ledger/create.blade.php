<form id="form-store" class="wow animated fadeIn" method="POST"
      action="{{ route('report-disbursement-ledger-store', ['type' => 'disbursement']) }}">
    @csrf
    <div class="card">
        <div class="card-body">
            <h4>Disbursement Ledger</h4>
            <hr>
        </div>
    </div>
</form>
