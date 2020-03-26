<div class="modal fade right mt-5" id="top-fluid-modal" tabindex="-1"
     role="dialog" data-backdrop="static" aria-hidden="true"
     data-keyboard="false">
    <div class="modal-dialog modal-sm  modal-side modal-top-right" role="document">
        <div class="modal-content">

            <!--Header-->
            <div class="modal-header">
                <button class="btn p-3 btn-rounded btn-link btn-sm waves-effect close"
                        data-dismiss="modal" type="button">
                    close <i class="fas fa-angle-right"></i>
                </button>
            </div>

            <!--Body-->
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <!-- Search form -->
                        <form class="form-inline d-flex justify-content-center md-form form-sm mt-0 py-4"
                              form action="{{ url()->current() }}" method="POST">
                            @csrf

                            <i class="fas fa-search" aria-hidden="true"></i>
                            <input class="form-control form-control-sm ml-3 w-75" type="search"
                                   placeholder="Search for keyword..."
                                   aria-label="Search" value="{{ !empty($keyword) ? $keyword : '' }}"
                                   autocomplete="off" id="search" name="keyword">
                            <small class="font-italic grey-text">
                                Press enter after the search bar is filled.
                            </small>

                            @if (!empty($keyword))
                            <a class="btn btn-link btn-sm btn-block red-text" href="{{ url()->current() }}">
                                <i class="fas fa-times-circle"></i> Clear Search
                            </a>
                            @endif
                        </form>
                    </div>

                </div>
            </div>
        </div>
        <!--/.Content-->

    </div>
</div>

<form action="#" method="POST" id="search-keyword">
    @csrf
    <input type="hidden" id="keyword" name="keyword">
</form>
