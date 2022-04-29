@extends('layouts.app')

@section('main-content')

<div class="row wow animated fadeIn">
    <section class="mb-5 col-12 module-container">
        <div class="card mdb-color darken-3">
            <div class="card-body">
                <h5 class="card-title white-text">
                    <strong>
                        <i class="fas fa-chalkboard"></i> Voucher Tracking (Search All)
                    </strong>
                </h5>
                <hr class="white hidden-xs">
                <ul class="breadcrumb mdb-color darken-3 mb-0 p-1 white-text hidden-xs">
                    <li>
                        <i class="fa fa-caret-right mx-2" aria-hidden="true"></i>
                    </li>
                    <li class="active">
                        <a href="#" class="waves-effect waves-light cyan-text" onclick="location.reload();">
                            Voucher Tracking
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
                            <a class="btn btn-outline-white btn-rounded btn-sm px-2"
                               onclick="$(this).generateExcel('track-all-{{ date('Y-m-d') }}', 'tab-search-content');">
                                <i class="fas fa-file-excel"></i> Download All
                            </a>
                        </div>
                        <div>
                            <a class="btn btn-outline-white btn-rounded btn-sm toggle-sidebar px-2"
                               onclick="$('#vtrack-dropdown').addClass('active');$('#vtrack-dropdown').find('.sidebar-submenu').css('display', 'block');$('#vtrack-search').focus();">
                                <i class="fas fa-search"></i> {{ !empty($keyword) ? (strlen($keyword) > 15) ?
                                'Search: '.substr($keyword, 0, 15).'...' : 'Search: '.$keyword : '' }}
                            </a>
                            <a href="#" class="btn btn-outline-white btn-rounded btn-sm px-2" onclick="location.reload();">
                                <i class="fas fa-sync-alt fa-pulse"></i>
                            </a>
                        </div>
                    </div>
                    <!--/Card image-->

                    <div class="px-2">
                        <input type="hidden" id="search" value="1">
                        <input type="hidden" id="keyword" value="{{ $keyword }}">

                        <ul class="nav nav-tabs" id="myTab" role="tablist">

                            @foreach ($modules as $keyID => $module)
                            <li class="nav-item">
                              <a class="nav-link {{ $counter == 1 ? 'active' : '' }}"
                                 id="{{ $keyID }}-tab" data-toggle="tab" href="#{{ $keyID }}"
                                 role="tab" aria-controls="{{ $keyID }}"
                                 aria-selected="true">{{ $module }}</a>
                            </li>
                                @php $counter++; @endphp
                            @endforeach

                            @php $counter = 1; @endphp
                        </ul>
                        <div class="tab-content" id="tab-search-content">

                            @foreach ($modules as $keyID => $module)
                            <div class="tab-pane fade {{ $counter == 1 ? 'show active' : '' }}"
                                    id="{{ $keyID }}" role="tabpanel" aria-labelledby="{{ $keyID }}-tab">
                                <div class="table-generate" id="table-generate-{{ $keyID }}"></div>
                                <div class="download-section"></div>
                            </div>
                                @php $counter++; @endphp
                            @endforeach

                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

@endsection

@section('custom-js')

<script src="{{ asset('assets/js/voucher-logs.js') }}"></script>
<script src="{{ asset('js/xlsx.full.min.js') }}"></script>
<script src="{{ asset('js/FileSaver.min.js') }}"></script>

@endsection
