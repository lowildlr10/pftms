<div id="section-show-selected">

    <ul class="nav nav-tabs" id="show-selected-tabs" role="tablist">

        @foreach ($data as $datCtr => $dat)
        <li class="nav-item">
            <a class="nav-link {{ $datCtr == 0 ? 'active' : '' }}"
               id="tab-{{ $datCtr }}" data-toggle="tab" href="#content-{{ $datCtr }}" role="tab"
               aria-controls="tab-{{ $datCtr }}" aria-selected="true">
                <strong>{{ $dat->mfo_pap }} : {{ $dat->period_ending }}</strong>
            </a>
        </li>
        @endforeach

    </ul>

    <div class="tab-content p-0" id="show-selected-tab-content">

        @foreach ($data as $datCtr => $dat)
        <div class="tab-pane fade {{ $datCtr == 0 ? 'show active' : '' }} sel-section"
             id="content-{{ $datCtr }}" role="tabpanel" aria-labelledby="tab-{{ $datCtr }}">

            <input type="hidden" class="mfo-pap-text" value="{{ $dat->mfo_pap }}">

            <div class="card mb-5">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderless table-sm table-condensed">
                            <tr class="text-center">
                                <td colspan="4" width="30%"></td>
                                <td colspan="5" width="40%">
                                    <strong>
                                        REGISTRY OF ALLOTMENTS, OBLIGATIONS AND DISBURSEMENTS
                                        PERSONNEL SERVICES/MAINTENANCE AND OTHER OPERATING EXPENSES
                                        For the Period Ending:
                                    </strong>
                                </td>
                                <td colspan="4" width="30%"></td>
                            </tr>
                            <tr class="text-center">
                                <td colspan="4"></td>
                                <td colspan="5">
                                    <strong>
                                        ({{ $dat->period_ending }})
                                    </strong>
                                </td>
                                <td colspan="4"></td>
                            </tr>

                            <tr>
                                <td colspan="2" width="10%">
                                    <strong>Entity Name:</strong>
                                </td>
                                <td colspan="2" width="20%">
                                    <strong>{{ $dat->entity_name }}</strong>
                                </td>
                                <td colspan="5" width="40%"></td>
                                <td colspan="2" width="10%">
                                    <strong>MFO/PAP:</strong>
                                </td>
                                <td colspan="2" width="20%">
                                    <strong>{{ $dat->mfo_pap }}</strong>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <strong>Fund Cluster:</strong>
                                </td>
                                <td colspan="2">
                                    <strong>{{ $dat->fund_cluster }}</strong>
                                </td>
                                <td colspan="5"></td>
                                <td colspan="2">
                                    <strong>Sheet No.:</strong>
                                </td>
                                <td colspan="2">
                                    <strong>{{ $dat->sheet_no }}</strong>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <strong>Legal Basis:</strong>
                                </td>
                                <td colspan="2">
                                    <strong>{{ $dat->legal_basis }}</strong>
                                </td>
                                <td colspan="5" width="32%"></td>
                                <td colspan="4" class="text-center font-weight-bold" width="24%">
                                    <em>
                                        Current/Cont Allotment
                                    </em>
                                </td>
                            </tr>
                        </table>

                        <table class="table table-bordered table-sm table-condensed">
                            <thead class="text-center">
                                <tr>
                                    <th rowspan="2" class="align-middle" width="7.69%">
                                        <strong>Data Received</strong>
                                    </th>
                                    <th rowspan="2" class="align-middle" width="7.69%">
                                        <strong>Data Obligated</strong>
                                    </th>
                                    <th rowspan="2" class="align-middle" width="7.69%">
                                        <strong>Data Released</strong>
                                    </th>
                                    <th colspan="3" width="23.077%">
                                        <strong>Reference</strong>
                                    </th>
                                    <th rowspan="2" class="align-middle" width="7.69%">
                                        <strong>UACS Object Code/Expendure</strong>
                                    </th>
                                    <th rowspan="2" class="align-middle" width="7.69%">
                                        <strong>Allotments</strong>
                                    </th>
                                    <th rowspan="2" class="align-middle" width="7.69%">
                                        <strong>Obligations</strong>
                                    </th>
                                    <th rowspan="2" class="align-middle" width="7.69%">
                                        <strong>Unobligated Allotments</strong>
                                    </th>
                                    <th rowspan="2" class="align-middle" width="7.69%">
                                        <strong>Disbursed</strong>
                                    </th>
                                    <th colspan="2" width="15.38%">
                                        <strong>Unpaid Obligations</strong>
                                    </th>
                                </tr>
                                <tr>
                                    <th class="align-middle">
                                        <strong>Payee</strong>
                                    </th>
                                    <th class="align-middle">
                                        <strong>Paticulars</strong>
                                    </th>
                                    <th class="align-middle">
                                        <strong>Serial Number</strong>
                                    </th>

                                    <th class="align-middle">
                                        <strong>Due and Demandable</strong>
                                    </th>
                                    <th class="align-middle">
                                        <strong>Not Yet Due and Received Obligated Released Expenditure Allotments Demandable</strong>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach ($dat->table_data as $tDat)
                                    @if (count($tDat->body) > 0)
                                        @foreach ($tDat->body as $tBody)
                                <tr>
                                            @foreach ($tBody as $tBodyCtr => $_tBody)
                                    <td class="font-weight-normal {{ $tBodyCtr >= 7 ? 'text-right' : '' }}">{{ $_tBody }}</td>
                                            @endforeach
                                </tr>
                                        @endforeach
                                    @else
                                <tr class="text-center grey lighten-5">
                                    <td colspan="13" class="red-text p-4">
                                        <strong>No data</strong>
                                    </td>
                                </tr>
                                    @endif

                                    @foreach ($tDat->footer as $tFooter)
                                <tr class="amber accent-1">
                                        @foreach ($tFooter as $tFootCtr => $_tFooter)
                                            @if ($tFootCtr == 0 || $tFootCtr == 1)
                                    <td colspan="2" class="text-left">
                                            @if ($tFootCtr == 1)
                                        <strong>({{ $_tFooter }})</strong>
                                            @else
                                        <strong>{{ $_tFooter }}</strong>
                                            @endif
                                    </td>
                                            @else
                                    <td class="text-right">
                                        <strong>{{ $_tFooter }}</strong>
                                    </td>
                                            @endif
                                         @endforeach
                                </tr>
                                    @endforeach

                                @endforeach

                                <tr class=" blue-grey lighten-4">
                                    <td colspan="2">
                                        <strong>TOTAL AS OF</strong>
                                    </td>
                                    <td colspan="2">
                                        <strong>({{ $tDat->month }})</strong>
                                    </td>
                                    <td colspan="3"></td>
                                    <td class="text-right">
                                        <strong>{{ $dat->total_allotment }}</strong>
                                    </td>
                                    <td class="text-right">
                                        <strong>{{ $dat->total_obligation }}</strong>
                                    </td>
                                    <td class="text-right">
                                        <strong>{{ $dat->total_unobligated }}</strong>
                                    </td>
                                    <td class="text-right">
                                        <strong>{{ $dat->total_disbursement }}</strong>
                                    </td>
                                    <td class="text-right">
                                        <strong>{{ $dat->total_due }}</strong>
                                    </td>
                                    <td class="text-right">
                                        <strong>{{ $dat->total_not_due }}</strong>
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
        @endforeach

    </div>
</div>


