<form id="form-update-item" class="wow animated fadeIn">
    <div class="row">
        <div class="col-md-6">
            <div class="md-form">
                <input type="date" id="date_abstract" class="form-control required"
                       value="{{ $abstractDate }}">
                <label for="date_abstract" class="{{ !empty($abstractDate) ? 'active' : '' }}">
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
                    <option value="{{ $mode->id }}" {{ $procurementMode == $mode->id ? 'selected' : '' }}>
                        {{ $mode->mode_name }}
                    </option>
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

                <table class="table table-bordered table-segment-group" style="border: 3px #3f5371 solid;">
                    <tr style="vertical-align: middle;">
                        <th style="background: #3f5371;">
                            <div class="form-group">
                                <div class="input-group">
                                    <span class="input-group-addon" style="background: #3f5371; color: #fff;">
                                        <i class="fas fa-sitemap"></i>
                                        <strong>Group No: {{ $abstract->group_no }}</strong>
                                    </span>
                                    <input type="hidden" class="grp_no" name="group_no[{{ $grpCtr }}]"
                                           value="{{ $abstract->group_no }}">
                                    <input type="hidden" class="grp_key" name="group_key[{{ $grpCtr }}]" value="{{ $grpCtr }}">
                                    <select name="bidder_count[{{ $grpCtr }}]" class="browser-default custom-select sel-bidder-count ml-3">
                                        <option value="0"> -- Select number of supplier. -- </option>

                                        @for ($countSupplier = 1; $countSupplier <= 5; $countSupplier++)

                                            @if ($countSupplier == $abstract->bidder_count)

                                        <option value="{{ $countSupplier }}" selected="selected">
                                            Number of Supplier: {{ $countSupplier }}
                                        </option>

                                            @else

                                        <option value="{{ $countSupplier }}">
                                            Number of Supplier: {{ $countSupplier }}
                                        </option>

                                            @endif

                                        @endfor

                                    </select>
                                </div>
                            </div>
                        </th>
                    </tr>
                    <tr style="vertical-align: middle;">
                        <td>
                            <div id="container_{{ $grpCtr + 1 }}">
                                <table class="table table-bordered z-depth-2">
                                    <tr class="header-group">
                                        <th style="text-align:center;" width="50px">#</th>
                                        <th style="text-align:center;" width="300px">Item Description</th>
                                        <th style="text-align:center;" width="100px">Unit</th>
                                        <th style="text-align:center;" width="100px">ABC (UNIT)</th>

                                        @if (!empty($abstract->suppliers) && isset($abstract->suppliers))

                                            @foreach ($abstract->suppliers as $key => $supplier)

                                        <th style="text-align:center;" width="320px">
                                            <div class="form-group">
                                                <select class="browser-default custom-select sel-supplier" name="selected_supplier[{{ $grpCtr }}][{{ $key }}]">

                                                    @if (!empty($supplierList))

                                                        @foreach ($supplierList as $bid)

                                                            @if ($bid->id == $supplier->id)

                                                    <option value="{{ $bid->id }}" selected="selected">
                                                        {{ $bid->company_name }}
                                                    </option>

                                                            @else

                                                    <option value="{{ $bid->id }}">
                                                        {{ $bid->company_name }}
                                                    </option>

                                                            @endif

                                                        @endforeach

                                                    @endif

                                                </select>
                                            </div>
                                        </th>

                                            @endforeach

                                        @endif

                                        <th style="text-align:center;" width="320px">Awarded To</th>
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

                                            @if (!empty($item->abstract_items) && isset($item->abstract_items))

                                                @foreach ($item->abstract_items as $absCtr => $abs)

                                        <td width="320px">
                                            <input type="hidden" class="abstract-id" name="abstract_id[{{ $grpCtr }}][{{ $listCtr }}][{{ $absCtr }}]"
                                                   value="{{ $abs->abstract_id }}">
                                            <div class="form-group">
                                                <label>Unit Cost</label>
                                                <input class=".quantity" type="hidden" value="{{ $item->quantity }}">
                                                <input type="number" class="form-control unit-cost required"
                                                       name="unit_cost[{{ $grpCtr }}][{{ $listCtr }}][{{ $absCtr }}]"
                                                       value="{{ $abs->unit_cost }}" min="0">
                                            </div>
                                            <div class="form-group">
                                                <label>Total Cost</label>
                                                <input type="number" class="form-control total-cost required"
                                                       name="total_cost[{{ $grpCtr }}][{{ $listCtr }}][{{ $absCtr }}]"
                                                       value="{{ $abs->total_cost }}" min="0">
                                            </div>
                                            <div class="form-group">
                                                <label>Specification</label>
                                                <textarea class="form-control specification"
                                                name="specification[{{ $grpCtr }}][{{ $listCtr }}][{{ $absCtr }}]"
                                                style="resize: none;" rows="3">{{ $abs->specification }}</textarea>
                                            </div>
                                            <div class="form-group">
                                                <label>Remarks</label>
                                                <textarea class="form-control remarks"
                                                name="remarks[{{ $grpCtr }}][{{ $listCtr }}][{{ $absCtr }}]"
                                                style="resize: none;" rows="3">{{ $abs->remarks }}</textarea>
                                            </div>
                                        </td>

                                                @endforeach

                                            @endif

                                        <td>

                                            @if (!empty($abstract->pr_items) && isset($abstract->pr_items))

                                            <div class="form-group">
                                                <label>Select a Supplier</label>
                                                <select class="browser-default custom-select awarded-to"
                                                        name="awarded_to[{{ $grpCtr }}][{{ $listCtr }}]">
                                                    <option value="">-- No awardee --</option>

                                                    @if (!empty($abstract->suppliers))
                                                        @foreach ($abstract->suppliers as $key => $supplier)
                                                            @if (!empty($supplierList))
                                                                @foreach ($supplierList as $bid)
                                                                    @if ($bid->id == $supplier->id)
                                                                        @if ($bid->id == $item->awarded_to)

                                                    <option value="{{ $bid->id }}" selected="selected">
                                                        {{ $bid->company_name }}
                                                    </option>
                                                                        @else

                                                    <option value="{{ $bid->id }}">
                                                        {{ $bid->company_name }}
                                                    </option>

                                                                        @endif
                                                                    @endif
                                                                @endforeach
                                                            @endif
                                                        @endforeach
                                                    @endif

                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Document Type</label>
                                                <select class="browser-default custom-select document-type required"
                                                        name="document_type[{{ $grpCtr }}][{{ $listCtr }}]">
                                                    <option value="" selected="selected">-- Select a document --</option>
                                                    <option value="PO" <?php if ($item->document_type == "PO") {echo "selected";} ?>>
                                                        Purchase Order (PO)
                                                    </option>
                                                    <option value="JO" <?php if ($item->document_type == "JO") {echo "selected";} ?>>
                                                       Job Order (JO)
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Remarks</label>
                                                <textarea class="form-control awarded-remarks"
                                                name="awarded_remarks[{{ $grpCtr }}][{{ $listCtr }}]"
                                                style="resize: none;" rows="3">{{ $item->awarded_remarks }}</textarea>
                                            </div>

                                            @else

                                            <center><strong>N/A</strong></center>

                                            @endif

                                        </td>
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
                    <option value="{{ $sig->id }}" {{ $chairperson == $sig->id ? 'selected' : '' }}>
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
                    <option value="{{ $sig->id }}" {{ $viceChairperson == $sig->id ? 'selected' : '' }}>
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
                    <option value="{{ $sig->id }}" {{ $firstMember == $sig->id ? 'selected' : '' }}>
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
                    <option value="{{ $sig->id }}" {{ $secondMember == $sig->id ? 'selected' : '' }}>
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
                    <option value="{{ $sig->id }}" {{ $thirdMember == $sig->id ? 'selected' : '' }}>
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
                    <option value="{{ $emp->id }}" {{ $endUser == $emp->id ? 'selected' : '' }}>
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

<form id="form-update" method="POST" action="{{ route('abstract-update', ['id' => $id]) }}">
    @csrf

    <input type="hidden" id="abstract_id" name="abstract_id" value="{{ $id }}">
    <input type="hidden" id="toggle" name="toggle" value="update">

    <input type="hidden" name="date_abstract">
    <input type="hidden" name="mode_procurement">
    <input type="hidden" name="sig_chairperson">
    <input type="hidden" name="sig_vice_chairperson">
    <input type="hidden" name="sig_first_member">
    <input type="hidden" name="sig_second_member">
    <input type="hidden" name="sig_third_member">
    <input type="hidden" name="sig_end_user">
</form>
