$(function() {
    const template = '<div class="tooltip md-tooltip">' +
                     '<div class="tooltip-arrow md-arrow"></div>' +
                     '<div class="tooltip-inner md-inner stylish-color"></div></div>';

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.fn.showRemarks = function(url) {
        $('#mdb-preloader').css('background', '#000000ab').fadeIn(300);
        $('#modal-body-show').load(url, function() {
            $('#mdb-preloader').fadeOut(300);
            $(this).slideToggle(500);
        });
        $("#modal-show").modal({keyboard: false, backdrop: 'static'})
						.on('shown.bs.modal', function() {
            $('#show-title').html('View Remarks');
		}).on('hidden.bs.modal', function() {
		    $('#modal-body-show').html('').css('display', 'none');
		});
    }

    function sendRemarks(url, refreshURL, formData) {
        $.ajax({
		    url: url,
            type: 'POST',
            processData: false,
            contentType: false,
            data: formData,
		    success: function(response) {
                $('#modal-body-show').load(refreshURL, function() {
                    $('#mdb-preloader').fadeOut(300);
                });
            },
            fail: function(xhr, textStatus, errorThrown) {
                sendRemarks(url, refreshURL, formData);
		    },
		    error: function(data) {
                sendRemarks(url, refreshURL, formData);
		    }
        });
    }

    function computeRemainingUacs() {
        $('.uacs_amount').on('keyup change', function() {
            const amount = parseFloat($('#remaining-original').val());
            let remaining = amount;

            $('.uacs_amount').each(function() {
                const valAmount = !isNaN(parseFloat($(this).val())) ? parseFloat($(this).val()) : 0.00;
                console.log(valAmount);
                remaining -= valAmount;
            });

            $('#remaining').val(remaining.toFixed(2));

            if (parseFloat($('#remaining').val()) < 0) {
                $('#remaining').addClass('input-error-highlighter')
                               .tooltip('show');
            } else {
                $('#remaining').removeClass('input-error-highlighter')
                               .tooltip('hide');
            }
        });
    }

    function initializeInputs() {
        $('#sel-uacs-code').change(function() {
            const uacsVals = $(this).val();
            const uacsDescs = $(this).find('option:selected').not('option:disabled').map(function(){
                return $(this).text().trim();
            }).get();
            let uacsHTML = '';

            if (!empty(uacsVals) && !empty(uacsDescs)) {
                uacsVals.forEach((uacsID, uacsIndex) => {
                    const uacsDescCode = uacsDescs[uacsIndex];
                    const uacsCode = uacsDescCode.split(" : ")[0];
                    const uacsDesc = uacsDescCode.split(" : ")[1];

                    let description = $(`#uacs_description_${uacsID}`).val();
                    let _uacsID = $(`#uacs_id_${uacsID}`).val();
                    let dvUacsID = $(`#dv_uacs_id_${uacsID}`).val();
                    let amount = $(`#uacs_amount_${uacsID}`).val();

                    description = !empty(description) ? description : '';
                    _uacsID = !empty(_uacsID) ? _uacsID : uacsID;
                    dvUacsID = !empty(dvUacsID) ? dvUacsID : '';
                    amount = !empty(amount) ? amount : 0;

                    uacsHTML += `
                    <div class="row" id="uacs_description_{{ $itemCtr }}">
                        <div class="col-md-10 border">
                            <div class="md-form form-sm" id="uacs_description_${uacsIndex}">
                                <input type="text" id="uacs_description_${uacsID}" name="uacs_description[]"
                                    class="form-control required" placeholder="Item Description for '${uacsDesc}'"
                                    value="${description}">
                                <input type="hidden" id="uacs_id_${uacsID}" name="uacs_id[]" value="${_uacsID}">
                                <input type="hidden" id="dv_uacs_id_${uacsID}" name="dv_uacs_id[]" value="${dvUacsID}">
                                <label for="uacs_description_${uacsID}" class="active">
                                    <span class="red-text">* </span>
                                    <strong>${uacsDescCode}</strong>
                                </label>
                            </div>
                            <div class="md-form form-sm" id="uacs_amount_${uacsIndex}">
                                <input type="number" id="uacs_amount_${uacsID}" name="uacs_amount[]"
                                    class="form-control uacs_amount required" value="${amount}">
                                <label for="uacs_amount_${uacsID}" class="active">
                                    <span class="red-text">* </span>
                                    <strong>Amount</strong>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-2 p-0 border">
                            <a onclick="$(this).deleteUacsItem(
                                '#uacs_description_${uacsIndex}', '#uacs_amount_${uacsIndex}',
                                '${uacsID}'
                            );"
                            class="btn btn-red btn-sm btn-block h-100 text-center" >
                                <strong>Del <i class="fas fa-trash-alt fa-2x"></i></strong>
                            </a>
                        </div>
                    </div>
                    `;
                });
                $('#remaining-amount-segment').show();
            } else {
                $('#remaining-amount-segment').hide();
                uacsHTML = '';
            }

            $('#uacs-description-segment').html(uacsHTML);

            computeRemainingUacs();
        });

        $('#amount').change(function() {
            const amount = $(this).val();
            $('#total').val(amount);
        });

        computeRemainingUacs();
    }

    $.fn.deleteUacsItem = function(elemDescID, elemAmountID, uacsID) {
        $(elemDescID).remove();
        $(elemAmountID).remove();

        //$(`#sel-uacs-code option:selected`).removeAttr('selected');
    }

    $.fn.refreshRemarks = function(refreshURL) {
        $('#mdb-preloader').css('background', '#000000ab').fadeIn(300);
        $('#modal-body-show').load(refreshURL, function() {
            $('#mdb-preloader').fadeOut(300);
        });
    }

    $.fn.storeRemarks = function(url, refreshURL) {
        let formData = new FormData();
        const message = $('#message').val(),
              withError = inputValidation(false);

		if (!withError) {
            $('#mdb-preloader').css('background', '#000000ab').fadeIn(300);
            formData.append('message', message);
			sendRemarks(url, refreshURL, formData);
        }
    }

    $.fn.showCreate = function(url) {
        $('#mdb-preloader').css('background', '#000000ab').fadeIn(300);
        $('#modal-body-create').load(url, function() {
            $('#mdb-preloader').fadeOut(300);
            $('.crud-select').materialSelect();
            $(this).slideToggle(500);

            $('#amount').keyup(function() {
                const amount = $(this).val();
                $('#total-amount').val(amount);
            }).change(function() {
                const amount = $(this).val();
                $('#total-amount').val(amount);
            });
        });
        $("#modal-lg-create").modal({keyboard: false, backdrop: 'static'})
						     .on('shown.bs.modal', function() {
            $('#create-title').html('Create Dibursement Voucher');
		}).on('hidden.bs.modal', function() {
		    $('#modal-body-create').html('').css('display', 'none');
		});
    }

    $.fn.showCreateLR = function(url) {
        $('#mdb-preloader').css('background', '#000000ab').fadeIn(300);
        $('#modal-body-create').load(url, function() {
            $('#mdb-preloader').fadeOut(300);
            $('.crud-select').materialSelect();
            $(this).slideToggle(500);

            $('#amount').change(function() {
                const amount = $(this).val();
                $('#total').val(amount);
            });
        });
        $("#modal-lg-create").modal({keyboard: false, backdrop: 'static'})
						     .on('shown.bs.modal', function() {
            $('#create-title').html('Create Liquidation Report from DV');
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

            $('#amount').keyup(function() {
                const amount = $(this).val();
                $('#total-amount').val(amount);
            }).change(function() {
                const amount = $(this).val();
                $('#total-amount').val(amount);
            });
        });
        $("#modal-lg-edit").modal({keyboard: false, backdrop: 'static'})
						   .on('shown.bs.modal', function() {
            $('#edit-title').html('Update Dibursement Voucher');
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
            $('#delete-title').html('Delete DV');
            $('#form-delete').attr('action', url);
		}).on('hidden.bs.modal', function() {
             $('#modal-delete-body').html('');
             $('#form-delete').attr('action', '#');
		});
    }

    $.fn.delete = function() {
        $('#form-delete').submit();
    }

	$.fn.showIssue = function(url) {
        $('#mdb-preloader').css('background', '#000000ab').fadeIn(300);
        $('#modal-body-issue').load(url, function() {
            $('#mdb-preloader').fadeOut(300);
            $(this).slideToggle(500);
        });
        $("#modal-issue").modal({keyboard: false, backdrop: 'static'})
						 .on('shown.bs.modal', function() {
            $('#issue-title').html('Submit Dibursement Voucher');
            $(this).find('.btn-orange').html('<i class="fas fa-paper-plane"></i> Submit');
		}).on('hidden.bs.modal', function() {
            $('#modal-body-issue').html('').css('display', 'none');
		});
    }

    $.fn.issue = function() {
        $('#form-issue').submit();
    }

    $.fn.showReceive = function(url) {
        $('#mdb-preloader').css('background', '#000000ab').fadeIn(300);
        $('#modal-body-receive').load(url, function() {
            $('#mdb-preloader').fadeOut(300);
            $(this).slideToggle(500);
        });
        $("#modal-receive").modal({keyboard: false, backdrop: 'static'})
						   .on('shown.bs.modal', function() {
            $('#receive-title').html('Receive Dibursement Voucher');
		}).on('hidden.bs.modal', function() {
            $('#modal-body-receive').html('').css('display', 'none');
		});
    }

    $.fn.receive = function() {
        $('#form-receive').submit();
    }

    $.fn.showIssueBack = function(url) {
        $('#mdb-preloader').css('background', '#000000ab').fadeIn(300);
        $('#modal-body-issue-back').load(url, function() {
            $('#mdb-preloader').fadeOut(300);
            $(this).slideToggle(500);
        });
        $("#modal-issue-back").modal({keyboard: false, backdrop: 'static'})
						      .on('shown.bs.modal', function() {
            $('#issue-back-title').html('Submit Back Dibursement Voucher');
		}).on('hidden.bs.modal', function() {
            $('#modal-body-issue-back').html('').css('display', 'none');
		});
    }

    $.fn.issueBack = function() {
        $('#form-issue-back').submit();
    }

    $.fn.showReceiveBack = function(url) {
        $('#mdb-preloader').css('background', '#000000ab').fadeIn(300);
        $('#modal-body-receive-back').load(url, function() {
            $('#mdb-preloader').fadeOut(300);
            $(this).slideToggle(500);
        });
        $("#modal-receive-back").modal({keyboard: false, backdrop: 'static'})
						        .on('shown.bs.modal', function() {
            $('#receive-back-title').html('Receive Back Dibursement Voucher');
		}).on('hidden.bs.modal', function() {
            $('#modal-body-receive-back').html('').css('display', 'none');
		});
    }

    $.fn.receiveBack = function() {
        $('#form-receive-back').submit();
    }

    $.fn.showPayment = function(url) {
        $('#mdb-preloader').css('background', '#000000ab').fadeIn(300);
        $('#modal-body-payment').load(url, function() {
            $('#mdb-preloader').fadeOut(300);
            $(this).slideToggle(500);
        });
        $("#modal-payment").modal({keyboard: false, backdrop: 'static'})
						        .on('shown.bs.modal', function() {
            $('#payment-title').html('To Payment');
		}).on('hidden.bs.modal', function() {
            $('#modal-body-payment').html('').css('display', 'none');
		});
    }

    $.fn.payment = function() {
        const withError = inputValidation(false);

        if (!withError) {
            $('#form-payment').submit();
        }
    }

    $.fn.showDisburse = function(url) {
        $('#mdb-preloader').css('background', '#000000ab').fadeIn(300);
        $('#modal-body-disburse').load(url, function() {
            $('#mdb-preloader').fadeOut(300);
            $(this).slideToggle(500);
        });
        $("#modal-disburse").modal({keyboard: false, backdrop: 'static'})
						        .on('shown.bs.modal', function() {
            $('#disburse-title').html('Disburse');
		}).on('hidden.bs.modal', function() {
            $('#modal-body-disburse').html('').css('display', 'none');
		});
    }

    $.fn.disburse = function() {
        const withError = inputValidation(false);

        if (!withError) {
            $('#form-disburse').submit();
        }
    }

    $.fn.showUacsItems = function(url) {
        $('#mdb-preloader').css('background', '#000000ab').fadeIn(300);
        $('#modal-body-uacs').load(url, function() {
            $('#mdb-preloader').fadeOut(300);
            $('.crud-select').materialSelect();
            $(this).slideToggle(500);
            initializeInputs();
        });
        $("#modal-uacs").modal({keyboard: false, backdrop: 'static'})
						        .on('shown.bs.modal', function() {
            $('#uacs-title').html('Update DV UACS Items');
		}).on('hidden.bs.modal', function() {
            $('#modal-body-uacs').html('').css('display', 'none');
		});
    }

    $.fn.updateUacsItems = function() {
        const withError = inputValidation(false);

        if (!withError) {
            $('#form-uacs-items').submit();
        }
    }

    $('.material-tooltip-main').tooltip({
        template: template
    });
});
