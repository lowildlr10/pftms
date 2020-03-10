<div class="modal fade" id="print-modal" tabindex="-1"
     role="dialog" data-keyboard="false" style="display: none;">
    <div class="modal-dialog modal-lg modal-notify modal-info" role="document" style="max-width: 950px;">
        <!--Content-->
        <div class="modal-content">
            <!--Header-->
            <div class="modal-header mdb-color">
                <p class="heading lead"><i class="fas fa-print"></i> <label id="print-title"></label></p>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="white-text">×</span>
                </button>
            </div>

            <!--Body-->
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-3">
                        <label><i class="fas fa-cog"></i> Settings</label>
                        <hr>
                        <div class="form-group">
                            <label>Select Paper Size:</label>
                            <select id="paper-size" class="mdb-select-print md-form colorful-select dropdown-primary">

                                @if (!empty($paperSizes) && isset($paperSizes))
                                    @foreach ($paperSizes as $paper)
                                <option value="{{ $paper->id }}">{{ $paper->paper_size }}</option>
                                    @endforeach
                                @endif

                            </select>
                        </div>
                        <div class="form-group">
                            <label>
                                Increase/Decrease Font Size by: ( <span id="incrdec-disp">0</span>% )
                            </label>
                            <input type="number" id="font-size" class="form-control" min="-100" max="100" value="0"
                                   onchange="$('#incrdec-disp').text($(this).val());">
                        </div>
                        <input type="hidden" name="other_param" id="other_param">
                    </div>
                    <div class="col-md-9">
                        <div id="modal-print-content">
                            <object class="border border-primary" type="application/pdf"
                                    width="100%" height="650">
                                <form method="POST" target="_blank">
                                    @csrf
                                    <input type="hidden" id="inp-document-type" name="document_type">
                                    <input type="hidden" id="inp-preview-toggle" name="preview_toggle">
                                    <input type="hidden" id="inp-font-scale" name="font_scale">
                                    <input type="hidden" id="inp-paper-size" name="paper_size">
                                    <input type="hidden" id="inp-other-param" name="other_param">
                                    <button type="submit" class="btn btn-link">
                                        <i class="fas fa-file-pdf"></i> Preview
                                    </button>
                                </form>
                            </object>
                        </div>
                    </div>
                </div>
            </div>

            <!--Footer-->
            <div class="modal-footer">
                <button class="btn btn-primary btn-sm waves-effect" onclick="$(this).download();">
                    <i class="fas fa-download"></i> Download
                </button>
                <button type="button" class="btn btn-outline-black btn-sm waves-effect"
                        data-dismiss="modal">Close</button>
            </div>
        </div>
        <!--/.Content-->
    </div>
</div>
