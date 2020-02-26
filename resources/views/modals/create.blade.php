<div class="modal custom-fullwidth-modal fade top" id="central-create-modal" tabindex="-1"
     role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-full-height modal-top" role="document">
        <!--Content-->
        <div class="modal-content">
            <!--Header-->
            <div class="modal-header stylish-color-dark white-text">
                <h6>
                    <i class="fas fa-cart-arrow-down"></i>
                    <span id="create-title"></span>
                </h6>
                <button type="button" class="close white-text" data-dismiss="modal"
                        aria-label="Close">
                    &times;
                </button>
            </div>

            <!--Body-->
            <div class="modal-body rgba-stylish-light transparent">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col d-flex justify-content-center">
                            <div class="card w-responsive">
                                <div class="card-body"><div id="modal-body-create"></div></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!--Footer-->
            <div class="modal-footer rgba-stylish-strong p-1">
                <button type="button" class="btn btn-indigo btn-sm waves-effect waves-light"
                        onclick="$(this).createUpdateDoc();">
                    <i class="fas fa-cart-arrow-down"></i> Create
                </button>
                <button type="button" class="btn btn btn-light btn-sm waves-effect" data-dismiss="modal">
                    <i class="far fa-window-close"></i> Cancel
                </button>
            </div>
        </div>
        <!--/.Content-->
    </div>
</div>
