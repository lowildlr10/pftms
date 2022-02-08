<form id="form-uacs-items" class="wow animated fadeIn" method="POST"
      action="{{ route('proc-dv-update-uacs-items', ['id' => $id]) }}">
    @csrf

    <div class="row">
        <div class="col-md-12">
            <select class="mdb-select crud-select md-form" searchable="Search here.."
                    id="sel-uacs-code" name="uacs_object_code[]" multiple>
                <option value="" disabled selected>Choose the MOOE account titles</option>

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
                        <div class="row" id="uacs_description_{{ $itemCtr }}">
                            <div class="col-md-10 border">
                                <div class="md-form form-sm">
                                    <input type="text" id="uacs_description_{{ $item->uacs_id }}"
                                        name="uacs_description[]" placeholder="Item Description for '{{ $item->description }}'"
                                        class="form-control required" value="{{ $item->description }}">
                                    <input type="hidden" id="uacs_id_{{ $item->uacs_id }}" name="uacs_id[]"
                                        value="{{ $item->uacs_id }}">
                                    <input type="hidden" id="dv_uacs_id_{{ $item->uacs_id }}" name="dv_uacs_id[]"
                                        value="{{ $item->id }}">
                                    <label for="uacs_description_{{ $item->uacs_id }}" class="active">
                                        <span class="red-text">* </span>
                                        <strong>{{ $item->uacs_code }} : {{ $item->description }}</strong>
                                    </label>
                                </div>
                                <div class="md-form form-sm" id="uacs_amount_{{ $itemCtr }}">
                                    <input type="number" id="uacs_amount_{{ $item->uacs_id }}" name="uacs_amount[]"
                                        class="form-control uacs_amount required" value="{{ $item->amount }}">
                                    <label for="uacs_amount_{{ $item->uacs_id }}" class="active">
                                        <span class="red-text">* </span>
                                        <strong>{{ $item->uacs_code }} Amount</strong>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-2 p-0 border">
                                <a onclick="$(this).deleteUacsItem(
                                        '#uacs_description_{{ $itemCtr }}', '#uacs_amount_{{ $itemCtr }}',
                                        '{{ $item->id }}'
                                    );"
                                    class="btn btn-red btn-sm btn-block h-100 text-center">
                                    <strong>Del <i class="fas fa-trash-alt fa-2x"></i></strong>
                                </a>
                            </div>
                        </div>
                            @endforeach
                        @endif

                        @if (count($_uacsItems) > 0 && count($uacsItems) == 0)
                            @foreach ($_uacsItems as $itemCtr => $item)
                        <div class="row" id="uacs_description_{{ $itemCtr }}">
                            <div class="col-md-10 border">
                                <div class="md-form form-sm">
                                    <input type="text" id="uacs_description_{{ $item->id }}"
                                        name="uacs_description[]" placeholder="Item Description for '{{ $item->account_title }}'"
                                        class="form-control required" value="">
                                    <input type="hidden" id="uacs_id_{{ $item->id }}" name="uacs_id[]"
                                        value="{{ $item->id }}">
                                    <input type="hidden" id="dv_uacs_id_{{ $item->id }}" name="dv_uacs_id[]"
                                           value="">
                                    <label for="uacs_description_{{ $item->id }}" class="active">
                                        <span class="red-text">* </span>
                                        <strong>{{ $item->uacs_code }} : {{ $item->account_title }}</strong>

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
                            <div class="col-md-2 p-0 border">
                                <a onclick="$(this).deleteUacsItem(
                                        '#uacs_description_{{ $itemCtr }}', '#uacs_amount_{{ $itemCtr }}',
                                        '{{ $item->id }}'
                                    );"
                                    class="btn btn-red btn-sm btn-block h-100 text-center" >
                                    <strong>Del <i class="fas fa-trash-alt fa-2x"></i></strong>
                                </a>
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
