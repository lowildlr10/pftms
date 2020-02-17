<div class="paginator-container border border-top">
    <div class="d-flex flex-row">
        <div class="px-3 pt-2">
            <label class="black-text" style="font-size: 0.9em;">
                Showing {{($paginator->currentpage()-1)*$paginator->perpage()+1}} to
                {{$paginator->currentpage() * $paginator->perpage()}} of {{$paginator->total()}} entries
            </label>
        </div>
    </div>

    <div class="d-flex flex-row-reverse" style="overflow: auto;">
        <div class="p-2">
            <div class="black-text">
                <ul class="pagination">

                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <li class="paginate_button page-item previous disabled" id="dtBasicExample_previous">
                            <a href="#" aria-controls="dtBasicExample"
                            data-dt-idx="0" tabindex="0" class="page-link">
                                Previous
                            </a>
                        </li>
                    @else
                        <li class="paginate_button page-item previous" id="dtBasicExample_previous">
                            <a href="{{ $paginator->previousPageUrl() }}" aria-controls="dtBasicExample"
                            data-dt-idx="0" tabindex="0" class="page-link">
                                Previous
                            </a>
                        </li>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <li class="disabled"><span>{{ $element }}</span></li>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <li class="paginate_button page-item active hidden-xs">
                                        <a href="#" aria-controls="dtBasicExample" data-dt-idx="1"
                                        tabindex="0" class="page-link mdb-color darken-2 rounded">
                                            {{ $page }}
                                        </a>
                                    </li>
                                @else
                                    <li class="paginate_button page-item hidden-xs">
                                        <a href="{{ $url }}" aria-controls="dtBasicExample" data-dt-idx="1"
                                        tabindex="0" class="page-link">
                                            {{ $page }}
                                        </a>
                                    </li>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <li class="paginate_button page-item next" id="dtBasicExample_next">
                            <a href="{{ $paginator->nextPageUrl() }}" aria-controls="dtBasicExample"
                            data-dt-idx="7" tabindex="0" class="page-link">Next</a>
                        </li>
                    @else
                        <li class="paginate_button page-item next disabled" id="dtBasicExample_next">
                            <a href="{{ $paginator->nextPageUrl() }}" aria-controls="dtBasicExample"
                            data-dt-idx="7" tabindex="0" class="page-link">Next</a>
                        </li>
                    @endif

                </ul>
            </div>
        </div>
    </div>
</div>
