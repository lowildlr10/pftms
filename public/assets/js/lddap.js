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

    function filterNaN(inputVal) {
        var outputVal = isNaN(inputVal) ? 0 : inputVal;

        return outputVal;
    }

    function computeGrandTotal() {
        var currentTotalGross = parseFloat($('#current-total-gross').val());
        var currentTotalWithholding = parseFloat($('#current-total-withholdingtax').val());
        var currentTotalNet = parseFloat($('#current-total-netamount').val());
        var priorTotalGross = parseFloat($('#prior-total-gross').val());
        var priorTotalWithholding = parseFloat($('#prior-total-withholdingtax').val());
        var priorTotalNet = parseFloat($('#prior-total-netamount').val());

        var grandTotalGross = currentTotalGross + priorTotalGross;
        var grandTotalWithholding = currentTotalWithholding + priorTotalWithholding;
        var grandTotalNet = currentTotalNet + priorTotalNet;

        $('#total-gross-amount').val(parseFloat(grandTotalGross, 2));
        $('#total-withholding-tax').val(parseFloat(grandTotalWithholding, 2));
        $('#total-net-amount').val(parseFloat(grandTotalNet, 2));

        $('#total-amount').val(parseFloat(grandTotalNet, 2));
    }

    $.fn.computeGrossTotal = function(type) {
        var totalGrossAmount = 0;
        var totalNetAmount = 0;
        var classGross, classWithholding, classNet,
            idTotalGross, idtotalNet;

        if (type == 'current') {
            classGross = '.current-gross-amount';
            classWithholding = '.current-withold-tax';
            classNet = '.current-net-amount';
            idTotalGross = '#current-total-gross';
            idtotalNet = '#current-total-netamount';
        } else if (type == 'prior') {
            classGross = '.prior-gross-amount';
            classWithholding = '.prior-withold-tax';
            classNet = '.prior-net-amount';
            idTotalGross = '#prior-total-gross';
            idtotalNet = '#prior-total-netamount';
        }

        $(classGross).each(function() {
            var grossAmount = filterNaN(parseFloat($(this).val()));
            var withholdingTax = filterNaN(parseFloat($(this).parent().parent()
                                                             .next().find(classWithholding)
                                                             .val()));
            var netAmount = filterNaN((grossAmount - withholdingTax));

            $(this).parent().parent().next().next()
                   .find(classNet)
                   .val(parseFloat(netAmount, 2));

            totalGrossAmount += grossAmount;
            totalNetAmount += netAmount;
        });

        $(idTotalGross).val(parseFloat(totalGrossAmount, 2));
        $(idtotalNet).val(parseFloat(totalNetAmount, 2));

        computeGrandTotal();
    }

    $.fn.computeWithholdingTaxTotal = function(type) {
        var totalWithholdingTax = 0;
        var totalNetAmount = 0;
        var classGross, classWithholding, classNet,
            idTotalWitholding, idtotalNet;

        if (type == 'current') {
            classGross = '.current-gross-amount';
            classWithholding = '.current-withold-tax';
            classNet = '.current-net-amount';
            idTotalWitholding = '#current-total-withholdingtax';
            idtotalNet = '#current-total-netamount';
        } else if (type == 'prior') {
            classGross = '.prior-gross-amount';
            classWithholding = '.prior-withold-tax';
            classNet = '.prior-net-amount';
            idTotalWitholding = '#prior-total-withholdingtax';
            idtotalNet = '#prior-total-netamount';
        }

        $(classWithholding).each(function() {
            var withholdingTax = filterNaN(parseFloat($(this).val()));
            var grossAmount = filterNaN(parseFloat($(this).parent().parent()
                                                          .prev().find(classGross)
                                                          .val()));
            var netAmount = filterNaN(parseFloat((grossAmount - withholdingTax)));

            $(this).parent().parent().next()
                   .find(classNet)
                   .val(parseFloat(netAmount, 2));

            totalWithholdingTax += withholdingTax;
            totalNetAmount += netAmount;
        });

        $(idTotalWitholding).val(parseFloat(totalWithholdingTax, 2));
        $(idtotalNet).val(parseFloat(totalNetAmount, 2));

        computeGrandTotal();
    }

    $.fn.computeNetAmountTotal  = function(type) {
        var totalNetAmount = 0;
        var classGross, classWithholding, classNet,
            idTotalGross, idTotalWitholding, idtotalNet;

        if (type == 'current') {
            classGross = '.current-gross-amount';
            classWithholding = '.current-withold-tax';
            classNet = '.current-net-amount';
            idTotalGross = '#current-total-gross';
            idTotalWitholding = '#current-total-withholdingtax';
            idtotalNet = '#current-total-netamount';
        } else if (type == 'prior') {
            classGross = '.prior-gross-amount';
            classWithholding = '.prior-withold-tax';
            classNet = '.prior-net-amount';
            idTotalGross = '#prior-total-gross';
            idTotalWitholding = '#prior-total-withholdingtax';
            idtotalNet = '#prior-total-netamount';
        }

        $(classNet).each(function() {
            var netAmount = filterNaN(parseFloat($(this).val()));

            totalNetAmount += netAmount;
        });

        $(idtotalNet).val(parseFloat(totalNetAmount, 2));

        computeGrandTotal();
    }

    $.fn.addRow = function(rowClass, type) {
        var lastRow = $(rowClass).last();
        var lastRowID = (lastRow.length > 0) ? lastRow.attr('id') : type+'-row-0';
        var _lastRowID = lastRowID.split('-');
        var newID = parseInt(_lastRowID[2]) + 1;

        var creditorName = '<td><div class="md-form form-sm my-0">'+
                           '<input type="text" class="form-control required form-control-sm"'+
                           'placeholder=" Value..." name="'+_lastRowID[0]+'_creditor_name[]">'+
                           '</div></td>';
        var creditorAccntNo = '<td><div class="md-form form-sm my-0">'+
                              '<input type="text" class="form-control required form-control-sm"'+
                              'placeholder=" Value..." name="'+_lastRowID[0]+'_creditor_acc_no[]">'+
                              '</div></td>';
        var orsNo = '<td> <div class="md-form form-sm my-0">'+
                    '<input type="text" class="form-control required form-control-sm"'+
                    'placeholder=" Value..." name="'+_lastRowID[0]+'_ors_no[]">'+
                    '</div></td>';
        var allotClassUacs = '<td><div class="md-form form-sm my-0">'+
                             '<input type="text" class="form-control required form-control-sm"'+
                             'placeholder=" Value..." name="'+_lastRowID[0]+'_allot_class_uacs[]">'+
                             '</div></td>';
        var grossAmmount = '<td><div class="md-form form-sm my-0">'+
                           '<input type="number" class="form-control required form-control-sm '+
                           _lastRowID[0]+'-gross-amount'+'" '+
                           'placeholder=" Value..." name="'+_lastRowID[0]+'_gross_amount[]" '+
                           'onkeyup="$(this).computeGrossTotal('+"'"+_lastRowID[0]+"'"+')" '+
                           'onchange="$(this).computeGrossTotal('+"'"+_lastRowID[0]+"'"+')">'+
                           '</div></td>';
        var withholdingTax = '<td><div class="md-form form-sm my-0">'+
                             '<input type="number" class="form-control required form-control-sm '+
                             _lastRowID[0]+'-withold-tax'+'" '+
                             'placeholder=" Value..." name="'+_lastRowID[0]+'_withold_tax[]" '+
                             'onkeyup="$(this).computeWithholdingTaxTotal('+"'"+_lastRowID[0]+"'"+')" '+
                             'onchange="$(this).computeWithholdingTaxTotal('+"'"+_lastRowID[0]+"'"+')">'+
                             '</div></td>';
        var netAmount = '<td><div class="md-form form-sm my-0">'+
                        '<input type="number" class="form-control required form-control-sm '+
                        _lastRowID[0]+'-net-amount'+'" '+
                        'placeholder=" Value..." name="'+_lastRowID[0]+'_net_amount[]" '+
                        'onkeyup="$(this).computeNetAmountTotal('+"'"+_lastRowID[0]+"'"+')" '+
                        'onchange="$(this).computeNetAmountTotal('+"'"+_lastRowID[0]+"'"+')">'+
                        '</div></td>';
        var remarks = '<td><div class="md-form form-sm my-0">'+
                      '<input type="text" class="form-control form-control-sm"'+
                      'placeholder=" Value..." name="'+_lastRowID[0]+'_remarks[]" '+
                      '</div></td>';
        var deleteButton = '<td><a onclick="'+
                           "$(this).deleteRow('#"+_lastRowID[0]+'-row-'+newID+"');" +'"'+
                           'class="btn btn-outline-red px-1 py-0">'+
                           '<i class="fas fa-minus-circle"></i></a></td>';

        var rowOutput = '<tr id="'+_lastRowID[0]+'-row-'+newID+'" class="'+_lastRowID[0]+'-row">'+
                        creditorName + creditorAccntNo + orsNo + allotClassUacs +
                        grossAmmount + withholdingTax + netAmount + remarks +
                        deleteButton + '</tr>';

        $(rowOutput).insertAfter('#' + lastRowID);
    }

    $.fn.deleteRow = function(row) {
        if (confirm('Are you sure you want to delete this row?')) {
            var _row = row.split('-');
            var type =  _row[0].replace('#', '');
            var rowClass = '.' + type + '-' + _row[1];
            var rowCount = $(rowClass).length;

            if (type == 'prior'){
                $(row).fadeOut(300, function() {
                    var grossAmount = parseFloat($(this).find('.prior-gross-amount').val());
                    var withholding = parseFloat($(this).find('.prior-withold-tax').val());
                    var netAmmount = parseFloat($(this).find('.prior-net-amount').val());

                    var totalGross = parseFloat($('#prior-total-gross').val());
                    var totalWithholding = parseFloat($('#prior-total-withholdingtax').val());
                    var totalNet = parseFloat($('#prior-total-netamount').val());

                    totalGross = parseFloat(totalGross - grossAmount, 2);
                    totalWithholding = parseFloat(totalWithholding - withholding, 2);
                    totalNet = parseFloat(totalNet - netAmmount, 2);
                    $('#prior-total-gross').val(totalGross);
                    $('#prior-total-withholdingtax').val(totalWithholding);
                    $('#prior-total-netamount').val(totalNet);

                    computeGrandTotal();

                    $(this).remove();
                });
            } else {
                if (rowCount > 1) {
                    $(row).fadeOut(300, function() {
                        var grossAmount = parseFloat($(this).find('.current-gross-amount').val());
                        var withholding = parseFloat($(this).find('.current-withold-tax').val());
                        var netAmmount = parseFloat($(this).find('.current-net-amount').val());

                        var totalGross = parseFloat($('#current-total-gross').val());
                        var totalWithholding = parseFloat($('#current-total-withholdingtax').val());
                        var totalNet = parseFloat($('#current-total-netamount').val());

                        totalGross = parseFloat(totalGross - grossAmount, 2);
                        totalWithholding = parseFloat(totalWithholding - withholding, 2);
                        totalNet = parseFloat(totalNet - netAmmount, 2);

                        $('#current-total-gross').val(totalGross);
                        $('#current-total-withholdingtax').val(totalWithholding);
                        $('#current-total-netamount').val(totalNet);

                        computeGrandTotal();

                        $(this).remove();
                    });
                } else {
                    alert('Cannot delete all row.');
                }
            }
		}
    }

    $.fn.createUpdateDoc = function() {
		var withError = inputValidation(false);

		if (!withError) {
            $('#form-create').submit();
            $('#form-edit').submit();
		}
	}

	$.fn.showCreate = function() {
        var createURL = baseURL + '/payment/lddap/create';

        $('#mdb-preloader').css('background', '#000000ab').fadeIn(300);
        $('#modal-body-create').load(createURL, function() {
            $('#mdb-preloader').fadeOut(300);
            $('.mdb-select').materialSelect();
        });
		$("#central-create-modal").modal().on('shown.bs.modal', function() {

		}).on('hidden.bs.modal', function() {
		    $('#modal-body-create').html(modalLoadingContent);
		});
    }

    $.fn.showEdit = function(id) {
        var editURL = baseURL + '/payment/lddap/edit/' + id;

        $('#mdb-preloader').css('background', '#000000ab').fadeIn(300);
		$('#modal-body-edit').load(editURL, function() {
            $('#mdb-preloader').fadeOut(300);
			$('.mdb-select').materialSelect();
		});
		$("#central-edit-modal").modal().on('shown.bs.modal', function() {

		}).on('hidden.bs.modal', function() {
		    $('#modal-body-edit').html(modalLoadingContent);
		});
	}

	$.fn.delete = function(id) {
		if (confirm('Are you sure you want to delete this LDDAP?')) {
			$('#form-validation').attr('action', 'lddap/delete/' + id).submit();
		}
    }

    $.fn.forApproval = function(id) {
		if (confirm('Set to "For Approval" this document?')) {
			$('#form-validation').attr('action', 'lddap/for-approval/' + id).submit();
		}
	}

	$.fn.approve = function(id) {
		if (confirm('Set to "Approved" this LDDAP document?')) {
			$('#form-validation').attr('action', 'lddap/approve/' + id).submit();
		}
    }
});
