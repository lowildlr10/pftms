$(function() {
	function inputValidation(withError) {
		var errorCount = 0;

        $(".required").each(function() {
			var inputField = $(this).val().replace(/^\s+|\s+$/g, "").length;

			if (inputField == 0) {
				$(this).addClass("input-error-highlighter");
				errorCount++;
			} else {
				$("input[name='quantity']").each(function() {
					if ($(this).val() == "0") {
			            $(this).addClass("input-error-highlighter");
			            errorCount++;
			        }
				});

				$(this).removeClass("input-error-highlighter");
			}
		});

		if (errorCount == 0) {
			withError = false;
		} else {
			withError = true;
		}

		return withError;
	}

	function showPrint(_key, _documentType, _otherParam = "") {
    	var paperSize = "";
    	var fontSize = 0;
    	var url = "";
    	var dateFrom, dateTo;
    	var otherParam = _otherParam;

     	documentType = _documentType;
    	key = _key;

		$('#other_param').val(_otherParam);

    	if (_documentType == 'label') {
    		$('#paper-size').val(2);
    		$('#print-title').html('Generate Property Label');
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
		        if (documentType == 'label') {
		        	alert('There is an error encountered on the generation of the Property Label.');
		        }
		    }
		});
	}

	function initializeQtyInput() {
		$('.quantity').each(function() {
			var maxLength = $(this).attr('max');

			$(this).keydown(function () {
			    // Save old value.
			    if (!$(this).val() || (parseInt($(this).val()) <= maxLength &&
			    	parseInt($(this).val()) >= 0)) {
			    	$(this).data("old", $(this).val());
			    }
			}).keyup(function () {
			    // Check correct, else revert back to old value.
			    if (!$(this).val() || (parseInt($(this).val()) <= maxLength &&
			    	parseInt($(this).val()) >= 0)) {
			    } else {
			      $(this).val($(this).data("old"));
			    }
			});
		});
	}

	$.fn.createUpdateDoc = function() {
		var withError = inputValidation(false);

		if (!withError) {
			$('#form-update').submit();
		}
	}

	$.fn.showCreate = function(classification) {
		$('#modal-body-create').load('stocks/show-create/' + classification);
		$("#central-create-modal").modal({keyboard: false, backdrop: 'static'})
						.on('shown.bs.modal', function() {

				   		}).on('hidden.bs.modal', function() {
					        $('#modal-body-create').html(modalLoadingContent);
					    });
	}

	$.fn.showEdit = function(key, classification, type) {
		$('#modal-body-issue').load('stocks/show/' + key +
									'?classification=' + classification +
									'&type=' + type);
		$("#central-issue-modal").modal({keyboard: false, backdrop: 'static'})
						.on('shown.bs.modal', function() {
				            initializeQtyInput();
				   		}).on('hidden.bs.modal', function() {
					        $('#modal-body-issue').html(modalLoadingContent);
					    });
	}

	$.fn.showEditIssue = function(invNo, classification, empID) {
		$('#modal-body-edit').load('stocks/edit/' + invNo +
								   '?classification=' + classification +
								   '&received_by=' + empID);
		$("#central-edit-modal").modal({keyboard: false, backdrop: 'static'})
						.on('shown.bs.modal', function() {
				            initializeQtyInput();
				   		}).on('hidden.bs.modal', function() {
					        $('#modal-body-edit').html(modalLoadingContent);
					    });
	}

	$.fn.showIssued = function(invNo, classification) {
		$('#modal-body-sm').load('stocks/issued/' + invNo +
								 '?classification=' + classification);
		$("#smcard-central-modal").modal({keyboard: false, backdrop: 'static'})
						.on('shown.bs.modal', function() {

				   		}).on('hidden.bs.modal', function() {
					        $('#modal-body-sm').html(modalLoadingContent);
					    });
	}

	$.fn.saveLabel = function(invID, empID, element) {
		var serialNo = element.val();

		$.post('stocks/update-serial-no/' + invID, {
			_token: $('meta[name=csrf-token]').attr('content'),
		    received_by: empID,
		    serial_no: serialNo
		}).done(function(data) {
			showPrint(invID, 'label', empID);
		}).fail(function(xhr, status, error) {

		});
	}

	$.fn.issueDoc = function() {
		var withError = inputValidation(false);

		if (!withError) {
			$('input[name^=quantity]').each(function(index) {
				var maxValue = parseInt($(this).attr('max'));
				var currentValue = parseInt($(this).val());

				if ((currentValue > maxValue) && currentValue == 0) {
					$(this).addClass("input-error-highlighter");
					withError = true;
				}
			});
		}

		if (!withError) {
			$('#form-update').submit();
		}
	}

	$.fn.delete = function(invNo, classification, empID) {
		if (confirm("Delete issued item/s for " + empID +
					" [" + classification + " No: " + invNo + "]?")) {
			$('#form-validation').attr('action', 'stocks/delete/' + invNo + '?received_by=' + empID).submit();
		}
	}

	$.fn.issued = function(invNo) {
		if (confirm("Set this " + invNo + " to 'ISSUED'?")) {
			$('#form-validation').attr('action', 'stocks/set-issued/' + invNo).submit();
		}
	}
});
