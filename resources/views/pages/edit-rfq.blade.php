<form id="form-create" method="POST" action="{{ url('procurement/rfq/update/' . $prID) }}" enctype="multipart/form-data"
      class="wow animated fadeIn">
    @csrf

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>RFQ Date:</label>
                <input type="date" id="date_canvass" name="date_canvass" class="form-control z-depth-1 required"
                       value="{{ $canvass->date_canvass }}">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>Signatory:</label>
                <select class="browser-default custom-select z-depth-1 required" name="sig_rfq">
                    <option value=""> -- Select a signatory -- </option>

                    @if (!empty($signatories))

                        @foreach ($signatories as $signatory)

                            @if ($signatory->id == $canvass->sig_rfq)

                    <option value="{{ $signatory->id }}" selected="selected">
                        {{ $signatory->name }} [ {{ $signatory->position }} ]
                    </option>

                            @else

                    <option value="{{ $signatory->id }}">
                        {{ $signatory->name }} [ {{ $signatory->position }} ]
                    </option>

                            @endif

                        @endforeach

                    @endif

                </select>
            </div>
        </div>
    </div>

	<div class="table-responsive z-depth-1">
		<table id="item-pr-table" class="table table-bordered table-hover z-depth-1 m-0">
            <thead class="mdb-color white-text">
                <tr>
                    <th width="3%">#</th>
                    <th width="5%">Qnty</th>
                    <th width="8%">Unit</th>
                    <th width="44%">Item Description</th>
                    <th width="15%">Estimate Unit Cost</th>
                    <th width="15%">Estimate Total Cost</th>
                    <th width="10%">Group No.</th>
                <tr>
            </thead>

            <tbody>
                @if (!empty($prItems))
                    @foreach ($prItems as $key => $item)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ $item->unit }}</td>
                    <td>{{ $item->item_description }}</td>
                    <td>{{ number_format($item->est_unit_cost, 2) }}</td>
                    <td>{{ number_format($item->est_total_cost, 2) }}</td>
                    <td>
                        <input type="hidden" name="pr_item_id[]" value="{{ $item->item_id }}">
                        <select class="browser-default custom-select z-depth-1" name="canvass_group[]">
                            @for ($i = 0; $i <= 20; $i++)
                                @if ($item->group_no == $i)
                            <option value="{{ $i }}" selected="selected">{{ $i }}</option>
                                @else
                            <option value="{{ $i }}">{{ $i }}</option>
                                @endif
                            @endfor
                        </select>
                    </td>
                </tr>
                    @endforeach
                @endif
            </tbody>
		</table>
    </div>
</form>
