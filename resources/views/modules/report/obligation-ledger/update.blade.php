<form id="form-update" class="wow animated fadeIn" method="POST"
      action="{{ route('report-obligation-ledger-update', ['id' => $id]) }}">
    @csrf
    <div class="card">
        <div class="card-body">
            <h4>Obligation Ledger</h4>
            <hr>
        </div>
    </div>
</form>
