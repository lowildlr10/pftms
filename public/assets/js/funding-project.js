const CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

$(function() {
    const template = '<div class="tooltip md-tooltip">' +
                     '<div class="tooltip-arrow md-arrow"></div>' +
                     '<div class="tooltip-inner md-inner stylish-color"></div></div>';

    function initializeSelect2() {
        $(".allot-class-tokenizer").select2({
            tokenSeparators: [','],
            placeholder: "Value...",
            width: '100%',
            maximumSelectionSize: 4,
            allowClear: true,
            ajax: {
                url: `${baseURL}/payment/lddap/get-ors-burs`,
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
                            return {
                                text: item.serial_no,
                                id: item.id
                            }
                        }),
                        pagination: {
                            more: true
                        }
                    };
                },
                cache: true
            }
            //theme: "material"
        });

        $(".mooe-tokenizer").select2({
            tokenSeparators: [','],
            placeholder: "Value...",
            width: '100%',
            maximumSelectionSize: 4,
            allowClear: true,
            ajax: {
                url: `${baseURL}/payment/lddap/get-ors-burs`,
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
                            return {
                                text: item.serial_no,
                                id: item.id
                            }
                        }),
                        pagination: {
                            more: true
                        }
                    };
                },
                cache: true
            }
            //theme: "material"
        });
    }

    $.fn.addRow = function(rowClass, type) {
        let lastRow = $(rowClass).last();
        let lastRowID = (lastRow.length > 0) ? lastRow.attr('id') : type+'-row-0';
        let _lastRowID = lastRowID.split('-');
        let newID = parseInt(_lastRowID[2]) + 1;

        let allotmentName = ``,
            allotmentType = ``,
            allotmentClassification = ``,
            allotmentBudget = ``;



        $(rowOutput).insertAfter('#' + lastRowID);

        /*
        let creditorName = `<td><div class="md-form form-sm my-0">
                            <textarea name="${_lastRowID[0]}_creditor_name[]" placeholder=" Value..."
                            class="md-textarea required form-control-sm w-100 py-1"></textarea>
                            </div></td>`;
        let creditorAccntNo = `<td><div class="md-form form-sm my-0">
                               <textarea name="${_lastRowID[0]}_creditor_acc_no[]" placeholder=" Value..."
                               class="md-textarea required form-control-sm w-100 py-1"></textarea>
                               </div></td>`;
        let orsNo = `<td><div class="md-form my-0">
                    <select class="mdb-select required ors-tokenizer" multiple="multiple"
                    name="${_lastRowID[0]}_ors_no[${newID-1}][]"></select></div></td>`;
        let allotClassUacs = `<td><div class="md-form form-sm my-0">
                              <textarea name="${_lastRowID[0]}_allot_class_uacs[]" placeholder=" Value..."
                              class="md-textarea required form-control-sm w-100 py-1"></textarea>
                              </div></td>`;
        let grossAmmount = '<td><div class="md-form form-sm my-0">'+
                           '<input type="number" class="form-control required form-control-sm '+
                           _lastRowID[0]+'-gross-amount'+'" '+
                           'placeholder=" Value..." name="'+_lastRowID[0]+'_gross_amount[]" '+
                           `id="${_lastRowID[0]}-gross-amount-${newID-1}" `+
                           'onkeyup="$(this).computeGrossTotal('+"'"+_lastRowID[0]+"'"+')" '+
                           'onchange="$(this).computeGrossTotal('+"'"+_lastRowID[0]+"'"+')" '+
                           'onclick="$(this).showCalc('+`'#${_lastRowID[0]}-gross-amount-${newID-1}', '${_lastRowID[0]}'`+')">'+
                           '</div></td>';
        let withholdingTax = '<td><div class="md-form form-sm my-0">'+
                             '<input type="number" class="form-control required form-control-sm '+
                             _lastRowID[0]+'-withold-tax'+'" '+
                             'placeholder=" Value..." name="'+_lastRowID[0]+'_withold_tax[]" '+
                             'onkeyup="$(this).computeWithholdingTaxTotal('+"'"+_lastRowID[0]+"'"+')" '+
                             'onchange="$(this).computeWithholdingTaxTotal('+"'"+_lastRowID[0]+"'"+')">'+
                             '</div></td>';
        let netAmount = '<td><div class="md-form form-sm my-0">'+
                        '<input type="number" class="form-control required form-control-sm '+
                        _lastRowID[0]+'-net-amount'+'" '+
                        'placeholder=" Value..." name="'+_lastRowID[0]+'_net_amount[]" '+
                        'onkeyup="$(this).computeNetAmountTotal('+"'"+_lastRowID[0]+"'"+')" '+
                        'onchange="$(this).computeNetAmountTotal('+"'"+_lastRowID[0]+"'"+')">'+
                        '</div></td>';
        let remarks = `<td><div class="md-form form-sm my-0">
                       <textarea name="${_lastRowID[0]}_remarks[]" placeholder=" Value..."
                       class="md-textarea form-control-sm w-100 py-1"></textarea>
                       </div></td>`;
        let deleteButton = '<td><a onclick="'+
                           "$(this).deleteRow('#"+_lastRowID[0]+'-row-'+newID+"');" +'"'+
                           'class="btn btn-outline-red px-1 py-0">'+
                           '<i class="fas fa-minus-circle"></i></a></td>';

        let rowOutput = '<tr id="'+_lastRowID[0]+'-row-'+newID+'" class="'+_lastRowID[0]+'-row">'+
                        creditorName + creditorAccntNo + orsNo + allotClassUacs +
                        grossAmmount + withholdingTax + netAmount + remarks +
                        deleteButton + '</tr>';

        $(rowOutput).insertAfter('#' + lastRowID);
        initializeSelect2();*/
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
            $('#create-title').html('Create Project Line-Item Budget');
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
            $('#edit-title').html('Update Source of Funds / Project');
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
                                     `Source of Funds / Project?`);
        $("#modal-delete").modal({keyboard: false, backdrop: 'static'})
						  .on('shown.bs.modal', function() {
            $('#delete-title').html('Delete Source of Funds / Project');
            $('#form-delete').attr('action', url);
		}).on('hidden.bs.modal', function() {
             $('#modal-delete-body').html('');
             $('#form-delete').attr('action', '#');
		});
    }

    $.fn.delete = function() {
        $('#form-delete').submit();
    }
});
