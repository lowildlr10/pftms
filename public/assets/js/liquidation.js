$(function() {
    $.fn.showCreate = function() {
        $('#mdb-preloader').css('background', '#000000ab').fadeIn(300);
		$('#modal-body-create').load('liquidation/create', function() {
            $('#mdb-preloader').fadeOut(300);
			$('#form-create').attr('action', 'liquidation/store');
		});
		$("#central-create-modal").modal()
						.on('shown.bs.modal', function() {

				   		}).on('hidden.bs.modal', function() {
					        $('#modal-body-create').html(modalLoadingContent);
					    });
    }

    $.fn.showEdit = function(key) {
        var editURL = baseURL + '/cadv-reim-liquidation/liquidation/' +
                      'edit/' + key;

        $('#mdb-preloader').css('background', '#000000ab').fadeIn(300);
		$('#modal-body-edit').load(editURL, function() {
            $('#mdb-preloader').fadeOut(300);
            $('.mdb-select').materialSelect();
        });
		$("#central-edit-modal").modal().on('shown.bs.modal', function() {

		}).on('hidden.bs.modal', function() {
		    $('#modal-body-edit').html(modalLoadingContent);
		});
    }

    $.fn.createUpdateDoc = function() {
		var withError = inputValidation(false);

		if (!withError) {
			$('#form-update').submit();
        }
    }

    $.fn.showIssue = function(id) {
		$('#modal-body-sm').load('liquidation/show-issue/' + id + '?back=0');
		$("#smcard-central-modal").modal()
						.on('shown.bs.modal', function() {

				   		}).on('hidden.bs.modal', function() {
					        $('#modal-body-sm').html(modalLoadingContent);
					    });
	}

	$.fn.showIssueBack = function(id) {
		$('#modal-body-sm').load('liquidation/show-issue/' + id + '?back=1');
		$("#smcard-central-modal").modal()
						.on('shown.bs.modal', function() {

				   		}).on('hidden.bs.modal', function() {
					        $('#modal-body-sm').html(modalLoadingContent);
					    });
	}

    $.fn.issue = function(id) {
		var withError = inputValidation(false);

		if (!withError) {
			$('#form-liquidation-issue').attr('action', 'liquidation/issue/' + id + '?back=0')
							            .submit();
		}
	}

	$.fn.issueBack = function(id) {
		var withError = inputValidation(false);

		if (!withError) {
			$('#form-liquidation-issue').attr('action', 'liquidation/issue/' + id + '?back=1')
							            .submit();
		}
	}

    $.fn.receive = function(id) {
		if (confirm("Receive this document?")) {
			$('#form-validation').attr('action', 'liquidation/receive/' + id + '?back=0').submit();
		}
	}

	$.fn.receiveBack = function(id) {
		if (confirm('Receive back this ORS/BURS document?')) {
			$('#form-validation').attr('action', 'liquidation/receive/' + id + '?back=1').submit();
		}
    }

    $.fn.liquidate = function(id) {
		if (confirm("Liquidate this voucher document?")) {
			$('#form-validation').attr('action', 'liquidation/liquidate/' + id).submit();
		}
    }
});
