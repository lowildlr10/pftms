<form id="form-update" class="wow animated fadeIn" method="POST" action="{{ route('rfq-update', ['id' => $id]) }}">
    @csrf

    <div class="row">
        <div class="col-md-6"></div>
        <div class="col-md-6">
            <div class="md-form">
                <input type="date" id="date-canvass" class="form-control required"
                       name="date_canvass" value="{{ $rfqDate }}">
                <label for="date-canvass" class="active">
                    Date <span class="red-text">*</span>
                </label>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 my-3 mb-4">
            <div class="table-responsive-md table-responsive-sm table-responsive-lg text-nowrap my-1">
                <table class="table z-depth-1 my-1" style="width: 100%;">
                    <thead class="mdb-color white-text">
                        <tr>
                            <th class="text-center" width="3%">#</th>
                            <th class="text-center" width="8%">
                                Unit
                            </th>
                            <th class="text-center" width="33%">
                                Item Description
                            </th>
                            <th class="text-center" width="10%">
                                Quantity
                            </th>
                            <th class="text-center" width="15%">
                                Unit Cost
                            </th>
                            <th class="text-center" width="15%">
                                Total Cost
                            </th>
                            <th width="10%">
                                Group No <span class="red-text">*</span>
                            </th>
                        <tr>
                    </thead>

                    <tbody>
                        @if (!empty($prItems))
                            @foreach ($prItems as $key => $item)
                        <tr>
                            <td align="center">{{ $key + 1 }}</td>
                            <td>
                                <div class="md-form my-0 py-0">
                                    <select class="mdb-select crud-select md-form required" searchable="Search here.." disabled>
                                        <option value="" disabled selected>Choose a Unit</option>

                                        @if (count($unitIssues) > 0)
                                            @foreach ($unitIssues as $unit)
                                        <option value="{{ $unit->id }}" {{ $unit->id == $item->unit_issue ? 'selected' : '' }}>
                                            {!! $unit->unit_name !!}
                                        </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </td>
                            <td>
                                <div class="md-form my-0 py-0">
                                    <textarea class="md-textarea form-control" placeholder="Item description..."
                                            rows="3" readonly>{{ $item->item_description }}</textarea>
                                </div>
                            </td>
                            <td>
                                <div class="md-form">
                                    <input type="number" class="form-control required"
                                        value="{{ $item->quantity }}" readonly>
                                </div>
                            </td>
                            <td>
                                <div class="md-form ">
                                    <input type="text" class="form-control required"
                                        value="&#8369; {{ number_format($item->est_unit_cost, 2) }}"
                                        readonly>
                                </div>
                            </td>
                            <td>
                                <div class="md-form ">
                                    <input type="text" class="form-control required"
                                        value="&#8369; {{ number_format($item->est_total_cost, 2) }}"
                                        readonly>
                                </div>
                            </td>
                            <td>
                                <input type="hidden" name="pr_item_id[]" value="{{ $item->id }}">
                                <div class="md-form my-0 py-0">
                                    <select class="mdb-select crud-select md-form required" searchable="Search here.."
                                            name="canvass_group[]">
                                        <option value="" disabled selected>Choose a value</option>

                                        @for ($grpNo = 0; $grpNo <= 20; $grpNo++)
                                            <option value="{{ $grpNo }}" {{ $item->group_no == $grpNo ? 'selected' : '' }}>
                                                {{ $grpNo }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                            </td>
                        </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="md-form">
                <select class="mdb-select crud-select md-form" searchable="Search here.."
                        name="canvassed_by">
                    <option value="" disabled selected>Choose a canvasser</option>
                    <option value="">-- None --</option>

                    @if (count($users) > 0)
                        @foreach ($users as $emp)
                    <option value="{{ $emp->id }}" {{ $emp->id == $canvassedBy ? 'selected' : '' }}>
                        {!! $emp->firstname.' '.$emp->lastname !!} [{!! $emp->position !!}]
                    </option>
                        @endforeach
                    @endif
                </select>
                <label class="mdb-main-label">
                    Canvassed By
                </label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="md-form">
                <select class="mdb-select crud-select md-form required" searchable="Search here.."
                        name="sig_rfq">
                    <option value="" disabled selected>Choose a signatory</option>

                    @if (count($signatories) > 0)
                        @foreach ($signatories as $sig)
                            @if ($sig->module->rfq->truly_yours)
                    <option value="{{ $sig->id }}" {{ $sig->id == $sigRFQ ? 'selected' : '' }}>
                        {!! $sig->name !!} [{!! $sig->module->rfq->designation !!}]
                    </option>
                            @endif
                        @endforeach
                    @endif
                </select>
                <label class="mdb-main-label">
                    Truly Yours <span class="red-text">*</span>
                </label>
            </div>
        </div>
    </div>
</form>
