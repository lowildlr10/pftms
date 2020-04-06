<form id="form-store" class="wow animated fadeIn" method="POST" action="{{ route('po-jo-store', ['prID' => $prID]) }}">
    @csrf

    <div class="md-form">
        <select class="mdb-select crud-select md-form" searchable="Search here.."
                name="awarded_to">
            <option value="0" disabled selected>Choose a awardee</option>

            @if (count($suppliers) > 0)
                @foreach ($suppliers as $bid)
            <option value="{{ $bid->id }}">
                {!! $bid->company_name !!}
            </option>
                @endforeach
            @endif
        </select>
        <label class="mdb-main-label">
            Awarded To <span class="red-text">*</span>
        </label>
    </div>

    <div class="md-form">
        <select class="mdb-select crud-select md-form" searchable="Search here.."
                name="document_type">
            <option value="0" disabled selected>Choose the document type</option>

            <option value="po">Purchase Order (PO)</option>
            <option value="jo">Job Order (JO)</option>
        </select>
        <label class="mdb-main-label">
            Document Type <span class="red-text">*</span>
        </label>
    </div>
</form>
