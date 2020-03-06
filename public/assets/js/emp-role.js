$(function() {
    const template = '<div class="tooltip md-tooltip">' +
                     '<div class="tooltip-arrow md-arrow"></div>' +
                     '<div class="tooltip-inner md-inner stylish-color"></div></div>';
    const cBoxIDs = [
        '#ca_ors_burs',
        '#ca_dv',
        '#ca_lr',
        '#proc_pr',
        '#proc_rfq',
        '#proc_abstract',
        '#proc_po_jo',
        '#proc_ors_burs',
        '#proc_iar',
        '#proc_dv',
        '#pay_lddap',
        '#inv_stock',
        '#track_pr_rfq',
        '#track_rfq_abs',
        '#track_abs_po',
        '#track_po_ors',
        '#track_po_iar',
        '#track_iar_stock',
        '#track_iar_dv',
        '#track_ors_dv',
        '#track_dv_lddap',
        '#track_dis_sum',
        '#track_sum_bank',
        '#lib_inv_class',
        '#lib_item_class',
        '#lib_proc_mode',
        '#lib_funding',
        '#lib_signatory',
        '#lib_sup_class',
        '#lib_supplier',
        '#lib_unit_issue',
        '#lib_paper_size',
        '#acc_division',
        '#acc_role',
        '#acc_group',
        '#acc_account',
        '#acc_user_log',
        '#place_region',
        '#place_province'
    ];

    function convertAccessToJson() {
        let jsonData = {};

        $.each(cBoxIDs, function(i, id) {
            const moduleCbox = $(id);
            const menuGroup = $(id + '-menu');
            const parentModule = moduleCbox.val();
            let _jsonData = {};

            if (parentModule) {
                if (moduleCbox.is(':checked')) {
                    _jsonData['is_allowed'] = 1;

                    menuGroup.find('input').each(function() {
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

                    menuGroup.find('input').each(function() {
                        const parentAttr = $(this).val();

                        if (parentAttr) {
                            _jsonData[parentAttr] = 0;
                        }
                    });
                }

                jsonData[parentModule] = _jsonData;
                //jsonData.push(_jsonData);
                //jsonData += JSON.stringify(_jsonData);
            }
        });

        jsonData = JSON.stringify(jsonData)

        return jsonData;
    }

    function toggleSignatoryInputs() {
		$.each(cBoxIDs, function(i, id) {
			const moduleCbox = $(id);

			moduleCbox.unbind('change').change(function() {
                const menuGroup = $(id + '-menu');

				if (moduleCbox.is(':checked')) {
                    menuGroup.slideToggle(300);
                    menuGroup.find('input').each(function() {
                        $(this).prop("checked", false);
                    });
				} else {
                    menuGroup.slideToggle(300);
                    menuGroup.find('input').each(function() {
                        $(this).prop("checked", false);
                    });
				}
			});
		});
	}

    $.fn.showCreate = function(url) {
        $('#mdb-preloader').css('background', '#000000ab').fadeIn(300);
        $('#modal-body-create').load(url, function() {
            $('#mdb-preloader').fadeOut(300);
            toggleSignatoryInputs();
        });
        $("#modal-sm-create").modal({keyboard: false, backdrop: 'static'})
						     .on('shown.bs.modal', function() {
            $('#create-title').html('Create Role');
		}).on('hidden.bs.modal', function() {
		     $('#modal-create-body').html(modalLoadingContent);
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
            toggleSignatoryInputs();
        });
        $("#modal-sm-edit").modal({keyboard: false, backdrop: 'static'})
						   .on('shown.bs.modal', function() {
            $('#edit-title').html('Update Role');
		}).on('hidden.bs.modal', function() {
		     $('#modal-edit-body').html(modalLoadingContent);
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
            $('#delete-title').html('Delete Role');
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
