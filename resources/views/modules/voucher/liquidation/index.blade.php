@extends('layouts.app')

@section('main-content')

<div class="row wow animated fadeIn">
    <section class="mb-5 col-12 module-container">
        <div class="card mdb-color darken-3">
            <div class="card-body">
                <h5 class="card-title white-text">
                    <strong>
                        <i class="fas fa-money-bill-wave-alt"></i> Liquidation Report
                    </strong>
                </h5>
                <hr class="white hidden-xs">
                <ul class="breadcrumb mdb-color darken-3 mb-0 p-1 white-text hidden-xs">
                    <li>
                        <i class="fa fa-caret-right mx-2" aria-hidden="true"></i>
                    </li>
                    <li class="active">
                        <a href="{{ url('cadv-reim-liquidation/liquidation') }}" class="waves-effect waves-light cyan-text">
                            Liquidation Report
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
                                    onclick="$(this).showCreate('{{ route('ca-lr-show-create') }}');">
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
                            <a href="{{ route('ca-lr') }}" class="btn btn-outline-white btn-rounded btn-sm px-2">
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
                                            @sortablelink('empclaimant.firstname', 'Claimant', [], ['class' => 'white-text'])
                                        </th>
                                        <th class="th-md" width="3%"></th>
                                    </tr>
                                </thead>
                                <!--Table head-->

                                <!--Table body-->
                                <tbody>
                                    @if (count($list) > 0)
                                        @foreach ($list as $listCtr => $lr)

                                    {{--
                                            @if (!$roleHasOrdinary && empty($lr->doc_status->date_issued) &&
                                                 Auth::user()->id != $lr->sig_claimant)
                                    <tr class="d-none">
                                            @else
                                    <tr class="hidden-xs">
                                            @endif
                                    --}}

                                    <tr class="hidden-xs">
                                        <td align="center">
                                            @if (!empty($lr->date_liquidated))
                                            <i class="fas fa-file-signature fa-lg green-text material-tooltip-main"
                                               data-toggle="tooltip" data-placement="right" title="Liquidated"></i>
                                            @else
                                                @if (!empty($lr->doc_status->date_issued) &&
                                                     empty($lr->doc_status->date_received) &&
                                                     empty($lr->doc_status->date_issued_back) &&
                                                     empty($lr->doc_status->date_received_back))
                                            <i class="fas fa-paper-plane fa-lg orange-text material-tooltip-main"
                                               data-toggle="tooltip" data-placement="right" title="Submitted"></i>
                                            @elseif (!empty($lr->doc_status->date_issued) &&
                                                     !empty($lr->doc_status->date_received) &&
                                                     empty($lr->doc_status->date_issued_back) &&
                                                     empty($lr->doc_status->date_received_back))
                                            <i class="fas fa-hand-holding fa-lg text-success material-tooltip-main"
                                               data-toggle="tooltip" data-placement="right" title="Received"></i>
                                            @elseif (!empty($lr->doc_status->date_issued) &&
                                                     !empty($lr->doc_status->date_received) &&
                                                     !empty($lr->doc_status->date_issued_back) &&
                                                     empty($lr->doc_status->date_received_back))
                                            <i class="fas fa-undo-alt fa-lg orange-text material-tooltip-main"
                                               data-toggle="tooltip" data-placement="right" title="Sumbitted Back"></i>
                                            @elseif (!empty($lr->doc_status->date_issued) &&
                                                     !empty($lr->doc_status->date_received) &&
                                                     !empty($lr->doc_status->date_issued_back) &&
                                                     !empty($lr->doc_status->date_received_back))
                                            <i class="fas fa-hand-holding fa-lg text-success material-tooltip-main"
                                               data-toggle="tooltip" data-placement="right" title="Received Back"></i>
                                                @else
                                            <i class="far fa-lg fa-file material-tooltip-main"
                                               data-toggle="tooltip" data-placement="right" title="Pending"></i>
                                                @endif
                                            @endif
                                        </td>
                                        <td align="center"></td>
                                        <td>{{ !empty($lr->serial_no) ? $lr->serial_no : 'NA' }}</td>
                                        <td>
                                            <i class="fas fa-caret-right"></i> {{
                                                (strlen($lr->particulars) > 150) ?
                                                substr($lr->particulars, 0, 150).'...' : $lr->particulars
                                            }}
                                        </td>
                                        <td>
                                        @if (isset($lr->empclaimant['firstname']))
                                        {{ $lr->empclaimant['firstname'] }} {{ $lr->empclaimant['lastname'] }}
                                        @elseif (isset($lr->bidclaimant['company_name']))
                                        {{ $lr->bidclaimant['company_name'] }}
                                        @elseif (isset($lr->customclaimant['payee_name']))
                                        {{ $lr->customclaimant['payee_name'] }}
                                        @else
                                        None
                                        @endif
                                        </td>
                                        <td align="center">
                                            @if (!empty($lr->date_liquidated))
                                                @if ((Auth::user()->role == 1 || Auth::user()->role == 4))
                                                    @if (!empty($lr->doc_status->issued_remarks) &&
                                                         !empty($lr->doc_status->date_issued) &&
                                                         empty($lr->doc_status->date_issued_back))
                                            <span class="red-text">
                                                <a data-target="#right-modal-{{ $listCtr + 1 }}" data-toggle="modal">
                                                    <i class="fas fa-exclamation-triangle fa-sm"></i>
                                                </a>
                                            </span>
                                                    @endif
                                                @else
                                                    @if (!empty($lr->doc_status->issued_back_remarks) &&
                                                         !empty($lr->doc_status->date_issued) &&
                                                         !empty($lr->doc_status->date_issued_back))
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
                                            [ Serial No: {{ !empty($lr->serial_no) ? $lr->serial_no : 'NA' }} ] <i class="fas fa-caret-right"></i> {{
                                                (strlen($lr->particulars) > 150) ?
                                                substr($lr->particulars, 0, 150).'...' : $lr->particulars
                                            }}<br>
                                            <small>
                                                @if (!empty($lr->date_liquidated))
                                                <b>Status:</b> Liquidated
                                                @else
                                                    @if (!empty($lr->doc_status->date_issued) &&
                                                        empty($lr->doc_status->date_received) &&
                                                        empty($lr->doc_status->date_issued_back) &&
                                                        empty($lr->doc_status->date_received_back))
                                                <b>Status:</b> Submitted
                                                @elseif (!empty($lr->doc_status->date_issued) &&
                                                        !empty($lr->doc_status->date_received) &&
                                                        empty($lr->doc_status->date_issued_back) &&
                                                        empty($lr->doc_status->date_received_back))
                                                <b>Status:</b> Received
                                                @elseif (!empty($lr->doc_status->date_issued) &&
                                                        !empty($lr->doc_status->date_received) &&
                                                        !empty($lr->doc_status->date_issued_back) &&
                                                        empty($lr->doc_status->date_received_back))
                                                <b>Status:</b> Submitted Back
                                                @elseif (!empty($lr->doc_status->date_issued) &&
                                                        !empty($lr->doc_status->date_received) &&
                                                        !empty($lr->doc_status->date_issued_back) &&
                                                        !empty($lr->doc_status->date_received_back))
                                                <b>Status:</b> Received Back
                                                    @else
                                                    <b>Status:</b> Pending
                                                    @endif
                                                @endif
                                            </small><br>
                                            <small>
                                                <b>Claimant: </b>
                                                @if (isset($lr->empclaimant['firstname']))
                                                {{ $lr->empclaimant['firstname'] }} {{ $lr->empclaimant['lastname'] }}
                                                @elseif (isset($lr->bidclaimant['company_name']))
                                                {{ $lr->bidclaimant['company_name'] }}
                                                @elseif (isset($lr->customclaimant['payee_name']))
                                                {{ $lr->customclaimant['payee_name'] }}
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
    @foreach ($list as $listCtr => $lr)
<div class="modal custom-rightmenu-modal fade right" id="right-modal-{{ $listCtr + 1 }}" tabindex="-1"
     role="dialog">
    <div class="modal-dialog modal-full-height modal-righty" role="document">
        <!--Content-->
        <div class="modal-content">
            <!--Header-->
            <div class="modal-header stylish-color-dark white-text">
                <h7>
                    <i class="fas fa-shopping-cart"></i>
                    <strong>SERIAL NO: {{ $lr->serial_no }}</strong>
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
                                        onclick="$(this).showPrint('{{ $lr->id }}', 'ca_lr');">
                                    <i class="fas fa-print blue-text"></i> Print LR
                                </button>
                                <button type="button" class="btn btn-outline-mdb-color
                                        btn-sm px-2 waves-effect waves-light"
                                        onclick="$(this).showEdit('{{ route('ca-lr-show-edit',
                                                                  ['id' => $lr->id]) }}');">
                                    <i class="fas fa-edit orange-text"></i> Edit
                                </button>

                                @if (!$lr->date_liquidated)
                                <button type="button" class="btn btn-outline-mdb-color
                                        btn-sm px-2 waves-effect waves-light"
                                        onclick="$(this).showDelete('{{ route('ca-lr-delete', ['id' => $lr->id]) }}',
                                                                              '{{ $lr->id }}');">
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
                            <strong>DV Date: </strong> {{ $lr->dv['date_dv'] }}<br>
                            <strong>LR Date: </strong> {{ $lr->date_liquidation }}<br>
                            <strong>Particulars: </strong> {{
                                (strlen($lr->particulars) > 150) ?
                                substr($lr->particulars, 0, 150).'...' : $lr->particulars
                            }}<br>
                            <strong>Payee: </strong>
                            @if (isset($lr->empclaimant['firstname']))
                            {{ $lr->empclaimant['firstname'] }} {{ $lr->empclaimant['lastname'] }}
                            @elseif (isset($lr->bidclaimant['company_name']))
                            {{ $lr->bidclaimant['company_name'] }}
                            @elseif (isset($lr->customclaimant['payee_name']))
                            {{ $lr->customclaimant['payee_name'] }}
                            @else
                            None
                            @endif
                            <br>

                            @if (!$lr->date_liquidated)
                                @if (!empty($lr->doc_status->issued_remarks) &&
                                     !empty($lr->doc_status->date_issued) &&
                                     empty($lr->doc_status->date_received) &&
                                     empty($lr->doc_status->date_issued_back) &&
                                     empty($lr->doc_status->date_received_back) &&
                                     $isAllowedReceive)
                            <strong class="red-text">
                                <i class="fas fa-exclamation-triangle fa-sm"></i> Remarks:
                                {{ $lr->doc_status->issued_remarks }}
                            </strong><br>
                                @elseif (!empty($lr->doc_status->received_remarks) &&
                                         !empty($lr->doc_status->date_issued) &&
                                         !empty($lr->doc_status->date_received) &&
                                         empty($lr->doc_status->date_issued_back) &&
                                         empty($lr->doc_status->date_received_back) &&
                                         $isAllowedIssue)
                            <strong class="red-text">
                                <i class="fas fa-exclamation-triangle fa-sm"></i> Remarks:
                                {{ $lr->doc_status->received_remarks }}
                            </strong><br>
                                @elseif (!empty($lr->doc_status->issued_back_remarks) &&
                                         !empty($lr->doc_status->date_issued) &&
                                         !empty($lr->doc_status->date_received) &&
                                         !empty($lr->doc_status->date_issued_back) &&
                                         empty($lr->doc_status->date_received_back) &&
                                         $isAllowedReceiveBack)
                            <strong class="red-text">
                                <i class="fas fa-exclamation-triangle fa-sm"></i> Remarks:
                                {{ $lr->doc_status->issued_back_remarks }}
                            </strong><br>
                                @elseif (!empty($lr->doc_status->received_back_remarks) &&
                                         !empty($lr->doc_status->date_issued) &&
                                         !empty($lr->doc_status->date_received) &&
                                         !empty($lr->doc_status->date_issued_back) &&
                                         !empty($lr->doc_status->date_received_back) &&
                                         $isAllowedReceive)
                            <strong class="red-text">
                                <i class="fas fa-exclamation-triangle fa-sm"></i> Remarks:
                                {{ $lr->doc_status->received_back_remarks }}
                            </strong><br>
                                @endif
                            @endif
                        </p>

                        <div class="btn-menu-2">
                            <button type="button" class="btn btn-sm btn-outline-elegant btn-rounded
                                    btn-block waves-effect mb-2"
                                    onclick="$(this).showAttachment('{{ $lr->id }}', 'proc-rfq');">
                                <i class="fas fa-paperclip fa-lg"></i> View Attachment
                            </button>
                            <button type="button" class="btn btn-sm btn-mdb-color btn-rounded
                                    btn-block waves-effect mb-2"
                                    onclick="$(this).showRemarks('{{ route('ca-lr-show-remarks',
                                                                ['id' => $lr->id]) }}');">
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

                    @if ($isAllowedDV && $lr->has_dv)
                    <li class="list-group-item justify-content-between">
                        <a type="button" class="btn btn-outline-warning waves-effect btn-block btn-md btn-rounded"
                           onclick="$(this).redirectToDoc('{{ route('ca-dv') }}', '{{ $lr->dv['id'] }}');">
                            <i class="fas fa-file-signature orange-text"></i> Edit DV
                        </a>
                    </li>
                    @endif

                    @if (empty($lr->date_liquidated))
                        @if (empty($lr->doc_status->date_issued) &&
                             empty($lr->doc_status->date_received) &&
                             empty($lr->doc_status->date_issued_back) &&
                             empty($lr->doc_status->date_received_back) &&
                             $isAllowedIssue)
                    <li class="list-group-item justify-content-between">
                        <button type="button" class="btn btn-outline-orange waves-effect btn-block btn-md btn-rounded"
                                onclick="$(this).showIssue('{{ route('ca-lr-show-issue', ['id' => $lr->id]) }}');">
                            <i class="fas fa-paper-plane"></i> Submit
                        </button>
                    </li>
                        @elseif (!empty($lr->doc_status->date_issued) &&
                                 empty($lr->doc_status->date_received) &&
                                 empty($lr->doc_status->date_issued_back) &&
                                 empty($lr->doc_status->date_received_back) &&
                                 $isAllowedReceive)
                    <li class="list-group-item justify-content-between">
                        <button type="button" class="btn btn-outline-green waves-effect btn-block btn-md btn-rounded"
                                onclick="$(this).showReceive('{{ route('ca-lr-show-receive', ['id' => $lr->id]) }}');">
                            <i class="fas fa-hand-holding"></i> Receive
                        </button>
                    </li>
                        @elseif (!empty($lr->doc_status->date_issued) &&
                                 !empty($lr->doc_status->date_received) &&
                                 empty($lr->doc_status->date_issued_back) &&
                                 empty($lr->doc_status->date_received_back))
                            @if ($isAllowedLiquidate)
                    <li class="list-group-item justify-content-between">
                        <button type="button" class="btn btn-outline-green waves-effect btn-block btn-md btn-rounded"
                                onclick="$(this).showLiquidate('{{ route('ca-lr-show-liquidate', ['id' => $lr->id]) }}');">
                            <i class="fas fa-file-signature"></i> Liquidate
                        </button>
                    </li>
                            @endif

                            @if ($isAllowedIssueBack)
                    <li class="list-group-item justify-content-between">
                        <button type="button" class="btn btn-outline-orange waves-effect btn-block btn-md btn-rounded"
                                onclick="$(this).showIssueBack('{{ route('ca-lr-show-issue-back', ['id' => $lr->id]) }}');">
                            <i class="fas fa-undo-alt"></i> Submit Back
                        </button>
                    </li>
                            @endif
                        @elseif (!empty($lr->doc_status->date_issued) &&
                                 !empty($lr->doc_status->date_received) &&
                                 !empty($lr->doc_status->date_issued_back) &&
                                 empty($lr->doc_status->date_received_back) &&
                                 $isAllowedReceiveBack)
                    <li class="list-group-item justify-content-between">
                        <button type="button" class="btn btn-outline-green waves-effect btn-block btn-md btn-rounded"
                                onclick="$(this).showReceiveBack('{{ route('ca-lr-show-receive-back', ['id' => $lr->id]) }}');">
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
@include('modals.liquidate')
@include('modals.print')
@include('modals.attachment')

@endsection

@section('custom-js')

<script type="text/javascript" src="{{ asset('assets/js/input-validation.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/liquidation.js') }}"></script>
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
