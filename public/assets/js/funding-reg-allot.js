const CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

$(function() {
    const template = '<div class="tooltip md-tooltip">' +
                     '<div class="tooltip-arrow md-arrow"></div>' +
                     '<div class="tooltip-inner md-inner stylish-color"></div></div>';
    let payeeData = {},
        mooeTitle = {};

    function initializeSelect2() {
        $('.payee-tokenizer').select2({
            tokenSeparators: [','],
            placeholder: "Value...",
            width: '100%',
            maximumSelectionSize: 4,
            allowClear: true,
            ajax: {
                url: `${baseURL}/report/registry-allot-obli-disb/get-payee`,
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
                            jsonData['name'] = item.name;
                            payeeData[item.id] = jsonData;

                            return {
                                text: `${item.name}`,
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

        $('.uacs-object-tokenizer').select2({
            tokenSeparators: [','],
            placeholder: "Value...",
            width: '100%',
            maximumSelectionSize: 4,
            allowClear: true,
            ajax: {
                url: `${baseURL}/report/registry-allot-obli-disb/get-uacs-object`,
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
                            jsonData['name'] = item.name;
                            jsonData['uacs_code'] = item.uacs_code;
                            mooeTitle[item.id] = jsonData;

                            return {
                                text: `${item.uacs_code}`,
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

    function initializeInputs() {
        $('#period-ending').change(function() {
            const periodEnding = $(this).val();

            $('#mdb-preloader').css('background', '#000000ab').fadeIn(300);
            $('#voucher-table-section').slideUp(300).html('');
            $('#voucher-table-section').load(`${baseURL}/report/registry-allot-obli-disb/get-vouchers`, {
                _token: CSRF_TOKEN,
                period_ending: periodEnding
            }, function() {
                $('#mdb-preloader').fadeOut(300);
                initializeSelect2();
                initializeSortable();
                $(this).slideDown(500);
            });
        });
    }

    $.fn.showCreate = function(url) {
        $('#mdb-preloader').css('background', '#000000ab').fadeIn(300);
        $('#modal-body-create').load(url, function() {
            $('#mdb-preloader').fadeOut(300);
            $('.crud-select').materialSelect();
            //initializeProjectInput();
            $(this).slideToggle(500);
            $('.datepicker').datepicker();
            initializeInputs()
        });
        $("#modal-lg-create").modal({keyboard: false, backdrop: 'static'})
						     .on('shown.bs.modal', function() {
            $('#create-title').html('Create Registry of Allotments, Obligations and Disbursement');
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
            initializeProjectInput();
            $(this).slideToggle(500);
            initializeSelect2();
            initializeSortable();
        });
        $("#modal-lg-edit").modal({keyboard: false, backdrop: 'static'})
						   .on('shown.bs.modal', function() {
            $('#edit-title').html('Update Registry of Allotments, Obligations and Disbursement');
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
});
