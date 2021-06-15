<form id="form-store" class="wow animated fadeIn" method="POST"
      action="{{ route('report-obligation-ledger-store', ['type' => 'obligation']) }}">
    @csrf
    <div class="card">
        <div class="card-body">
            <h4>Obligation Ledger</h4>
            <hr>
            <div class="row">
                <div class="col-md-12  px-0 table-responsive">
                    <table class="table table-sm table-hover table-bordered" style="width: max-content;">
                        <thead class="text-center">
                            <tr>
                                <th class="align-middle" colspan="5"></th>

                                @foreach ($classItemCounts as $classKey => $count)
                                    @if ($count > 0)
                                <th class="align-middle" colspan="{{ $count }}">
                                    {{ $classKey }}
                                </th>
                                    @endif
                                @endforeach

                                <th class="align-middle" width="5px"></th>
                                <th width="1px"></th>
                            </tr>
                        </thead>

                        <thead class="text-center">
                            <tr>
                                <th class="align-top" width="150px">
                                    <small class="font-weight-bold">
                                        <span class="red-text">* </span> Date
                                    </small>
                                </th>
                                <th class="align-top" width="200px">
                                    <small class="font-weight-bold">
                                        <span class="red-text">* </span> Payee
                                    </small>
                                </th>
                                <th class="align-top" width="300px">
                                    <small class="font-weight-bold">
                                        <span class="red-text">* </span> Particulars
                                    </small>
                                </th>
                                <th class="align-top" width="150px">
                                    <small class="font-weight-bold">
                                        <span class="red-text">* </span> ObR No
                                    </small>
                                </th>
                                <th class="align-top" width="180px">
                                    <small class="font-weight-bold">
                                        <span class="red-text">* </span> Total
                                    </small>
                                </th>

                                @foreach ($ledgerItems as $grpClassItems)
                                    @foreach ($grpClassItems as $ctr => $item)
                                        @if (is_int($ctr))
                                <th class="align-top" width="250px">
                                    <small class="font-weight-bold">
                                        <span class="red-text">* </span> {{ $item->allotment_name }}
                                    </small>
                                </th>
                                        @else
                                            @foreach ($item as $itm)
                                <th class="align-top" width="250px">
                                    <small class="font-weight-bold">
                                        <span class="red-text">* </span> {{ explode('::', $itm->allotment_name)[1] }}
                                    </small>
                                </th>
                                            @endforeach
                                        @endif
                                    @endforeach
                                @endforeach

                                <th class="align-top" width="5px"></th>
                                <th width="1px"></th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($approvedBudgets as $approvedCtr => $approvedBud)
                            <tr>
                                <td align="right" colspan="4" class="red-text font-weight-bold">
                                    {{ $approvedBud->label }}
                                </td>
                                <td align="center" class="red-text font-weight-bold">
                                    {{ number_format($approvedBud->total, 2) }}
                                </td>

                                @foreach ($ledgerItems as $grpClassItems)
                                    @foreach ($grpClassItems as $ctr => $item)
                                        @if ($approvedCtr == 0)
                                            @if (is_int($ctr))
                                <td align="center" class="red-text font-weight-bold">
                                    <div class="md-form form-sm my-0">
                                        <input type="number" placeholder=" Value..."
                                                class="form-control required form-control-sm py-1"
                                                value="{{ $item->allotment_cost }}">
                                    </div>
                                </td>
                                            @else
                                                @foreach ($item as $itm)
                                <td align="center" class="red-text font-weight-bold">
                                    <div class="md-form form-sm my-0">
                                        <input type="number" placeholder=" Value..."
                                                class="form-control required form-control-sm py-1"
                                                value="{{ $itm->allotment_cost }}">
                                    </div>
                                </td>
                                                @endforeach
                                            @endif
                                        @else
                                            @php $realignOrderKey = "realignment_$approvedCtr"; @endphp
                                {{--
                                            @if (is_int($ctr))
                                <td align="center" class="red-text font-weight-bold">
                                    <div class="md-form form-sm my-0">
                                        <input type="number" placeholder=" Value..."
                                                class="form-control required form-control-sm py-1"
                                                value="{{ $item->{$realignOrderKey}->allotment_cost }}">
                                    </div>
                                </td>
                                            @else
                                                @foreach ($item as $itm)
                                <td align="center" class="red-text font-weight-bold">
                                    <div class="md-form form-sm my-0">
                                        <input type="number" placeholder=" Value..."
                                                class="form-control required form-control-sm py-1"
                                                value="{{ $itm->{$realignOrderKey}->allotment_cost }}">
                                    </div>
                                </td>
                                                @endforeach
                                            @endif
                                --}}
                                        @endif
                                    @endforeach
                                @endforeach

                                <td colspan="2"></td>
                            </tr>
                            @endforeach
                        </tbody>

                        <tbody id="item-row-container" class="sortable">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</form>
