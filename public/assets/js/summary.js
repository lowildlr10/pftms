const CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

$(function() {
    const template = '<div class="tooltip md-tooltip">' +
                     '<div class="tooltip-arrow md-arrow"></div>' +
                     '<div class="tooltip-inner md-inner stylish-color"></div></div>';
    let lddapData = {};

    function initializeMaxValue() {
		$('.allotment-ps').each(function() {
			const maxLength = $(this).attr('max');

            $(this).unbind('keydown').unbind('keyup');
			$(this).keydown(function () {
			    // Save old value.
			    if (!$(this).val() || (parseFloat($(this).val()) <= maxLength &&
			    	parseFloat($(this).val()) >= 0)) {
			    	$(this).data("old", $(this).val());
			    }
			}).keyup(function () {
			    // Check correct, else revert back to old value.
			    if (!$(this).val() || (parseFloat($(this).val()) <= maxLength &&
			    	parseFloat($(this).val()) >= 0)) {
			    } else {
			      $(this).val($(this).data("old"));
			    }
			});
        });

        $('.allotment-mooe').each(function() {
			const maxLength = $(this).attr('max');

            $(this).unbind('keydown').unbind('keyup');
			$(this).keydown(function () {
			    // Save old value.
			    if (!$(this).val() || (parseFloat($(this).val()) <= maxLength &&
			    	parseFloat($(this).val()) >= 0)) {
			    	$(this).data("old", $(this).val());
			    }
			}).keyup(function () {
			    // Check correct, else revert back to old value.
			    if (!$(this).val() || (parseFloat($(this).val()) <= maxLength &&
			    	parseFloat($(this).val()) >= 0)) {
			    } else {
			      $(this).val($(this).data("old"));
			    }
			});
        });

        $('.allotment-co').each(function() {
			const maxLength = $(this).attr('max');

            $(this).unbind('keydown').unbind('keyup');
			$(this).keydown(function () {
			    // Save old value.
			    if (!$(this).val() || (parseFloat($(this).val()) <= maxLength &&
			    	parseFloat($(this).val()) >= 0)) {
			    	$(this).data("old", $(this).val());
			    }
			}).keyup(function () {
			    // Check correct, else revert back to old value.
			    if (!$(this).val() || (parseFloat($(this).val()) <= maxLength &&
			    	parseFloat($(this).val()) >= 0)) {
			    } else {
			      $(this).val($(this).data("old"));
			    }
			});
        });

        $('.allotment-fe').each(function() {
            const maxLength = $(this).attr('max');

            $(this).unbind('keydown').unbind('keyup');
			$(this).keydown(function () {
			    // Save old value.
			    if (!$(this).val() || (parseFloat($(this).val()) <= maxLength &&
			    	parseFloat($(this).val()) >= 0)) {
			    	$(this).data("old", $(this).val());
			    }
			}).keyup(function () {
			    // Check correct, else revert back to old value.
			    if (!$(this).val() || (parseFloat($(this).val()) <= maxLength &&
			    	parseFloat($(this).val()) >= 0)) {
			    } else {
			      $(this).val($(this).data("old"));
			    }
			});
		});
    }

    function initializeSelect2() {
        $('.lddap-tokenizer').select2({
            tokenSeparators: [','],
            placeholder: "LDDAP No...",
            width: '100%',
            maximumSelectionSize: 4,
            allowClear: true,
            ajax: {
                url: `${baseURL}/payment/summary/get-lddap`,
                type: "post",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        _token: CSRF_TOKEN,
                        search: params.term
                    };
                },
                processResults: function(data) {
                    return {
                        results: $.map(data, function(item) {
                            let jsonData = {};
                            jsonData['lddap_ada_no'] = item.lddap_ada_no;
                            jsonData['date_lddap'] = item.date_lddap;
                            jsonData['total_amount'] = item.total_amount;
                            lddapData[item.id] = jsonData;

                            return {
                                text: `${item.lddap_ada_no}`,
                                id: item.id
                            }
                        }),
                        pagination: {
                            more: true
                        }
                    };
                },
                cache: true
            },
            //theme: "material"
        }).on('select2:select', function(e) {
            const lddapID = e.params.data.id,
                  lddapDate = lddapData[lddapID].date_lddap,
                  totalAmount = lddapData[lddapID].total_amount;

            $(this).closest('tr').children().each(function(i, elem) {
                $(elem).find('.date-issue').val(lddapDate);
                $(elem).find('.total').val(totalAmount);
                $(elem).find('.allotment-ps').attr('max', totalAmount);
                $(elem).find('.allotment-mooe').attr('max', totalAmount);
                $(elem).find('.allotment-co').attr('max', totalAmount);
                $(elem).find('.allotment-fe').attr('max', totalAmount);
            });

            computeGrandTotal();
            initializeMaxValue();
        });

        $('#total-amount').keyup(function() {
            $('#total-amount-words').val(toWordsconvert($(this).val()));
            $('#total-amount-words').siblings('label').addClass('active');
        });
    }

    $.fn.addRow = function(rowClass) {
        const type = 'item';
        let lastRow = $(rowClass).last();
        let lastRowID = (lastRow.length > 0) ? lastRow.attr('id') : 'item-row-0';
        let _lastRowID = lastRowID.split('-');
        let newID = parseInt(_lastRowID[2]) + 1;

        let lddapNo = `<td><div class="md-form form-sm my-0">
                       <select class="mdb-select required lddap-tokenizer"
                       name="lddap_id[]"></select>
                       </div></td>`;
        let dateIssue = `<td><div class="md-form form-sm my-0">
                         <input type="date" placeholder=" Value..." name="date_issue[]"
                         class="form-control required form-control-sm date-issue"></div></td>`;
        let total = `<td><div class="md-form form-sm my-0">
                    <input type="number" placeholder=" Value..." name="total[]"
                    class="form-control required form-control-sm total"
                    id="total-${newID-1}" min="0"
                    onkeyup="$(this).computeAll()"
                    onchange="$(this).computeAll()">
                    </div></td>`;
        let ps = `<td><div class="md-form form-sm my-0">
                <input type="number" placeholder=" Value..." name="allotment_ps[]"
                class="form-control required form-control-sm allotment-ps"
                id="allotment-ps-${newID-1}" min="0"
                onkeyup="$(this).computeAll()"
                onchange="$(this).computeAll()">
                </div></td>`;
        let mooe = `<td><div class="md-form form-sm my-0">
                <input type="number" placeholder=" Value..." name="allotment_mooe[]"
                class="form-control required form-control-sm allotment-mooe"
                id="allotment-mooe-${newID-1}" min="0"
                onkeyup="$(this).computeAll()"
                onchange="$(this).computeAll()">
                </div></td>`;
        let co = `<td><div class="md-form form-sm my-0">
                <input type="number" placeholder=" Value..." name="allotment_co[]"
                class="form-control required form-control-sm allotment-co"
                id="allotment-co-${newID-1}" min="0"
                onkeyup="$(this).computeAll()"
                onchange="$(this).computeAll()">
                </div></td>`;
        let fe = `<td><div class="md-form form-sm my-0">
                <input type="number" placeholder=" Value..." name="allotment_fe[]"
                class="form-control required form-control-sm allotment-fe"
                id="allotment-fe-${newID-1}" min="0"
                onkeyup="$(this).computeAll()"
                onchange="$(this).computeAll()">
                </div></td>`;
        let remarks = `<td><div class="md-form form-sm my-0">
                       <textarea name="allotment_remarks[]" placeholder=" Value..."
                       class="md-textarea form-control-sm w-100 py-1"></textarea>
                       </div></td>`;
        let deleteButton = '<td><a onclick="'+
                           "$(this).deleteRow('#"+'item-row-'+newID+"');" +'"'+
                           'class="btn btn-outline-red px-1 py-0">'+
                           '<i class="fas fa-minus-circle"></i></a></td>';

        let rowOutput = '<tr id="'+_lastRowID[0]+'-row-'+newID+'" class="'+_lastRowID[0]+'-row">'+
                        lddapNo + dateIssue + total + ps + mooe + co + fe + remarks +
                        deleteButton + '</tr>';

        $(rowOutput).insertAfter('#' + lastRowID);
        $('#lddap-no-pcs').text($('.item-row').length);
        initializeSelect2();
    }

    $.fn.deleteRow = function(row) {
        if (confirm('Are you sure you want to delete this row?')) {
            let _row = row.split('-');
            let type =  _row[0].replace('#', '');
            let rowClass = '.item-' + _row[1];
            let rowCount = $(rowClass).length;

            if (rowCount > 1) {
                $(row).fadeOut(300, function() {
                    $(this).remove();
                    $('#lddap-no-pcs').text($('.item-row').length);
                    $(this).computeAll();
                });
            } else {
                alert('Cannot delete all row.');
            }
		}
    }

    function filterNaN(inputVal) {
        let outputVal = isNaN(inputVal) ? 0 : inputVal;

        return outputVal;
    }

    function computeGrandTotal() {
        let grandTotal = 0;

        $('.total').each(function() {
            let total = filterNaN(parseFloat($(this).val()));

            grandTotal += total;
        });

        $('#total').val(parseFloat(grandTotal).toFixed(2));
        $('#total-amount').next('label').addClass('active');
        $('#total-amount').val(parseFloat(grandTotal).toFixed(2));
        $('#total-amount-words').val(toWordsconvert(parseFloat(grandTotal).toFixed(2)));
        $('#total-amount-words').siblings('label').addClass('active');
    }

    function computeAllotment() {
        const allotmentElems = [
            '.allotment-ps',
            '.allotment-mooe',
            '.allotment-co',
            '.allotment-fe'
        ], allotmentTotalElems = [
            '#total-ps',
            '#total-mooe',
            '#total-co',
            '#total-fe'
        ];

        $.each(allotmentElems, function(ctr, elem) {
            let allotmentTotal = 0;

            $(elem).each(function() {
                let value = filterNaN(parseFloat($(this).val()));

                allotmentTotal += value;
            });

            $(allotmentTotalElems[ctr]).val(parseFloat(allotmentTotal).toFixed(2));
        });
    }

    $.fn.computeAll = function() {
        computeGrandTotal();
        computeAllotment();
    }

    $.fn.showCreate = function(url) {
        $('#mdb-preloader').css('background', '#000000ab').fadeIn(300);
        $('#modal-body-create').load(url, function() {
            $('#mdb-preloader').fadeOut(300);
            $('.crud-select').materialSelect();
            $(this).slideToggle(500);
            initializeSelect2();
        });
        $("#modal-lg-create").modal({keyboard: false, backdrop: 'static'})
						     .on('shown.bs.modal', function() {
            $('#create-title').html('Create Summary of LDDAP');
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
            initializeSelect2();
        });
        $("#modal-lg-edit").modal({keyboard: false, backdrop: 'static'})
						   .on('shown.bs.modal', function() {
            $('#edit-title').html('Update Summary of LDDAP');
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
            $('#delete-title').html('Delete Summary of LDDAP');
            $('#form-delete').attr('action', url);
		}).on('hidden.bs.modal', function() {
             $('#modal-delete-body').html('');
             $('#form-delete').attr('action', '#');
		});
    }

    $.fn.delete = function() {
        $('#form-delete').submit();
    }

    $.fn.showApproval = function(url, name) {
        $('#modal-body-approval').html(`Are you sure you want to set this
                                       document to 'For Approval'?`);
        $("#modal-approval").modal({keyboard: false, backdrop: 'static'})
						  .on('shown.bs.modal', function() {
            $('#approval-title').html('Approval Summary of LDDAP');
            $('#form-approval').attr('action', url);
		}).on('hidden.bs.modal', function() {
             $('#modal-approval-body').html('');
             $('#form-approval').attr('action', '#');
		});
    }

    $.fn.approval = function() {
        $('#form-approval').submit();
    }

    $.fn.showApprove = function(url, name) {
        $('#modal-body-approve').html(`Are you sure you want to set this
                                       document to 'Approved'?`);
        $("#modal-approve").modal({keyboard: false, backdrop: 'static'})
						  .on('shown.bs.modal', function() {
            $('#approve-title').html('Approve Summary of LDDAP');
            $('#form-approve').attr('action', url);
		}).on('hidden.bs.modal', function() {
             $('#modal-approve-body').html('');
             $('#form-approve').attr('action', '#');
		});
    }

    $.fn.showSubmissionBank = function(url, name) {
        $('#modal-body-submission-bank').html(`Are you sure you want to set this
                                       document to 'For Submission to Bank'?`);
        $("#modal-submission-bank").modal({keyboard: false, backdrop: 'static'})
						  .on('shown.bs.modal', function() {
            $('#submission-bank-title').html('Submission to Bank');
            $('#form-submission-bank').attr('action', url);
		}).on('hidden.bs.modal', function() {
             $('#modal-submission-bank-body').html('');
             $('#form-submission-bank').attr('action', '#');
		});
    }

    $.fn.submissionBank = function() {
        $('#form-submission-bank').submit();
    }

    $.fn.approve = function() {
        $('#form-approve').submit();
    }

    $('.material-tooltip-main').tooltip({
        template: template
    });
});
