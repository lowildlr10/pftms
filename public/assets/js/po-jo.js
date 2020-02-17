$(function() {
	function inputValidation(withError) {
		var errorCount = 0;

        $(".required").each(function() {
			var inputField = $(this).val().replace(/^\s+|\s+$/g, "").length;

			if (inputField == 0) {
				$(this).addClass("input-error-highlighter");
				errorCount++;
			} else {
				$(".input-quantity").each(function() {
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

	$.fn.computeCost = function(cnt, obj, t = 1) {
		var grandTotal = 0;
		var objId;
		var totalCost = 0;

		if (t == 1) {
			if (obj != null) {
		      objId = obj;
		    } else {
		      objId = this.id;
		    }

			if (objId.search(/quantity/i) == 0) {
				cnt = parseFloat(objId.replace('quantity',' '), 10);
			} else {
				cnt = parseFloat(objId.replace('unit_cost',' '), 10);
			}

			totalCost = $('#unit_cost' + cnt).val() * $('#quantity' + cnt).val()

			$('#total_cost' + cnt).val(totalCost.toFixed(2));
		}

		$("input[name='total_cost[]']").each(function() {
            var isExcluded = $(this).closest('td').siblings().find('.exclude').val();

            if (isExcluded == 'n') {
                grandTotal += parseFloat($(this).val());
            }
		});

		$("input[name='grand_total']").val(grandTotal.toFixed(2));
	}

	$.fn.viewItems = function(id, poNo, awardedTo, toggle = "po") {
		$('#modal-body-content-2').load('pr/show/' + id +
										'?awarded=' + awardedTo + '&toggle=' + toggle +
										'&po_no=' + poNo);
		$("#view-modal").modal({keyboard: false, backdrop: 'static'})
						.on('shown.bs.modal', function() {

				   		}).on('hidden.bs.modal', function() {
					        $('#modal-body-content-2').html(modalLoadingContent);
					    });
	}

	$.fn.viewIssue = function(poNo) {
		$('#modal-body-content-3').load('po-jo/show-issue/' + poNo);
		$("#view-modal-issue").modal({keyboard: false, backdrop: 'static'})
						.on('shown.bs.modal', function() {

				   		}).on('hidden.bs.modal', function() {
					        $('#modal-body-content-1').html(modalLoadingContent);
					    });
	}

	$.fn.viewCreate = function(poNo, toggle) {
        $('#mdb-preloader').css('background', '#000000ab').fadeIn(300);

		if (toggle == 'po') {
			$('#modal-body-edit').load('po-jo/show/' + poNo + '?toggle=po', function() {
                $('#mdb-preloader').fadeOut(300);
            });
		} else if (toggle == 'jo') {
			$('#modal-body-edit').load('po-jo/show/' + poNo + '?toggle=jo', function() {
                $('#mdb-preloader').fadeOut(300);
            });
		}

		$('#btn-create-update').attr('onclick', '$(this).update("' + poNo + '","' + toggle + '")');
		$("#central-edit-modal").modal()
						.on('shown.bs.modal', function() {

				   		}).on('hidden.bs.modal', function() {
					        $('#modal-body-edit').html(modalLoadingContent);
					    });
	}

	$.fn.viewIssue = function(poNo, toggle) {
		$('#modal-body-sm').load('po-jo/show-issue/' + poNo);
		$("#smcard-central-modal").modal()
						.on('shown.bs.modal', function() {

				   		}).on('hidden.bs.modal', function() {
					        $('#modal-body-sm').html(modalLoadingContent);
					    });
	}

	$.fn.createUpdateDoc = function(poNo, toggle) {
		var withError = inputValidation(false);

		if (!withError) {
			$('#form-create').submit();
		}
	}

    $.fn.accountantSigned = function(poNo) {
		if (confirm('Set to cleared/signed by accountant this PO/JO [' + poNo + '] ?')) {
			$('#form-validation').attr('action', 'po-jo/accountant-signed/' + poNo).submit();
		}
	}

	$.fn.approve = function(poNo) {
		if (confirm('Approve PO/JO [' + poNo + '] ?')) {
			$('#form-validation').attr('action', 'po-jo/approve/' + poNo).submit();
		}
	}

	$.fn.issue = function(poNo) {
		var withError = inputValidation(false);

		if (!withError) {
			$('#form-po-jo-issue').submit();
		}
	}

	$.fn.receive = function(poNo) {
		if (confirm('Receive PO/JO [' + poNo + '] ?')) {
			$('#form-validation').attr('action', 'po-jo/receive/' + poNo).submit();
		}
	}

	$.fn.cancel = function(poNo) {
		if (confirm('Cancel this PO/JO [' + poNo + '] ?')) {
			$('#form-validation').attr('action', 'po-jo/cancel/' + poNo).submit();
		}
    }

    $.fn.unCancel = function(poNo) {
		if (confirm('Uncancel this PO/JO [' + poNo + '] ?')) {
			$('#form-validation').attr('action', 'po-jo/uncancel/' + poNo).submit();
		}
	}

	$.fn.createORS_BURS = function(poNo) {
		if (confirm('Create the ORS/BURS for this PO/JO  [' + poNo + '] ?')) {
			$('#form-validation').attr('action', 'po-jo/create-ors-burs/' + poNo).submit();
		}
	}

	$.fn.toDelivery = function(poNo) {
		if (confirm("Set this PO/JO No: " + poNo + " for delivery?")) {
			$('#form-validation').attr('action', 'po-jo/delivery/' + poNo).submit();
		}
	}

	$.fn.toInspection = function(poNo) {
		if (confirm("Set this PO/JO No: " + poNo + " for inspection?")) {
			$('#form-validation').attr('action', 'po-jo/inspection/' + poNo).submit();
		}
	}
});
