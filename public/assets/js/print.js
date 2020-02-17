$(function() {
    var isSelectInitiated = false;
    var documentType, key;

	$('#paper-size').unbind('change').change(function() {
    	var otherParam = $('#other_param').val();
    	var paperSize = $('#paper-size').val();
    	var fontSize = $('#font-size').val();
    	var url = baseURL + '/print/' + key + '/?document_type=' + documentType + '&preview_toggle=preview' +
                  '&font_scale=' + fontSize + '&paper_size=' + paperSize + '&other_param=' + otherParam;
        var urlPost = baseURL + '/print/' + key;

        $('#inp-document-type').val(documentType);
        $('#inp-preview-toggle').val('download');
        $('#inp-font-scale').val(fontSize);
        $('#inp-paper-size').val(paperSize);
        $('#inp-other-param').val(otherParam);

    	printDoc(url, documentType, urlPost);
    });

    $('#font-size').unbind('change').change(function() {
    	var otherParam = $('#other_param').val();
    	var paperSize = $('#paper-size').val();
    	var fontSize = $('#font-size').val();
    	var url = baseURL + '/print/' + key + '/?document_type=' + documentType + '&preview_toggle=preview' +
                  '&font_scale=' + fontSize + '&paper_size=' + paperSize + '&other_param=' + otherParam;
        var urlPost = baseURL + '/print/' + key;

        $('#inp-document-type').val(documentType);
        $('#inp-preview-toggle').val('download');
        $('#inp-font-scale').val(fontSize);
        $('#inp-paper-size').val(paperSize);
        $('#inp-other-param').val(otherParam);

    	printDoc(url, documentType, urlPost);
    });

    $.fn.showPrint = function(_key, _documentType, _otherParam = "") {
    	var paperSize = "";
    	var fontSize = 0;
    	var url = "";
    	var dateFrom, dateTo;
    	var otherParam = _otherParam;

     	documentType = _documentType;
    	key = _key;

        $('#other_param').val(_otherParam);

    	if (_documentType == 'pr') {
    		$('#paper-size').val(2);
    		$('#print-title').html('Generate Purchase Request');
    	} else if (_documentType == 'rfq') {
    		$('#paper-size').val(2);
    		$('#print-title').html('Generate Request for Quotation');
    	} else if (_documentType == 'abstract') {
    		$('#paper-size').val(3);
    		$('#print-title').html('Generate Abstract of Bids and Quotation');
    	} else if (_documentType == 'po-jo') {
    		$('#paper-size').val(2);
    		$('#print-title').html('Generate Purchase/Job Order');
    	} else if (_documentType == 'ors-burs') {
    		$('#paper-size').val(2);
    		$('#print-title').html('Generate Obligation/Budget Utilization Request Status');
    	} else if (_documentType == 'cashadvance-ors-burs') {
    		$('#paper-size').val(2);
    		$('#print-title').html('Generate Obligation/Budget Utilization Request Status');
    	} else if (_documentType == 'iar') {
    		$('#paper-size').val(2);
    		$('#print-title').html('Generate Inspection and Acceptance Report');
    	} else if (_documentType == 'dv') {
    		$('#paper-size').val(2);
    		$('#print-title').html('Generate Disbursement Voucher');
    	} else if (_documentType == 'cashadvance-dv') {
    		$('#paper-size').val(2);
    		$('#print-title').html('Generate Disbursement Voucher');
    	} else if (_documentType == 'liquidation') {
    		$('#paper-size').val(2);
    		$('#print-title').html('Generate Liquidation Report');
    	} else if (_documentType == 'lddap') {
    		$('#paper-size').val(2);
    		$('#print-title').html('Generate List of Due and Demandable Accounts Payable');
    	} else if (_documentType == 'par') {
    		$('#paper-size').val(2);
    		$('#print-title').html('Generate Property Acknowledgement Reciept');
    	} else if (_documentType == 'ris') {
    		$('#paper-size').val(2);
    		$('#print-title').html('Generate Requisition and Issue Slip');
    	} else if (_documentType == 'ics') {
    		$('#paper-size').val(2);
    		$('#print-title').html('Generate Inventory Custodian Slip');
    	} else if (_documentType == 'label') {
    		$('#paper-size').val(1);
    		$('#print-title').html('Generate Property Label Tag');
    	} else if (_documentType == 'voucher-logs') {
    		$('#paper-size').val(3);
    		$('#print-title').html('Generate Voucher Tracking');

    		dateFrom = $('#date-from').val();
    		dateTo = $('#date-to').val();
    		otherParam = dateFrom + "," + dateTo + "," + _otherParam;
    		$('#other_param').val(otherParam);
    	}

    	paperSize = $('#paper-size').val();

    	url = baseURL + '/print/' + key + '/?document_type=' + documentType + '&preview_toggle=preview' +
    		  '&font_scale=' + fontSize + '&paper_size=' + paperSize + '&test=true' + '&other_param=' + otherParam;

        $('#inp-document-type').val(documentType);
        $('#inp-preview-toggle').val('download');
        $('#inp-font-scale').val(fontSize);
        $('#inp-paper-size').val(paperSize);
        $('#inp-other-param').val(otherParam);

    	$.ajax({
		    url: url,
		    success: function(data) {
		    	url = baseURL + '/print/' + key + '/?document_type=' + documentType + '&preview_toggle=preview' +
                      '&font_scale=' + fontSize + '&paper_size=' + paperSize + '&test=false'  + '&other_param=' + otherParam;

                $('#modal-print-content object').attr('data', url);
                $('#modal-print-content object form').attr('action', url);
		    	$("#print-modal").modal().on('shown.bs.modal', function() {
					if (isSelectInitiated == false) {
						$('.mdb-select-print').materialSelect();
						isSelectInitiated = true;
					}
				}).on('hidden.bs.modal', function() {
                    $('#modal-print-content').html($('#modal-print-content').html());
                    $('#modal-print-content object').removeAttr('data').removeAttr('url');
                    $('#modal-print-content object form').removeAttr('target');

                    $('#document-type').val('');
                    $('#preview-toggle').val('');
                    $('#font-scale').val('');
                    $('#paper-size').val('');
                    $('#other-param').val('');

                    documentType = '';
                    key = '';
				});
		    },
		    error: function(xhr, error){
		        if (documentType == 'rfq') {
		        	alert('Complete first the field for "RFQ Date" and "Signatory".');
		        } else if (documentType == 'abstract') {
		        	alert('Complete first the field for "Abstract Date" and "Modes of Procurement".');
		        } else if (documentType == 'po-jo') {
		        	alert('Complete first the field for "PO Date", "Mode of Procurement", "Place of Delivery", ' +
		        		  '"Delivery Term", "Date of Delivery", "Payment Term", "Total Amount in Words", ' +
		        		  'and "Signatories" for Purchase Order or "JO Date", "Place of Delivery", "Date of Delivery", ' +
		        		  '"Payment Term", "Amount Words", and "Signatories" for Job Order.');
		        } else if (documentType == 'ors-burs') {
		        	alert('Complete first the field for "Serial Number", "ORS/BURS Date" and "Signatories".');
		        } else if (documentType == 'cashadvance-ors-burs') {
		        	alert('Complete first the field for "Serial Number", "ORS/BURS Date" and "Signatories".');
		        }  else if (documentType == 'iar') {
		        	alert('Complete first the field for "IAR Date" and "Signatories".');
		        } else if (documentType == 'dv') {
		        	alert('Complete first the field for "DV No" and IAR Date".');
		        } else if (documentType == 'cashadvance-dv') {
		        	alert('Complete first the field for "DV No" and IAR Date".');
		        } else if (documentType == 'liquidation') {
		        	alert('Complete first the field for Liquidation Date".');
		        }
		    }
		});
	}

	$.fn.download = function() {
		var paperSize = $('#paper-size').val();
    	var fontSize = $('#font-size').val();
    	var otherParam = $('#other_param').val();
    	var url = baseURL + '/print/' + key;

        //$('#modal-print-content object').attr('data', url);
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
                    if (documentType == 'rfq') {
                        alert('Complete first the field for "RFQ Date" and "Signatory".');
                    } else if (documentType == 'abstract') {
                        alert('Complete first the field for "Abstract Date" and "Modes of Procurement".');
                    } else if (documentType == 'po-jo') {
                        alert('Complete first the field for "PO Date", "Mode of Procurement", "Place of Delivery", ' +
                            '"Delivery Term", "Date of Delivery", "Payment Term", "Total Amount in Words", ' +
                            'and "Signatories" for Purchase Order or "JO Date", "Place of Delivery", "Date of Delivery", ' +
                            '"Payment Term", "Amount Words", and "Signatories" for Job Order.');
                    } else if (documentType == 'ors-burs') {
                        alert('Complete first the field for "Serial Number", "ORS/BURS Date" and "Signatories".');
                    } else if (documentType == 'cashadvance-ors-burs') {
                        alert('Complete first the field for "Serial Number", "ORS/BURS Date" and "Signatories".');
                    }  else if (documentType == 'iar') {
                        alert('Complete first the field for "IAR Date" and "Signatories".');
                    } else if (documentType == 'dv') {
                        alert('Complete first the field for "DV No" and IAR Date".');
                    } else if (documentType == 'cashadvance-dv') {
                        alert('Complete first the field for "DV No" and IAR Date".');
                    } else if (documentType == 'liquidation') {
                        alert('Complete first the field for Liquidation Date".');
                    }
                }
            });
        });
    }
});
