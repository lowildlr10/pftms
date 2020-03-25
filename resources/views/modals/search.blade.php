<div class="modal fade top" id="top-fluid-modal" tabindex="-1"
     role="dialog" data-backdrop="static" aria-hidden="true"
     data-keyboard="false">
    <div class="modal-dialog modal-sm modal-primary" role="document">
        <div class="modal-content">

            <!--Body-->
            <div class="modal-body rgba-stylish-light transparent">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <!-- Search form -->
                                <div class="form-inline md-form form-sm">
                                    <i class="fa fa-search" aria-hidden="true"></i>
                                    <input name="search" class="form-control ml-3 w-75" type="search"
                                           placeholder="Search" id="search-box"
                                           autocomplete="off" aria-controls="dtmaterial"
                                           value="{{ !empty($keyword) ? $keyword : '' }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!--Footer-->
                    <div class="modal-footer p-1">
                        <button class="btn btn-mdb-color btn-sm waves-effect"
                                data-dismiss="modal" type="button">
                            <i class="far fa-window-close"></i> Close
                        </button>
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
