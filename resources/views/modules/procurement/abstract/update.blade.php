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

            @if ($canSetModeProc)
            <div class="md-form">
                <select class="mdb-select crud-select md-form required" searchable="Search here.."
                        id="mode_procurement">
                    <option value="0" disabled selected>Choose a mode of procurement</option>

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
            @endif

        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                @if (count($abstractItems) > 0)
                    @foreach ($abstractItems as $grpCtr => $abstract)
                <table class="table table-bordered table-segment-group">
                    <tr style="vertical-align: middle;">
                        <th class="blue lighten-5 py-0">
                            <div class="md-form grp-group">
                                <input type="hidden" class="grp_no" name="group_no[{{ $grpCtr }}]"
                                       value="{{ $abstract->group_no }}">
                                <input type="hidden" class="grp_key" name="group_key[{{ $grpCtr }}]" value="{{ $grpCtr }}">
                                <select class="sel-bidder-count mdb-select crud-select md-form"
                                        searchable="Search here.." id="bidder_count_{{ $grpCtr }}"
                                        name="bidder_count[{{ $grpCtr }}]">
                                    <option value="0" disabled selected>Choose the number of supplier</option>

                                    @for ($countSupplier = 0; $countSupplier <= 6; $countSupplier++)
                                        <option {{ $countSupplier == $abstract->bidder_count ? 'selected' : '' }}
                                                value="{{ $countSupplier }}">
                                            Number of Supplier: {{ $countSupplier }}
                                        </option>
                                    @endfor
                                </select>
                                <label class="mdb-main-label" for="bidder_count_{{ $grpCtr }}">
                                    <i class="fas fa-sitemap"></i>
                                    Group No: {{ $abstract->group_no }} <span class="red-text">*</span>
                                </label>
                            </div>
                        </th>
                    </tr>
                    <tr style="vertical-align: middle;">
                        <td class="p-0">
                            <div id="container_{{ $grpCtr + 1 }}" class="item-segment">
                                @if ($abstract->bidder_count > 0)
                                    @if (isset($abstract->pr_item_count) && $abstract->pr_item_count)
                                        @php
                                            $currentFirstItemNo = 1;
                                            $currentLastItemNo = 0;
                                            $counter = 1;
                                            $totalItemCount = 0;
                                            $pages = [];
                                        @endphp

                                <ul class="nav nav-tabs mdb-color lighten-5" role="tablist">
                                        @if ($abstract->pr_item_count < 10)
                                    <li class="nav-item">
                                        <a class="nav-link active font-weight-bold" id="tab-1-{{ $abstract->pr_item_count }}"
                                           data-toggle="tab"
                                           href="#page-1-{{ $abstract->pr_item_count }}"
                                           role="tab" aria-controls="home"
                                           aria-selected="true">
                                            #{{ $currentFirstItemNo }} to #{{ $abstract->pr_item_count }}
                                        </a>
                                    </li>
                                            @php
                                                $pages[] = (object) [
                                                    'first' => 1,
                                                    'last' => $abstract->pr_item_count
                                                ]
                                            @endphp
                                        @elseif ($abstract->pr_item_count >= 10  && $totalItemCount == 0)
                                            @php $totalItemCount = $abstract->pr_item_count @endphp
                                        @endif

                                        @if ($abstract->pr_item_count >= 10)
                                            @for ($ctr = 1; $ctr <= $abstract->pr_item_count; $ctr++)
                                                @if ($counter == 1)
                                                    @php $currentFirstItemNo = $ctr; @endphp
                                                @endif

                                                @if ($totalItemCount >= 10 && $counter == 10)
                                                    @php
                                                        $currentLastItemNo = $currentFirstItemNo + 9;
                                                        $pages[] = (object) [
                                                            'first' => $currentFirstItemNo,
                                                            'last' => $currentLastItemNo
                                                        ];
                                                        $totalItemCount -= $counter;
                                                    @endphp
                                    <li class="nav-item">
                                        <a class="nav-link font-weight-bold {{ count($pages) == 1 ? 'active' : '' }}"
                                           id="tab-{{ $currentFirstItemNo }}-{{ $currentLastItemNo }}"
                                           data-toggle="tab"
                                           href="#page-{{ $currentFirstItemNo }}-{{ $currentLastItemNo }}"
                                           role="tab"
                                           aria-selected="true">
                                            #{{ $currentFirstItemNo }} to #{{ $currentLastItemNo }}
                                        </a>
                                    </li>
                                                    @php $counter = 0; @endphp
                                                @elseif ($totalItemCount < 10)
                                                    @php
                                                        $currentLastItemNo = $abstract->pr_item_count;
                                                        $pages[] = (object) [
                                                            'first' => $currentFirstItemNo,
                                                            'last' => $currentLastItemNo
                                                        ];
                                                    @endphp
                                    <li class="nav-item">
                                        <a class="nav-link font-weight-bold" id="tab-{{ $currentFirstItemNo }}-{{ $currentLastItemNo }}"
                                           data-toggle="tab"
                                           href="#page-{{ $currentFirstItemNo }}-{{ $currentLastItemNo }}"
                                           role="tab"
                                           aria-selected="true">
                                            #{{ $currentFirstItemNo }} to #{{ $currentLastItemNo }}
                                        </a>
                                    </li>
                                                    @php break; @endphp
                                                @endif

                                                @php
                                                    $counter++;
                                                @endphp
                                            @endfor
                                        @endif
                                </ul>
                                    @endif

                                <table class="table table-bordered z-depth-1 wow animated fadeIn p-0 m-0">
                                    <thead class="header-group">
                                        <tr>
                                            @if (!empty($abstract->suppliers) && isset($abstract->suppliers))
                                                @foreach ($abstract->suppliers as $key => $supplier)
                                            <th width="320px">
                                                <div class="form-group">
                                                    <select class="mdb-select md-form sel-supplier sel-supplier-{{ $grpCtr }} required"
                                                            id="sel-bidder-count-{{ $grpCtr }}-{{ $key }}"
                                                            onchange="$(this).setSupplierHeaderName($(this), '.bid-head-{{ $key }}',
                                                                                                    $(this).find(':selected').text());"
                                                            name="selected_supplier[{{ $grpCtr }}][{{ $key }}]"
                                                            searchable="Search here..">
                                                        <option value="" disabled selected>Choose a supplier</option>
                                                        @if (!empty($suppliers))
                                                            @foreach ($suppliers as $supplierCounter => $bid)
                                                        <option value="{{ $bid->id }}" {{ $bid->id == $supplier->id ? 'selected' : '' }}>
                                                            Supplier #{{ $key + 1 }} : {{ $bid->company_name }}
                                                        </option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                            </th>
                                                @endforeach
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td colspan="{{ $abstract->bidder_count ? $abstract->bidder_count : 1 }}" class="p-0 m-0">
                                                <div class="tab-content p-0">
                                                    @foreach ($pages as $pageCtr => $page)
                                                    <div class="tab-pane fade {{ $pageCtr == 0 ? 'show active' : '' }}"
                                                         id="page-{{ $page->first }}-{{ $page->last }}" role="tabpanel">
                                                        <table class="table p-0 m-0">
                                                            <thead>
                                                                <tr>
                                                                    <th class="text-center font-weight-bold" width="50px">#</th>
                                                                    <th class="text-center font-weight-bold" width="300px">Item Description</th>
                                                                    <th class="text-center font-weight-bold" width="100px">Unit</th>
                                                                    <th class="text-center font-weight-bold" width="100px">ABC (UNIT)</th>
                                                                    @if (!empty($abstract->suppliers) && isset($abstract->suppliers))
                                                                        @foreach ($abstract->suppliers as $key => $supplier)
                                                                    <th class="text-center font-weight-bold" width="320px">
                                                                            @foreach ($suppliers as $supplierCounter => $bid)
                                                                                @if ($bid->id == $supplier->id)
                                                                        <span class="bid-head-{{ $key }}">
                                                                            Supplier #{{ $key + 1 }} : {{ $bid->company_name }}
                                                                        </span>
                                                                                @endif
                                                                            @endforeach
                                                                    </th>
                                                                        @endforeach
                                                                    <th class="text-center font-weight-bold" width="320px">Awarded To</th>
                                                                    @endif
                                                                </tr>
                                                            </thead>

                                                            <tbody class="table-data">
                                                                @if (!empty($abstract->pr_items) && isset($abstract->pr_items))
                                                                    @foreach ($abstract->pr_items as $listCtr => $item)
                                                                        @if ($page->last >= ($listCtr + 1) && $page->first <= ($listCtr + 1))
                                                                <tr>
                                                                    <td align="center">
                                                                        {{ $listCtr + 1 }}
                                                                        <input type="hidden" class="item-id"
                                                                            value="{{ $item->item_id }}">
                                                                    </td>
                                                                    <td>
                                                                        {{ (strlen($item->item_description) > 150) ?
                                                                            substr($item->item_description, 0, 150).'...' : $item->item_description  }}
                                                                        ({{ $item->quantity }} {{ $item->quantity > 1 ? 'pcs.' : 'pc.' }})
                                                                    </td>
                                                                    <td align="center">{{ $item->unit_name }}</td>
                                                                    <td align="center">{{ $item->est_unit_cost }}</td>
                                                                            @if ($item->abstract_item_count > 0)
                                                                                @foreach($item->abstract_items as $absItemCtr => $absItem)
                                                                    <td width="320px">
                                                                        <div class="md-form form-sm">
                                                                            <input class="quantity" type="hidden" value="{{ $item->quantity }}">
                                                                            <input class="abstract-item-id" type="hidden" value="{{ $absItem->id }}">
                                                                            <input type="number" class="form-control form-control-sm unit-cost required"
                                                                                   id="unit_cost-{{ $abstract->group_no }}-{{ $listCtr }}-{{ $absItemCtr }}" min="0"
                                                                                   value="{{ $absItem->unit_cost }}">
                                                                            <label for="unit_cost-{{ $abstract->group_no }}-{{ $listCtr }}-{{ $absItemCtr }}"
                                                                                   class="active">
                                                                                Unit Cost <span class="red-text">*</span>
                                                                            </label>
                                                                        </div>
                                                                        <div class="md-form form-sm">
                                                                            <input type="number" class="form-control form-control-sm total-cost required"
                                                                                   id="total_cost-{{ $abstract->group_no }}-{{ $listCtr }}-{{ $absItemCtr }}" min="0"
                                                                                   value="{{ $absItem->total_cost }}">
                                                                            <label for="total_cost-{{ $abstract->group_no }}-{{ $listCtr }}-{{ $absItemCtr }}"
                                                                                   class="active">
                                                                                Total Cost <span class="red-text">*</span>
                                                                            </label>
                                                                        </div>
                                                                        <div class="md-form form-sm">
                                                                            <textarea class="md-textarea form-control form-control-sm specification"
                                                                                    id="specification-{{ $abstract->group_no }}-{{ $listCtr }}-{{ $absItemCtr }}"
                                                                                    style="resize: none;" rows="3">{{ $absItem->specification }}</textarea>
                                                                            <label for="specification-{{ $abstract->group_no }}-{{ $listCtr }}-{{ $absItemCtr }}"
                                                                                   class="active">
                                                                                Specifications
                                                                            </label>
                                                                        </div>
                                                                        <div class="md-form form-sm">
                                                                            <textarea class="md-textarea form-control form-control-sm remarks"
                                                                                    id="remarks-{{ $abstract->group_no }}-{{ $listCtr }}-{{ $absItemCtr }}"
                                                                                    style="resize: none;" rows="3">{{ $absItem->remarks }}</textarea>
                                                                            <label for="remarks-{{ $abstract->group_no }}-{{ $listCtr }}-{{ $absItemCtr }}"
                                                                                   class="active">
                                                                                Remarks
                                                                            </label>
                                                                        </div>
                                                                    </td>
                                                                                @endforeach
                                                                            @endif
                                                                            @if ($abstract->bidder_count > 0)
                                                                    <td>
                                                                        <div class="form-group">
                                                                            <label class="mdb-main-label">
                                                                                Awarded To
                                                                            </label>
                                                                            <select class="browser-default custom-select awarded-to" searchable="Search here..">
                                                                                <option value="" disabled selected>Choose an awardee</option>
                                                                                <option value="">-- No awardee --</option>

                                                                                @if (!empty($abstract->suppliers))
                                                                                    @foreach ($abstract->suppliers as $bid)
                                                                                <option value="{{ $bid->id }}" {{ $bid->id == $item->awarded_to ? 'selected' : '' }}>
                                                                                    {{ $bid->company_name }}
                                                                                </option>
                                                                                    @endforeach
                                                                                @endif
                                                                            </select>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label class="mdb-main-label">
                                                                                Document Type <span class="red-text">*</span>
                                                                            </label>
                                                                            <select class="browser-default custom-select document-type"
                                                                                    searchable="Search here..">
                                                                                <option value="po" {{ $item->document_type == 'po' ? 'selected' : '' }}>
                                                                                    Purchase Order (PO)
                                                                                </option>
                                                                                <option value="jo" {{ $item->document_type == 'jo' ? 'selected' : '' }}>
                                                                                    Job Order (JO)
                                                                                </option>
                                                                            </select>
                                                                        </div>
                                                                        <div class="md-form form-sm">
                                                                            <textarea class="md-textarea form-control form-control-sm awarded-remarks"
                                                                                    id="awarded_remarks-{{ $abstract->group_no }}-{{ $listCtr }}"
                                                                                    style="resize: none;" rows="3">{{ $item->awarded_remarks }}</textarea>
                                                                            <label for="awarded_remarks-{{ $abstract->group_no }}-{{ $listCtr }}"
                                                                                   class="active">
                                                                                Remarks
                                                                            </label>
                                                                        </div>
                                                                    </td>
                                                                            @endif
                                                                </tr>
                                                                        @endif
                                                                    @endforeach
                                                                @endif
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    @endforeach

                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                @else
                                <table class="table table-bordered m-0 mb-0">
                                    <tr class="header-group">
                                        <th class="text-center font-weight-bold" width="3%">
                                            #
                                        </th>
                                        <th class="text-center font-weight-bold" width="71%">
                                            Item Description
                                        </th>
                                        <th class="text-center font-weight-bold" width="10%">
                                            Unit
                                        </th>
                                        <th class="text-center font-weight-bold" width="16%">
                                            ABC (UNIT)
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
                                        <td>
                                            {{ (strlen($item->item_description) > 150) ?
                                                substr($item->item_description, 0, 150).'...' : $item->item_description  }}
                                            ({{ $item->quantity }} {{ $item->quantity > 1 ? 'pcs.' : 'pc.' }})
                                        </td>
                                        <td align="center">{{ $item->unit_name }}</td>
                                        <td align="center">{{ $item->est_unit_cost }}</td>
                                    </tr>
                                        @endforeach
                                    @endif
                                </table>
                                @endif
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
                    <option value="0" disabled selected>Choose a chairperson</option>

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
                    <option value="0" disabled selected>Choose a vice chairperson</option>

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
                    <option value="0" disabled selected>Choose a first member</option>

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
                    <option value="0" disabled selected>Choose a second member</option>
                    <option value="">-- None --</option>

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
                    <option value="0" disabled selected>Choose a third member</option>
                    <option value="">-- None --</option>

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
                    <option value="0" disabled selected>Choose an end user </option>

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
