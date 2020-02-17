$(function() {
	var documentType, key;

	/*
	$('.row-item').unbind('mouseenter').mouseenter(function() {
		$('.menu-slide').slideUp();
		
		if ($(this).next('tr').find('.menu-slide').is(':visible')) {
			//$(this).next('tr').find('.menu-slide').slideUp();
		} else {
			$(this).next('tr').find('.menu-slide').slideDown();
		}
	});

	$('.custom-menu-row').unbind('mouseleave').mouseleave(function() {
		$('.menu-slide').slideUp();
	});*/

	$.fn.slide = function(element) {
		$('.menu-slide').slideUp();
		$('.row-item').removeClass('table-row-highlighter');

		if ($('#' + element).is(':visible')) {
			$('#' + element).slideUp();
			$('#' + element).closest('td').closest('tr').prev('tr').removeClass('table-row-highlighter');
		} else {
			$('#' + element).slideDown();
			$('#' + element).closest('td').closest('tr').prev('tr').addClass('table-row-highlighter');
		}
	}

	$.fn.approve = function(type, docName) {
		var isChecked = $('input[name="pr_chk[]"]:checked').length > 0;

		if (isChecked) {
			if (type == 'issue') {
				if (confirm("Issue the selected " + docName + "?")) {
					$('#type').val(type);
					$('#form-check').submit();
				}
			} else if (type == 'receive') {
				if (confirm("Received the selected " + docName + "?")) {
					$('#type').val(type);
					$('#form-check').submit();
				}
			} else if (type == 'issue-ors') {
				if (confirm("Issue ORS/BURS for the selected " + docName + "?")) {
					$('#type').val(type);
					$('#form-check').submit();
				}
			} else if (type == 'delete') {
				if (confirm("Delete the selected " + docName + "?")) {
					$('#type').val(type);
					$('#form-check').submit();
				}
			} else if (type == 'approve') {
				if (confirm("Approve the selected " + docName + "?")) {
					$('#type').val(type);
					$('#form-check').submit();
				}
			}  else if (type == 'disapprove') {
				if (confirm("Disapprove the selected " + docName + "?")) {
					$('#type').val(type);
					$('#form-check').submit();
				}
			} else if (type == 'obligate') {
				if (confirm("Obligate the selected " + docName + "?")) {
					$('#type').val(type);
					$('#form-check').submit();
				}
			} else if (type == 'inspect') {
				if (confirm("Inspect the selected " + docName + "?")) {
					$('#type').val(type);
					$('#form-check').submit();
				}
			} else if (type == 'payment') {
				if (confirm("Set the selected " + docName + " to payment?")) {
					$('#type').val(type);
					$('#form-check').submit();
				}
			}
		} else {
			alert('You need to check atleast one item.');
		}
	}

	$('#sel-filter').unbind('change').change(function() {
		var filter = $(this).val();
		var search = $('#input-search').val();
	    	
	    $('#val-filter').val(filter);
	    $('#val-search').val(search);

	    $('#form-filter-search').submit();
	});

	$('#input-search').keypress(function(e) {
	    if (e.which == 13) {
	    	var filter = $('#sel-filter').val();
	    	var search = $(this).val();

	    	$('#val-filter').val(filter);
	        $('#val-search').val(search);

	        $('#form-filter-search').submit();
	    }
	});

	$('#pr_chk_all').click(function() {
		$('input:checkbox').not(this).prop('checked', this.checked);
	});

	$(".alert").fadeTo(4000, 0.7, function() {
		$(this).fadeOut();
	});

    $('#paper-size').unbind('change').change(function() {
    	var otherParam = $('#other_param').val();
    	var paperSize = $('#paper-size').val();
    	var fontSize = $('#font-size').val();
    	var url = '../print/' + key + '?document_type=' + documentType + '&preview_toggle=preview' +
    		  	  '&font_scale=' + fontSize + '&paper_size=' + paperSize + '&other_param=' + otherParam;

    	$.ajax({
		    url: url,
		    dataType: 'html',
		    success: function(data) {
		    	$('#modal-print-content iframe').attr('src', url);
		    },
		    error: function(xhr, error){
		        if (documentType == 'rfq') {
		        	alert('Complete first the field for "RFQ Date" and "Signatory".');
		        } else if (documentType == 'abstract') {
		        	alert('Complete first the field for "Abstract Date" and "Signatories".');
		        } else if (documentType == 'po-jo') {
		        	alert('Complete first the field for "PO Date", "Mode of Procurement", "Place of Delivery", ' +
		        		  '"Delivery Term", "Date of Delivery", "Payment Term", "Total Amount in Words", ' +
		        		  'and "Signatories" for Purchase Order or "JO Date", "Place of Delivery", "Date of Delivery", ' +
		        		  '"Payment Term", "Amount Words", and "Signatories" for Job Order.');
		        } else if (documentType == 'ors-burs') {
		        	alert('Complete first the field for "Serial Number", "ORS/BURS Date" and "Signatories".');
		        } else if (documentType == 'iar') {
		        	alert('Complete first the field for "IAR Date", and "Signatories".');
		        }
		    }
		});
    });

    $('#font-size').unbind('change').change(function() {
    	var otherParam = $('#other_param').val();
    	var paperSize = $('#paper-size').val();
    	var fontSize = $('#font-size').val();
    	var url = '../print/' + key + '?document_type=' + documentType + '&preview_toggle=preview' +
    		  	  '&font_scale=' + fontSize + '&paper_size=' + paperSize + '&other_param=' + otherParam;

    	$.ajax({
		    url: url,
		    dataType: 'html',
		    success: function(data) {
		    	$('#modal-print-content iframe').attr('src', url);
		    },
		    error: function(xhr, error){
		        if (documentType == 'rfq') {
		        	alert('Complete first the field for "RFQ Date" and "Signatory".');
		        } else if (documentType == 'abstract') {
		        	alert('Complete first the field for "Abstract Date" and "Signatories".');
		        } else if (documentType == 'po-jo') {
		        	alert('Complete first the field for "PO Date", "Mode of Procurement", "Place of Delivery", ' +
		        		  '"Delivery Term", "Date of Delivery", "Payment Term", "Total Amount in Words", ' +
		        		  'and "Signatories" for Purchase Order or "JO Date", "Place of Delivery", "Date of Delivery", ' +
		        		  '"Payment Term", "Amount Words", and "Signatories" for Job Order.');
		        } else if (documentType == 'ors-burs') {
		        	alert('Complete first the field for "Serial Number", "ORS/BURS Date" and "Signatories".');
		        } else if (documentType == 'iar') {
		        	alert('Complete first the field for "IAR Date", and "Signatories".');
		        }
		    }
		});
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
    	} else if (_documentType == 'par') {
    		$('#paper-size').val(2);
    		$('#print-title').html('Generate Property Acknowledgement Reciept');
    	} else if (_documentType == 'ris') {
    		$('#paper-size').val(2);
    		$('#print-title').html('Generate Requisition and Issue Slip');
    	} else if (_documentType == 'ics') {
    		$('#paper-size').val(2);
    		$('#print-title').html('Generate Inventory Custodian Slip');
    	} else if (_documentType == 'voucher-logs') {
    		$('#paper-size').val(3);
    		$('#print-title').html('Generate Voucher Tracking');

    		dateFrom = $('#date-from').val();
    		dateTo = $('#date-to').val();
    		otherParam = dateFrom + "," + dateTo + "," + _otherParam;
    		$('#other_param').val(otherParam);
    	}

    	paperSize = $('#paper-size').val();
    	
    	url = '../print/' + key + '?document_type=' + documentType + '&preview_toggle=preview' +
    		  '&font_scale=' + fontSize + '&paper_size=' + paperSize + '&test=true' + '&other_param=' + otherParam;

    	$.ajax({
		    url: url,
		    dataType: 'html',
		    success: function(data) {
		    	url = '../print/' + key + '?document_type=' + documentType + '&preview_toggle=preview' +
    		  		  '&font_scale=' + fontSize + '&paper_size=' + paperSize + '&test=false'  + '&other_param=' + otherParam;

		    	$('#modal-print-content iframe').attr('src', url);
		    	$("#print-modal").modal({keyboard: false, backdrop: 'static'})
							.on('shown.bs.modal', function() {
					            
					   		}).on('hidden.bs.modal', function() {
						        $('#modal-print-content iframe').attr('src', '');
						    });
		    },
		    error: function(xhr, error){
		        if (documentType == 'rfq') {
		        	alert('Complete first the field for "RFQ Date" and "Signatory".');
		        } else if (documentType == 'abstract') {
		        	alert('Complete first the field for "Abstract Date" and "Signatories".');
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
		        }
		    }
		});	
	}

	$.fn.download = function() {
		var paperSize = $('#paper-size').val();
    	var fontSize = $('#font-size').val();
    	var otherParam = $('#other_param').val();
    	var url = '../print/' + key + '?document_type=' + documentType + '&preview_toggle=download' +
    		  	  '&font_scale=' + fontSize + '&paper_size=' + paperSize + '&other_param=' + otherParam;

    	$('#modal-print-content iframe').attr('src', url);
	}
	
});