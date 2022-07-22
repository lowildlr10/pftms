@extends('layouts.app')

@section('main-content')

<div class="row wow animated fadeIn">
    <section class="mb-5 col-12 module-container">
        <div class="card mdb-color darken-3">
            <div class="card-body">
                <h5 class="card-title white-text">
                    <strong>
                        <i class="fas fa-money-bill-wave-alt"></i> Obligation / Budget Utilization & Request Status
                    </strong>
                </h5>
                <hr class="white hidden-xs">
                <ul class="breadcrumb mdb-color darken-3 mb-0 p-1 white-text hidden-xs">
                    <li>
                        <i class="fa fa-caret-right mx-2" aria-hidden="true"></i>
                    </li>
                    <li class="active">
                        <a href="{{ url('cadv-reim-liquidation/ors-burs') }}" class="waves-effect waves-light cyan-text">
                            Obligation / Budget Utilization & Request Status
                        </a>
                    </li>
                </ul>

                <!-- Table with panel -->
                <div class="card card-cascade narrower">

                    <!--Card image-->
                    <div class="gradient-card-header unique-color
                                narrower py-2 px-2 mb-1 d-flex justify-content-between
                                align-items-center">
                        <div>
                            <button type="button" class="btn btn-outline-white btn-rounded btn-sm px-2"
                                    onclick="$(this).showCreate('{{ route('ca-ors-burs-show-create') }}');">
                                <i class="fas fa-pencil-alt"></i> Create
                            </button>
                        </div>
                        <div>
                            <button class="btn btn-outline-white btn-rounded btn-sm px-2"
                                    data-target="#top-fluid-modal" data-toggle="modal"
                                    onclick="$('#search').focus()">
                                <i class="fas fa-search"></i> {{ !empty($keyword) ? (strlen($keyword) > 15) ?
                                'Search: '.substr($keyword, 0, 15).'...' : 'Search: '.$keyword : '' }}
                            </button>
                            <a href="{{ route('ca-ors-burs') }}" class="btn btn-outline-white btn-rounded btn-sm px-2">
                                <i class="fas fa-sync-alt fa-pulse"></i>
                            </a>
                        </div>
                    </div>
                    <!--/Card image-->

                    <div class="px-2">
                        <div class="table-wrapper table-responsive border rounded">

                            <!--Table-->
                            <table id="dtmaterial" class="table table-hover" cellspacing="0" width="100%">

                                <!--Table head-->
                                <thead class="mdb-color darken-3 white-text hidden-xs">
                                    <tr>
                                        <th class="th-md" width="3%"></th>
                                        <th class="th-md" width="3%"></th>
                                        <th class="th-md" width="13%">
                                            @sortablelink('serial_no', 'Serial No.', [], ['class' => 'white-text'])
                                        </th>
                                        <th class="th-md" width="58%">
                                            @sortablelink('particulars', 'Particulars', [], ['class' => 'white-text'])
                                        </th>
                                        <th class="th-md" width="20%">
                                            @sortablelink('emppayee.firstname', 'Payee', [], ['class' => 'white-text'])
                                        </th>
                                        <th class="th-md" width="3%"></th>
                                    </tr>
                                </thead>
                                <!--Table head-->

                                <!--Table body-->
                                <tbody>
                                    @if (count($list) > 0)
                                        @foreach ($list as $listCtr => $ors)

                                    {{--
                                            @if (!$roleHasOrdinary && empty($ors->doc_status->date_issued) &&
                                                 Auth::user()->id != $ors->payee)
                                    <tr class="d-none">
                                            @else
                                    <tr class="hidden-xs">
                                            @endif
                                    --}}

                                    <tr class="hidden-xs">
                                        <td align="center">
                                            @if (!empty($ors->date_obligated))
                                            <i class="fas fa-file-signature fa-lg green-text material-tooltip-main"
                                               data-toggle="tooltip" data-placement="right" title="Obligated"></i>
                                            @else
                                                @if (!empty($ors->doc_status->date_issued) &&
                                                     empty($ors->doc_status->date_received) &&
                                                     empty($ors->doc_status->date_issued_back) &&
                                                     empty($ors->doc_status->date_received_back))
                                            <i class="fas fa-paper-plane fa-lg orange-text material-tooltip-main"
                                               data-toggle="tooltip" data-placement="right" title="Submitted"></i>
                                            @elseif (!empty($ors->doc_status->date_issued) &&
                                                     !empty($ors->doc_status->date_received) &&
                                                     empty($ors->doc_status->date_issued_back) &&
                                                     empty($ors->doc_status->date_received_back))
                                            <i class="fas fa-hand-holding fa-lg text-success material-tooltip-main"
                                               data-toggle="tooltip" data-placement="right" title="Received"></i>
                                            @elseif (!empty($ors->doc_status->date_issued) &&
                                                     !empty($ors->doc_status->date_received) &&
                                                     !empty($ors->doc_status->date_issued_back) &&
                                                     empty($ors->doc_status->date_received_back))
                                            <i class="fas fa-undo-alt fa-lg orange-text material-tooltip-main"
                                               data-toggle="tooltip" data-placement="right" title="Sumbitted Back"></i>
                                            @elseif (!empty($ors->doc_status->date_issued) &&
                                                     !empty($ors->doc_status->date_received) &&
                                                     !empty($ors->doc_status->date_issued_back) &&
                                                     !empty($ors->doc_status->date_received_back))
                                            <i class="fas fa-hand-holding fa-lg text-success material-tooltip-main"
                                               data-toggle="tooltip" data-placement="right" title="Received"></i>
                                                @else
                                            <i class="far fa-lg fa-file material-tooltip-main"
                                               data-toggle="tooltip" data-placement="right" title="Pending"></i>
                                                @endif
                                            @endif
                                        </td>
                                        <td align="center"></td>
                                        <td>{{ !empty($ors->serial_no) ? $ors->serial_no : 'NA' }}</td>
                                        <td>
                                            <i class="fas fa-caret-right"></i> {{
                                                (strlen($ors->particulars) > 150) ?
                                                substr($ors->particulars, 0, 150).'...' : $ors->particulars
                                            }}
                                        </td>
                                        <td>
                                            @if (isset($ors->emppayee['firstname']))
                                            {{ $ors->emppayee['firstname'] }} {{ $ors->emppayee['lastname'] }}
                                            @elseif (isset($ors->bidpayee['company_name']))
                                            {{ $ors->bidpayee['company_name'] }}
                                            @elseif (isset($ors->custompayee['payee_name']))
                                            {{ $ors->custompayee['payee_name'] }}
                                            @else
                                            None
                                            @endif
                                        </td>
                                        <td align="center">
                                            @if (!empty($ors->date_obligated))
                                                @if ((Auth::user()->role == 1 || Auth::user()->role == 4))
                                                    @if (!empty($ors->doc_status->issued_remarks) &&
                                                         !empty($ors->doc_status->date_issued) &&
                                                         empty($ors->doc_status->date_issued_back))
                                            <span class="red-text">
                                                <a data-target="#right-modal-{{ $listCtr + 1 }}" data-toggle="modal">
                                                    <i class="fas fa-exclamation-triangle fa-sm"></i>
                                                </a>
                                            </span>
                                                    @endif
                                                @else
                                                    @if (!empty($ors->doc_status->issued_back_remarks) &&
                                                         !empty($ors->doc_status->date_issued) &&
                                                         !empty($ors->doc_status->date_issued_back))
                                            <span class="red-text">
                                                <a data-target="#right-modal-{{ $listCtr + 1 }}" data-toggle="modal">
                                                    <i class="fas fa-exclamation-triangle fa-sm"></i>
                                                </a>
                                            </span>
                                                    @endif
                                                @endif
                                            @endif
                                            <a class="btn-floating btn-sm btn-mdb-color p-2 waves-effect material-tooltip-main mr-0"
                                               data-target="#right-modal-{{ $listCtr + 1 }}" data-toggle="modal"
                                               data-toggle="tooltip" data-placement="left" title="Open">
                                                <i class="fas fa-folder-open"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <tr class="d-none show-xs">
                                        <td data-target="#right-modal-{{ $listCtr + 1 }}" data-toggle="modal">
                                            [ <b>Serial No:</b> {{
                                                !empty($ors->serial_no) && $ors->serial_no != '.' ?
                                                $ors->serial_no : 'NA' }} ] <i class="fas fa-caret-right"></i> {{
                                                (strlen($ors->particulars) > 150) ?
                                                substr($ors->particulars, 0, 150).'...' : $ors->particulars
                                            }}<br>
                                            <small>
                                                @if (!empty($ors->ors['date_obligated']))
                                                <b>Status:</b> Obligated
                                                @else
                                                    @if (!empty($ors->doc_status->date_issued) &&
                                                        empty($ors->doc_status->date_received) &&
                                                        empty($ors->doc_status->date_issued_back) &&
                                                        empty($ors->doc_status->date_received_back))
                                                <b>Status:</b> Submitted
                                                @elseif (!empty($ors->doc_status->date_issued) &&
                                                        !empty($ors->doc_status->date_received) &&
                                                        empty($ors->doc_status->date_issued_back) &&
                                                        empty($ors->doc_status->date_received_back))
                                                <b>Status:</b> Received
                                                @elseif (!empty($ors->doc_status->date_issued) &&
                                                        !empty($ors->doc_status->date_received) &&
                                                        !empty($ors->doc_status->date_issued_back) &&
                                                        empty($ors->doc_status->date_received_back))
                                                <b>Status:</b> Submitted Back
                                                @elseif (!empty($ors->doc_status->date_issued) &&
                                                        !empty($ors->doc_status->date_received) &&
                                                        !empty($ors->doc_status->date_issued_back) &&
                                                        !empty($ors->doc_status->date_received_back))
                                                <b>Status:</b> Received
                                                    @else
                                                <b>Status:</b> Pending
                                                    @endif
                                                @endif
                                            </small><br>
                                            <small>
                                                <b>Payee: </b>
                                                @if (isset($ors->emppayee['firstname']))
                                                {{ $ors->emppayee['firstname'] }} {{ $ors->emppayee['lastname'] }}
                                                @elseif (isset($ors->bidpayee['company_name']))
                                                {{ $ors->bidpayee['company_name'] }}
                                                @elseif (isset($ors->custompayee['payee_name']))
                                                {{ $ors->custompayee['payee_name'] }}
                                                @else
                                                None
                                                @endif
                                            </small>
                                        </td>
                                    </tr>
                                        @endforeach
                                    @else
                                    <tr>
                                        <td colspan="6" class="text-center py-5">
                                            <h6 class="red-text">
                                                No available data.
                                            </h6>
                                        </td>
                                    </tr>
                                    @endif
                                </tbody>
                                <!--Table body-->
                            </table>
                            <!--Table-->
                        </div>

                        <div class="mt-3">
                            {!! $list->appends(\Request::except('page'))->render('pagination') !!}
                        </div>
                    </div>
                </div>
                <!-- Table with panel -->
            </div>
        </div>
    </section>
</div>

<!-- Modals -->

@if (count($list) > 0)
    @foreach ($list as $listCtr => $ors)
<div class="modal custom-rightmenu-modal fade right" id="right-modal-{{ $listCtr + 1 }}" tabindex="-1"
     role="dialog">
    <div class="modal-dialog modal-full-height modal-righty" role="document">
        <!--Content-->
        <div class="modal-content">
            <!--Header-->
            <div class="modal-header stylish-color-dark white-text">
                <h7>
                    <i class="fas fa-shopping-cart"></i>
                    <strong>SERIAL NO: {{ $ors->serial_no }}</strong>
                </h7>
                <button type="button" class="close white-text" data-dismiss="modal"
                        aria-label="Close">
                    &times;
                </button>
            </div>
            <!--Body-->
            <div class="modal-body">
                <div class="card card-cascade z-depth-1 mb-3">
                    <div class="gradient-card-header rgba-white-light p-0">
                        <div class="p-0">
                            <div class="btn-group btn-menu-1 p-0">
                                <button type="button" class="btn btn-outline-mdb-color
                                        btn-sm px-2 waves-effect waves-light"
                                        onclick="$(this).showPrint('{{ $ors->id }}',
                                                                   'ca_{{ $ors->document_type }}');">
                                    <i class="fas fa-print blue-text"></i> Print ORS/BURS
                                </button>
                                <button type="button" class="btn btn-outline-mdb-color
                                        btn-sm px-2 waves-effect waves-light"
                                        onclick="$(this).showEdit('{{ route('ca-ors-burs-show-edit',
                                                                  ['id' => $ors->id]) }}');">
                                    <i class="fas fa-edit orange-text"></i> Edit
                                </button>

                                @if (!$ors->date_obligated)
                                <button type="button" class="btn btn-outline-mdb-color
                                        btn-sm px-2 waves-effect waves-light"
                                        onclick="$(this).showDelete('{{ route('ca-ors-burs-delete', ['id' => $ors->id]) }}',
                                                                              '{{ $ors->id }}');">
                                    <i class="fas fa-trash-alt red-text"></i> Delete
                                </button>
                                @else
                                <button type="button" class="btn btn-outline-mdb-color
                                        btn-sm px-2 waves-effect waves-light" disabled>
                                    <i class="fas fa-trash-alt red-text"></i> Delete
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <p>
                            <strong>ORS/BURS Date: </strong> {{ $ors->date_ors_burs }}<br>
                            <strong>Particulars: </strong> {{
                                (strlen($ors->particulars) > 150) ?
                                substr($ors->particulars, 0, 150).'...' : $ors->particulars
                            }}<br>
                            <strong>Payee: </strong>
                            @if (isset($ors->emppayee['firstname']))
                            {{ $ors->emppayee['firstname'] }} {{ $ors->emppayee['lastname'] }}
                            @elseif (isset($ors->bidpayee['company_name']))
                            {{ $ors->bidpayee['company_name'] }}
                            @elseif (isset($ors->custompayee['payee_name']))
                            {{ $ors->custompayee['payee_name'] }}
                            @else
                            None
                            @endif
                            <br>

                            @if (!$ors->date_obligated)
                                @if (!empty($ors->doc_status->issued_remarks) &&
                                     !empty($ors->doc_status->date_issued) &&
                                     empty($ors->doc_status->date_received) &&
                                     empty($ors->doc_status->date_issued_back) &&
                                     empty($ors->doc_status->date_received_back) &&
                                     $isAllowedReceive)
                            <strong class="red-text">
                                <i class="fas fa-exclamation-triangle fa-sm"></i> Remarks:
                                {{ $ors->doc_status->issued_remarks }}
                            </strong><br>
                                @elseif (!empty($ors->doc_status->received_remarks) &&
                                         !empty($ors->doc_status->date_issued) &&
                                         !empty($ors->doc_status->date_received) &&
                                         empty($ors->doc_status->date_issued_back) &&
                                         empty($ors->doc_status->date_received_back) &&
                                         $isAllowedIssue)
                            <strong class="red-text">
                                <i class="fas fa-exclamation-triangle fa-sm"></i> Remarks:
                                {{ $ors->doc_status->received_remarks }}
                            </strong><br>
                                @elseif (!empty($ors->doc_status->issued_back_remarks) &&
                                         !empty($ors->doc_status->date_issued) &&
                                         !empty($ors->doc_status->date_received) &&
                                         !empty($ors->doc_status->date_issued_back) &&
                                         empty($ors->doc_status->date_received_back) &&
                                         $isAllowedReceiveBack)
                            <strong class="red-text">
                                <i class="fas fa-exclamation-triangle fa-sm"></i> Remarks:
                                {{ $ors->doc_status->issued_back_remarks }}
                            </strong><br>
                                @elseif (!empty($ors->doc_status->received_back_remarks) &&
                                         !empty($ors->doc_status->date_issued) &&
                                         !empty($ors->doc_status->date_received) &&
                                         !empty($ors->doc_status->date_issued_back) &&
                                         !empty($ors->doc_status->date_received_back) &&
                                         $isAllowedReceive)
                            <strong class="red-text">
                                <i class="fas fa-exclamation-triangle fa-sm"></i> Remarks:
                                {{ $ors->doc_status->received_back_remarks }}
                            </strong><br>
                                @endif
                            @endif
                        </p>

                        <div class="btn-menu-2">
                            <button type="button" class="btn btn-sm btn-outline-elegant btn-rounded
                                    btn-block waves-effect mb-2"
                                    onclick="$(this).showAttachment('{{ $ors->id }}', 'proc-rfq');">
                                <i class="fas fa-paperclip fa-lg"></i> View Attachment
                            </button>
                            <button type="button" class="btn btn-sm btn-mdb-color btn-rounded
                                    btn-block waves-effect mb-2"
                                    onclick="$(this).showRemarks('{{ route('ca-ors-burs-show-remarks',
                                                                ['id' => $ors->id]) }}');">
                                <i class="far fa-comment-dots"></i> View Remarks
                            </button>
                        </div>
                    </div>
                </div>
                <hr>
                <ul class="btn-menu-3 list-group z-depth-0">
                    <li class="list-action-header list-group-item justify-content-between">
                        <h5><strong><i class="fas fa-pen-nib"></i> Actions</strong></h5>
                    </li>

                    @if ($isAllowedDV && $isAllowedDVCreate)
                        @if ($ors->has_dv)
                    <li class="list-group-item justify-content-between">
                        <a type="button" class="btn btn-outline-warning waves-effect btn-block btn-md btn-rounded"
                           onclick="$(this).redirectToDoc('{{ route('ca-dv') }}', '{{ $ors->id }}');">
                            <i class="fas fa-file-signature orange-text"></i> Edit DV
                        </a>
                    </li>
                        @else
                    <li class="list-group-item justify-content-between">
                        <button type="button" class="btn btn-outline-green waves-effect btn-block btn-md btn-rounded"
                                onclick="$(this).showCreateDV('{{ route('ca-dv-show-create-ors', ['orsID' => $ors->id]) }}');">
                            <i class="fas fa-pencil-alt green-text"></i> Create DV
                        </button>
                    </li>
                        @endif
                    @endif

                    @if (empty($ors->date_obligated))
                        @if (empty($ors->doc_status->date_issued) &&
                             empty($ors->doc_status->date_received) &&
                             empty($ors->doc_status->date_issued_back) &&
                             empty($ors->doc_status->date_received_back) &&
                             $isAllowedIssue)
                    <li class="list-group-item justify-content-between">
                        <button type="button" class="btn btn-outline-orange waves-effect btn-block btn-md btn-rounded"
                                onclick="$(this).showIssue('{{ route('ca-ors-burs-show-issue', ['id' => $ors->id]) }}');">
                            <i class="fas fa-paper-plane"></i> Submit
                        </button>
                    </li>
                        @elseif (!empty($ors->doc_status->date_issued) &&
                                 empty($ors->doc_status->date_received) &&
                                 empty($ors->doc_status->date_issued_back) &&
                                 empty($ors->doc_status->date_received_back) &&
                                 $isAllowedReceive)
                    <li class="list-group-item justify-content-between">
                        <button type="button" class="btn btn-outline-green waves-effect btn-block btn-md btn-rounded"
                                onclick="$(this).showReceive('{{ route('ca-ors-burs-show-receive', ['id' => $ors->id]) }}');">
                            <i class="fas fa-hand-holding"></i> Receive
                        </button>
                    </li>
                        @elseif (!empty($ors->doc_status->date_issued) &&
                                 !empty($ors->doc_status->date_received) &&
                                 empty($ors->doc_status->date_issued_back) &&
                                 empty($ors->doc_status->date_received_back))
                            @if ($isAllowedObligate)
                    <li class="list-group-item justify-content-between">
                        <button type="button" class="btn btn-outline-green waves-effect btn-block btn-md btn-rounded"
                                onclick="$(this).showObligate('{{ route('ca-ors-burs-show-obligate', ['id' => $ors->id]) }}');">
                            <i class="fas fa-file-signature"></i> Obligate
                        </button>
                    </li>
                            @endif

                            @if ($isAllowedIssueBack)
                    <li class="list-group-item justify-content-between">
                        <button type="button" class="btn btn-outline-orange waves-effect btn-block btn-md btn-rounded"
                                onclick="$(this).showIssueBack('{{ route('ca-ors-burs-show-issue-back', ['id' => $ors->id]) }}');">
                            <i class="fas fa-undo-alt"></i> Submit Back
                        </button>
                    </li>
                            @endif
                        @elseif (!empty($ors->doc_status->date_issued) &&
                                 !empty($ors->doc_status->date_received) &&
                                 !empty($ors->doc_status->date_issued_back) &&
                                 empty($ors->doc_status->date_received_back) &&
                                 $isAllowedReceiveBack)
                    <li class="list-group-item justify-content-between">
                        <button type="button" class="btn btn-outline-green waves-effect btn-block btn-md btn-rounded"
                                onclick="$(this).showReceiveBack('{{ route('ca-ors-burs-show-receive-back', ['id' => $ors->id]) }}');">
                            <i class="fas fa-hand-holding"></i> Receive Back
                        </button>
                    </li>
                        @endif
                    @endif
                </ul>
            </div>
            <!--Footer-->
            <div class="modal-footer justify-content-end rgba-stylish-strong p-1">
                <a type="button" class="btn btn-sm btn btn-light waves-effect py-1" data-dismiss="modal">
                    <i class="far fa-window-close"></i> Close
                </a>
            </div>
        </div>
      <!--/.Content-->
    </div>
</div>
    @endforeach
@endif

@include('modals.search-post')
@include('modals.show')
@include('modals.create')
@include('modals.edit')
@include('modals.delete-destroy')
@include('modals.issue')
@include('modals.receive')
@include('modals.issue-back')
@include('modals.receive-back')
@include('modals.obligate')
@include('modals.uacs-items')
@include('modals.print')
@include('modals.attachment')

@endsection

@section('custom-js')

<script type="text/javascript" src="{{ asset('assets/js/input-validation.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/ors-burs.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/print.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/attachment.js') }}"></script>

@if (!empty(session("success")))
    @include('modals.alert')
    <script type="text/javascript">
        $(function() {
            $('#modal-success').modal();
        });
    </script>
@elseif (!empty(session("warning")))
    @include('modals.alert')
    <script type="text/javascript">
        $(function() {
            $('#modal-warning').modal();
        });
    </script>
@elseif (!empty(session("failed")))
    @include('modals.alert')
    <script type="text/javascript">
        $(function() {
            $('#modal-failed').modal();
        });
    </script>
@endif

@endsection
