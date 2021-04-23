<ul class="list-group p-0">
    <li class="list-group-item justify-content-between mb-3 z-depth-1">
        <h5>
            <strong>
                <i class="fas fa-list-ol"></i>

                Line Item Budget
            </strong>
        </h5>
        <hr>
        <div class="btn-group btn-menu-1">
            <button onclick="$(this).showPrint('{{ $id }}', 'fund_lib');"
               class="btn btn-outline-mdb-color btn-rounded btn-sm px-2 waves-effect">
               <i class="fas fa-print text-info"></i> Print
            </button>
        </div>
    </li>

    <li class="list-group-item justify-content-between mb-3 z-depth-1">
        <h5>
            <strong>
                <i class="fas fa-stream"></i>

                Realignments
            </strong>
        </h5>
        <hr>

        @if (count($realignments) > 0)
            @foreach ($realignments as $ctr => $realignment)
        <button onclick="$(this).showPrint('{{ $realignment->id }}', 'fund_lib_realignment');"
                class="btn btn-outline-mdb-color btn-rounded btn-block btn-sm px-2 waves-effect">
            <i class="fas fa-print text-info"></i> Print Realignment {{ $ctr + 1 }}
        </button>
            @endforeach
        @else
        <h6 class="text-center red-text">
            No realignment
        </h6>
        @endif
    </li>
</ul>
