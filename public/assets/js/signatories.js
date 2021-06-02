$(function() {
    const template = '<div class="tooltip md-tooltip">' +
                     '<div class="tooltip-arrow md-arrow"></div>' +
                     '<div class="tooltip-inner md-inner stylish-color"></div></div>';
    const cBoxIDs = [
        '#pr',
        '#rfq',
        '#abs',
        '#po',
        '#jo',
        '#ors',
        '#iar',
        '#dv',
        '#ris',
        '#par',
        '#ics',
        '#lr',
        '#lddap',
        '#summary',
        '#lib',
        '#librealign',
    ];

    function convertAccessToJson() {
        let jsonData = {};

        $.each(cBoxIDs, function(i, id) {
            const moduleCbox = $(id);
            const menuGroup = $(id + '-menu');
            const parentModule = moduleCbox.val();
            let _jsonData = {};

            const _id = id.replace('#', '');

            const _designationInp = '#' + _id + '_designation';
            const designationInp = $(_designationInp);

            if (parentModule) {
                _jsonData['designation'] = designationInp.val() ? designationInp.val() : '';

                if (moduleCbox.is(':checked')) {
                    _jsonData['is_allowed'] = 1;

                    menuGroup.find('input').not(_designationInp).each(function() {
                        const parentAttr = $(this).val();

                        if (parentAttr) {
                            if ($(this).is(':checked')) {
                                _jsonData[parentAttr] = 1;
                            } else {
                                _jsonData[parentAttr] = 0;
                            }
                        }
                    });
                } else {
                    _jsonData['is_allowed'] = 0;

                    menuGroup.find('input').not(_designationInp).each(function() {
                        const parentAttr = $(this).val();

                        if (parentAttr) {
                            _jsonData[parentAttr] = 0;
                        }
                    });
                }

                jsonData[parentModule] = _jsonData;
            }
        });

        jsonData = JSON.stringify(jsonData)

        return jsonData;
    }

    function toggleSignatoryInputs() {
		$.each(cBoxIDs, function(i, id) {
            const moduleCbox = $(id);
            const menuGroup = $(id + '-menu');
            const _id = id.replace('#', '');

            const _designationInp = '#' + _id + '_designation';
            const designationInp = $(_designationInp);

            const _selectAllCheck = '#sel-' + _id;
            const selectAllCheck = $(_selectAllCheck);

            const _allowedCheck = '#allowed-' + _id;
            const allowedCheck = $(_allowedCheck);

			moduleCbox.unbind('change').change(function() {
				if (moduleCbox.is(':checked')) {
                    menuGroup.slideToggle(300)
                             .find('input')
                             .not(_designationInp).each(function() {
                        $(this).prop("checked", false)
                               .prop('indeterminate', false);
                    });
                    allowedCheck.prop("checked", true);
                    designationInp.addClass('required')
                                  .removeClass('input-error-highlighter');
				} else {
                    menuGroup.slideToggle(300)
                             .find('input')
                             .not(_designationInp).each(function() {
                        $(this).prop("checked", false)
                               .prop('indeterminate', false);
                    });
                    allowedCheck.prop("checked", false);
                    designationInp.removeClass('required')
                                  .removeClass('input-error-highlighter');
                }
            });

            selectAllCheck.unbind('change').change(function() {
                selectAllCheck.prop('indeterminate', false);

                if (selectAllCheck.is(':checked')) {
                    menuGroup.find('input').not(_allowedCheck).each(function() {
                        $(this).prop("checked", true);
                    });
                } else {
                    menuGroup.find('input').not(_allowedCheck).each(function() {
                        $(this).prop("checked", false);
                    });
                }
            });

            menuGroup.find('input').not(_selectAllCheck)
                     .not(_allowedCheck)
                     .each(function() {
                if ($(this).is(':checked')) {
                    $(selectAllCheck).prop('indeterminate', true);
                }

                $(this).unbind('change').change(function() {
                    $(selectAllCheck).prop('indeterminate', true);
                });
            });
		});
	}

    $.fn.showCreate = function(url) {
        $('#mdb-preloader').css('background', '#000000ab').fadeIn(300);
        $('#modal-body-create').load(url, function() {
            $('#mdb-preloader').fadeOut(300);
            $('.mdb-select').materialSelect();
            $(this).slideToggle(500);
            toggleSignatoryInputs();
        });
        $("#modal-sm-create").modal({keyboard: false, backdrop: 'static'})
						     .on('shown.bs.modal', function() {
            $('#create-title').html('Create Signatories');
		}).on('hidden.bs.modal', function() {
		    $('#modal-body-create').html('').css('display', 'none');
		});
    }

    $.fn.store = function() {
        const withError = inputValidation(false);
        const jsonData = convertAccessToJson();

		if (!withError) {
            $('#json-access').val(jsonData);
			$('#form-store').submit();
        }
    }

    $.fn.showEdit = function(url) {
        $('#mdb-preloader').css('background', '#000000ab').fadeIn(300);
        $('#modal-body-edit').load(url, function() {
            $('#mdb-preloader').fadeOut(300);
            $('.mdb-select').materialSelect();
            $(this).slideToggle(500);
            toggleSignatoryInputs();
        });
        $("#modal-sm-edit").modal({keyboard: false, backdrop: 'static'})
						   .on('shown.bs.modal', function() {
            $('#edit-title').html('Update Signatories');
		}).on('hidden.bs.modal', function() {
            $('#modal-body-edit').html('').css('display', 'none');
		});
    }

    $.fn.update = function() {
        const withError = inputValidation(false);
        const jsonData = convertAccessToJson();

		if (!withError) {
            $('#json-access').val(jsonData);
			$('#form-update').submit();
		}
    }

    $.fn.showDelete = function(url, name) {
		$('#modal-body-delete').html(`Are you sure you want to delete '${name}'?`);
        $("#modal-delete").modal({keyboard: false, backdrop: 'static'})
						  .on('shown.bs.modal', function() {
            $('#delete-title').html('Delete Signatories');
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
