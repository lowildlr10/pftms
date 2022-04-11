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
        let dropdownParent = '#modal-lg-create';

        if ($('#modal-lg-create').hasClass('show')) {
            dropdownParent = '#modal-lg-create';
        } else {
            dropdownParent = '#modal-lg-edit';
        }

        $('.allot-class-tokenizer').select2({
            tokenSeparators: [','],
            placeholder: "Value...",
            width: '100%',
            maximumSelectionSize: 4,
            allowClear: true,
            dropdownParent: $(dropdownParent),
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
        $('.uacs-class-tokenizer').select2({
            tokenSeparators: [','],
            placeholder: "Value...",
            width: '100%',
            maximumSelectionSize: 4,
            allowClear: true,
            dropdownParent: $(dropdownParent),
            ajax: {
                url: `${baseURL}/fund-utilization/project-lib/get-uacs-object`,
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
                            jsonData['uacs_code'] = item.uacs_code;
                            jsonData['name'] = item.name;
                            allotClassData[item.id] = jsonData;

                            return {
                                text: `${item.uacs_code} : ${item.name}`,
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

    function initializeProjectInput() {
        $('#project').on("change", function() {
            const projID = $(this).val();

            $.each(projects, function(projCtr, project) {
                if (project.id == projID) {
                    const coimplementorsElem = project.coimplementors.length > 0 ?
                                            project.coimplementors.map((coimplementor, index) => {
                                                return `<th id="coimplementor-${index}" class="align-middle coimplementor" width="250px">
                                                    <b id="coimplementor-name-${index}">
                                                    <span class="red-text">* </span>${coimplementor.coimplementor_name}
                                                    </b>
                                                    <input id="coimplementor-id-${index}" type="hidden" value="${coimplementor.id}">
                                                </th>`
                                            }) : [];
                    $('.coimplementor').remove();
                    $('#implementor').after(coimplementorsElem);
                    $('.item-row').remove();
                    $('#item-row-container').fadeIn(300).first().prepend(`<tr id="item-row-0" class="item-row"></tr>`);
                    $('#approved-budget').val(project.project_cost)
                                         .siblings()
                                         .addClass('active');
                    $('#remaining-budget').val(project.project_cost);
                    $('#implementor-name').html(`<span class="red-text">* </span>${project.implementor_name}`);
                    return false;
                }
            });
        });
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

    $.fn.addRow = function(rowClass, type, isRealignment = false) {
        const lastRow = $(rowClass).last();
        const countCoimplementors = $('.coimplementor').length;
        let headerCoimps = [];
        let lastRowID = (lastRow.length > 0) ? lastRow.attr('id') : type+'-row-0';
        let rowOutput = "";
        let newID = 1;

        if (countCoimplementors > 0) {
            $('.coimplementor').each(function(index, elem) {
                const coimpID = $(`#coimplementor-id-${index}`).val();
                headerCoimps.push({
                    'id': coimpID
                });
            });
        }

        $(rowClass).each(function() {
            const elemExplodedID = $(this).attr('id').split('-');
            const elemIndexID = elemExplodedID[2];

            if (parseInt(elemIndexID) >= newID) {
                newID = parseInt(elemIndexID) + 1;
            }
        });

        if (type == 'item') {
            let allotmentName = `
                <td>
                    <div class="md-form form-sm my-0">
                        <input name="row_type[${newID}]" type="hidden" value="${type}">
                        <input type="hidden" name="allotment_id[${newID}]">
                        <input type="hidden" name="allotment_realign_id[${newID}]">
                        <input type="text" placeholder=" Value..." name="allotment_name[${newID}]"
                            class="form-control required form-control-sm allotment-name py-1"
                            id="allotment-name-${newID}">
                    </div>
                </td>`,
                uacsCode = `
                <td>
                    <div class="md-form my-0">
                        <select class="mdb-select required uacs-class-tokenizer"
                                name="uacs_code[${newID}]"></select>
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
                coimplementorBudget = headerCoimps.length > 0 ? headerCoimps.map((headerCoimp, index) => {
                    return `<td>
                        <div class="md-form form-sm my-0">
                            <input type="hidden" name="coimplementor_id[${newID}][${index}]" value="${headerCoimp.id}">
                            <input type="number" placeholder=" Value..." name="coimplementor_budget[${newID}][${index}]"
                                class="form-control required form-control-sm coimplementor-budget allotted-budget py-1"
                                id="coimplementor-budget-${newID}-${index}" min="0"
                                onkeyup="$(this).totalBudgetIsValid();"
                                onchange="$(this).totalBudgetIsValid();">
                        </div>
                    </td>`
                }) : '';
                justification = !isRealignment ? '' : `
                <td>
                    <div class="md-form form-sm my-0">
                        <textarea id="justification" class="md-textarea form-control"
                                  name="justification[${newID}]" rows="2"
                                  placeholder="Justification"></textarea>
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
                <td class="align-middle" style="width: 1px;">
                    <a href="#" class="grey-text">
                        <i class="fas fa-ellipsis-v"></i>
                    </a>
                </td>`;

            rowOutput = '<tr id="item-row-'+newID+'" class="item-row">'+
                        allotmentName + uacsCode + allotmentClassification +
                        allotmentBudget + coimplementorBudget + justification +
                        deleteButton + sortableButton + '</tr>';

            $(rowOutput).insertAfter('#' + lastRowID);
            initializeSelect2();
        } else if (type == 'header') {
            let allotmentHeaderName = `
                <td>
                    <div class="md-form form-sm my-0">
                        <input name="row_type[${newID}]" type="hidden" value="${type}">
                        <input type="hidden" name="allotment_id[${newID}]">
                        <input type="hidden" name="allotment_realign_id[${newID}]">
                        <input type="hidden"name="allot_class[${newID}]">
                        <input type="hidden"name="allotted_budget[${newID}]">
                        <input type="text" placeholder="Header Value..." name="allotment_name[${newID}]"
                            class="form-control required form-control-sm allotment-name py-1 font-weight-bold"
                            id="allotment-name-${newID}">` +
                        (headerCoimps.length > 0 ? headerCoimps.map((headerCoimp, index) => {
                            return `<input type="hidden" name="coimplementor_id[${newID}][${index}]">
                            <input type="hidden" name="coimplementor_budget[${newID}][${index}]">`
                        }) : '') +
                    `</div>
                </td>`,
                additionalTD = !isRealignment ? `<td colspan="${countCoimplementors + 3}"></td>` :
                               `<td colspan="${countCoimplementors + 4}"></td>`,
                deleteButton = `
                <td class="align-middle">
                    <a onclick="$(this).deleteRow('#header-row-${newID}');"
                    class="btn btn-outline-red px-1 py-0">
                        <i class="fas fa-minus-circle"></i>
                    </a>
                </td>`,
                sortableButton = `
                <td class="align-middle" style="width: 1px;">
                    <a href="#" class="grey-text">
                        <i class="fas fa-ellipsis-v"></i>
                    </a>
                </td>`;
            rowOutput = '<tr id="header-row-'+newID+'" class="item-row">'+ allotmentHeaderName +
                        additionalTD + deleteButton + sortableButton + '</tr>';

            $(rowOutput).insertAfter('#' + lastRowID);
        } else if (type == 'header-break') {
             let allotmentHeaderName = `
                <td colspan="` + ((!isRealignment ? 4 : 5) + countCoimplementors) + `">
                    <hr>
                    <div class="md-form form-sm my-0">
                        <input name="row_type[${newID}]" type="hidden" value="${type}">
                        <input type="hidden" name="allotment_id[${newID}]">
                        <input type="hidden" name="allotment_realign_id[${newID}]">
                        <input type="hidden"name="allot_class[${newID}]">
                        <input type="hidden"name="allotted_budget[${newID}]">
                        <input type="hidden" name="allotment_name[${newID}]" id="allotment-name-${newID}">` +
                        (headerCoimps.length > 0 ? headerCoimps.map((headerCoimp, index) => {
                            return `<input type="hidden" name="coimplementor_id[${newID}][${index}]">
                            <input type="hidden" name="coimplementor_budget[${newID}][${index}]">`
                        }) : '') +
                    `</div>
                </td>`,
                deleteButton = `
                <td class="align-middle">
                    <a onclick="$(this).deleteRow('#headerbreak-row-${newID}');"
                    class="btn btn-outline-red px-1 py-0">
                        <i class="fas fa-minus-circle"></i>
                    </a>
                </td>`,
                sortableButton = `
                <td class="align-middle" style="width: 1px;">
                    <a href="#" class="grey-text">
                        <i class="fas fa-ellipsis-v"></i>
                    </a>
                </td>`;
            rowOutput = '<tr id="headerbreak-row-'+newID+'" class="item-row">'+ allotmentHeaderName +
                        deleteButton + sortableButton + '</tr>';

            $(rowOutput).insertAfter('#' + lastRowID);
        }
    }

    $.fn.deleteRow = function(row) {
        if (confirm('Are you sure you want to delete this row?')) {
            let _row = row.split('-');
            let rowClass = '.item-' + _row[1];
            let rowCount = $(rowClass).length;

            $(row).fadeOut(300, function() {
                $(this).remove();
                $(this).totalBudgetIsValid();
            });
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
            initializeProjectInput();
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
            initializeProjectInput();
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

		if ($(this).totalBudgetIsValid() && !withError) {
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
