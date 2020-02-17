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

	$.fn.viewItems = function(id) {
        $('#mdb-preloader').css('background', '#000000ab').fadeIn(300);
		$('#modal-body-edit').load('rfq/show/' + id, function() {
            $('#mdb-preloader').fadeOut(300);
        });
		$("#central-edit-modal").modal()
						.on('shown.bs.modal', function() {
				            $('.mdb-select-1').materialSelect();
				            $('.mdb_upload').mdb_upload();
				   		}).on('hidden.bs.modal', function() {
					        $('#modal-body-edit').html(modalLoadingContent);
					    });
	}

	$.fn.viewIssue = function(id) {
		$('#modal-body-sm').load('rfq/show-issue/' + id);
		$("#smcard-central-modal").modal()
						.on('shown.bs.modal', function() {

				   		}).on('hidden.bs.modal', function() {
					        $('#modal-body-sm').html(modalLoadingContent);
					    });
	}

	$.fn.createUpdateDoc = function() {
		var withError = inputValidation(false);

		if (!withError) {
			$('#form-create').submit();
		}
	}

	$.fn.issue = function() {
		var withError = inputValidation(false);

		if (!withError) {
			$('#form-rfq-issue').submit();
		}
	}

	$.fn.receive = function(id) {
		if (confirm('Receive RFQ document?')) {
			$('#form-validation').attr('action', 'rfq/receive/' + id).submit();
		}
	}

});
