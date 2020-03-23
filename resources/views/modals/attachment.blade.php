<div class="modal fade top" id="modal-attachment" tabindex="-1" role="dialog"
     data-backdrop="true"  data-keyboard="false" style="display: none; padding-right: 17px;">
    <div class="modal-dialog cascading-modal" role="document">
        <!--Content-->
        <div class="modal-content">
            <div class="modal-c-tabs">
                <ul class="nav nav-tabs md-tabs tabs-2 blue-grey darken-2 p-2" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#body-attachments" role="tab"
                           onclick="$(this).toggleModalBody('#modal-body-attachment', 'attachment');"
                           id="btn-attachments">
                            <i class="fas fa-paperclip"></i> Attachments
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#add-attachment" role="tab"
                           onclick="$(this).toggleModalBody('#add-attachments', 'add');"
                           id="btn-add-attachments">
                            <i class="fas fa-file-upload"></i> Upload
                        </a>
                    </li>
                </ul>
                <!-- Tab panels -->
                <div class="tab-content">
                    <div class="tab-pane fade in show active" id="body-attachments" role="tabpanel">
                        <!--Body-->
                        <div id="modal-body-attachment" class="modal-body">

                        </div>
                        <!--Footer-->
                        <div class="modal-footer">
                            <button type="button" class="btn btn btn-light btn-sm waves-effect" data-dismiss="modal">
                                <i class="far fa-window-close"></i> Close
                            </button>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="add-attachment" role="tabpanel">
                        <!--Body-->
                        <div id="modal-body-add-attachment" class="modal-body">
                            <form class="md-form">
                                @csrf
                                <div class="form-group">
                                    <div class="file-field">
                                        <div class="btn btn-outline-black btn-sm float-left">
                                            <span>
                                                <i class="fas fa-file-import"></i> Import
                                            </span>
                                            <input id="attachment" name="attachment[]" type="file" accept="application/pdf"
                                                   onchange="$(this).initUpload('proc-rfq')" multiple>
                                        </div>
                                        <div class="file-path-wrapper">
                                            <input class="file-path validate" type="text"
                                                placeholder="Upload one or more files">
                                        </div>
                                    </div>
                                </div>
                                <div id="new-attachments" class="form-group collapse">
                                    <div class="card">
                                        <div class="card-body">
                                                <h6 class="card-title">Recently added:</h6>
                                            <p id="body-new-attachments" class="card-text"></p>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!--Footer-->
                        <div class="modal-footer">
                            <button type="button" class="btn btn btn-light btn-sm waves-effect" data-dismiss="modal">
                                <i class="far fa-window-close"></i> Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
