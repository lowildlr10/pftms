$(function() {
    let isSelectInitiated = false;
    let key, documentType, otherParam;

	$('#paper-size').unbind('change').change(function() {
    	const otherParam = $('#other_param').val(),
              paperSize = $('#paper-size').val(),
              fontSize = $('#font-size').val(),
    	      url = `${baseURL}/print/${key}/?document_type=${documentType}&preview_toggle=preview
                  &font_scale=${fontSize}&paper_size=${paperSize}&other_param=${otherParam}`,
              urlPost = `${baseURL}/print/${key}`;

        $('#inp-document-type').val(documentType);
        $('#inp-preview-toggle').val('download');
        $('#inp-font-scale').val(fontSize);
        $('#inp-paper-size').val(paperSize);
        $('#inp-other-param').val(otherParam);

    	printDoc(url, documentType, urlPost);
    });

    $('#font-size').unbind('change').change(function() {
    	const otherParam = $('#other_param').val(),
              paperSize = $('#paper-size').val(),
              fontSize = $('#font-size').val(),
    	      url = `${baseURL}/print/${key}/?document_type=${documentType}&preview_toggle=preview
                  &font_scale=${fontSize}&paper_size=${paperSize}&other_param=${otherParam}`,
              urlPost = `${baseURL}/print/${key}`;

        $('#inp-document-type').val(documentType);
        $('#inp-preview-toggle').val('download');
        $('#inp-font-scale').val(fontSize);
        $('#inp-paper-size').val(paperSize);
        $('#inp-other-param').val(otherParam);

    	printDoc(url, documentType, urlPost);
    });

    $.fn.showPrint = function(_key, _documentType, _otherParam = "") {
        const paperSize = $('#paper-size').val(),
              fontSize = $('#font-size').val(),
              url = `${baseURL}/print/${_key}/?document_type=${_documentType}&preview_toggle=preview` +
                    `&font_scale=${fontSize}&paper_size=${paperSize}&test=true&other_param=${_otherParam}`;

        key = _key;
        documentType = _documentType;
        otherParam = _otherParam;

        switch (documentType) {
            case 'proc_pr':
                $('#print-title').html('Generate Purchase Request');
                break;
            case 'proc_rfq':
                $('#print-title').html('Generate Request for Quotation');
                break;
            case 'proc_abstract':
                $('#print-title').html('Generate Abstract of Bids and Quotation');
                break;
            case 'proc_po':
                $('#print-title').html('Generate Purchase Order');
                break;
            case 'proc_jo':
                $('#print-title').html('Generate Job Order');
                break;
            case 'proc_ors':
                $('#print-title').html('Generate Obligation Request Status');
                break;
            case 'proc_burs':
                $('#print-title').html('Generate Budget Utilization Request Status');
                break;
            case 'ca_ors':
                $('#print-title').html('Generate Obligation Request Status');
                break;
            case 'ca_burs':
                $('#print-title').html('Generate Budget Utilization Request Status');
                break;
            case 'proc_iar':
                $('#print-title').html('Generate Inspection and Acceptance Report');
                break;
            case 'proc_dv':
                $('#print-title').html('Generate Disbursement Voucher');
                break;
            case 'ca_dv':
                $('#print-title').html('Generate Disbursement Voucher');
                break;
            case 'ca_lr':
                $('#print-title').html('Generate Liquidation Report');
                break;
            case 'pay_lddap':
                $('#print-title').html('Generate List of Due and Demandable Accounts Payable');
                break;
            case 'inv_par':
                $('#print-title').html('Generate Property Acknowledgement Reciept');
                break;
            case 'inv_ris':
                $('#print-title').html('Generate Requisition and Issue Slip');
                break;
            case 'inv_ics':
                $('#print-title').html('Generate Inventory Custodian Slip');
                break;
            case 'inv_label':
                $('#print-title').html('Generate Property Label Tag');
                break;

            default:
                break;
        }

    	$.ajax({
		    url: url,
		    success: function(data) {
                $("#print-modal").modal({keyboard: false, backdrop: 'static'})
                                 .on('shown.bs.modal', function() {
					if (isSelectInitiated == false) {
						isSelectInitiated = true;
                    }

                    $('#other_param').val(otherParam);

                    $('#inp-document-type').val(documentType);
                    $('#inp-preview-toggle').val('download');
                    $('#inp-font-scale').val(fontSize);
                    $('#inp-paper-size').val(paperSize);
                    $('#inp-other-param').val(otherParam);
				}).on('hidden.bs.modal', function() {
                    $('#modal-print-content').html($('#modal-print-content').html());
                    $('#modal-print-content object').removeAttr('data').removeAttr('url');
                    $('#modal-print-content object form').removeAttr('target');

                    $('#document-type').val('');
                    $('#preview-toggle').val('');
                    $('#font-size').val(0);
                    $('#paper-size').val('0');
                    $('#other-param').val('');

                    $('#inp-document-type').val('');
                    $('#inp-preview-toggle').val('');
                    $('#inp-font-scale').val('');
                    $('#inp-paper-size').val('');
                    $('#inp-other-param').val('');
				});
		    },
		    error: function(xhr, error){
                alert('Please fill-up all the required(*) fields first before printing the document.');
		    }
		});
	}

	$.fn.download = function() {
		const paperSize = $('#paper-size').val(),
    	      fontSize = $('#font-size').val(),
    	      otherParam = $('#other_param').val(),
    	      url = urlPost = `${baseURL}/print/${key}`;

        $('#inp-document-type').val(documentType);
        $('#inp-preview-toggle').val('download');
        $('#inp-font-scale').val(fontSize);
        $('#inp-paper-size').val(paperSize);
        $('#inp-other-param').val(otherParam);
        $('#modal-print-content object form').attr('action', url).submit();
    }

    function printDoc(url, documentType, urlPost = "") {
        $('#mdb-preloader').css('background', '#000000ab')
                           .fadeIn(300, function() {
            $.ajax({
                url: url,
                success: function(data) {
                    $('#modal-print-content object').attr('data', url);
                    $('#modal-print-content object form').attr('action', urlPost);
                    $('#mdb-preloader').fadeOut(300);
                },
                error: function(xhr, error){
                    $('#mdb-preloader').fadeOut(300);
                    alert('Please fill-up all the required(*) fields first before printing the document.');
                }
            });
        });
    }

    $('#paper-size').materialSelect();
});
