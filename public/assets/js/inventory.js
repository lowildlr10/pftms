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

    $.fn.addRow = function(tableID) {
		const table = $(tableID).get(0);
		const rowCount = table.rows.length;
		let row = table.insertRow(rowCount);
		const iteration = rowCount;
		const origCount = $('#item-count').get(0).value;
		const conCount = eval(origCount) + 1;
	    const colCount = table.rows[0].cells.length;

		row.id = "row-" + (rowCount - 1);

	    for (let i = 0; i < colCount; i++) {
	        const newcell = row.insertCell(i);
			$(newcell).html($(table.rows[1].cells[i]).html());

			switch(i){
				case 0:
                    /*
                    $(newcell).find('div.md-form').first().append($(newcell).find('select'));
                    $(newcell).find('div.select-wrapper').remove();
                    $(newcell).find('select').attr('id', 'unit' + conCount).val('').materialSelect();

                    $(newcell).find('input')
							  .attr('name', 'unit[]')
                              .val('');*/

                    $(newcell).find('select')
							  .attr('name', 'unit[]')
                              .val('');
				break;
				case 1:
                    $(newcell).find('textarea')
                              .attr('name', 'description[]')
							  .val('');
				break;
				case 2:
					$(newcell).find('input')
							  .attr('name', 'quantity[]')
							  .val('');
                break;
                case 3:
					$(newcell).find('input')
							  .attr('name', 'amount[]')
							  .val('');
                break;
                case 4:
                    /*
                    $(newcell).find('div.md-form').first().append($(newcell).find('select'));
                    $(newcell).find('div.select-wrapper').remove();
                    $(newcell).find('select').attr('id', 'item_classification' + conCount).val('').materialSelect();

                    $(newcell).find('input')
							  .attr('name', 'item_classification[]')
                              .val('');*/
                    $(newcell).find('select')
							  .attr('name', 'item_classification[]')
                              .val('');
				break;
				case 5:
					$(newcell).find('a')
							  .attr('onclick', "$(this).deleteRow('#row-" + (rowCount - 1) + "')");
				break;
			}
	    }

	    $("#item-count").val(conCount);
	}

	$.fn.showCreate = function(url, classification) {
        let name = '';

		if (classification == 'ris') {
			name = 'Requisition and Issue Slip';
		} else if (classification == 'par') {
			name = 'Property Aknowledgement Receipt';
		} else {
			name = 'Inventory Custodian Slip';
        }

		$('#mdb-preloader').css('background', '#000000ab').fadeIn(300);
        $('#modal-body-create').load(url, function() {
            $('#mdb-preloader').fadeOut(300);
            $('.crud-select').materialSelect();
            $(this).slideToggle(500);
            initializeQtyInput();
        });
        $("#modal-lg-create").modal({keyboard: false, backdrop: 'static'})
						     .on('shown.bs.modal', function() {
            $('#create-title').html(`Create ${name}`);
		}).on('hidden.bs.modal', function() {
		    $('#modal-body-create').html('').css('display', 'none');
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

    $.fn.showEdit = function(url, classification) {
        let name = '';

		if (classification == 'ris') {
			name = 'Requisition and Issue Slip';
		} else if (classification == 'par') {
			name = 'Property Aknowledgement Receipt';
		} else {
			name = 'Inventory Custodian Slip';
        }

        $('#mdb-preloader').css('background', '#000000ab').fadeIn(300);
        $('#modal-body-edit').load(url, function() {
            $('#mdb-preloader').fadeOut(300);
            $('.crud-select').materialSelect();
            $(this).slideToggle(500);
        });
        $("#modal-lg-edit").modal({keyboard: false, backdrop: 'static'})
						   .on('shown.bs.modal', function() {
            $('#edit-title').html(`Update ${name}`);
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
        if (confirm('Are you sure you want to remove this item?')) {
			const rowCount = $('#row-items tr').length;

			if (rowCount > 1) {
                $(row).fadeOut(300, function() {
                    $(this).remove();
                });
			} else {
				alert('Cannot delete all the rows.');
			}
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

    $.fn.showDelete = function(url, name) {
		$('#modal-body-delete').html(`Are you sure you want to delete "${name}"?`);
        $("#modal-delete").modal({keyboard: false, backdrop: 'static'})
						  .on('shown.bs.modal', function() {
            $('#delete-title').html('Delete Inventory');
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

    $('.material-tooltip-main').tooltip({
        template: template
    });
});
