<table class="table table-sm table-hover table-bordered m-0">
    <thead>
        <tr>
            <th class="text-center" rowspan="2" width="7%">
                <small>
                    Date Received
                </small>
            </th>
            <th class="text-center" rowspan="2" width="7%">
                <small>
                    Date Obligated
                </small>
            </th>
            <th class="text-center" rowspan="2" width="7%">
                <small>
                    Date Released
                </small>
            </th>
            <th class="text-center" colspan="3" width="21%">
                <small>
                    Reference
                </small>
            </th>
            <th class="text-center" rowspan="2" width="8%">
                <small>
                    UACS Object Code/Expenditure
                </small>
            </th>
            <th class="text-center" rowspan="2" width="7%">
                <small>
                    Allotments
                </small>
            </th>
            <th class="text-center" rowspan="2" width="7%">
                <small>
                    Obligations
                </small>
            </th>
            <th class="text-center" rowspan="2" width="7%">
                <small>
                    Unobligated Allotments
                </small>
            </th>
            <th class="text-center" rowspan="2" width="7%">
                <small>
                    Disbursements
                </small>
            </th>
            <th class="text-center" colspan="2" width="14%">
                <small>
                    Unpaid Obligations
                </small>
            </th>
            <th width="10%"></th>
            <th width="2%"></th>
        </tr>
        <tr>
            <th class="text-center" width="7%">
                <small>
                    Payee
                </small>
            </th>
            <th class="text-center" width="14%">
                <small>
                    Particulars
                </small>
            </th>
            <th class="text-center" width="7%">
                <small>
                    Serial Number
                </small>
            </th>
            <th class="text-center" width="7%">
                <small>
                    Due and Demandable
                </small>
            </th>
            <th class="text-center" width="7%">
                <small>
                    Not Yet Due and Demandable
                </small>
            </th>
            <th class="text-center" width="10%">
                <small>
                    Is Excluded?
                </small>
            </th>
            <th width="2%"></th>
        </tr>
    </thead>

    @if (count($vouchers) > 0)
    <tbody id="item-row-container" class="sortable">
        @foreach ($vouchers as $itemCtr => $item)
        <tr class="item-row">
            <td>
                <div class="md-form form-sm my-0">
                    @php
                        $_dateReceived = strtotime($item->log_date_received);
                        $dateReceived = date('Y-m-d', $_dateReceived);
                    @endphp
                    <input type="date" name="date_received[]" value="{{ $dateReceived }}"
                           class="form-control required form-control-sm date-received text-center">
                </div>
            </td>
            <td>
                <div class="md-form form-sm my-0">
                    @php
                        $_dateObligated = strtotime($item->date_obligated);
                        $dateObligated = date('Y-m-d', $_dateObligated);
                    @endphp
                    <input type="date" name="date_obligated[]" value="{{ $dateObligated }}"
                           class="form-control required form-control-sm date-obligated">
                </div>
            </td>
            <td>
                <div class="md-form form-sm my-0">
                    <input type="date" name="date_released[]"
                           class="form-control required form-control-sm date-released text-center">
                </div>
            </td>
            <td>
                <div class="md-form my-0">
                    <select class="mdb-select required payee-tokenizer payee"
                            name="payee[]">
                        @foreach ($employees as $emp)
                            @if ($emp->id == $item->payee)
                        <option value="{{$emp->id}}" selected>{{$emp->firstname}} {{$emp->lastname}}</option>
                                @php break @endphp
                            @endif
                        @endforeach
                        @foreach ($suppliers as $bid)
                            @if ($bid->id == $item->payee)
                        <option value="{{$bid->id}}" selected>{{$bid->company_name}}</option>
                                @php break @endphp
                            @endif
                        @endforeach
                        @foreach ($customPayees as $pay)
                            @if ($pay->id == $item->payee)
                        <option value="{{$pay->id}}" selected>
                            {{$pay->payee_name}} (Manually Added)
                        </option>
                                @php break @endphp
                            @endif
                        @endforeach
                    </select>
                </div>
            </td>
            <td>
                <div class="md-form form-sm my-0">
                    <textarea name="particulars[]" placeholder="..."
                              class="md-textarea required form-control-sm w-100 py-1 particulars"
                    >{{ $item->particulars }}</textarea>
                </div>
            </td>
            <td>
                <div class="md-form form-sm my-0">
                    <input type="text" placeholder="..." name="serial_number[]" value="{{ $item->serial_no }}"
                           class="form-control required form-control-sm serial-number">
                    <input type="hidden" name="ors_id[]" class="ors-id" value="{{ $item->ors_id }}">
                </div>
            </td>
            <td>
                <div class="md-form my-0">
                    <select class="mdb-select uacs-object-tokenizer uacs-object" multiple="multiple"
                            name="uacs_object_no[{{ $itemCtr }}][]">
                        @foreach ($uacsObjects as $object)
                            @if (in_array($object->id, unserialize($item->uacs_object)))
                        <option value="{{ $object->id }}" selected>{{ $object->uacs_code }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </td>
            <td>
                <div class="md-form form-sm my-0">
                    <input type="number" placeholder="..." name="allotments[]" id="allotment-{{ $itemCtr }}"
                           class="form-control required form-control-sm allotments"
                           onkeyup="$(this).solveUnobligated('{{ $itemCtr }}')"
                           onchange="$(this).solveUnobligated('{{ $itemCtr }}')">
                </div>
            </td>
            <td>
                <div class="md-form form-sm my-0">
                    <input type="number" placeholder="..." name="obligations[]" value="{{ $item->obligation }}"
                           class="form-control required form-control-sm obligations" id="obligation-{{ $itemCtr }}"
                           onkeyup="$(this).solveUnobligated('{{ $itemCtr }}')"
                           onchange="$(this).solveUnobligated('{{ $itemCtr }}')">
                </div>
            </td>
            <td>
                <div class="md-form form-sm my-0">
                    <input type="number" placeholder="..." name="unobligated[]" id="unobligated-{{ $itemCtr }}"
                           class="form-control required form-control-sm unobligated"
                           onkeyup="$(this).solveUnobligated('{{ $itemCtr }}')"
                           onchange="$(this).solveUnobligated('{{ $itemCtr }}')">
                </div>
            </td>
            <td>
                <div class="md-form form-sm my-0">
                    <input type="number" placeholder="..." name="disbursements[]"
                           value="{{ $item->disbursement ? $item->disbursement : 0 }}"
                           class="form-control required form-control-sm disbursements" id="disbursement-{{ $itemCtr }}">
                </div>
            </td>
            <td>
                <div class="md-form form-sm my-0">
                    <input type="number" placeholder="..." name="due_demandable[]"
                           class="form-control required form-control-sm due-demandable"
                           id="due-demandable-{{ $itemCtr }}"
                           onclick="$(this).solveDueDemandable('{{ $itemCtr }}')">
                </div>
            </td>
            <td>
                <div class="md-form form-sm my-0">
                    <input type="number" placeholder="..." name="not_due_demandable[]"
                           class="form-control required form-control-sm not-due-demandable"
                           id="not-due-demandable-{{ $itemCtr }}"
                           onclick="$(this).solveNotYetDueDemandable('{{ $itemCtr }}')">
                </div>
            </td>
            <th class="align-middle text-center" scope="row">
                <input class="form-check-input is-excluded" type="checkbox" value="1" id="check-{{ $itemCtr }}"
                       onclick="$(this).highlightExcluded($(this));">
                <label class="form-check-label" for="check-{{ $itemCtr }}" class="label-table"></label>
            </th>
            <td class="align-middle">
                <a href="#" class="grey-text">
                    <i class="fas fa-ellipsis-v"></i>
                </a>
            </td>
        </tr>
        @endforeach
    </tbody>
    @else
    <tbody>
        <tr>
            <td id="item-row-empty" class="py-3 red-text pl-4" colspan="14">
                <h5>
                    <i class="fas fa-times-circle"></i> <em>No voucher is obligated nor created.</em>
                </h5>
            </td>
        </tr>
    </tbody>
    @endif
</table>
