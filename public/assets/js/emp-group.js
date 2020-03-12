$(function() {
    const template = '<div class="tooltip md-tooltip">' +
                     '<div class="tooltip-arrow md-arrow"></div>' +
                     '<div class="tooltip-inner md-inner stylish-color"></div></div>';

    function toggleGroupInputs() {
        const menuGroup = $('#division-menu');
        const _selectAllCheck = '#sel-all';
        const selectAllCheck = $(_selectAllCheck);

        selectAllCheck.unbind('change').change(function() {
            selectAllCheck.prop('indeterminate', false);
            if (selectAllCheck.is(':checked')) {
                menuGroup.find('input').each(function() {
                    $(this).prop("checked", true);
                });
            } else {
                menuGroup.find('input').each(function() {
                    $(this).prop("checked", false);
                });
            }
        });

        menuGroup.find('input').not(_selectAllCheck)
                 .each(function() {
            if ($(this).is(':checked')) {
                $(selectAllCheck).prop('indeterminate', true);
            }

            $(this).unbind('change').change(function() {
                $(selectAllCheck).prop('indeterminate', true);
            });
        });
    }

    $.fn.showCreate = function(url) {
        $('#mdb-preloader').css('background', '#000000ab').fadeIn(300);
        $('#modal-body-create').load(url, function() {
            $('#mdb-preloader').fadeOut(300);
            $('.mdb-select').materialSelect();
            $(this).slideToggle(500);
            toggleGroupInputs();
        });
        $("#modal-sm-create").modal({keyboard: false, backdrop: 'static'})
						     .on('shown.bs.modal', function() {
            $('#create-title').html('Create Group');
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

    $.fn.showEdit = function(url) {
        $('#mdb-preloader').css('background', '#000000ab').fadeIn(300);
        $('#modal-body-edit').load(url, function() {
            $('#mdb-preloader').fadeOut(300);
            $('.mdb-select').materialSelect();
            $(this).slideToggle(500);
            toggleGroupInputs();
        });
        $("#modal-sm-edit").modal({keyboard: false, backdrop: 'static'})
						   .on('shown.bs.modal', function() {
            $('#edit-title').html('Update Group');
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

    $.fn.showDelete = function(url, name) {
		$('#modal-body-delete').html(`Are you sure you want to delete '${name}'?`);
        $("#modal-delete").modal({keyboard: false, backdrop: 'static'})
						  .on('shown.bs.modal', function() {
            $('#delete-title').html('Delete Group');
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
