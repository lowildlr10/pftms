$(function() {
	const template = '<div class="tooltip md-tooltip">' +
                     '<div class="tooltip-arrow md-arrow"></div>' +
                     '<div class="tooltip-inner md-inner stylish-color"></div></div>';

    $.fn.showItem = function(url) {
        $('#mdb-preloader').css('background', '#000000ab').fadeIn(300);
        $('#modal-body-show').load(url, function() {
            $('#mdb-preloader').fadeOut(300);
            $('.crud-select').materialSelect();
            $(this).slideToggle(500);
        });
        $("#modal-show").modal({keyboard: false, backdrop: 'static'})
						.on('shown.bs.modal', function() {
            $('#show-title').html('View Items');
		}).on('hidden.bs.modal', function() {
		    $('#modal-body-show').html('').css('display', 'none');
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
            $('#edit-title').html('Update Purchase Request');
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
            $('#issue-title').html('Issue Request for Quotation');
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

    $.fn.showReceive = function(url) {
        $('#mdb-preloader').css('background', '#000000ab').fadeIn(300);
        $('#modal-body-receive').load(url, function() {
            $('#mdb-preloader').fadeOut(300);
            $(this).slideToggle(500);
        });
        $("#modal-receive").modal({keyboard: false, backdrop: 'static'})
						 .on('shown.bs.modal', function() {
            $('#receive-title').html('Receive Request for Quotation');
		}).on('hidden.bs.modal', function() {
            $('#modal-body-receive').html('').css('display', 'none');
		});
    }

    $.fn.receive = function() {
        $('#form-receive').submit();
    }
});
