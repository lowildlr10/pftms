$(function() {
    $.fn.showCreate = function(type) {
        $('#mdb-preloader').css('background', '#000000ab').fadeIn(300);

        if (type == 'cashadvance') {
            var createURL = baseURL + '/cadv-reim-liquidation/ors-burs/' +
                            'create?module_type=' + type;
        } else {
            var createURL = baseURL + '/procurement/ors-burs/' +
                            'create?module_type=' + type;
        }

        $('#modal-body-create').load(createURL, function() {
            $('#mdb-preloader').fadeOut(300);
            $('.mdb-select').materialSelect();
        });
		$("#central-create-modal").modal().on('shown.bs.modal', function() {

		}).on('hidden.bs.modal', function() {
			$('#modal-body-create').html(modalLoadingContent);
		});
    }

    $.fn.showEdit = function(key, type) {
        $('#mdb-preloader').css('background', '#000000ab').fadeIn(300);

        if (type == 'cashadvance') {
            var editURL = baseURL + '/cadv-reim-liquidation/dv/' +
                          'edit/' + key + '/?module_type=' + type;
        } else {
            var editURL = baseURL + '/procurement/dv/' +
                          'edit/' + key + '/?module_type=' + type;
        }

        $('#modal-body-edit').load(editURL, function() {
            $('#mdb-preloader').fadeOut(300);
            $('.mdb-select').materialSelect();

            $('#others-check').unbind('change').change(function() {
                if (this.checked) {
                    $('#other-payment').fadeIn()
                                       .addClass('required');
                } else {
                    $('#other-payment').fadeOut()
                                       .removeClass('required')
                                       .val('');
                }
            });
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

	$.fn.viewIssue = function(id) {
		$('#modal-body-sm').load('dv/show-issue/' + id + '?back=0');
		$("#smcard-central-modal").modal()
						.on('shown.bs.modal', function() {

				   		}).on('hidden.bs.modal', function() {
					        $('#modal-body-sm').html(modalLoadingContent);
					    });
	}

	$.fn.viewIssueBack = function(id) {
		$('#modal-body-sm').load('dv/show-issue/' + id + '?back=1');
		$("#smcard-central-modal").modal()
						.on('shown.bs.modal', function() {

				   		}).on('hidden.bs.modal', function() {
					        $('#modal-body-sm').html(modalLoadingContent);
					    });
	}

	$.fn.issue = function(id) {
		var withError = inputValidation(false);

		if (!withError) {
			$('#form-dv-issue').attr('action', 'dv/issue/' + id + '?back=0')
							   .submit();
		}
	}

	$.fn.issueBack = function(id) {
		var withError = inputValidation(false);

		if (!withError) {
			$('#form-dv-issue').attr('action', 'dv/issue/' + id + '?back=1')
							   .submit();
		}
	}

	$.fn.receive = function(id) {
		if (confirm("Receive this document?")) {
			$('#form-validation').attr('action', 'dv/receive/' + id + '?back=0').submit();
		}
	}

	$.fn.receiveBack = function(id) {
		if (confirm('Receive back this ORS/BURS document?')) {
			$('#form-validation').attr('action', 'dv/receive/' + id + '?back=1').submit();
		}
    }

    $.fn.createLiquidation = function(id) {
		if (confirm('Create the Liquidation Report?')) {
            var url = baseURL + '/cadv-reim-liquidation/ors-burs/create-liquidation/' + id;
			$('#form-validation').attr('action', url).submit();
		}
	}

	$.fn.payment = function(id) {
		if (confirm("Disburse this voucher document?")) {
			$('#form-validation').attr('action', 'dv/payment/' + id).submit();
		}
    }
});
