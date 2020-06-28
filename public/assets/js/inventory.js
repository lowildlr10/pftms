$(function() {
    const template = '<div class="tooltip md-tooltip">' +
                     '<div class="tooltip-arrow md-arrow"></div>' +
                     '<div class="tooltip-inner md-inner stylish-color"></div></div>';

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

	function initializeQtyInput() {
		$('.quantity').each(function() {
			var maxLength = $(this).attr('max');

			$(this).keydown(function () {
			    // Save old value.
			    if (!$(this).val() || (parseInt($(this).val()) <= maxLength &&
			    	parseInt($(this).val()) >= 0)) {
			    	$(this).data("old", $(this).val());
			    }
			}).keyup(function () {
			    // Check correct, else revert back to old value.
			    if (!$(this).val() || (parseInt($(this).val()) <= maxLength &&
			    	parseInt($(this).val()) >= 0)) {
			    } else {
			      $(this).val($(this).data("old"));
			    }
			});
		});
    }

    $.fn.showCreateIssueItem = function(url) {
        $('#mdb-preloader').css('background', '#000000ab').fadeIn(300);
        $('#modal-body-create').load(url, function() {
            $('#mdb-preloader').fadeOut(300);
            $('.crud-select').materialSelect();
            $(this).slideToggle(500);
            initializeQtyInput();
        });
        $("#modal-lg-create").modal({keyboard: false, backdrop: 'static'})
						     .on('shown.bs.modal', function() {
            $('#create-title').html('Create Issue Item / Property');
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
            $('.crud-select').materialSelect();
            $(this).slideToggle(500);
        });
        $("#modal-edit").modal({keyboard: false, backdrop: 'static'})
						   .on('shown.bs.modal', function() {
            $('#edit-title').html('Update Issued Items');
		}).on('hidden.bs.modal', function() {
            $('#modal-body-edit').html('').css('display', 'none');
		});
    }

    $.fn.showUpdateIssueItem = function(url) {
        $('#mdb-preloader').css('background', '#000000ab').fadeIn(300);
        $('#modal-body-edit').load(url, function() {
            $('#mdb-preloader').fadeOut(300);
            $('.crud-select').materialSelect();
            $(this).slideToggle(500);
            initializeQtyInput();
        });
        $("#modal-lg-edit").modal({keyboard: false, backdrop: 'static'})
						     .on('shown.bs.modal', function() {
            $('#edit-title').html('Update Issue Item / Property');
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

    $.fn.deleteRow = function(row) {
        if (confirm('Are you sure you want to delete this row?')) {
            $(row).fadeOut(300, function() {
                $(this).remove();
            });
		}
    }

    $.fn.showRecipients = function(url) {
        $('#mdb-preloader').css('background', '#000000ab').fadeIn(300);
        $('#modal-body-show').load(url, function() {
            $('#mdb-preloader').fadeOut(300);
            $(this).slideToggle(500);
        });
        $("#modal-show").modal({keyboard: false, backdrop: 'static'})
						   .on('shown.bs.modal', function() {
            $('#show-title').html('Recipients');
		}).on('hidden.bs.modal', function() {
            $('#modal-body-show').html('').css('display', 'none');
		});
    }

    $.fn.showDeleteIssue = function(url, name) {
		$('#modal-body-delete').html(`Are you sure you want to delete ${name}'s `+
                                     `issued items?`);
        $("#modal-delete").modal({keyboard: false, backdrop: 'static'})
						  .on('shown.bs.modal', function() {
            $('#delete-title').html('Delete Recipient');
            $('#form-delete').attr('action', url);
		}).on('hidden.bs.modal', function() {
             $('#modal-delete-body').html('');
             $('#form-delete').attr('action', '#');
		});
    }

    $.fn.delete = function() {
        $('#form-delete').submit();
    }

    $.fn.showIssue = function(url, name) {
        $('#modal-body-issue').html(`Are you sure you want to set this '${name}'
                                      to 'Issued'?`);
        $("#modal-issue").modal({keyboard: false, backdrop: 'static'})
						  .on('shown.bs.modal', function() {
            $('#issue-title').html('Set to Issued');
            $('#form-issue').attr('action', url);
		}).on('hidden.bs.modal', function() {
             $('#modal-issue-body').html('');
             $('#form-issue').attr('action', '#');
		});
    }

    $.fn.issue = function() {
        $('#form-issue').submit();
    }









    /*

	$.fn.showCreate = function(classification) {
		$('#modal-body-create').load('stocks/show-create/' + classification);
		$("#central-create-modal").modal({keyboard: false, backdrop: 'static'})
						.on('shown.bs.modal', function() {

				   		}).on('hidden.bs.modal', function() {
					        $('#modal-body-create').html(modalLoadingContent);
					    });
	}

	$.fn.showEdit = function(key, classification, type) {
		$('#modal-body-issue').load('stocks/show/' + key +
									'?classification=' + classification +
									'&type=' + type);
		$("#central-issue-modal").modal({keyboard: false, backdrop: 'static'})
						.on('shown.bs.modal', function() {
				            initializeQtyInput();
				   		}).on('hidden.bs.modal', function() {
					        $('#modal-body-issue').html(modalLoadingContent);
					    });
	}

	$.fn.showEditIssue = function(invNo, classification, empID) {
		$('#modal-body-edit').load('stocks/edit/' + invNo +
								   '?classification=' + classification +
								   '&received_by=' + empID);
		$("#central-edit-modal").modal({keyboard: false, backdrop: 'static'})
						.on('shown.bs.modal', function() {
				            initializeQtyInput();
				   		}).on('hidden.bs.modal', function() {
					        $('#modal-body-edit').html(modalLoadingContent);
					    });
	}

	$.fn.showIssued = function(invNo, classification) {
		$('#modal-body-sm').load('stocks/issued/' + invNo +
								 '?classification=' + classification);
		$("#smcard-central-modal").modal({keyboard: false, backdrop: 'static'})
						.on('shown.bs.modal', function() {

				   		}).on('hidden.bs.modal', function() {
					        $('#modal-body-sm').html(modalLoadingContent);
					    });
	}

	$.fn.saveLabel = function(invID, empID, element) {
		var serialNo = element.val();

		$.post('stocks/update-serial-no/' + invID, {
			_token: $('meta[name=csrf-token]').attr('content'),
		    received_by: empID,
		    serial_no: serialNo
		}).done(function(data) {
			showPrint(invID, 'label', empID);
		}).fail(function(xhr, status, error) {

		});
	}

	$.fn.issueDoc = function() {
		var withError = inputValidation(false);

		if (!withError) {
			$('input[name^=quantity]').each(function(index) {
				var maxValue = parseInt($(this).attr('max'));
				var currentValue = parseInt($(this).val());

				if ((currentValue > maxValue) && currentValue == 0) {
					$(this).addClass("input-error-highlighter");
					withError = true;
				}
			});
		}

		if (!withError) {
			$('#form-update').submit();
		}
	}

	$.fn.delete = function(invNo, classification, empID) {
		if (confirm("Delete issued item/s for " + empID +
					" [" + classification + " No: " + invNo + "]?")) {
			$('#form-validation').attr('action', 'stocks/delete/' + invNo + '?received_by=' + empID).submit();
		}
	}

	$.fn.issued = function(invNo) {
		if (confirm("Set this " + invNo + " to 'ISSUED'?")) {
			$('#form-validation').attr('action', 'stocks/set-issued/' + invNo).submit();
		}
    }*/

    $('.material-tooltip-main').tooltip({
        template: template
    });
});
