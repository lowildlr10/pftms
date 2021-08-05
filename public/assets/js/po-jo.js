$(function() {
    const template = '<div class="tooltip md-tooltip">' +
                     '<div class="tooltip-arrow md-arrow"></div>' +
                     '<div class="tooltip-inner md-inner stylish-color"></div></div>';

    function initializeInputs() {
        $('#mdb-preloader').fadeOut(300);
        $('.crud-select').materialSelect();
        $('#grand-total').keyup(function() {
            $('#amount-words').val(toWordsconvert($(this).val()));
            $('#amount-words').siblings('label').addClass('active');
        });
    }

	$.fn.computeCost = function(cnt, obj, t = 1) {
		let grandTotal = 0,
		    objId,
		    totalCost = 0;

		if (t == 1) {
			if (obj != null) {
		      objId = obj;
		    } else {
		      objId = this.id;
		    }

			if (objId.search(/quantity/i) == 0) {
				cnt = parseFloat(objId.replace('quantity',' '), 10);
			} else {
				cnt = parseFloat(objId.replace('unit_cost',' '), 10);
			}

			totalCost = $('#unit_cost' + cnt).val() * $('#quantity' + cnt).val()

			$('#total_cost' + cnt).val(totalCost.toFixed(2));
            $('#amount-words').val(toWordsconvert(parseFloat(totalCost).toFixed(2)));
            $('#amount-words').siblings('label').addClass('active');
		}

		$(".total-cost").each(function() {
            const isExcluded = $(this).closest('td')
                                      .siblings()
                                      .find('select.exclude')
                                      .val();

            if (isExcluded == 'n') {
                grandTotal += parseFloat($(this).val());
            }
		});

		$("#grand-total").val(grandTotal.toFixed(2));
	}

	$.fn.showItem = function(url) {
        $('#mdb-preloader').css('background', '#000000ab').fadeIn(300);
        $('#modal-body-show').load(url, function() {
            $(this).slideToggle(500);
            initializeInputs();
        });
        $("#modal-show").modal({keyboard: false, backdrop: 'static'})
						.on('shown.bs.modal', function() {
            $('#show-title').html('View Items');
		}).on('hidden.bs.modal', function() {
		    $('#modal-body-show').html('').css('display', 'none');
		});
    }

    $.fn.showCreate = function(url) {
        $('#mdb-preloader').css('background', '#000000ab').fadeIn(300);
        $('#modal-body-create').load(url, function() {
            $(this).slideToggle(500);
            initializeInputs();
        });
        $("#modal-sm-create").modal({keyboard: false, backdrop: 'static'})
						     .on('shown.bs.modal', function() {
            $('#create-title').html('Create Purchase/Job Order');
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
            $(this).slideToggle(500);
            initializeInputs();
        });
        $("#modal-lg-edit").modal({keyboard: false, backdrop: 'static'})
						   .on('shown.bs.modal', function() {
            $('#edit-title').html('Update Purchase/Job Order');
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
		$('#modal-body-delete').html(`Are you sure you want to delete this ${name} `+
                                     `document?`);
        $("#modal-delete").modal({keyboard: false, backdrop: 'static'})
						  .on('shown.bs.modal', function() {
            $('#delete-title').html('Delete Purchase/Job Order');
            $('#form-delete').attr('action', url);
		}).on('hidden.bs.modal', function() {
             $('#modal-delete-body').html('');
             $('#form-delete').attr('action', '#');
		});
    }

    $.fn.delete = function() {
        $('#form-delete').submit();
    }

    $.fn.showIssue = function(url) {
        $('#mdb-preloader').css('background', '#000000ab').fadeIn(300);
        $('#modal-body-issue').load(url, function() {
            $(this).slideToggle(500);
            initializeInputs();
        });
        $("#modal-issue").modal({keyboard: false, backdrop: 'static'})
						 .on('shown.bs.modal', function() {
            $('#issue-title').html('Issue Purchase/Job Order');
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
            $('#receive-title').html('Receive Purchase/Job Order');
		}).on('hidden.bs.modal', function() {
            $('#modal-body-receive').html('').css('display', 'none');
		});
    }

    $.fn.receive = function() {
        $('#form-receive').submit();
    }

    $.fn.showAccountantSigned = function(url, name) {
        $('#modal-body-accountant-signed').html(`Are you sure you want to set this ${name} `+
                                                `document to 'Cleared/Signed by Accountant'?`);
        $("#modal-accountant-signed").modal({keyboard: false, backdrop: 'static'})
						  .on('shown.bs.modal', function() {
            $('#accountant-signed-title').html('PO/JO Cleared/Signed by Accountant');
            $('#form-accountant-signed').attr('action', url);
		}).on('hidden.bs.modal', function() {
             $('#modal-accountant-signed-body').html('');
             $('#form-accountant-signed').attr('action', '#');
		});
    }

    $.fn.accountantSigned = function() {
        $('#form-accountant-signed').submit();
    }

    $.fn.showApprove = function(url, name) {
        $('#modal-body-approve').html(`Are you sure you want to set this ${name} `+
                                      `document to 'Approved'?`);
        $("#modal-approve").modal({keyboard: false, backdrop: 'static'})
						  .on('shown.bs.modal', function() {
            $('#approve-title').html('Approve Purchase/Job Order');
            $('#form-approve').attr('action', url);
		}).on('hidden.bs.modal', function() {
             $('#modal-approve-body').html('');
             $('#form-approve').attr('action', '#');
		});
    }

    $.fn.approve = function() {
        $('#form-approve').submit();
    }

    $.fn.showCancel = function(url, name) {
		$('#modal-body-cancel').html(`Are you sure you want to cancel '${name}'?`);
        $("#modal-cancel").modal({keyboard: false, backdrop: 'static'})
						  .on('shown.bs.modal', function() {
            $('#cancel-title').html('Cancel Purchase/Job Order');
            $('#form-cancel').attr('action', url);
		}).on('hidden.bs.modal', function() {
             $('#modal-cancel-body').html('');
             $('#form-cancel').attr('action', '#');
		});
    }

    $.fn.cancel = function() {
        $('#form-cancel').submit();
    }

    $.fn.showUncancel = function(url, name) {
		$('#modal-body-uncancel').html(`Are you sure you want to uncancel '${name}'?`);
        $("#modal-uncancel").modal({keyboard: false, backdrop: 'static'})
						  .on('shown.bs.modal', function() {
            $('#uncancel-title').html('Uncancel Purchase/Job Order');
            $('#form-uncancel').attr('action', url);
		}).on('hidden.bs.modal', function() {
             $('#modal-uncancel-body').html('');
             $('#form-uncancel').attr('action', '#');
		});
    }

    $.fn.unCancel = function() {
        $('#form-uncancel').submit();
    }

    $.fn.showCreateORS = function(url, name) {
        $('#modal-body-create-ors').html(`Are you sure you want to create the ORS/BURS `+
                                         `document for this ${name} document?`);
        $("#modal-create-ors").modal({keyboard: false, backdrop: 'static'})
						  .on('shown.bs.modal', function() {
            $('#create-ors-title').html('Create ORS/BURS for PO/JO');
            $('#form-create-ors').attr('action', url);
		}).on('hidden.bs.modal', function() {
             $('#modal-create-ors-body').html('');
             $('#form-create-ors').attr('action', '#');
		});
    }

    $.fn.createORS = function() {
        $('#form-create-ors').submit();
    }

    $.fn.showForDelivery = function(url, name) {
        $('#modal-body-delivery').html(`Are you sure you want to set this ${name} `+
                                       `document to 'For Delivery'?`);
        $("#modal-delivery").modal({keyboard: false, backdrop: 'static'})
						  .on('shown.bs.modal', function() {
            $('#delivery-title').html('PO/JO For Delivery');
            $('#form-delivery').attr('action', url);
		}).on('hidden.bs.modal', function() {
             $('#modal-delivery-body').html('');
             $('#form-delivery').attr('action', '#');
		});
    }

    $.fn.forDelivery = function() {
        $('#form-delivery').submit();
    }

    $.fn.showForInspection = function(url, name) {
        $('#modal-body-inspection').html(`Are you sure you want to set this ${name} `+
                                         `document to 'For Inspection'?`);
        $("#modal-inspection").modal({keyboard: false, backdrop: 'static'})
						  .on('shown.bs.modal', function() {
            $('#inspection-title').html('PO/JO For Inspection');
            $('#form-inspection').attr('action', url);
		}).on('hidden.bs.modal', function() {
             $('#modal-inspection-body').html('');
             $('#form-inspection').attr('action', '#');
		});
    }

    $.fn.forInspection = function() {
        $('#form-inspection').submit();
    }

    $('.material-tooltip-main').tooltip({
        template: template
    });
});
