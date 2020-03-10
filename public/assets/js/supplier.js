$(function() {
    const template = '<div class="tooltip md-tooltip">' +
                     '<div class="tooltip-arrow md-arrow"></div>' +
                     '<div class="tooltip-inner md-inner stylish-color"></div></div>';

    function toggleInput() {
        const attachChkboxElem = $('#check-attachment');

        $('#nature-business').unbind('change').change(function() {
            const natureBusiness = $(this).val();

            if (natureBusiness == 'others') {
                $('#field-nature-business-others').slideToggle(300);
                $('#nature-business-others').removeClass('input-error-highlighter')
                                            .addClass('required')
                                            .val('');
            } else {
                $('#field-nature-business-others').slideUp(300);
                $('#nature-business-others').removeClass('input-error-highlighter')
                                            .removeClass('required')
                                            .val('');
            }
        });

        $('#attachment-7').unbind('change').change(function() {
            if ($(this).is(':checked')) {
                $('#field-attachment-others').slideToggle(300);
                $('#attachment-others').removeClass('input-error-highlighter')
                                       .addClass('required')
                                       .val('');
            } else {
                $('#field-attachment-others').slideToggle(300);
                $('#attachment-others').removeClass('input-error-highlighter')
                                       .removeClass('required')
                                       .val('');
            }
        });

        attachChkboxElem.find('input[type="checkbox"]').each(function() {
			$(this).click(function() {
				let checkValues = [];
				let attachmentVal = "";

				attachChkboxElem.find('input[type="checkbox"]:checked').each(function() {
					let tempCheckVal = $(this).val();
					checkValues.push(tempCheckVal);
				});

				$.each(checkValues, function(i, val) {
					if (i == 0) {
						attachmentVal += val;
					} else {
						attachmentVal += "-" + val;
					}
				});

				$('#attachment').val(attachmentVal);
			});
		});
    }

    $.fn.showCreate = function(url) {
        $('#mdb-preloader').css('background', '#000000ab').fadeIn(300);
        $('#modal-body-create').load(url, function() {
            $('#mdb-preloader').fadeOut(300);
            $('.mdb-select').materialSelect();
            $(this).slideToggle(500);
            toggleInput();
        });
        $("#modal-sm-create").modal({keyboard: false, backdrop: 'static'})
						     .on('shown.bs.modal', function() {
            $('#create-title').html('Create Supplier');
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
            toggleInput();
        });
        $("#modal-sm-edit").modal({keyboard: false, backdrop: 'static'})
						   .on('shown.bs.modal', function() {
            $('#edit-title').html('Update Supplier');
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
            $('#delete-title').html('Delete Supplier');
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
