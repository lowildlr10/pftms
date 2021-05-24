const CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

$(function() {
    const template = '<div class="tooltip md-tooltip">' +
                     '<div class="tooltip-arrow md-arrow"></div>' +
                     '<div class="tooltip-inner md-inner stylish-color"></div></div>';
    let agenciesData = {};

    function initializeSelect2() {
        $('.agencies-tokenizer').select2({
            tokenSeparators: [','],
            placeholder: "Coimplementing Agency/LGU",
            width: '100%',
            maximumSelectionSize: 4,
            allowClear: true,
            ajax: {
                url: `${baseURL}/libraries/agency-lgu/get-agencies-lgus`,
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
                            jsonData['agency_name'] = item.agency_name;
                            agenciesData[item.id] = jsonData;

                            return {
                                text: `${item.agency_name}`,
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

    function toggleComimplementingMenu() {
		const moduleCbox = $('#coimplementing-agency');
        const menuGroup = $('#coimplementing-agency-menu');

		moduleCbox.unbind('change').change(function() {
			if (moduleCbox.is(':checked')) {
                menuGroup.slideToggle(300);
                initializeSelect2();
                $('.coimplementing-agency-lgus').addClass('required');
                $('.coimplementing-project-cost').addClass('required');
			} else {
                menuGroup.slideToggle(300);
                $('.coimplementing-agency-lgus').removeClass('required input-error-highlighter');
                $('.coimplementing-project-cost').removeClass('required input-error-highlighter');
            }
        });
	}

    $.fn.computeTotalProjectCost = function() {
        let totalProjectCost = 0;

        totalProjectCost += parseFloat($('#implementing-project-cost').val());
        $('.coimplementing-project-cost').each(function() {
            totalProjectCost += parseFloat($(this).val());
        });
        $('#project-cost').val(totalProjectCost);
    }

    $.fn.addRow = function(rowClass) {
        let lastRow = $(rowClass).last();
        let lastRowID = (lastRow.length > 0) ? lastRow.attr('id') : 'coimplementing-form-group-0';
        let _lastRowID = lastRowID.split('-');
        let newID = parseInt(_lastRowID[2]) + 1;

        let coimplementingAgency = `
            <div class="md-form">
                <select class="mdb-select form-control-sm agencies-tokenizer required coimplementing-agency-lgus"
                        name="comimplementing_agency_lgus[]"></select>
            </div>`,
            projectCost = `
            <div class="md-form mt-3">
                <input type="number" class="form-control required coimplementing-project-cost"
                       name="coimplementing_project_costs[]"
                       onkeyup="$(this).computeTotalProjectCost();"
                       onchange="$(this).computeTotalProjectCost();">
                <label for="coimplementing-project-cost" class="active">
                    Project Cost (Co-implementing Agency/LGU) <span class="red-text">*</span>
                </label>
            </div>`,
            deleteButton = `
            <a href="#" class="btn btn-outline-red btn-sm btn-block"
               onclick="$(this).deleteRow('#coimplementing-form-group-${newID}');">
                Delete
            </a>`;

        let rowOutput = `
        <div class="coimplementing-form-group border rounded p-3"
             id="coimplementing-form-group-${newID}">
            ${coimplementingAgency}
            ${projectCost}
            ${deleteButton}
        </div>`;

        $(rowOutput).insertAfter('#' + lastRowID);
        initializeSelect2();
    }

    $.fn.deleteRow = function(elemID) {
        if (confirm('Are you sure you want to delete this row?')) {
            let rowClass = '.coimplementing-form-group';
            let rowCount = $(rowClass).length;

            if (rowCount > 1) {
                $(elemID).fadeOut(300, function() {
                    $(this).remove();
                });
            } else {
                alert('Cannot delete all row.');
            }
		}
    }

    $.fn.showCreate = function(url) {
        $('#mdb-preloader').css('background', '#000000ab').fadeIn(300);
        $('#modal-body-create').load(url,function() {
            $('#mdb-preloader').fadeOut(300);
            $(this).slideToggle(500);
            $('.crud-select').materialSelect();
            toggleComimplementingMenu();
        });
        $("#modal-sm-create").modal({keyboard: false, backdrop: 'static'})
						     .on('shown.bs.modal', function() {
            $('#create-title').html('Create Project');
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
            $(this).slideToggle(500);
            $('.crud-select').materialSelect();
            toggleComimplementingMenu();
            initializeSelect2();
        });
        $("#modal-sm-edit").modal({keyboard: false, backdrop: 'static'})
						   .on('shown.bs.modal', function() {
            $('#edit-title').html('Update Project');
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
            $('#delete-title').html('Delete Project');
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
    $('.mdb-select-filter').materialSelect();
});
