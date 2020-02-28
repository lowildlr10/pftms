<div class="modal custom-fullwidth-modal fade top" id="central-edit-modal" tabindex="-1"
     role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-full-height modal-top" role="document">
        <!--Content-->
        <div class="modal-content">
            <!--Header-->
            <div class="modal-header stylish-color-dark white-text">
                <h7 class="mt-1">
                    <i class="fas fa-edit"></i>
                    <span id="edit-title"></span>
                </h7>
                <button type="button" class="close white-text" data-dismiss="modal"
                        aria-label="Close">
                    &times;
                </button>
            </div>

            <!--Body-->
            <div class="modal-body rgba-grey-slight">
                <div id="modal-body-edit">
                    <div class="mt-5" style="height: 150px;">
                        <center>
                            <div class="preloader-wrapper big active crazy">
                                <div class="spinner-layer spinner-blue-only">
                                    <div class="circle-clipper left">
                                        <div class="circle"></div>
                                    </div>
                                    <div class="gap-patch">
                                        <div class="circle"></div>
                                    </div>
                                    <div class="circle-clipper right">
                                        <div class="circle"></div>
                                    </div>
                                </div>
                            </div><br>
                        </center>
                    </div>
                </div>
            </div>

            <!--Footer-->
            <div class="modal-footer rgba-stylish-strong p-1">
                <button type="button" class="btn btn-orange btn-sm waves-effect waves-light"
                        onclick="$(this).createUpdateDoc();">
                    <i class="fas fa-edit"></i> Update
                </button>
                <button type="button" class="btn btn btn-light btn-sm waves-effect" data-dismiss="modal">
                    <i class="far fa-window-close"></i> Cancel
                </button>
            </div>
        </div>
        <!--/.Content-->
    </div>
</div>
