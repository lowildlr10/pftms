<form id="form-store-item" class="wow animated fadeIn">
    <div class="row">
        <div class="col-md-6">
            <div class="md-form">
                <input type="date" id="date_abstract" class="form-control required">
                <label for="date_abstract">
                    Abstract Date <span class="red-text">*</span>
                </label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="md-form">
                <select class="mdb-select crud-select md-form required" searchable="Search here.."
                        id="mode_procurement">
                    <option value="" disabled selected>Choose a mode of procurement</option>

                    @if (!empty($procurementModes))
                        @foreach ($procurementModes as $mode)
                    <option value="{{ $mode->id }}">{{ $mode->mode_name }}</option>
                        @endforeach
                    @endif
                </select>
                <label class="mdb-main-label">
                    Mode of Procurement <span class="red-text">*</span>
                </label>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                @if (count($abstractItems) > 0)
                    @foreach ($abstractItems as $grpCtr => $abstract)
                <table class="table table-bordered table-segment-group">
                    <tr style="vertical-align: middle;">
                        <th class="mdb-color darken-2 py-0">
                            <div class="md-form">
                                <input type="hidden" class="grp_no" name="group_no[{{ $grpCtr }}]"
                                       value="{{ $abstract->group_no }}">
                                <input type="hidden" class="grp_key" name="group_key[{{ $grpCtr }}]" value="{{ $grpCtr }}">
                                <select class="sel-bidder-count mdb-select crud-select md-form required"
                                        searchable="Search here.." id="bidder_count_{{ $grpCtr }}"
                                        name="bidder_count[{{ $grpCtr }}]">
                                    <option value="0" disabled selected>Choose a number of supplier</option>

                                    @for ($countSupplier = 1; $countSupplier <= 5; $countSupplier++)
                                        <option value="{{ $countSupplier }}">
                                            Number of Supplier: {{ $countSupplier }}
                                        </option>
                                    @endfor
                                </select>
                                <label for="bidder_count_{{ $grpCtr }}">
                                    <i class="fas fa-sitemap"></i>
                                    Group No: {{ $abstract->group_no }}  <span class="red-text">*</span>
                                </label>
                            </div>
                        </th>
                    </tr>
                    <tr style="vertical-align: middle;">
                        <td class="p-0">
                            <div id="container_{{ $grpCtr + 1 }}">
                                <table class="table table-bordered m-0">
                                    <tr class="header-group">
                                        <th style="text-align:center;" width="3%">
                                            <strong>#</strong>
                                        </th>
                                        <th style="text-align:center;" width="71%">
                                            <strong>Item Description</strong>
                                        </th>
                                        <th style="text-align:center;" width="10%">
                                            <strong>Unit</strong>
                                        </th>
                                        <th style="text-align:center;" width="16%">
                                            <strong>ABC (UNIT)</strong>
                                        </th>
                                    </tr>

                                    @if (!empty($abstract->pr_items) && isset($abstract->pr_items))
                                        @foreach ($abstract->pr_items as $listCtr => $item)
                                    <tr>
                                        <td align="center">
                                            {{ $listCtr + 1 }}
                                            <input type="hidden" class="item-id"
                                                   name="item_id[{{ $grpCtr }}][{{ $listCtr }}]"
                                                   value="{{ $item->item_id }}" class="item_id">
                                        </td>
                                        <td>{{ substr($item->item_description, 0, 300) }}...</td>
                                        <td align="center">{{ $item->unit_name }}</td>
                                        <td align="center">{{ $item->est_unit_cost }}</td>
                                    </tr>
                                        @endforeach
                                    @endif
                                </table>
                            </div>
                        </td>
                    </tr>
                </table>
                    @endforeach
                @endif

            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-md-2">
            <div class="md-form">
                <select class="sig-abstracts mdb-select crud-select md-form required"
                        searchable="Search here.." id="sig_chairperson">
                    <option value="" disabled selected>Choose a chairperson</option>

                    @if (count($signatories) > 0)
                        @foreach ($signatories as $sig)
                            @if ($sig->module->abs->chairperson)
                    <option value="{{ $sig->id }}">
                        {!! $sig->name !!} [{!! $sig->module->abs->designation !!}]
                    </option>
                            @endif
                        @endforeach
                    @endif
                </select>
                <label class="mdb-main-label">
                    Chairperson <span class="red-text">*</span>
                </label>
            </div>
        </div>

        <div class="col-md-2">
            <div class="md-form">
                <select class="sig-abstracts mdb-select crud-select md-form required"
                        searchable="Search here.." id="sig_vice_chairperson">
                    <option value="" disabled selected>Choose a vice chairperson</option>

                    @if (count($signatories) > 0)
                        @foreach ($signatories as $sig)
                            @if ($sig->module->abs->vice_chair)
                    <option value="{{ $sig->id }}">
                        {!! $sig->name !!} [{!! $sig->module->abs->designation !!}]
                    </option>
                            @endif
                        @endforeach
                    @endif
                </select>
                <label class="mdb-main-label">
                    Vice Chairperson <span class="red-text">*</span>
                </label>
            </div>
        </div>

        <div class="col-md-2">
            <div class="md-form">
                <select class="sig-abstracts mdb-select crud-select md-form required"
                        searchable="Search here.." id="sig_first_member">
                    <option value="" disabled selected>Choose a first member</option>

                    @if (count($signatories) > 0)
                        @foreach ($signatories as $sig)
                            @if ($sig->module->abs->member)
                    <option value="{{ $sig->id }}">
                        {!! $sig->name !!} [{!! $sig->module->abs->designation !!}]
                    </option>
                            @endif
                        @endforeach
                    @endif
                </select>
                <label class="mdb-main-label">
                    First Member <span class="red-text">*</span>
                </label>
            </div>
        </div>

        <div class="col-md-2">
            <div class="md-form">
                <select class="sig-abstracts mdb-select crud-select md-form "
                        searchable="Search here.." id="sig_second_member">
                    <option value="" disabled selected>Choose a second member</option>

                    @if (count($signatories) > 0)
                        @foreach ($signatories as $sig)
                            @if ($sig->module->abs->member)
                    <option value="{{ $sig->id }}">
                        {!! $sig->name !!} [{!! $sig->module->abs->designation !!}]
                    </option>
                            @endif
                        @endforeach
                    @endif
                </select>
                <label class="mdb-main-label">
                    Second Member
                </label>
            </div>
        </div>

        <div class="col-md-2">
            <div class="md-form">
                <select class="sig-abstracts mdb-select crud-select md-form"
                        searchable="Search here.." id="sig_third_member">
                    <option value="" disabled selected>Choose a third member</option>

                    @if (count($signatories) > 0)
                        @foreach ($signatories as $sig)
                            @if ($sig->module->abs->member)
                    <option value="{{ $sig->id }}">
                        {!! $sig->name !!} [{!! $sig->module->abs->designation !!}]
                    </option>
                            @endif
                        @endforeach
                    @endif
                </select>
                <label class="mdb-main-label">
                    Third Member
                </label>
            </div>
        </div>

        <div class="col-md-2">
            <div class="md-form">
                <select class="sig-abstracts mdb-select crud-select md-form required"
                        searchable="Search here.." id="sig_end_user">
                    <option value="" disabled selected>Choose an end user </option>

                    @if (count($users) > 0)
                        @foreach ($users as $emp)
                    <option value="{{ $emp->id }}">
                        {!! $emp->firstname !!} {!! $emp->lastname !!} [{!! $emp->position !!}]
                    </option>
                        @endforeach
                    @endif
                </select>
                <label class="mdb-main-label">
                    End User <span class="red-text">*</span>
                </label>
            </div>
        </div>
    </div>
</form>

<form id="form-store" method="POST" action="{{ route('abstract-store', ['id' => $id]) }}">
    @csrf

    <input type="hidden" id="pr_no" name="pr_no" value="">
    <input type="hidden" id="pr_id" name="pr_id" value="">

    <input type="hidden" name="date_abstract">
    <input type="hidden" name="mode_procurement">
    <input type="hidden" name="sig_chairperson">
    <input type="hidden" name="sig_vice_chairperson">
    <input type="hidden" name="sig_first_member">
    <input type="hidden" name="sig_second_member">
    <input type="hidden" name="sig_third_member">
    <input type="hidden" name="sig_end_user">
</form>
