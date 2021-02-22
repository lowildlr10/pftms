<ul class="list-group z-depth-1">
    <li class="list-group-item justify-content-between text-center">
        <h5><strong><i class="fas fa-users fa-lg"></i> Issuee</strong></h5>
    </li>

	@if (count($issuedTo) > 0)
		@foreach($issuedTo as $emp)
    <li class="list-group-item justify-content-between">
        <div class="card z-depth-1">
            <div class="card-body">
                <h5>
                    <strong>
                        <i class="fas fa-user"></i>

                        @if (!empty($emp->middlename))
                        {{ $emp->firstname }} {{ $emp->middlename[0] }}. {{ $emp->lastname }}
                        @else
                        {{ $emp->firstname }} {{ $emp->lastname }}
                        @endif
                    </strong>

                    @if ($classificationAbrv == 'par' || $classificationAbrv == 'ics')
                    <button class="btn btn-outline-mdb-color btn-sm py-0 px-1 my-0 ml-1 mb-2 z-depth-0
                                   waves-effect waves-light"
                                   onclick="$(this).showPrint('{{ $inventoryNo }}',
                                                              'label',
                                                              '{{ $emp->emp_id }}');">
                        <i class="fas fa-barcode"></i> Generate Label
                    </button>
                    @endif
                </h5>
                <hr>
                <div class="btn-group btn-menu-1">
                    <button onclick="$(this).showEditIssue('{{ $inventoryNo }}',
                                                           '{{ $classificationAbrv }}',
                                                           '{{ $emp->emp_id }}');"
                       class="btn btn-outline-mdb-color btn-rounded btn-sm px-2 waves-effect">
                       <i class="fas fa-edit orange-text"></i> Edit
                    </button>
                    <button onclick="$(this).showPrint('{{ $inventoryNo }}',
                                                       '{{ $classificationAbrv }}',
                                                       '{{ $emp->emp_id }}');"
                       class="btn btn-outline-mdb-color btn-rounded btn-sm px-2 waves-effect">
                       <i class="fas fa-print text-info"></i> Print
                    </button>
                    <button onclick="$(this).delete('{{ $inventoryNo }}',
                                                    '{{ strtoupper($classificationAbrv) }}',
                                                    '{{ $emp->emp_id }}');"
                       class="btn btn-outline-mdb-color btn-rounded btn-sm px-2 waves-effect">
                       <i class="fas fa-trash red-text"></i> Delete
                    </button>
                </div>
            </div>
        </div>
    </li>
		@endforeach
	@else
    <li class="list-group-item justify-content-between">
        <h5 class="red-text">Not yet issued.</h5>
    </li>
    @endif

</ul>
<hr>
