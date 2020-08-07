$(function() {
	const template = '<div class="tooltip md-tooltip">' +
                     '<div class="tooltip-arrow md-arrow"></div>' +
                     '<div class="tooltip-inner md-inner stylish-color"></div></div>';

    $.fn.computeCost = function(cnt, obj) {
		let objId,
		    totalCost = 0;

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
                    $(newcell).find('div.md-form').first().append($(newcell).find('select'));
                    $(newcell).find('div.select-wrapper').remove();
                    $(newcell).find('select').attr('id', 'unit' + conCount).prop('selectedIndex', 0).materialSelect();

                    $(newcell).find('input')
                              .first()
							  .attr('name', 'item_id[]')
                              .val('0');
				break;
				case 1:
					$(newcell).find('textarea')
							  .val('');
				break;
				case 2:
					$(newcell).find('input')
							  .attr('id', 'quantity' + conCount)
							  .attr('onkeyup', '$(this).computeCost(' + conCount + ', "unit_cost' + conCount + '")')
							  .attr('onchange', '$(this).computeCost(' + conCount + ', "unit_cost' + conCount + '")')
							  .val('');
				break;
				case 3:
					$(newcell).find('input')
							  .attr('id', 'unit_cost' + conCount)
							  .attr('onkeyup', '$(this).computeCost(' + conCount + ', "unit_cost' + conCount + '")')
							  .attr('onchange', '$(this).computeCost(' + conCount + ', "unit_cost' + conCount + '")')
							  .val('');
				break;
				case 4:
					$(newcell).find('input')
							  .attr('id', 'total_cost' + conCount)
							  .attr('readonly', 'readonly')
							  .val('');
				break;
				case 5:
					$(newcell).find('a')
							  .attr('onclick', "$(this).deleteRow('item-pr-table', 'row-" + (rowCount - 1) + "')");
				break;
			}
	    }

	    $("#item-count").val(conCount);
	}

	$.fn.deleteRow = function(tableID, row) {
		if (confirm('Are you sure you want to remove this item?')) {
			const rowCount = $('#row-items tr').length;

			if (rowCount > 1) {
                $('#' + row).fadeOut(300, function() {
                    $(this).remove();
                });
			} else {
				alert('Cannot delete all the rows.');
			}
		}
	}

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

    $.fn.showCreate = function(url) {
        $('#mdb-preloader').css('background', '#000000ab').fadeIn(300);
        $('#modal-body-create').load(url, function() {
            $('#mdb-preloader').fadeOut(300);
            $('.crud-select').materialSelect();
            $(this).slideToggle(500);
        });
        $("#modal-lg-create").modal({keyboard: false, backdrop: 'static'})
						     .on('shown.bs.modal', function() {
            $('#create-title').html('Create Purchase Request');
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

    $.fn.showDelete = function(url, name) {
		$('#modal-body-delete').html(`Are you sure you want to delete '${name}'?`);
        $("#modal-delete").modal({keyboard: false, backdrop: 'static'})
						  .on('shown.bs.modal', function() {
            $('#delete-title').html('Delete Purchase Request');
            $('#form-delete').attr('action', url);
		}).on('hidden.bs.modal', function() {
             $('#modal-delete-body').html('');
             $('#form-delete').attr('action', '#');
		});
    }

    $.fn.delete = function() {
        $('#form-delete').submit();
    }

    $.fn.showApprove = function(url, name) {
		$('#modal-body-approve').html(`Are you sure you want to approve '${name}'?`);
        $("#modal-approve").modal({keyboard: false, backdrop: 'static'})
						  .on('shown.bs.modal', function() {
            $('#approve-title').html('Approve Purchase Request');
            $('#form-approve').attr('action', url);
		}).on('hidden.bs.modal', function() {
             $('#modal-approve-body').html('');
             $('#form-approve').attr('action', '#');
		});
    }

    $.fn.approve = function() {
        $('#form-approve').submit();
    }

	$.fn.showDisapprove = function(url, name) {
		$('#modal-body-disapprove').html(`Are you sure you want to disapprove '${name}'?`);
        $("#modal-disapprove").modal({keyboard: false, backdrop: 'static'})
						  .on('shown.bs.modal', function() {
            $('#disapprove-title').html('Disapprove Purchase Request');
            $('#form-disapprove').attr('action', url);
		}).on('hidden.bs.modal', function() {
             $('#modal-disapprove-body').html('');
             $('#form-disapprove').attr('action', '#');
		});
    }

    $.fn.disapprove = function() {
        $('#form-disapprove').submit();
    }

	$.fn.showCancel = function(url, name) {
		$('#modal-body-cancel').html(`Are you sure you want to cancel '${name}'?`);
        $("#modal-cancel").modal({keyboard: false, backdrop: 'static'})
						  .on('shown.bs.modal', function() {
            $('#cancel-title').html('Cancel Purchase Request');
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
		$('#modal-body-uncancel').html(`Are you sure you want to restore '${name}'?`);
        $("#modal-uncancel").modal({keyboard: false, backdrop: 'static'})
						  .on('shown.bs.modal', function() {
            $('#uncancel-title').html('Restore Purchase Request');
            $('#form-uncancel').attr('action', url);
		}).on('hidden.bs.modal', function() {
             $('#modal-uncancel-body').html('');
             $('#form-uncancel').attr('action', '#');
		});
    }

    $.fn.unCancel = function() {
        $('#form-uncancel').submit();
    }

    $('.material-tooltip-main').tooltip({
        template: template
    });
});
