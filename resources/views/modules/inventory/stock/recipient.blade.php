<ul class="list-group p-0">
	@if (count($invStockIssues) > 0)
		@foreach($invStockIssues as $inv)
    <li class="list-group-item justify-content-between mb-3 z-depth-1">
        <h5>
            <strong>
                <i class="fas fa-user"></i>

                @if (!empty($inv->recipient['middlename']))
                {{ $inv->recipient['firstname'] }} {{ $inv->recipient['middlename'][0] }}. {{ $inv->recipient['lastname'] }}
                @else
                {{ $inv->recipient['firstname'] }} {{ $inv->recipient['lastname'] }}
                @endif
            </strong>

            @if ($inv->classification == 'par' || $inv->classification == 'ics')
            <button class="btn btn-outline-mdb-color btn-sm py-0 px-1 my-0 ml-1 mb-2 z-depth-0
                           waves-effect waves-light"
                           onclick="$(this).showPrint('{{ $inv->id }}', 'inv_label');">
                <i class="fas fa-barcode"></i> Generate Label
            </button>
            @endif
        </h5>
        <hr>
        <div class="btn-group btn-menu-1">
            @if ($isAllowedUpdate)
            <button onclick="$(this).showUpdateIssueItem(`{{ route('stocks-show-update-issue-item', [
                        'invStockIssueID' => $inv->id,
                        'classification' => $inv->classification
                    ]) }}`);"
               class="btn btn-outline-mdb-color btn-rounded btn-sm px-2 waves-effect">
               <i class="fas fa-edit orange-text"></i> Edit
            </button>
            @endif

            <button onclick="$(this).showPrint('{{ $inv->id }}', 'inv_{{ $inv->classification }}');"
               class="btn btn-outline-mdb-color btn-rounded btn-sm px-2 waves-effect">
               <i class="fas fa-print text-info"></i> Print
            </button>

            @if ($isAllowedDelete || $isAllowedDestroy)
            <button onclick="$(this).showDeleteIssue('{{ route('stocks-delete-issue', [
                        'invStockIssueID' => $inv->id
                    ]) }}', '{{ $inv->recipient['firstname'] }}');"
               class="btn btn-outline-mdb-color btn-rounded btn-sm px-2 waves-effect">
               <i class="fas fa-trash red-text"></i> Delete
            </button>
            @endif
        </div>
    </li>
		@endforeach
	@else
    <h6 class="text-center red-text">
        Not yet issued.
    </h6>
    @endif
</ul>
