<div class="animated fadeIn">
    <div id="search-container" class="col-md-12">
        @if (!empty($search))
        <label class="red-text">
            You searched for "{{ $search }}"
        </label>
        @endif
    </div>

    <div class="table-wrapper table-responsive">
        @yield('log-content')
    </div>
    <div>

        @if (isset($module))

            @switch($module)
                @case('pr-rfq')
                {{ $prRfqData->appends(Request::except('page'))->appends(['module' => $module])->render('voucher-logs-pagination') }}
                    @break
                @case('rfq-abstract')
                {{ $rfqAbsData->appends(Request::except('page'))->appends(['module' => $module])->render('voucher-logs-pagination') }}
                    @break
                @case('abstract-po')
                {{ $absPoData->appends(Request::except('page'))->appends(['module' => $module])->render('voucher-logs-pagination') }}
                    @break
                @case('po-ors')
                {{ $poOrsData->appends(Request::except('page'))->appends(['module' => $module])->render('voucher-logs-pagination') }}
                    @break
                @case('po-iar')
                {{ $poIarData->appends(Request::except('page'))->appends(['module' => $module])->render('voucher-logs-pagination') }}
                    @break
                @case('iar-stock')
                {{ $iarStockData->appends(Request::except('page'))->appends(['module' => $module])->render('voucher-logs-pagination') }}
                    @break
                @case('iar-dv')
                {{ $iarDvData->appends(Request::except('page'))->appends(['module' => $module])->render('voucher-logs-pagination') }}
                    @break
                @case('ors-dv')
                {{ $orsDvData->appends(Request::except('page'))->appends(['module' => $module])->render('voucher-logs-pagination') }}
                    @break
                @default

            @endswitch

        @else

        @endif

    </div>
</div>
