const CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

$(function() {
    const template = '<div class="tooltip md-tooltip">' +
                     '<div class="tooltip-arrow md-arrow"></div>' +
                     '<div class="tooltip-inner md-inner stylish-color"></div></div>';
    let selectAjaxData = {};

    function initializeSelect2(reInit = false) {
        if (reInit) {
            $('.industry-tokenizer').select2('destroy');
            $('.coimp-agencies-tokenizer').select2('destroy');
            $('.agency-tokenizer').select2('destroy');
        }

        /*
        let dropdownParent = '#modal-sm-create';

        if ($('#modal-sm-create').hasClass('show')) {
            dropdownParent = '#modal-sm-create';
        } else {
            dropdownParent = '#modal-sm-edit';
        }*/

        let dropdownParent = '';

        //console.log(dropdownParent);

        let singleSelectConf = {
            tags: true,
            tokenSeparators: [','],
            placeholder: "Coimplementing Agency/LGU",
            width: '100%',
            maximumSelectionSize: 4,
            allowClear: true,
            dropdownParent: '',
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
                            selectAjaxData[item.id] = jsonData;

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
        };
        let multiSelectConf = {
            tags: true,
            tokenSeparators: [',', '/'],
            width: 'resolve',
            placeholder: 'Insert here...',
            multiple: true,
            maximumSelectionLength: 5,
            allowClear: true,
            theme: "classic"
        };

        $('.directory-tokenizer').select2(multiSelectConf);
        $('.directory-tokenizer').on("select2:select", function (evt) {
            const element = evt.params.data.element;
            const $element = $(element);

            $element.detach();
            $(this).append($element);
            $(this).trigger("change");
        });
        $('select[name="directory[]"]').on("select2:select change", function (evt) {
            const selValues = $(this).val();
            let directoryDisplay = '';

            $.each(selValues, function(i, val) {
                if (i == 0) {
                    directoryDisplay += val;
                } else {
                    directoryDisplay += ` / ${val}`;
                }
            });

            if (selValues.length > 0) {
                $('#directory-view').html(`(${directoryDisplay})`);
            } else {
                $('#directory-view').html('(e.g. "MOOE / RO / DRRM")');
            }
        });

        multiSelectConf['maximumSelectionLength'] = false


        $('.coimp-agencies-tokenizer').each((key, elem) => {
            singleSelectConf['placeholder'] = 'Coimplementing Agency/LGU';
            singleSelectConf['dropdownParent'] = $(elem).parent();
            //singleSelectConf['dropdownParent'] = $(`${dropdownParent} #coimplementing-agency-menu`);
            singleSelectConf['ajax']['url'] = `${baseURL}/libraries/agency-lgu/get-agencies-lgus`;
            singleSelectConf['ajax']['processResults'] = (data) => {
                return {
                    results: $.map(data, function(item) {
                        let jsonData = {};
                        jsonData['agency_name'] = item.agency_name;
                        selectAjaxData[item.id] = jsonData;

                        return {
                            text: `${item.agency_name}`,
                            id: item.id
                        }
                    }),
                    pagination: {
                        more: true
                    }
                };
            };

            $(elem).select2(singleSelectConf);
        });

        singleSelectConf['placeholder'] = 'Choose an Industry/Sector';
        singleSelectConf['dropdownParent'] = $('.industry-tokenizer').parent();
        singleSelectConf['ajax']['url'] = `${baseURL}/libraries/industry-sector/get-industry-sector`;
        singleSelectConf['ajax']['processResults'] = (data) => {
            return {
                results: $.map(data, function(item) {
                    let jsonData = {};
                    jsonData['sector_name'] = item.sector_name;
                    selectAjaxData[item.id] = jsonData;

                    return {
                        text: `${item.sector_name}`,
                        id: item.id
                    }
                }),
                pagination: {
                    more: true
                }
            };
        };

        $('.industry-tokenizer').select2(singleSelectConf);

        multiSelectConf['tags'] = false;
        $('.proj-site-tokenizer').select2(multiSelectConf);

        singleSelectConf['placeholder'] = 'Choose an Implementing Agency';
        singleSelectConf['dropdownParent'] = $('.agency-tokenizer').parent();
        singleSelectConf['ajax']['url'] = `${baseURL}/libraries/agency-lgu/get-agencies-lgus`;
        singleSelectConf['ajax']['processResults'] = (data) => {
            return {
                results: $.map(data, function(item) {
                    let jsonData = {};
                    jsonData['agency_name'] = item.agency_name;
                    selectAjaxData[item.id] = jsonData;

                    return {
                        text: `${item.agency_name}`,
                        id: item.id
                    }
                }),
                pagination: {
                    more: true
                }
            };
        };

        $('.agency-tokenizer').select2(singleSelectConf);

        multiSelectConf['tags'] = false;
        $('.proponent-tokenizer').select2(multiSelectConf);

        multiSelectConf['tags'] = true;
        multiSelectConf.ajax = {
            url: `${baseURL}/libraries/monitoring-office/get-monitoring-office`,
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
                        jsonData['office_name'] = item.office_name;
                        selectAjaxData[item.id] = jsonData;

                        return {
                            text: `${item.office_name}`,
                            id: item.id
                        }
                    }),
                    pagination: {
                        more: true
                    }
                };
            },
            cache: true
        };

        $('.monitoring-tokenizer').select2(multiSelectConf);
    }

    function toggleComimplementingMenu() {
		const moduleCbox = $('#coimplementing-agency');
        const menuGroup = $('#coimplementing-agency-menu');

		moduleCbox.unbind('change').change(function() {
			if (moduleCbox.is(':checked')) {
                menuGroup.slideToggle(300);
                initializeSelect2(true);
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
            if (!isNaN(parseFloat($(this).val()))) {
                totalProjectCost += parseFloat($(this).val());
            }
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
                <em><small>(Dynamic)</small></em>
                <select class="mdb-select form-control-sm coimp-agencies-tokenizer coimplementing-agency-lgus"
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

        $('.coimp-agencies-tokenizer').select2();
        initializeSelect2(true);
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
            initializeSelect2();
            //initializeSelect2();
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
            //initializeSelect2();
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
    $('.treeview-animated').mdbTreeview();
    $('.treeview-animated-element').addClass('white dark-grey-text');
    $('.treeview-animated-items').find('.closed').addClass('h4');
});
