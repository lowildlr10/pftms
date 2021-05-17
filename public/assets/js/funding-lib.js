const CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

$(function() {
    const template = '<div class="tooltip md-tooltip">' +
                     '<div class="tooltip-arrow md-arrow"></div>' +
                     '<div class="tooltip-inner md-inner stylish-color"></div></div>';
    let allotClassData = {};

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

    function initializeSortable() {
        $('.sortable').sortable({
            items: '> tr:not(.exclude-sortable)'
        });
        $('.sortable').disableSelection();
    }

    $.fn.totalBudgetIsValid = () => {
        let totalBudget = filterNaN($('#approved-budget').val()),
            totalAllotted = 0;

        $('.allotted-budget').each(function() {
            totalAllotted += filterNaN(parseFloat($(this).val()));
        });

        totalBudget -= totalAllotted;

        $('#remaining-budget').val(filterNaN(totalBudget).toFixed(2));

        if (totalBudget < 0) {
            $('#remaining-budget').addClass('input-error-highlighter')
                                  .tooltip('show');

            return false;
        }

        $('#remaining-budget').removeClass('input-error-highlighter')
                              .tooltip('hide');
        return true;
    }

    $.fn.addRow = function(rowClass, type) {
        let lastRow = $(rowClass).last();
        let lastRowID = (lastRow.length > 0) ? lastRow.attr('id') : type+'-row-0';
        let _lastRowID = lastRowID.split('-');
        let newID = parseInt(_lastRowID[2]) + 1;
        let rowOutput = "";

        if (type == 'item') {
            let allotmentName = `
                <td>
                    <div class="md-form form-sm my-0">
                        <input name="row_type[${newID}]" type="hidden" value="${type}">
                        <input type="text" placeholder=" Value..." name="allotment_name[${newID}]"
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
                        <input type="number" placeholder=" Value..." name="allotted_budget[${newID}]"
                            class="form-control required form-control-sm allotted-budget py-1"
                            id="allotted-budget-${newID}" min="0"
                            onkeyup="$(this).totalBudgetIsValid();"
                            onchange="$(this).totalBudgetIsValid();">
                    </div>
                </td>`,
                deleteButton = `
                <td class="align-middle">
                    <a onclick="$(this).deleteRow('#item-row-${newID}');"
                    class="btn btn-outline-red px-1 py-0">
                        <i class="fas fa-minus-circle"></i>
                    </a>
                </td>`,
                sortableButton = `
                <td class="align-middle">
                    <a href="#" class="grey-text">
                        <i class="fas fa-ellipsis-v"></i>
                    </a>
                </td>`;

            rowOutput = '<tr id="item-row-'+newID+'" class="item-row">'+
                        allotmentName + allotmentClassification + allotmentBudget +
                        deleteButton + sortableButton + '</tr>';

            $(rowOutput).insertAfter('#' + lastRowID);
            initializeSelect2();
        } else if (type == 'header') {
            let allotmentHeaderName = `
                <td>
                    <div class="md-form form-sm my-0">
                        <input name="row_type[${newID}]" type="hidden" value="${type}">
                        <input type="hidden"name="allot_class[${newID}]">
                        <input type="hidden"name="allotted_budget[${newID}]">
                        <input type="text" placeholder="Header Value..." name="allotment_name[${newID}]"
                            class="form-control required form-control-sm allotment-name py-1"
                            id="allotment-name-${newID}">
                    </div>
                </td>`,
                deleteButton = `
                <td class="align-middle">
                    <a onclick="$(this).deleteRow('#header-row-${newID}');"
                    class="btn btn-outline-red px-1 py-0">
                        <i class="fas fa-minus-circle"></i>
                    </a>
                </td>`,
                sortableButton = `
                <td class="align-middle">
                    <a href="#" class="grey-text">
                        <i class="fas fa-ellipsis-v"></i>
                    </a>
                </td>`;
            rowOutput = '<tr id="header-row-'+newID+'" class="item-row">'+ allotmentHeaderName +
                        '<td colspan="2"></td>' + deleteButton + sortableButton + '</tr>';

            $(rowOutput).insertAfter('#' + lastRowID);
        } else if (type == 'header-break') {
             let allotmentHeaderName = `
                <td colspan="3">
                    <hr>
                    <div class="md-form form-sm my-0">
                        <input name="row_type[${newID}]" type="hidden" value="${type}">
                        <input type="hidden"name="allot_class[${newID}]">
                        <input type="hidden"name="allotted_budget[${newID}]">
                        <input type="hidden" name="allotment_name[${newID}]" id="allotment-name-${newID}">
                    </div>
                </td>`,
                deleteButton = `
                <td class="align-middle">
                    <a onclick="$(this).deleteRow('#headerbreak-row-${newID}');"
                    class="btn btn-outline-red px-1 py-0">
                        <i class="fas fa-minus-circle"></i>
                    </a>
                </td>`,
                sortableButton = `
                <td class="align-middle">
                    <a href="#" class="grey-text">
                        <i class="fas fa-ellipsis-v"></i>
                    </a>
                </td>`;
            rowOutput = '<tr id="header-row-'+newID+'" class="item-row">'+ allotmentHeaderName +
                        deleteButton + sortableButton + '</tr>';

            $(rowOutput).insertAfter('#' + lastRowID);
        }
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

    $.fn.showPrintList = function(url) {
        $('#mdb-preloader').css('background', '#000000ab').fadeIn(300);
        $('#modal-body-show').load(url, function() {
            $('#mdb-preloader').fadeOut(300);
            $(this).slideToggle(500);
        });
        $("#modal-show").modal({keyboard: false, backdrop: 'static'})
						     .on('shown.bs.modal', function() {
            $('#show-title').html('Print Line-Item Budget');
		}).on('hidden.bs.modal', function() {
		    $('#modal-body-show').html('').css('display', 'none');
		});
    }

    $.fn.showCreate = function(url) {
        $('#mdb-preloader').css('background', '#000000ab').fadeIn(300);
        $('#modal-body-create').load(url, function() {
            $('#mdb-preloader').fadeOut(300);
            $('.crud-select').materialSelect();
            $('#project').on("change", function() {
                const projID = $(this).val();

                $.each(projects, function(projCtr, project) {
                    if (project.id == projID) {
                        $('#approved-budget').val(project.project_cost)
                                             .siblings()
                                             .addClass('active');
                    }
                });
            });
            $(this).slideToggle(500);
            initializeSelect2();
            initializeSortable();
        });
        $("#modal-lg-create").modal({keyboard: false, backdrop: 'static'})
						     .on('shown.bs.modal', function() {
            $('#create-title').html('Create Project Line-Item Budget');
		}).on('hidden.bs.modal', function() {
		    $('#modal-body-create').html('').css('display', 'none');
		});
    }

    $.fn.showCreateRealignment = function(url) {
        $('#mdb-preloader').css('background', '#000000ab').fadeIn(300);
        $('#modal-body-create').load(url, function() {
            $('#mdb-preloader').fadeOut(300);
            $('.crud-select').materialSelect();
            $(this).slideToggle(500);
            initializeSelect2();
            initializeSortable();
        });
        $("#modal-lg-create").modal({keyboard: false, backdrop: 'static'})
						     .on('shown.bs.modal', function() {
            $('#create-title').html('Create Realignment for Line-Item Budget');
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
            initializeSelect2();
            initializeSortable();
        });
        $("#modal-lg-edit").modal({keyboard: false, backdrop: 'static'})
						   .on('shown.bs.modal', function() {
            $('#edit-title').html('Update Project Line-Item Budget');
		}).on('hidden.bs.modal', function() {
            $('#modal-body-edit').html('').css('display', 'none');
		});
    }

    $.fn.showEditRealignment = function(url) {
        $('#mdb-preloader').css('background', '#000000ab').fadeIn(300);
        $('#modal-body-edit').load(url, function() {
            $('#mdb-preloader').fadeOut(300);
            $('.crud-select').materialSelect();
            $(this).slideToggle(500);
            initializeSelect2();
            initializeSortable();
        });
        $("#modal-lg-edit").modal({keyboard: false, backdrop: 'static'})
						   .on('shown.bs.modal', function() {
            $('#edit-title').html('Update Realignment for Line-Item Budget');
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

    $.fn.showApprove = function(url, isRealignment) {
        let msg = `Are you sure you want to set this
                   document to 'Approved'?`,
            title = 'Approve Line-Item Budgets';

        if (isRealignment) {
            msg = `Are you sure you want to set this
                   realignment to 'Approved'?`;
            title = 'Approve Realignment';
        }

        $('#modal-body-approve').html(msg);
        $("#modal-approve").modal({keyboard: false, backdrop: 'static'})
						  .on('shown.bs.modal', function() {
            $('#approve-title').html(title);
            $('#form-approve').attr('action', url);
		}).on('hidden.bs.modal', function() {
             $('#modal-approve-body').html('');
             $('#form-approve').attr('action', '#');
		});
    }

    $.fn.approve = function() {
        $('#form-approve').submit();
    }

    $.fn.showDisapprove = function(url, isRealignment) {
        let msg = `Are you sure you want to set this
                   document to 'Disapproved'?`,
            title = 'Disapprove Line-Item Budgets';

        if (isRealignment) {
            msg = `Are you sure you want to set this
                   realignment to 'Disapproved'?`;
            title = 'Disapprove Realignment';
        }

        $('#modal-body-disapprove').html(msg);
        $("#modal-disapprove").modal({keyboard: false, backdrop: 'static'})
						  .on('shown.bs.modal', function() {
            $('#disapprove-title').html(title);
            $('#form-disapprove').attr('action', url);
		}).on('hidden.bs.modal', function() {
             $('#modal-disapprove-body').html('');
             $('#form-disapprove').attr('action', '#');
		});
    }

    $.fn.disapprove = function() {
        $('#form-disapprove').submit();
    }

    $('.material-tooltip-main').tooltip({
        template: template
    });
});
