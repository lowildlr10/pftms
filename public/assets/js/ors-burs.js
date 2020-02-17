$(function() {
    $.fn.showCreate = function(type) {
        if (type == 'cashadvance') {
            var createURL = baseURL + '/cadv-reim-liquidation/ors-burs/' +
                            'create?module_type=' + type;
        } else {
            var createURL = baseURL + '/procurement/ors-burs/' +
                            'create?module_type=' + type;
        }

        $('#mdb-preloader').css('background', '#000000ab').fadeIn(300);
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
            var editURL = baseURL + '/cadv-reim-liquidation/ors-burs/' +
                          'edit/' + key + '/?module_type=' + type;
        } else {
            var editURL = baseURL + '/procurement/ors-burs/' +
                          'edit/' + key + '/?module_type=' + type;
        }

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
            $('#form-store').submit();
            $('#form-update').submit();
        }
	}

	$.fn.viewItems = function(key) {
		$('#modal-body-edit').load('ors-burs/show/' + key);
		$("#central-edit-modal").modal()
						.on('shown.bs.modal', function() {

				   		}).on('hidden.bs.modal', function() {
					        $('#modal-body-edit').html(modalLoadingContent);
					    });
	}

	$.fn.viewIssue = function(id) {
		$('#modal-body-sm').load('ors-burs/show-issue/' + id + '?back=0');
		$("#smcard-central-modal").modal()
						.on('shown.bs.modal', function() {

				   		}).on('hidden.bs.modal', function() {
					        $('#modal-body-sm').html(modalLoadingContent);
					    });
	}

	$.fn.viewIssueBack = function(id) {
		$('#modal-body-sm').load('ors-burs/show-issue/' + id + '?back=1');
		$("#smcard-central-modal").modal()
						.on('shown.bs.modal', function() {

				   		}).on('hidden.bs.modal', function() {
					        $('#modal-body-sm').html(modalLoadingContent);
					    });
    }

	$.fn.delete = function(id) {
		if (confirm('Are you sure you want to delete this document?')) {
			$('#form-validation').attr('action', 'ors-burs/delete/' + id).submit();
		}
	}

	$.fn.issue = function(id) {
		var withError = inputValidation(false);

		if (!withError) {
			$('#form-ors-burs-issue').attr('action', 'ors-burs/issue/' + id + '?back=0')
									 .submit();
		}
	}

	$.fn.issueBack = function(id) {
		var withError = inputValidation(false);

		if (!withError) {
			$('#form-ors-burs-issue').attr('action', 'ors-burs/issue/' + id + '?back=1')
									 .submit();
		}
	}

	$.fn.receive = function(id) {
		if (confirm('Receive this ORS/BURS document?')) {
			$('#form-validation').attr('action', 'ors-burs/receive/' + id + '?back=0').submit();
		}
	}

	$.fn.receiveBack = function(id) {
		if (confirm('Receive back this ORS/BURS document?')) {
			$('#form-validation').attr('action', 'ors-burs/receive/' + id + '?back=1').submit();
		}
	}

	$.fn.createDV = function(id) {
		if (confirm('Create the DV document?')) {
			$('#form-validation').attr('action', 'ors-burs/create-dv/' + id).submit();
		}
	}

	$.fn.obligate = function(id) {
		if (confirm('Obligate this ORS/BURS document?')) {
			$('#form-validation').attr('action', 'ors-burs/obligate/' + id).submit();
		}
	}
});
