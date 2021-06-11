<form id="form-store" class="wow animated fadeIn" method="POST"
      action="{{ route('report-obligation-ledger-store', ['type' => 'obligation']) }}">
    @csrf
    <div class="card">
        <div class="card-body">
            <h4>Obligation Ledger</h4>
            <hr>
            <div class="row">
                <div class="col-md-12">
                    @if ($hasLIB)

                    @else
                    <h5 class="w-100 py-4 text-center">
                        Create a Line-Item Budget
                        <a href="{{ route('fund-project-lib') }}">
                            here
                        </a>
                        first.
                    </h5>
                    @endif
                </div>
            </div>
        </div>
    </div>
</form>
