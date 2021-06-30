<div class="modal custom-fullwidth-modal fade top" id="modal-lg-show-full" tabindex="-1"
     role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-full-height modal-top" role="document">
        <!--Content-->
        <div class="modal-content">
            <!--Header-->
            <div class="modal-header stylish-color-dark white-text">
                <h7 class="mt-1">
                    <i class="fas fa-file-import"></i>
                    <span id="show-full-title"></span>
                </h7>
                <button type="button" class="close white-text" data-dismiss="modal"
                        aria-label="Close">
                    &times;
                </button>
            </div>

            <!--Body-->
            <div class="modal-body rgba-stylish-light transparent">
                <div class="card">
                    <div class="card-body">
                        <div id="modal-body-show-full" style="display: none;"></div>
                    </div>
                </div>
            </div>

            <!--Footer-->
            <div class="modal-footer rgba-stylish-strong p-1">
                <button type="button" class="btn btn btn-dark-green btn-sm waves-effect"
                        onclick="$(this).generateExcel();">
                    <i class="fas fa-file-excel"></i> Download as Excel
                </button>
                <button type="button" class="btn btn btn-light btn-sm waves-effect" data-dismiss="modal">
                    <i class="far fa-window-close"></i> Close
                </button>
            </div>
        </div>
        <!--/.Content-->
    </div>
</div>
