const CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

$(function() {
    const template = '<div class="tooltip md-tooltip">' +
                     '<div class="tooltip-arrow md-arrow"></div>' +
                     '<div class="tooltip-inner md-inner stylish-color"></div></div>';
    let payeeData = {},
        unitData = {},
        mooeTitle = {};

    function s2ab(s) {
        var buf = new ArrayBuffer(s.length);
        var view = new Uint8Array(buf);
        for (var i=0; i<s.length; i++) view[i] = s.charCodeAt(i) & 0xFF;
        return buf;
    }

    function filterNaN(inputVal) {
        let outputVal = isNaN(inputVal) ? 0 : inputVal;

        return outputVal;
    }

    function initializeSelect2(forType) {
        $('.payee-tokenizer').select2({
            tokenSeparators: [','],
            placeholder: "Value...",
            width: '100%',
            maximumSelectionSize: 4,
            allowClear: true,
            ajax: {
                url: `${baseURL}/report/ledger/${forType}/get-payee`,
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

        $('.unit-tokenizer').select2({
            tokenSeparators: [','],
            placeholder: "Value...",
            width: '100%',
            maximumSelectionSize: 4,
            allowClear: true,
            ajax: {
                url: `${baseURL}/report/ledger/${forType}/get-unit`,
                type: "post",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        _token: CSRF_TOKEN,
                        search: params.term
                    };
                },
                processResults: function(res) {
                    const data = [
                        {id: '-', name: '-- None --'},
                        ...res
                    ];

                    return {
                        results: $.map(data, function(item) {
                            let jsonData = {};
                            jsonData['name'] = item.name;
                            unitData[item.id] = jsonData;

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

        $('.mooe-title-tokenizer').select2({
            tokenSeparators: [','],
            placeholder: "Value...",
            width: '100%',
            maximumSelectionSize: 4,
            allowClear: true,
            ajax: {
                url: `${baseURL}/report/ledger/${forType}/get-unit`,
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

    $.fn.generateExcel = function() {
        const forType = $('#for').val();
        const ledgerType = $('#type').val();
        const fileName = `${forType} ledger - ${ledgerType}.xlsx`;

		wb = XLSX.utils.table_to_book(document.getElementById('table-ledger'), {sheet:ledgerType});
    	wbout = XLSX.write(wb, {bookType:'xlsx', bookSST:true, type: 'binary'});

    	saveAs(new Blob([s2ab(wbout)],{type:"application/octet-stream"}), fileName);
    }

    function initializeSortable() {
        $('.sortable').sortable({
            items: '> tr:not(.exclude-sortable)'
        });
        $('.sortable').disableSelection();
    }

    $.fn.computeTotalPriorYear = function() {
        let totalPriorYear = 0;

        $('.prior-year').each(function() {
            const priorYear = parseFloat($(this).val());
            totalPriorYear += priorYear;
        });

        $('#total-prior-year').val(totalPriorYear.toFixed(2));
    }

    $.fn.computeTotalContinuing = function() {
        let totalContinuing = 0;

        $('.continuing').each(function() {
            const continuing = parseFloat($(this).val());
            totalContinuing += continuing;
        });

        $('#total-continuing').val(totalContinuing.toFixed(2));
    }

    $.fn.computeTotalCurrent = function() {
        let totalCurrent = 0;

        $('.current').each(function() {
            const current = parseFloat($(this).val());
            totalCurrent += current;
        });

        $('#total-current').val(totalCurrent.toFixed(2));
    }

    $.fn.computeTotalRemaining = function() {
        let totalRemaining = $('#current-total-budget').val();

        $('.amount').each(function() {
            const amount = parseFloat($(this).val());
            totalRemaining -= amount;
        });

        $('#total-remaining').val(totalRemaining.toFixed(2));
    }

    $.fn.computeTotalRemaining2 = function() {
        let totalRemaining = 0;

        $('.amount').each(function() {
            const amount = parseFloat($(this).val());
            totalRemaining += amount;
        });

        $('#total-remaining').val(totalRemaining.toFixed(2));
    }

    $.fn.computeAllotmentRemaining = function() {
        const allotmentCount = parseInt($('#allotment-count').val());

        for (let allotCtr = 1; allotCtr <= allotmentCount; allotCtr++) {
            let allotRemaining = $(`#allotment-cost-${allotCtr}`).val();

            $(`.allotment-${allotCtr}`).each(function() {
                const remaining = $(this).val();
                allotRemaining -= remaining;
            });

            $(`#remaining-${allotCtr}`).val(allotRemaining.toFixed(2))
        }
    }

    function initializeLedgerInput() {
        const allotmentCount = parseInt($('#allotment-count').val());
        const forType = $('#for').val();
        const ledgerType = $('#type').val();

        try {
            if (forType == 'obligation') {
                if (ledgerType == 'saa') {
                    $(this).computeTotalRemaining();
                    $(this).computeAllotmentRemaining();
                }
            } else {
                if (ledgerType == 'saa') {
                    $(this).computeTotalRemaining();
                    $(this).computeAllotmentRemaining();
                } else if (ledgerType == 'mooe') {
                    $(this).computeTotalCurrent();
                    $(this).computeTotalContinuing();
                    $(this).computeTotalPriorYear();
                } else if (ledgerType == 'lgia') {
                    $(this).computeTotalRemaining2();
                    $(this).computeTotalCurrent();
                    $(this).computeTotalContinuing();
                    $(this).computeTotalPriorYear();
                }
            }
        } catch (error) { }

        for (let allotCtr = 1; allotCtr <= allotmentCount; allotCtr++) {
            $(`.allotment-${allotCtr}`).each(function() {
                const allotName = $(`#allot-name-${allotCtr}`).text();
                const allotCost = parseFloat($(`#allotment-cost-${allotCtr}`).val()).toFixed(2);
                $(this).attr('title', `Column: ${allotName} (Amount: ${allotCost})`);
            });
        }

        $('.material-tooltip-main').tooltip({
            template: template
        });
    }

    function storeLedgerItems(id, forType, ledgerType, formData) {
        const storeDataURL = toggle == 'store' ?
                             `${baseURL}/procurement/abstract/store-items/${abstractID}` :
                             `${baseURL}/procurement/abstract/update-items/${abstractID}`;
        $.ajax({
		    url: storeDataURL,
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
                ledgerType(id, forType, ledgerType, formData);
		    },
		    error: function(data) {
                console.log('error');
                ledgerType(id, forType, ledgerType, formData);
		    }
        });
    }

    function processData() {
        const allotmentCount = parseInt($('#allotment-count').val()),
              forType = $('#for').val(),
              ledgerType = $('#type').val();

        console.log(allotmentCount);
        console.log(forType);
        console.log(ledgerType);

        /*
        $("input[name=date_abstract]").val(dateAbstract);
        $("input[name=mode_procurement]").val(modeProcurement);
        $("input[name=sig_chairperson]").val(sigChairperson);
        $("input[name=sig_vice_chairperson]").val(sigViceChairperson);
        $("input[name=sig_first_member]").val(sigFirstPerson);
        $("input[name=sig_second_member]").val(sigSecondPerson);
        $("input[name=sig_third_member]").val(sigThirdPerson);
        $("input[name=sig_end_user]").val(sigEndUser);

        $('select.sel-bidder-count').each(function(grpKey, elemSelBidder) {
            const bidderCount = parseInt($(elemSelBidder).val());

            let selectedSuppliers = [];
            const containerID = '#container_' + (grpKey + 1);

            $(containerID).find('.sel-supplier').each(function() {
                const selectedSupplier = $(this).val();
                selectedSuppliers.push({'selected_supplier' : selectedSupplier});
            });

            $(containerID).find('tbody.table-data').each(function(tblIndex, tableBody) {
                $(tableBody).find('tr').each(function(rowCtr, elemRow) {
                    let jsonData = {},
                        prItemID = "",
                        abstractitemIDs = [],
                        unitCosts = [],
                        totalCosts = [],
                        specifications = [],
                        remarks = [],
                        awardedTo = 0,
                        documentType = "",
                        awardedRemark = "",
                        formData = new FormData();

                    prItemID = $(elemRow).find('.item-id').val();
                    jsonData['select_suppliers'] = JSON.stringify(selectedSuppliers);
                    jsonData['bidder_count'] = bidderCount;
                    jsonData['pr_item_id'] = prItemID;

                    $(elemRow).find('.abstract-item-id').each(function() {
                        const abstractitemID = $(this).val();
                        abstractitemIDs.push({'abs_item_id' : abstractitemID});
                    });

                    jsonData['abstract_item_ids'] = JSON.stringify(abstractitemIDs);

                    $(elemRow).find('.unit-cost').each(function() {
                        const unitCost = parseFloat($(this).val()).toFixed(2);
                        unitCosts.push({'unit_cost' : unitCost});
                    });

                    jsonData['unit_costs'] = JSON.stringify(unitCosts);

                    $(elemRow).find('.total-cost').each(function() {
                        const totalCost = parseFloat($(this).val()).toFixed(2);
                        totalCosts.push({'total_cost' : totalCost});
                    });

                    jsonData['total_costs'] = JSON.stringify(totalCosts);

                    $(elemRow).find('.specification').each(function() {
                        const specification = $(this).val();
                        specifications.push({'specification' : specification});
                    });

                    jsonData['specifications'] = JSON.stringify(specifications);

                    $(elemRow).find('.remarks').each(function() {
                        const remark = $(this).val();
                        remarks.push({'remarks' : remark});
                    });

                    jsonData['remarks'] = JSON.stringify(remarks);

                    awardedTo = $(elemRow).find('.awarded-to').val();
                    documentType = $(elemRow).find('.document-type').val();
                    awardedRemark = $(elemRow).find('.awarded-remarks').val();

                    jsonData['awarded_to'] = awardedTo;
                    jsonData['document_type'] = documentType;
                    jsonData['awarded_remark'] = awardedRemark;

                    formData.append('json_data', JSON.stringify(jsonData));

                    storeAbstractItems(abstractID, toggle, formData);
                });
            });
        });*/
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

    $.fn.showLedger = function(url, title) {
        $('#mdb-preloader').css('background', '#000000ab').fadeIn(300);
        $('#modal-body-show-full').load(url, function() {
            $('#mdb-preloader').fadeOut(300);
            $('.crud-select').materialSelect();
            initializeLedgerInput();
            $(this).slideToggle(500);
        });
        $("#modal-lg-show-full").modal({keyboard: false, backdrop: 'static'})
						     .on('shown.bs.modal', function() {
            $('#show-full-title').html(title);
		}).on('hidden.bs.modal', function() {
		    $('#modal-body-show-full').html('').css('display', 'none');
		});
    }

    $.fn.showCreate = function(url) {
        $('#mdb-preloader').css('background', '#000000ab').fadeIn(300);
        $('#modal-body-create').load(url, function() {
            const forType = $('#for').val();

            $('#mdb-preloader').fadeOut(300);
            $('.crud-select').materialSelect();
            initializeLedgerInput();
            $(this).slideToggle(500);
            initializeSelect2(forType);
            initializeSortable();
        });
        $("#modal-lg-create").modal({keyboard: false, backdrop: 'static'})
						     .on('shown.bs.modal', function() {
            $('#create-title').html('Create Ledger');
		}).on('hidden.bs.modal', function() {
		    $('#modal-body-create').html('').css('display', 'none');
		});
    }

    $.fn.store = function() {
        const withError = inputValidation(false);


		if ($(this).totalBudgetIsValid() && !withError) {
            $('#mdb-preloader').css('background', '#000000ab')
                               .fadeIn(300, function() {
                /*
                processData();
                $(document).ajaxStop(function() {
                    //$('#form-store').submit();
                });*/
                $('#form-store').submit();
            });
        }
    }

    $.fn.showEdit = function(url) {
        $('#mdb-preloader').css('background', '#000000ab').fadeIn(300);
        $('#modal-body-edit').load(url, function() {
            const forType = $('#for').val();

            $('#mdb-preloader').fadeOut(300);
            $('.crud-select').materialSelect();
            initializeLedgerInput();
            $(this).slideToggle(500);
            initializeSelect2(forType);
            initializeSortable();
        });
        $("#modal-lg-edit").modal({keyboard: false, backdrop: 'static'})
						   .on('shown.bs.modal', function() {
            $('#edit-title').html('Update Ledger');
		}).on('hidden.bs.modal', function() {
            $('#modal-body-edit').html('').css('display', 'none');
		});
    }

    $.fn.update = function() {
        const withError = inputValidation(false);

		if ($(this).totalBudgetIsValid() && !withError) {
            $('#mdb-preloader').css('background', '#000000ab')
                               .fadeIn(300, function() {
                $('#form-update').submit();
            });
		}
    }

    $.fn.showDelete = function(url, name) {
		$('#modal-body-delete').html(`Are you sure you want to delete this ${name} `+
                                     `Ledger?`);
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

    $('.treeview-animated').mdbTreeview();
    $('.treeview-animated-element').addClass('white dark-grey-text');
    $('.treeview-animated-items').find('.closed').addClass('h4');
});
