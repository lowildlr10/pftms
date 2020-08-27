$(function() {
	const template = '<div class="tooltip md-tooltip">' +
                     '<div class="tooltip-arrow md-arrow"></div>' +
                     '<div class="tooltip-inner md-inner stylish-color"></div></div>';

    $.fn.showCreateStocks = function(url) {
        $('#mdb-preloader').css('background', '#000000ab').fadeIn(300);
        $('#modal-body-create').load(url, function() {
            $('#mdb-preloader').fadeOut(300);
            $('.crud-select').materialSelect();
            $(this).slideToggle(500);
        });
        $("#modal-lg-create").modal({keyboard: false, backdrop: 'static'})
						   .on('shown.bs.modal', function() {
            $('#create-title').html('Create Inventory Stocks');
		}).on('hidden.bs.modal', function() {
            $('#modal-body-create').html('').css('display', 'none');
		});
    }

    $.fn.store = function() {
        const withError = inputValidation(false);

		if (!withError) {
			$('#form-store').submit();
        }
    }

    $.fn.showEditStocks = function(url) {
        $('#mdb-preloader').css('background', '#000000ab').fadeIn(300);
        $('#modal-body-edit').load(url, function() {
            $('#mdb-preloader').fadeOut(300);
            $('.crud-select').materialSelect();
            $(this).slideToggle(500);
        });
        $("#modal-lg-edit").modal({keyboard: false, backdrop: 'static'})
						   .on('shown.bs.modal', function() {
            $('#edit-title').html('Update Inventory Stocks');
		}).on('hidden.bs.modal', function() {
            $('#modal-body-edit').html('').css('display', 'none');
		});
    }

    $.fn.showEdit = function(url) {
        $('#mdb-preloader').css('background', '#000000ab').fadeIn(300);
        $('#modal-body-edit').load(url, function() {
            $('#mdb-preloader').fadeOut(300);
            $('.crud-select').materialSelect();
            $(this).slideToggle(500);
        });
        $("#modal-lg-edit").modal({keyboard: false, backdrop: 'static'})
						   .on('shown.bs.modal', function() {
            $('#edit-title').html('Update Inspection and Acceptance Report');
		}).on('hidden.bs.modal', function() {
            $('#modal-body-edit').html('').css('display', 'none');
		});
    }

    $.fn.update = function() {
        const withError = inputValidation(false);

		if (!withError) {
			$('#form-update').submit();
		}
    }

	$.fn.showIssue = function(url) {
        $('#mdb-preloader').css('background', '#000000ab').fadeIn(300);
        $('#modal-body-issue').load(url, function() {
            $('#mdb-preloader').fadeOut(300);
            $('.crud-select').materialSelect();
            $(this).slideToggle(500);
        });
        $("#modal-issue").modal({keyboard: false, backdrop: 'static'})
						 .on('shown.bs.modal', function() {
            $('#issue-title').html('Issue Inspection and Acceptance Report');
		}).on('hidden.bs.modal', function() {
            $('#modal-body-issue').html('').css('display', 'none');
		});
    }

    $.fn.issue = function() {
        const withError = inputValidation(false);

		if (!withError) {
			$('#form-issue').submit();
		}
    }

    $.fn.showInspect = function(url) {
        $('#mdb-preloader').css('background', '#000000ab').fadeIn(300);
        $('#modal-body-inspect').load(url, function() {
            $('#mdb-preloader').fadeOut(300);
            $(this).slideToggle(500);
        });
        $("#modal-inspect").modal({keyboard: false, backdrop: 'static'})
						   .on('shown.bs.modal', function() {
            $('#inspect-title').html('Inspect Inspection and Acceptance Report');
		}).on('hidden.bs.modal', function() {
            $('#modal-body-inspect').html('').css('display', 'none');
		});
    }

    $.fn.inspect = function() {
        $('#form-inspect').submit();
    }

    $.fn.showRestore = function(url) {
		$('#modal-body-restore').html(`Are you sure you want to restore Purchase/Job Order?`);
        $("#modal-restore").modal({keyboard: false, backdrop: 'static'})
						   .on('shown.bs.modal', function() {
            $('#restore-title').html('Restore Purchase/Job Order');
            $('#form-restore').attr('action', url);
		}).on('hidden.bs.modal', function() {
             $('#modal-restore-body').html('');
             $('#form-restore').attr('action', '#');
		});
    }

    $.fn.restore = function() {
        $('#form-restore').submit();
    }

    $('.material-tooltip-main').tooltip({
        template: template
    });
});
