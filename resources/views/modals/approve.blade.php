<div class="modal fade top" id="modal-approve" tabindex="-1"
     role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-top" role="document">
        <!--Content-->
        <div class="modal-content">
            <!--Header-->
            <div class="modal-header success-color-dark white-text">
                <h7 class="mt-1">
                    <i class="fas fa-thumbs-up"></i>
                    <span id="approve-title"></span>
                </h7>
                <button type="button" class="close white-text" data-dismiss="modal"
                        aria-label="Close">
                    &times;
                </button>
            </div>

            <!--Body-->
            <div class="modal-body p-4">
                <h6 id="modal-body-approve"></h6>
                <form id="form-approve" action="#" method="POST">@csrf</form>
            </div>

            <!--Footer-->
            <div class="modal-footer p-1">
                <button type="button" class="btn btn-green btn-sm waves-effect waves-light"
                        onclick="$(this).approve();">
                        <i class="fas fa-thumbs-up"></i> Approve
                </button>
                <button type="button" class="btn btn btn-light btn-sm waves-effect" data-dismiss="modal">
                    <i class="far fa-window-close"></i> Close
                </button>
            </div>
        </div>
        <!--/.Content-->
    </div>
</div>

