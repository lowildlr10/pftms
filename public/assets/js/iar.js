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

	$.fn.viewCreate = function(iarNo) {
        $('#mdb-preloader').css('background', '#000000ab').fadeIn(300);
		$('#modal-body-edit').load('iar/show/' + iarNo, function() {
            $('#mdb-preloader').fadeOut(300);
        });
		$('#btn-create-update').attr('onclick', '$(this).update("' + iarNo + '")');
		$("#central-edit-modal").modal()
						.on('shown.bs.modal', function() {

				   		}).on('hidden.bs.modal', function() {
					        $('#modal-body-edit').html(modalLoadingContent);
					    });
	}

	$.fn.viewIssue = function(iarNo) {
		$('#modal-body-sm').load('iar/show-issue/' + iarNo);
		$("#smcard-central-modal").modal()
						.on('shown.bs.modal', function() {

				   		}).on('hidden.bs.modal', function() {
					        $('#modal-body-sm').html(modalLoadingContent);
					    });
	}

	$.fn.showInventory = function(poNo, type) {
		$('#modal-body-issue').load('../inventory/stocks/create/' + poNo, function() {
			if (type == 'create') {
				$('#form-create-inventory').attr('action', '../inventory/stocks/store/' + poNo);
				$('#btn-inventory').html('<i class="fas fa-pencil-alt"></i> Create');
			} else if (type == 'update') {
				$('#form-create-inventory').attr('action', '../inventory/stocks/update-stocks/' + poNo);
				$('#btn-inventory').html('<i class="fas fa-edit"></i> Update');
			}
		});

		$("#central-issue-modal").modal()
						.on('shown.bs.modal', function() {

				   		}).on('hidden.bs.modal', function() {
					        $('#modal-body-issue').html(modalLoadingContent);
					    });
	}

	$.fn.issueDoc = function() {
		var withError = inputValidation(false);

		if (!withError) {
			$('#form-create-inventory').submit();
		}
	}

	$.fn.createUpdateDoc = function(poNo) {
		var withError = inputValidation(false);

		if (!withError) {
			$('#form-update').submit();
		}
	}

	$.fn.issue = function() {
		var withError = inputValidation(false);

		if (!withError) {
			$('#form-iar-issue').submit();
		}
	}

	$.fn.receive = function(iarNo) {
		if (confirm("Receive PO/JO document for [ IAR No: " + iarNo + " ]?")) {
			$('#form-validation').attr('action', 'iar/receive/' + iarNo).submit();
		}
	}

	$.fn.inspect = function(iarNo) {
		if (confirm("Inspect this item [ IAR No: " + iarNo + " ]?")) {
			$('#form-validation').attr('action', 'iar/inspect/' + iarNo).submit();
		}
	}
});
