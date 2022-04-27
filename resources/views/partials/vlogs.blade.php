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
        {{ $data->appends(Request::except('page'))->render('voucher-logs-pagination') }}
    </div>
</div>
