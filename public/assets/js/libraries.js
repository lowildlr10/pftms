$(function() {
	function inputValidation(withError) {
		var errorCount = 0;

        $(".required").each(function() {
			var inputField = $(this).val().replace(/^\s+|\s+$/g, "").length;

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

	function toggleSignatoryInputs() {
		var cBoxIDs = ['#p-req', '#abs', '#po-jo', '#ors', '#iar',
                       '#dv', '#ris', '#par', '#ics', '#liquidation',
                       '#lddap', '#summary', '#lib', '#librealign'];

		/*
		var cBoxNames = ['p_req', 'abs', 'po_jo', 'ors', 'iar',
						 'dv', 'ris', 'par', 'ics', 'liquidation'];
		*/

		$.each(cBoxIDs, function(i, id) {
			var moduleCbox = $(id);

			moduleCbox.unbind('change').change(function() {
				var selInputs = $(id + '-sign');

				if (moduleCbox.is(':checked')) {
					selInputs.removeAttr('disabled')
							 .addClass('required');
				} else {
					selInputs.attr('disabled', 'disabled')
							 .removeClass('required')
							 .removeClass('input-error-highlighter')
							 .val('');
				}
			});
		});


		/*
		$.each(cBoxNames, function(i, name) {
			var moduleCbox = $('input[name="' + name + '"]');

			moduleCbox.unbind('change').change(function() {
				var selInputs = moduleCbox.closest('.checkbox');

				if (moduleCbox.is(':checked')) {
					selInputs.next('.well').css('background', '#fff');
					selInputs.next('.well').find('select')
							 .removeAttr('disabled')
							 .addClass('required');
				} else {
					selInputs.next('.well').css('background', '#f5f5f5');
					selInputs.next('.well').find('select')
							 .attr('disabled', 'disabled')
							 .removeClass('required')
							 .val('');
				}
			});
		});*/
	}

	function toggleSupplierInputs() {
		var attachChkboxElem = $('#check-attachment');

		$('select[name="nature_business"]').change(function() {
			var natureBusiness = $(this).val();

			if (natureBusiness == 'others') {
				$(this).next('div').fadeIn().removeAttr('hidden').find('input')
								   .addClass('required').removeAttr('disabled', 'disabled');
			} else {
				$(this).next('div').fadeOut().attr('hidden', 'hidden').find('input')
								   .removeClass('required').attr('disabled', 'disabled');
			}
		});

		attachChkboxElem.find('input[type="checkbox"]').each(function() {
			$(this).click(function() {
				var checkValues = [];
				var attachmentVal = "";

				attachChkboxElem.find('input[type="checkbox"]:checked').each(function() {
					var tempCheckVal = $(this).val();
					checkValues.push(tempCheckVal);
				});

				checkInOthers = $.inArray("7", checkValues);

				if (checkInOthers > -1) {
					attachChkboxElem.next('div').removeAttr('hidden').find('input')
								    .addClass('required').removeAttr('disabled', 'disabled');
				} else {
					attachChkboxElem.next('div').attr('hidden', 'hidden').find('input')
								    .removeClass('required').attr('disabled', 'disabled');
				}

				$.each(checkValues, function(i, val) {
					if (i == 0) {
						attachmentVal += val;
					} else {
						attachmentVal += "-" + val;
					}
				});

				$('input[name="attachment"]').val(attachmentVal);
			});
		});
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

	    for (var i = 0; i < colCount; i++) {
	        var newcell = row.insertCell(i);
	        $(newcell).html($(table.rows[1].cells[i]).html());


			switch(i){
				case 0:
					$(newcell).find('input')
							  .attr('id', 'id' + conCount)
							  .attr('name', 'id' + conCount);
				break;
				case 1:

				break;
				case 2:

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
							  .attr('onclick', '$(this).deleteRow(\'item-pr-table\',\'id' + conCount + '\')');
				break;
			}
	    }

	    $("#item-count").val(conCount);
	}

	$.fn.deleteRow = function(tableID, who) {
		if (confirm('Are you sure you want to remove this item?')) {
			document.getElementById(who).checked = 1;

			try {
			    var table = document.getElementById(tableID);
			    var rowCount = table.rows.length;

				for (var i = 0; i<rowCount; i++) {
			        var row = table.rows[i];
			        var chkbox = row.cells[0].childNodes[0];

			        if (null != chkbox && true == chkbox.checked) {
						if (rowCount <= 2) {
							 alert("Cannot delete all the rows.");
							 document.getElementById(who).checked=0;
						} else {
							table.deleteRow(i);
			            }

						rowCount--;
			            i--;
			        }
			    }
		    } catch(e) {
		        alert(e);
		    }
		}
	}

	$.fn.createUpdate = function() {
		var withError = inputValidation(false);

		if (!withError) {
			$('#form-create-update').submit();
		}
    }

    $.fn.showCreate = function(type) {
		if (type == 'employee') {
			$('#modal-body-sm').load('../profile/create');
		} else {
			$('#modal-body-sm').load('create/' + type, function() {
				$('#form-create-update').attr('action', 'store/' + type);

				if (type == 'signatory') {
					toggleSignatoryInputs();
				} else if (type == 'supplier') {
					toggleSupplierInputs();
				}
			});
		}

		$("#smcard-central-modal").modal({keyboard: false, backdrop: 'static'})
						          .on('shown.bs.modal', function() {
		    $('#btn-create-update').html('<i class="fas fa-pencil-alt"></i> Create')
		    					   .removeClass('btn-orange')
		    					   .addClass('btn-primary');
		}).on('hidden.bs.modal', function() {
		     $('#modal-body-sm').html(modalLoadingContent);
		 });
	}

    /*
	$.fn.showCreate = function(type) {
		if (type == 'employee') {
			$('#modal-body-sm').load('../profile/create');
		} else {
			$('#modal-body-sm').load('create/' + type, function() {
				$('#form-create-update').attr('action', 'store/' + type);

				if (type == 'signatory') {
					toggleSignatoryInputs();
				} else if (type == 'supplier') {
					toggleSupplierInputs();
				}
			});
		}

		$("#smcard-central-modal").modal({keyboard: false, backdrop: 'static'})
						.on('shown.bs.modal', function() {
				            $('#btn-create-update').html('<i class="fas fa-pencil-alt"></i> Create')
				            					   .removeClass('btn-orange')
				            					   .addClass('btn-primary');
				   		}).on('hidden.bs.modal', function() {
					        $('#modal-body-sm').html(modalLoadingContent);
					    });
	}*/

	$.fn.showEdit = function(key, type) {
		if (type == 'employee') {
			$('#modal-body-sm').load('../profile/edit/' + key);
		} else {
			$('#modal-body-sm').load('edit/' + type + '?key=' + key, function() {
				$('#form-create-update').attr('action', 'update/' + type);

				if (type == 'signatory') {
					toggleSignatoryInputs();
				} else if (type == 'supplier') {
					toggleSupplierInputs();
				}
			});
		}

		$("#smcard-central-modal").modal({keyboard: false, backdrop: 'static'})
						.on('shown.bs.modal', function() {
				            $('#btn-create-update').html('<i class="fas fa-edit"></i> Update')
				            					   .removeClass('btn-primary')
				            					   .addClass('btn-orange');
				   		}).on('hidden.bs.modal', function() {
					        $('#modal-body-sm').html(modalLoadingContent);
					    });
	}

	$.fn.delete = function(id, type) {
		if (type == 'division') {
			if (confirm('Are you sure you want to delete this division?')) {
				$('#form-validation').attr('action', 'delete/' + type + '?key=' + id).submit();
			}
		} else if (type == 'item_classification') {
			if (confirm('Are you sure you want to delete this item classification?')) {
				$('#form-validation').attr('action', 'delete/' + type + '?key=' + id).submit();
			}
		} else if (type == 'mode_procurement') {
			if (confirm('Are you sure you want to delete this mode of procurement?')) {
				$('#form-validation').attr('action', 'delete/' + type + '?key=' + id).submit();
			}
		} else if (type == 'project') {
			if (confirm('Are you sure you want to delete this project/charging?')) {
				$('#form-validation').attr('action', 'delete/' + type + '?key=' + id).submit();
			}
		} else if (type == 'signatory') {
			if (confirm('Are you sure you want to delete this signatory?')) {
				$('#form-validation').attr('action', 'delete/' + type + '?key=' + id).submit();
			}
		} else if (type == 'supplier_classification') {
			if (confirm('Are you sure you want to delete this supplier classification?')) {
				$('#form-validation').attr('action', 'delete/' + type + '?key=' + id).submit();
			}
		} else if (type == 'supplier') {
			if (confirm('Are you sure you want to delete this supplier?')) {
				$('#form-validation').attr('action', 'delete/' + type + '?key=' + id).submit();
			}
		} else if (type == 'unit_issue') {
			if (confirm('Are you sure you want to delete this unit of issue?')) {
				$('#form-validation').attr('action', 'delete/' + type + '?key=' + id).submit();
			}
		} else if (type == 'employee') {
			if (confirm('Are you sure you want to delete this employee?')) {
				$('#form-validation').attr('action', '../profile/delete/' + id).submit();
			}
		} else if (type == 'user_group') {
			if (confirm('Are you sure you want to delete this group?')) {
				$('#form-validation').attr('action', 'delete/' + type + '?key=' + id).submit();
			}
		}
	}

	$('.mdb-select-filter').materialSelect();

});
