const CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

$(function() {
    const template = '<div class="tooltip md-tooltip">' +
                     '<div class="tooltip-arrow md-arrow"></div>' +
                     '<div class="tooltip-inner md-inner stylish-color"></div></div>';
    let allotClassData = {},
        accountTitleData = {};

    function filterNaN(inputVal) {
        let outputVal = isNaN(inputVal) ? 0 : inputVal;

        return outputVal;
    }

    function initializeSelect2() {
        $('.allot-class-tokenizer').select2({
            tokenSeparators: [','],
            placeholder: "Value...",
            width: '100%',
            maximumSelectionSize: 4,
            allowClear: true,
            ajax: {
                url: `${baseURL}/fund-utilization/project-lib/get-allot-class`,
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
                            jsonData['class_name'] = item.class_name;
                            allotClassData[item.id] = jsonData;

                            return {
                                text: `${item.class_name}`,
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
        });
    }

    $.fn.totalBudgetIsValid = () => {
        let totalBudget = $('#approved-budget').val(),
            totalAllotted = 0;

        $('.allotted-budget').each(function() {
            totalAllotted += parseFloat($(this).val())
        });

        totalBudget -= totalAllotted;

        $('#remaining-budget').val(filterNaN(totalBudget).toFixed(2));

        if (totalBudget < 0) {
            $('#remaining-budget').addClass('input-error-highlighter')
                                  .tooltip('enable')
                                  .tooltip('toggle');

            return false;
        }

        $('#remaining-budget').removeClass('input-error-highlighter')
                              .tooltip('dispose');
        return true;
    }

    $.fn.addRow = function(rowClass, type) {
        let lastRow = $(rowClass).last();
        let lastRowID = (lastRow.length > 0) ? lastRow.attr('id') : type+'-row-0';
        let _lastRowID = lastRowID.split('-');
        let newID = parseInt(_lastRowID[2]) + 1;

        let allotmentName = `
            <td>
                <div class="md-form form-sm my-0">
                    <input type="text" placeholder=" Value..." name="allotment_name[]"
                        class="form-control required form-control-sm allotment-name py-1"
                        id="allotment-name-${newID}">
                </div>
            </td>`,
            allotmentClassification = `
            <td>
                <div class="md-form my-0">
                    <select class="mdb-select required allot-class-tokenizer"
                            name="allot_class[${newID}]"></select>
                </div>
            </td>`,
            allotmentBudget = `
            <td>
                <div class="md-form form-sm my-0">
                    <input type="number" placeholder=" Value..." name="allotted_budget[]"
                        class="form-control required form-control-sm allotted-budget py-1"
                        id="allotted-budget-${newID}" min="0"
                        onkeyup="$(this).totalBudgetIsValid();"
                        onchange="$(this).totalBudgetIsValid();">
                </div>
            </td>`,
            deleteButton = `
            <td>
                <a onclick="$(this).deleteRow('#item-row-${newID}');"
                   class="btn btn-outline-red px-1 py-0">
                    <i class="fas fa-minus-circle"></i>
                </a>
            </td>`;

        let rowOutput = '<tr id="item-row-'+newID+'" class="item-row">'+
            allotmentName + allotmentClassification + allotmentBudget +
            deleteButton + '</tr>';

        $(rowOutput).insertAfter('#' + lastRowID);
        initializeSelect2();
    }

    $.fn.deleteRow = function(row) {
        if (confirm('Are you sure you want to delete this row?')) {
            let _row = row.split('-');
            let rowClass = '.item-' + _row[1];
            let rowCount = $(rowClass).length;

            if (rowCount > 1) {
                $(row).fadeOut(300, function() {
                    $(this).remove();
                    $(this).totalBudgetIsValid();
                });
            } else {
                alert('Cannot delete all row.');
            }
		}
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

		if ($(this).totalBudgetIsValid() && !withError) {
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

    $('.material-tooltip-main').tooltip({
        template: template
    });
});
