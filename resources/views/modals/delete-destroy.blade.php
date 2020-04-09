<div class="modal fade top" id="modal-delete" tabindex="-1"
     role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-top" role="document">
        <!--Content-->
        <div class="modal-content">
            <!--Header-->
            <div class="modal-header danger-color-dark white-text">
                <h7 class="mt-1">
                    <i class="fas fa-trash"></i>
                    <span id="delete-title"></span>
                </h7>
                <button type="button" class="close white-text" data-dismiss="modal"
                        aria-label="Close">
                    &times;
                </button>
            </div>

            <!--Body-->
            <div class="modal-body p-4">
                <h6 id="modal-body-delete"></h6>
                <form id="form-delete" action="#" method="POST">
                    @csrf

                    @if ($isAllowedDestroy)
                    <div class="custom-control custom-checkbox pt-3">
                        <input type="checkbox" class="custom-control-input" id="check-destroy"
                               name="destroy" value="1">
                        <label class="custom-control-label grey-text" for="check-destroy">
                            Delete the data permanently.
                        </label>
                    </div>
                    @endif
                </form>
            </div>

            <!--Footer-->
            <div class="modal-footer p-1">
                <button type="button" class="btn btn-red btn-sm waves-effect waves-light"
                        onclick="$(this).delete();">
                        <i class="fas fa-trash"></i> Delete
                </button>
                <button type="button" class="btn btn btn-light btn-sm waves-effect" data-dismiss="modal">
                    <i class="far fa-window-close"></i> Close
                </button>
            </div>
        </div>
        <!--/.Content-->
    </div>
</div>

