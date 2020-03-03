$(function() {
    const template = '<div class="tooltip md-tooltip">' +
                     '<div class="tooltip-arrow md-arrow"></div>' +
                     '<div class="tooltip-inner md-inner stylish-color"></div></div>';

    $.fn.showCreate = function(url) {
        $('#modal-body-create').load(url);
        $('#mdb-preloader').css('background', '#000000ab').fadeIn(300);
        $("#modal-sm-create").modal({keyboard: false, backdrop: 'static'})
						     .on('shown.bs.modal', function() {
            $('#mdb-preloader').fadeOut(300);
            $('#create-title').html('Create Funding Source');
		}).on('hidden.bs.modal', function() {
		     $('#modal-create-body').html(modalLoadingContent);
		});
    }

    $.fn.store = function() {
        const withError = inputValidation(false);

		if (!withError) {
			$('#form-store').submit();
		}
    }

    $.fn.showEdit = function(url) {
        $('#modal-body-edit').load(url);
        $('#mdb-preloader').css('background', '#000000ab').fadeIn(300);
        $("#modal-sm-edit").modal({keyboard: false, backdrop: 'static'})
						   .on('shown.bs.modal', function() {
            $('#mdb-preloader').fadeOut(300);
            $('#edit-title').html('Update Funding Source');
		}).on('hidden.bs.modal', function() {
		     $('#modal-edit-body').html(modalLoadingContent);
		});
    }

    $.fn.update = function() {
        const withError = inputValidation(false);

		if (!withError) {
			$('#form-update').submit();
		}
    }

    $.fn.showDelete = function(url, divisionName) {
		$('#modal-body-delete').html(`Are you sure you want to delete '${divisionName}'?`);
        $("#modal-delete").modal({keyboard: false, backdrop: 'static'})
						  .on('shown.bs.modal', function() {
            $('#delete-title').html('Delete Funding Source');
            $('#form-delete').attr('action', url);
		}).on('hidden.bs.modal', function() {
             $('#modal-delete-body').html('');
             $('#form-delete').attr('action', '#');
		});
    }

    $.fn.delete = function() {
        $('#form-delete').submit();
    }

    $('.material-tooltip-main').tooltip({
        template: template
    });
    $('.mdb-select-filter').materialSelect();
});
