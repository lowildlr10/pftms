$(function() {
	function inputValidation(withError) {
		var errorCount = 0;

        $(".required").each(function() {
            var inputField = $(this).val().replace(/^\s+|\s+$/g, "").length;
            console.log($(this));

			if (inputField == 0) {
				$(this).addClass("input-error-highlighter");
				errorCount++;
			} else {
				$(".input-quantity").each(function() {
					if ($(this).val() == "0") {
			            $(this).addClass("input-error-highlighter");
			            errorCount++;
			        }
				});

				$(this).removeClass("input-error-highlighter");
			}
		});

		if (errorCount == 0) {
			withError = false;
		} else {
			withError = true;
		}

		return withError;
	}

	$.fn.computeCost = function(cnt, obj) {
		var objId;
		var totalCost = 0;

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
		var table = $(tableID).get(0);
		var rowCount = table.rows.length;
		var row = table.insertRow(rowCount);
		var iteration = rowCount;
		var origCount = $('#item-count').get(0).value;
		var conCount = eval(origCount) + 1;
	    var colCount = table.rows[0].cells.length;

		row.id = "row-" + (rowCount - 1);

	    for (var i = 0; i < colCount; i++) {
	        var newcell = row.insertCell(i);
			$(newcell).html($(table.rows[1].cells[i]).html());

			switch(i){
				case 0:
                    $(newcell).addClass('hidden-xs')
                              .find('input')
							  .attr('id', 'id' + conCount)
                              .attr('name', 'id' + conCount);
				break;
				case 1:
					$(newcell).find('select')
							  .attr('id', 'unit' + conCount)
							  .val('1');
				break;
				case 2:
					$(newcell).find('textarea')
							  .attr('id', 'item_description' + conCount)
							  .val('');
				break;
				case 3:
					$(newcell).find('input')
							  .attr('id', 'quantity' + conCount)
							  .attr('onkeyup', '$(this).computeCost(' + conCount + ', "unit_cost' + conCount + '")')
							  .attr('onchange', '$(this).computeCost(' + conCount + ', "unit_cost' + conCount + '")')
							  .val('');
				break;
				case 4:
					$(newcell).find('input')
							  .attr('id', 'unit_cost' + conCount)
							  .attr('onkeyup', '$(this).computeCost(' + conCount + ', "unit_cost' + conCount + '")')
							  .attr('onchange', '$(this).computeCost(' + conCount + ', "unit_cost' + conCount + '")')
							  .val('');
				break;
				case 5:
					$(newcell).find('input')
							  .attr('id', 'total_cost' + conCount)
							  .attr('disabled', 'disabled')
							  .val('');
				break;
				case 6:
					$(newcell).find('a')
							  .attr('onclick', "$(this).deleteRow('item-pr-table', 'row-" + (rowCount - 1) + "')");
				break;
			}
	    }

	    $("#item-count").val(conCount);
	}

	$.fn.deleteRow = function(tableID, row) {
		if (confirm('Are you sure you want to remove this item?')) {
			var rowCount = $('#row-items tr').length;

			if (rowCount > 1) {

				$('#' + row).remove();
			} else {
				alert('Cannot delete all the rows.');
			}
		}
	}

	$.fn.createUpdateDoc = function() {
		var withError = inputValidation(false);

		if (!withError) {
			$('#form-create').submit();
		}
	}

	$.fn.showCreate = function() {
        $('#btn-create-update').html('<i class="fas fa-pencil-alt"></i> Create');
        $('#mdb-preloader').css('background', '#000000ab').fadeIn(300);
		$('#modal-body-create').load('pr/create', function() {
            $('#mdb-preloader').fadeOut(300);
			$('#form-create').attr('action', 'pr/save');
		});
		$("#central-create-modal").modal({keyboard: false, backdrop: 'static'})
						.on('shown.bs.modal', function() {
				            $('.mdb-select-1').materialSelect();
				   		}).on('hidden.bs.modal', function() {
					        $('#modal-body-create').html(modalLoadingContent);
					    });
	}

	$.fn.viewItems = function(id) {
		$('#modal-body-content-2').load('pr/show/' + id);
		$("#view-modal").modal({keyboard: false, backdrop: 'static'})
						.on('shown.bs.modal', function() {

				   		}).on('hidden.bs.modal', function() {
					        $('#modal-body-content-2').html('<br><div class="progress">' +
																  '<div class="progress-bar progress-bar-striped active" role="progressbar"' +
																  'aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width:100%">' +
																    'Loading...' +
																  '</div>' +
															   '</div>');
					    });
	}

	$.fn.showEdit = function(id) {
        $('#btn-create-update').html('<i class="fas fa-edit"></i> Update');
        $('#mdb-preloader').css('background', '#000000ab').fadeIn(300);
		$('#modal-body-edit').load('pr/edit/' + id, function() {
            $('#mdb-preloader').fadeOut(300);
			$('#form-create').attr('action', 'pr/update/' + id);
		});
		$("#central-edit-modal").modal()
						.on('shown.bs.modal', function() {
				            $('.mdb-select-1').materialSelect();
				   		}).on('hidden.bs.modal', function() {
					        $('#modal-body-edit').html(modalLoadingContent);
					    });
	}

	$.fn.delete = function(id) {
		if (confirm('Are you sure you want to delete this purchase request?')) {
			$('#form-validation').attr('action', 'pr/delete/' + id).submit();
		}
	}

	$.fn.approve = function(id) {
		if (confirm('Are you sure you want to approve this purchase request?')) {
			$('#form-validation').attr('action', 'pr/approve/' + id).submit();
		}
	}

	$.fn.disapprove = function(id) {
		if (confirm('Are you sure you want to disapprove this purchase request?')) {
			$('#form-validation').attr('action', 'pr/disapprove/' + id).submit();
		}
	}

	$.fn.cancel = function(id) {
		if (confirm('Are you sure you want to cancel this purchase request?')) {
			$('#form-validation').attr('action', 'pr/cancel/' + id).submit();
		}
	}

	$('.mdb-select-filter').materialSelect();

});