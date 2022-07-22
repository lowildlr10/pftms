<div class="modal fade" id="print-modal" tabindex="-1"
     role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-notify modal-info" role="document" style="max-width: 950px;">
        <!--Content-->
        <div class="modal-content">
            <!--Header-->
            <div class="modal-header mdb-color  white-text">
                <h7 class="mt-1">
                    <i class="fas fa-print"></i>
                    <span id="print-title"></span>
                </h7>

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
                        <div class="md-form">
                            <select id="paper-size" class="mdb-select md-form required">
                                <option value="0">-- Choose a paper type --</option>

                                @if (isset($paperSizes) && count($paperSizes) > 0)
                                    @foreach ($paperSizes as $paper)
                                <option value="{{ $paper->id }}">
                                    {{ $paper->paper_type }} ({{ $paper->width }}x{{ $paper->height }}{{ $paper->unit }})
                                </option>
                                    @endforeach
                                @endif
                            </select>
                            <label class="mdb-main-label">
                                Paper Type <span class="red-text">*</span>
                            </label>
                        </div>
                        <div class="md-form">
                            <input type="number" id="font-size" class="form-control" min="-100" max="100" value="0"
                                   onchange="$('#incrdec-disp').text($(this).val());">
                            <label for="font-size" class="active">
                                Font Scaling: <span id="incrdec-disp">0</span>% <span class="red-text">*</span>
                            </label>
                        </div>
                        <input type="hidden" name="other_param" id="other_param">
                    </div>
                    <div class="col-md-9">
                        <div id="modal-print-content">
                            <object class="border border-primary z-depth-1-half" type="application/pdf"
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
                <button class="btn btn-primary btn-sm waves-effect" onclick="$(this).downloadPDF();">
                    <i class="fas fa-file-pdf"></i> Download as PDF
                </button>
                <button class="btn btn-primary btn-sm waves-effect" onclick="$(this).downloadImage();">
                    <i class="fas fa-image"></i> Download as Image
                </button>
                <button type="button" class="btn btn-outline-black btn-sm waves-effect"
                        data-dismiss="modal">Close</button>
            </div>
        </div>
        <!--/.Content-->
    </div>
</div>
