<form id="form-update" class="wow animated fadeIn" method="POST"
      action="{{ route('report-raod-update', ['id' => $id]) }}">
    @csrf

    <div class="card">
        <div class="card-body">
            <h4 class="text-center">
                REGISTRY OF ALLOTMENTS, OBLIGATIONS AND DISBURSEMENTS<br>
                PERSONNEL SERVICES/MAINTENANCE AND OTHER OPERATING EXPENSES
            </h4>
            <hr>
            <div class="row">
                <div class="offset-md-4 col-md-4">
                    <label for="period-ending" class="active">
                        <span class="red-text">* </span>
                        <b>For the Period Ending</b>
                    </label>
                    <div class="form-group">
                        <input type="month" id="period-ending" name="period_ending"
                               value="{{ $periodEnding }}" class="form-control required">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-5">
                    <div class="md-form form-sm">
                        <input type="text" id="entity-name" name="entity_name"
                               class="form-control required" value="{{ $entityName }}">
                        <label for="entity-name" class="active">
                            <span class="red-text">* </span>
                            <strong>Entity Name</strong>
                        </label>
                    </div>
                </div>
                <div class="col-md-5 offset-md-2">
                    <div class="md-form form-sm">
                        <select class="mdb-select crud-select sm-form required" searchable="Search here.."
                                name="mfo_pap[]" id="mfo-pap" multiple>
                            <option value="" disabled selected>Choose the MFO PAP</option>

                            @if (count($mfoPAPs) > 0)
                                @foreach ($mfoPAPs as $pap)
                            <option value="{{ $pap->id }}" {{ in_array($pap->id, $mfoPAP) ? 'selected' : '' }}>
                                {!! $pap->code !!} : {!! $pap->description !!}
                            </option>
                                @endforeach
                            @endif
                        </select>
                        <label for="mfo-pap" class="active">
                            <span class="red-text">* </span>
                            <b>MFO/PAP</b>
                        </label>
                    </div>
                    {{--
                    <div class="md-form form-sm">
                        <input type="text" id="mfo-pap" name="mfo_pap"
                               class="form-control required" value="{{ $mfoPAP }}">
                        <label for="mfo-pap" class="active">
                            <span class="red-text">* </span>
                            <strong>MFO/PAP</strong>
                        </label>
                    </div>
                    --}}
                </div>
            </div>
            <div class="row">
                <div class="col-md-5">
                    <div class="md-form form-sm">
                        <input type="text" id="fund-cluster" name="fund_cluster"
                               class="form-control required" value="{{ $fundCluster }}">
                        <label for="fund-cluster" class="active">
                            <span class="red-text">* </span>
                            <strong>Fund Cluster</strong>
                        </label>
                    </div>
                </div>
                <div class="col-md-5 offset-md-2">
                    <div class="md-form form-sm">
                        <input type="text" id="sheet-no" name="sheet_no"
                               class="form-control required" value="{{ $sheetNo }}">
                        <label for="sheet-no" class="active">
                            <span class="red-text">* </span>
                            <strong>Sheet No.</strong>
                        </label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-5">
                    <div class="md-form form-sm">
                        <input type="text" id="legal-basis" name="legal_basis"
                               class="form-control required" value="{{ $legalBasis }}">
                        <label for="legal-basis" class="active">
                            <span class="red-text">* </span>
                            <strong>Legal Basis</strong>
                        </label>
                    </div>
                </div>
                <div class="col-md-5 offset-md-2">
                    <em>Current /Cont Allotment</em>
                </div>
            </div>
            <div class="row">
                <div id="voucher-table-section" class="col-md-12 border px-0 table-responsive">
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

                        @if (count($regItems) > 0)
                        <tbody id="item-row-container" class="sortable">
                            @foreach ($regItems as $itemCtr => $item)
                            <tr class="item-row {{ $item->raod ? ($item->raod->is_excluded == 'y' ? 'red lighten-4' : '') : '' }}">
                                <td>
                                    <div class="md-form form-sm my-0">
                                        @php
                                            $_dateReceived = $item->raod ? strtotime($item->raod->date_received) :
                                                             strtotime($item->date_received);
                                            $dateReceived = date('Y-m-d', $_dateReceived);
                                        @endphp
                                        <input type="date" name="date_received[]" value="{{ $dateReceived }}"
                                               class="form-control required form-control-sm date-received text-center">
                                    </div>

                                    @if (empty($item->raod))
                                    <span class="badge badge-success mt-0">New</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="md-form form-sm my-0">
                                        @php
                                            $_dateObligated = strtotime($item->raod ? $item->raod->date_obligated :
                                                              $item->ors_date_obligated);
                                            $dateObligated = date('Y-m-d', $_dateObligated);
                                        @endphp
                                        <input type="date" name="date_obligated[]" value="{{ $dateObligated }}"
                                               class="form-control required form-control-sm date-obligated">
                                    </div>
                                </td>
                                <td>
                                    <div class="md-form form-sm my-0">
                                        @php
                                            $_dateReleased = $item->raod ? strtotime($item->raod->date_released) :
                                                             strtotime($item->ors_date_released);
                                            $dateReleased = date('Y-m-d', $_dateReleased);
                                        @endphp
                                        <input type="date" name="date_released[]" value="{{ $item->raod ? $dateReleased : NULL }}"
                                               class="form-control required form-control-sm date-released text-center">
                                    </div>
                                </td>
                                <td>
                                    <div class="md-form my-0">
                                        <select class="mdb-select required payee-tokenizer payee"
                                                name="payee[]">
                                            @foreach ($employees as $emp)
                                                @if ($emp->id == ($item->raod ? $item->raod->payee : $item->ors_payee))
                                            <option value="{{$emp->id}}" selected>
                                                {{$emp->firstname}} {{$emp->lastname}} (Registered Employee)
                                            </option>
                                                    @php break @endphp
                                                @endif
                                            @endforeach
                                            @foreach ($suppliers as $bid)
                                                @if ($bid->id == ($item->raod ? $item->raod->payee : $item->ors_payee))
                                            <option value="{{$bid->id}}" selected>
                                                {{$bid->company_name}} (Registered Supplier)
                                            </option>
                                                    @php break @endphp
                                                @endif
                                            @endforeach
                                            @foreach ($customPayees as $pay)
                                                @if ($pay->id == ($item->raod ? $item->raod->payee : $item->ors_payee))
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
                                        >{{ $item->raod ? $item->raod->particulars : $item->ors_particulars }}</textarea>
                                    </div>
                                </td>
                                <td>
                                    <div class="md-form form-sm my-0">
                                        <input type="text" placeholder="..." name="serial_number[]"
                                               value="{{ $item->raod ? $item->raod->serial_number : $item->ors_serial_number }}"
                                               class="form-control required form-control-sm serial-number">
                                        <input type="hidden" name="ors_id[]" class="ors-id" value="{{ $item->ors_id }}">
                                    </div>
                                </td>
                                <td>
                                    <div class="md-form my-0">
                                        <select class="mdb-select uacs-object-tokenizer uacs-object" multiple="multiple"
                                                name="uacs_object_no[{{ $itemCtr }}][]">
                                            @foreach ($uacsObjects as $object)
                                                @php
                                                    $uacs = $item->raod ? unserialize($item->raod->uacs_object_code) : [];
                                                    $orsUacs = unserialize($item->ors_uacs_object_code);
                                                    $objCode = $item->raod ? $uacs : $orsUacs;
                                                @endphp

                                                @if (in_array($object->id, $objCode))
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
                                               value="{{ $item->raod ? $item->raod->allotments : '' }}"
                                               onkeyup="$(this).solveUnobligated('{{ $itemCtr }}')"
                                               onchange="$(this).solveUnobligated('{{ $itemCtr }}')">
                                    </div>
                                </td>
                                <td>
                                    <div class="md-form form-sm my-0">
                                        <input type="number" placeholder="..." name="obligations[]"
                                               class="form-control required form-control-sm obligations"
                                               id="obligation-{{ $itemCtr }}"
                                               value="{{ $item->raod ? $item->raod->obligations : $item->ors_amount }}"
                                               onkeyup="$(this).solveUnobligated('{{ $itemCtr }}')"
                                               onchange="$(this).solveUnobligated('{{ $itemCtr }}')">
                                    </div>
                                </td>
                                <td>
                                    <div class="md-form form-sm my-0">
                                        <input type="number" placeholder="..." name="unobligated[]" id="unobligated-{{ $itemCtr }}"
                                               class="form-control required form-control-sm unobligated"
                                               value="{{ $item->raod ? $item->raod->unobligated_allot : '' }}"
                                               onkeyup="$(this).solveUnobligated('{{ $itemCtr }}')"
                                               onchange="$(this).solveUnobligated('{{ $itemCtr }}')">
                                    </div>
                                </td>
                                <td>
                                    <div class="md-form form-sm my-0">
                                        <input type="number" placeholder="..." name="disbursements[]"
                                               value="{{
                                                    $item->raod ? $item->raod->disbursement :
                                                    ($item->dv_amount ? $item->dv_amount : 0)
                                                }}"
                                               class="form-control required form-control-sm disbursements"
                                               id="disbursement-{{ $itemCtr }}">
                                    </div>
                                </td>
                                <td>
                                    <div class="md-form form-sm my-0">
                                        <input type="number" placeholder="..." name="due_demandable[]"
                                               class="form-control required form-control-sm due-demandable"
                                               id="due-demandable-{{ $itemCtr }}"
                                               value="{{ $item->raod ? $item->raod->due_demandable : '' }}"
                                               onclick="$(this).solveDueDemandable('{{ $itemCtr }}')">
                                    </div>
                                </td>
                                <td>
                                    <div class="md-form form-sm my-0">
                                        <input type="number" placeholder="..." name="not_due_demandable[]"
                                               class="form-control required form-control-sm not-due-demandable"
                                               id="not-due-demandable-{{ $itemCtr }}"
                                               value="{{ $item->raod ? $item->raod->not_due_demandable : '' }}"
                                               onclick="$(this).solveNotYetDueDemandable('{{ $itemCtr }}')">
                                    </div>
                                </td>
                                <th class="align-middle text-center" scope="row">
                                    <input class="form-check-input is-excluded" type="checkbox" value="1" id="check-{{ $itemCtr }}"
                                           {{ $item->raod ? ($item->raod->is_excluded == 'y' ? 'checked' : '') : '' }}
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
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" id="reg-id" value="{{ $id }}">
    <input type="hidden" id="toggle" value="update">
</form>
