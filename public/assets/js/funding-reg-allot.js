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

    function storeUpdateItems(toggle, regID, formData) {
        const uri = toggle == 'store' ?
             `${baseURL}/report/registry-allot-obli-disb/store-items/${regID}` :
             `${baseURL}/report/registry-allot-obli-disb/store-items/${regID}`;

        $.ajax({
		    url: uri,
            type: 'POST',
            processData: false,
            contentType: false,
            //async: false,
            data: formData,
            //dataType: 'json',
		    success: function(response) {
                console.log(response);
            },
            fail: function(xhr, textStatus, errorThrown) {
                console.log('fail');
                storeUpdateItems(toggle, regID, formData);
		    },
		    error: function(data) {
                console.log('error');
                storeUpdateItems(toggle, regID, formData);
		    }
        });
    }

    function initStoreUpdateItems(toggle, regID) {
        $('.item-row').each(function(index, elem) {
            const orderNo = index + 1;
                  dateReceived = $(elem).find('.date-received').val();
                  dateObligated = $(elem).find('.date-obligated').val();
                  dateReleased = $(elem).find('.date-released').val();
                  payee = $(elem).find('.payee').val();
                  particulars = $(elem).find('.particulars').val();
                  serialNumber = $(elem).find('.serial-number').val();
                  uacsObjectCode = $(elem).find('.uacs-object').val();
                  allotments = $(elem).find('.allotments').val();
                  obligations = $(elem).find('.obligations').val();
                  unobligatedAllot = $(elem).find('.unobligated').val();
                  disbursement = $(elem).find('.disbursements').val();
                  dueDemandable = $(elem).find('.due-demandable').val();
                  notDueDemandable = $(elem).find('.not-due-demandable').val();

            let formData = new FormData();
            formData.append('reg_allotment_id', regID);
            formData.append('order_no', orderNo);
            formData.append('date_received', dateReceived);
            formData.append('date_obligated', dateObligated);
            formData.append('date_released', dateReleased);
            formData.append('payee', payee);
            formData.append('particulars', particulars);
            formData.append('serial_number', serialNumber);
            formData.append('uacs_object_code', uacsObjectCode);
            formData.append('allotments', allotments);
            formData.append('obligations', obligations);
            formData.append('unobligated_allot', unobligatedAllot);
            formData.append('disbursement', disbursement);
            formData.append('due_demandable', dueDemandable);
            formData.append('not_due_demandable', notDueDemandable);
            formData.append('_token', CSRF_TOKEN);
            storeUpdateItems(toggle, regID, formData);
        });

        $(document).ajaxStop(function() {
            location.replace(`${baseURL}/report/registry-allot-obli-disb?keyword=${regID}&status=success`);
        });
    }

    function storeUpdateReg(toggle, formData) {
        let regID = $('#reg-id').val();
        const uri = toggle == 'store' ?
                    `${baseURL}/report/registry-allot-obli-disb/store` :
                    `${baseURL}/report/registry-allot-obli-disb/update/${regID}`;
        $.ajax({
		    url: uri,
            type: 'POST',
            processData: false,
            contentType: false,
            //async: false,
            data: formData,
            //dataType: 'json',
		    success: function(response) {
                regID = response;
                initStoreUpdateItems(toggle, regID);
            },
            fail: function(xhr, textStatus, errorThrown) {
                console.log('fail');
                storeUpdateReg(regID, toggle, formData);
		    },
		    error: function(data) {
                console.log('error');
                storeUpdateReg(regID, toggle, formData);
		    }
        });
    }

    function initStoreUpdate() {
        const toggle = $('#toggle').val();
        let formData = new FormData();

        formData.append('period_ending', $('#period-ending').val());
        formData.append('entity_name', $('#entity-name').val());
        formData.append('mfo_pap', $('#mfo-pap').val());
        formData.append('fund_cluster', $('#fund-cluster').val());
        formData.append('sheet_no', $('#sheet-no').val());
        formData.append('legal_basis', $('#legal-basis').val());
        formData.append('_token', CSRF_TOKEN);
        storeUpdateReg(toggle, formData);
    }

    $.fn.solveUnobligated = function(ctr) {
        const allotment = $(`#allotment-${ctr}`).val();
        const obligation = $(`#obligation-${ctr}`).val();
        const unobligated = allotment - obligation;
        $(`#unobligated-${ctr}`).val(unobligated);
    }

    $.fn.solveDueDemandable = function(ctr) {
        const obligation = $(`#obligation-${ctr}`).val() ?
                            $(`#obligation-${ctr}`).val() : 0;
        $(`#due-demandable-${ctr}`).val(obligation);
        $(`#not-due-demandable-${ctr}`).val(0);
    }

    $.fn.solveNotYetDueDemandable = function(ctr) {
        const obligation = $(`#obligation-${ctr}`).val() ?
                            $(`#obligation-${ctr}`).val() : 0;
        $(`#due-demandable-${ctr}`).val(0);
        $(`#not-due-demandable-${ctr}`).val(obligation);
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
			$('#mdb-preloader').css('background', '#000000ab')
                               .fadeIn(300, function() {
                initStoreUpdate();
            });
		}
    }

    $.fn.showEdit = function(url) {
        $('#mdb-preloader').css('background', '#000000ab').fadeIn(300);
        $('#modal-body-edit').load(url, function() {
            $('#mdb-preloader').fadeOut(300);
            $('.crud-select').materialSelect();
            initializeSelect2();
            $(this).slideToggle(500);
            $('.datepicker').datepicker();
            initializeInputs()
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

		if (!withError) {
			$('#mdb-preloader').css('background', '#000000ab')
                               .fadeIn(300, function() {
                initStoreUpdate();
            });
		}
    }

    $.fn.showDelete = function(url) {
		$('#modal-body-delete').html(`Are you sure you want to delete this `+
                                     `Registry of Allotments, Obligations and Disbursement?`);
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
