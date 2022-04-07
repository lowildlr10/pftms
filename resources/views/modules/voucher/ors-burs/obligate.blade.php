<form id="form-obligate" class="wow animated fadeIn" method="POST"
      action="{{ route('ca-ors-burs-obligate', ['id' => $id]) }}">
    @csrf

    <div class="row">
        <div class="col-md-12">
            <div class="md-form">
                <select class="mdb-select crud-select md-form required" searchable="Search here.."
                        name="type" id="type">
                    <option value="" disabled selected>Choose a type</option>

                    @if (count($serialNos) > 0)
                        @foreach ($serialNos as $serial)
                    <option value="{{ $serial->type }}-{{ $serial->serial_no }}">
                        {!! strtoupper($serial->type) !!}
                    </option>
                        @endforeach
                    @endif
                </select>
                <label class="mdb-main-label">
                    Type <span class="red-text">*</span>
                </label>
            </div>

            <div class="md-form">
                <input type="text" id="serial_no" class="form-control required"
                       name="serial_no" value="{{ $serialNo }}">
                <label for="serial_no" class="{{ !empty($serialNo) ? 'active' : '' }}">
                    Serial Number <span class="red-text">*</span>
                </label>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <label for="sel-uacs-code">Choose the MOOE account titles</label>
            <select class="mdb-select md-form" searchable="Search here.."
                    id="sel-uacs-code" name="uacs_object_code[]" multiple>
                <option value="" disabled>Choose the MOOE account titles</option>

                @if (count($mooeTitles) > 0)
                    @foreach ($mooeTitles as $mooe)
                <option value="{{ $mooe->id }}" {{ in_array($mooe->id, $uacsObjectCode) ? 'selected' : '' }}>
                    {!! $mooe->uacs_code !!} : {!! $mooe->account_title !!}
                </option>
                    @endforeach
                @endif
            </select>
        </div>
        <div class="col-md-12 px-0 text-center">
            <div class="col-md-12 border p-0">
                <div id="remaining-amount-segment" class="col-md-12"
                     style="{{ count($uacsItems) > 0 || count($_uacsItems) > 0 ? '' : 'display: none;'}}">
                    <h4 class="pt-3">UACS Item/s Breakdown</h4>
                    <hr>
                    <div class="md-form form-sm">
                        <label for="uacs_description" class="active">
                            Remaining Amount
                        </label>
                        <input id="remaining-original" type="hidden" value="{{ $amount }}">
                        <input id="remaining" type="number" class="form-control" value="{{ $amount }}"
                               data-toggle="tooltip" data-placement="right"
                               title="This should be equals or greater than zero." readonly>
                    </div>
                </div>
                <div class="col-md-12">
                    <div id="uacs-description-segment">
                        @if (count($uacsItems) > 0)
                            @foreach ($uacsItems as $itemCtr => $item)
                        <div class="col-md-12 border">
                            <div class="md-form form-sm" id="uacs_description_{{ $itemCtr }}">
                                <input type="text" id="uacs_description_{{ $item->uacs_id }}"
                                    name="uacs_description[]" placeholder="Item Description for '{{ $item->description }}'"
                                    class="form-control required" value="{{ $item->description }}">
                                <input type="hidden" id="uacs_id_{{ $item->uacs_id }}" name="uacs_id[]"
                                    value="{{ $item->uacs_id }}">
                                <input type="hidden" id="ors_uacs_id_{{ $item->uacs_id }}" name="ors_uacs_id[]"
                                    value="{{ $item->id }}">
                                <label for="uacs_description_{{ $item->uacs_id }}" class="active">
                                    <span class="red-text">* </span>
                                    <strong>{{ $item->uacs_code }} : {{ $item->description }}</strong>
                                    <a onclick="$(this).deleteUacsItem(
                                        '#uacs_description_{{ $itemCtr }}', '#uacs_amount_{{ $itemCtr }}',
                                        '{{ $item->id }}'
                                    );"
                                    class="btn btn-red btn-sm py-0 rounded" >
                                        <strong>Delete</strong>
                                    </a>
                                </label>
                            </div>
                            <div class="md-form form-sm" id="uacs_amount_{{ $itemCtr }}">
                                <input type="text" id="uacs_amount_{{ $item->uacs_id }}" name="uacs_amount[]"
                                    class="form-control uacs_amount required" value="{{ $item->amount }}">
                                <label for="uacs_amount_{{ $item->uacs_id }}" class="active">
                                    <span class="red-text">* </span>
                                    <strong>{{ $item->uacs_code }} Amount</strong>
                                </label>
                            </div>
                        </div>
                            @endforeach
                        @endif

                        @if (count($_uacsItems) > 0 && count($uacsItems) == 0)
                            @foreach ($_uacsItems as $itemCtr => $item)
                        <div class="col-md-12 border">
                            <div class="md-form form-sm" id="uacs_description_{{ $itemCtr }}">
                                <input type="text" id="uacs_description_{{ $item->id }}"
                                    name="uacs_description[]" placeholder="Item Description for '{{ $item->account_title }}'"
                                    class="form-control required" value="">
                                <input type="hidden" id="uacs_id_{{ $item->id }}" name="uacs_id[]"
                                    value="{{ $item->id }}">
                                <label for="uacs_description_{{ $item->id }}" class="active">
                                    <span class="red-text">* </span>
                                    <strong>{{ $item->uacs_code }} : {{ $item->account_title }}</strong>
                                    <a onclick="$(this).deleteUacsItem(
                                        '#uacs_description_{{ $itemCtr }}', '#uacs_amount_{{ $itemCtr }}',
                                        '{{ $item->id }}'
                                    );"
                                    class="btn btn-red btn-sm py-0 rounded" >
                                        <strong>Delete</strong>
                                    </a>
                                </label>
                            </div>
                            <div class="md-form form-sm" id="uacs_amount_{{ $itemCtr }}">
                                <input type="text" id="uacs_amount_{{ $item->id }}" name="uacs_amount[]"
                                    class="form-control uacs_amount required" value="0">
                                <label for="uacs_amount_{{ $item->id }}" class="active">
                                    <span class="red-text">* </span>
                                    <strong>{{ $item->uacs_code }} Amount</strong>
                                </label>
                            </div>
                        </div>
                            @endforeach
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
