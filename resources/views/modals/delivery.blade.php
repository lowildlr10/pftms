<div class="modal fade top" id="modal-delivery" tabindex="-1"
     role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-top" role="document">
        <!--Content-->
        <div class="modal-content">
            <!--Header-->
            <div class="modal-header black white-text">
                <h7 class="mt-1">
                    <i class="fas fa-truck"></i>
                    <span id="delivery-title"></span>
                </h7>
                <button type="button" class="close white-text" data-dismiss="modal"
                        aria-label="Close">
                    &times;
                </button>
            </div>

            <!--Body-->
            <div class="modal-body p-4">
                <h6 id="modal-body-delivery"></h6>
                <form id="form-delivery" action="#" method="POST">@csrf</form>
            </div>

            <!--Footer-->
            <div class="modal-footer p-1">
                <button type="button" class="btn btn-black btn-sm waves-effect waves-light"
                        onclick="$(this).forDelivery();">
                        <i class="fas fa-truck"></i> For Delivery
                </button>
                <button type="button" class="btn btn btn-light btn-sm waves-effect" data-dismiss="modal">
                    <i class="far fa-window-close"></i> Close
                </button>
            </div>
        </div>
        <!--/.Content-->
    </div>
</div>

