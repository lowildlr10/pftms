<!-- Success modal -->
<div class="modal fade right" id="modal-success" tabindex="-1" role="dialog"
     aria-hidden="true" data-backdrop="true">
    <div class="modal-dialog modal-side modal-bottom-right modal-notify
         modal-success" role="document">
        <!--Content-->
        <div class="modal-content rounded" style="background-color: #ffffffe6 !important;">
            <!--Header-->
            <div class="modal-header p-2">
                <p class="heading"><strong>Success</strong></p>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="white-text">×</span>
                </button>
            </div>

            <!--Body-->
            <div class="modal-body py-4">
                <div class="row rounded">
                    <div class="col-3">
                        <p></p>
                        <p class="text-center">
                            <i class="fa fa-check fa-3x mb-3 animated rotateIn green-text"></i>
                        </p>
                    </div>

                    <div class="col-9">
                        <p></p>
                        @if (!empty(session("success")))
                        <p>{{ session("success") }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <!--Footer-->
            <div class="modal-footer justify-content-center py-1">
                <a type="button" class="btn btn-light btn-sm waves-effect" data-dismiss="modal">Close</a>
            </div>
        </div>
        <!--/.Content-->
    </div>
</div>

<!-- Warning modal -->
<div class="modal fade right" id="modal-warning" tabindex="-1" role="dialog"
     aria-hidden="true" data-backdrop="true">
    <div class="modal-dialog modal-side modal-bottom-right modal-notify
         modal-warning" role="document">
        <!--Content-->
        <div class="modal-content rounded" style="background-color: #ffffffe6 !important;">
            <!--Header-->
            <div class="modal-header p-2">
                <p class="heading"><strong>Warning</strong></p>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="white-text">&times;</span>
                </button>
            </div>

            <!--Body-->
            <div class="modal-body py-4">
                <div class="row">
                    <div class="col-3">
                        <p></p>
                        <p class="text-center">
                            <i class="fas fa-exclamation-triangle fa-3x mb-3 animated jackInTheBox orange-text"></i>
                        </p>
                    </div>

                    <div class="col-9">
                        <p></p>
                        @if (!empty(session("warning")))
                        <p>{{ session("warning") }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <!--Footer-->
            <div class="modal-footer justify-content-center py-1">
                <a type="button" class="btn btn-light btn-sm waves-effect" data-dismiss="modal">Close</a>
            </div>
        </div>
        <!--/.Content-->
    </div>
</div>

<!-- Failed modal -->
<div class="modal fade right" id="modal-failed" tabindex="-1" role="dialog"
     aria-hidden="true" data-backdrop="true">
    <div class="modal-dialog modal-side modal-bottom-right modal-notify
         modal-danger" role="document">
        <!--Content-->
        <div class="modal-content rounded" style="background-color: #ffffffe6 !important;">
            <!--Header-->
            <div class="modal-header">
                <p class="heading"><strong>Failed</strong></p>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="white-text">&times;</span>
                </button>
            </div>

            <!--Body-->
            <div class="modal-body py-4">
                <div class="row">
                    <div class="col-3">
                        <p></p>
                        <p class="text-center">
                            <i class="fas fa-exclamation-circle fa-3x mb-3 animated bounceIn red-text"></i>
                        </p>
                    </div>

                    <div class="col-9">
                        <p></p>
                        @if (!empty(session("failed")))
                        <p>{{ session("failed") }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <!--Footer-->
            <div class="modal-footer justify-content-center py-1">
                <a type="button" class="btn btn-light btn-sm waves-effect" data-dismiss="modal">Close</a>
            </div>
        </div>
        <!--/.Content-->
    </div>
</div>
