$(function() {
    const template = '<div class="tooltip md-tooltip">' +
                     '<div class="tooltip-arrow md-arrow"></div>' +
                     '<div class="tooltip-inner md-inner stylish-color"></div></div>';

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function multiplyInputs(element) {
		const unitCost = parseFloat(element.val()),
		      quantity = parseInt(element.prev('input').val());
		let totalCost = unitCost * quantity;

		if (totalCost == null || totalCost == 0) {
			totalCost = 0.00;
		}

		element.closest('td')
			   .find('.total-cost')
			   .val(totalCost.toFixed(2));
	}

    function setMultiplyTwoInputs() {
		$('.unit-cost').each(function() {
			$(this).unbind('keyup').unbind('change')
			.keyup(function() {
				multiplyInputs($(this));
			}).change(function() {
				multiplyInputs($(this));
			});
		});
    }

    function checkSelectUniqueness() {
		$('.header-group').each(function(keyGroup) {
			const headerGroup = $(this);

			headerGroup.find('.sel-supplier').each(function(index) {
				const selectedSupplier = $(this);
				let oldValue = 0;

				selectedSupplier.unbind('click').click(function() {
				    oldValue = selectedSupplier.val();
				}).unbind('change').change(function() {
					const supplierID = selectedSupplier.val();
					let selectHtmlValues = '<option value="">-- No awardee --</option>',
                        hasDuplicate = false;

					headerGroup.find('.sel-supplier').each(function(index2) {
                        const _supplierID = $(this).val(),
                              optSelected = $(this).find('option:selected').text();

						if (index != index2) {
							if (_supplierID == supplierID) {
								selectedSupplier.val(oldValue);
								hasDuplicate = true;
								alert('The selected suppliers must be unique.');
							}
						}

						selectHtmlValues += `<option value="${_supplierID}">${optSelected}</option>`;
					});

					if (!hasDuplicate) {
						headerGroup.closest('.table-segment-group').find('.awarded-to').each(function() {
							$(this).html(selectHtmlValues);
						});
					}
				});
			});
		});
    }

    function initInputs(id) {
        $("input[name=has_vice_chair]").unbind('click').click(function() {
            if ($("input[name=has_vice_chair]:checked").val() == 'n') {
                $('#sig_vice_chairman').attr('disabled', 'disabled');
            } else {
                $('#sig_vice_chairman').removeAttr('disabled');
            }
        });

        $("input[name=has_sec_member]").unbind('click').click(function() {
            if ($("input[name=has_sec_member]:checked").val() == 'n') {
                $('#sig_second_member').attr('disabled', 'disabled')
                                       .removeClass('required')
                                       .val('');
                $('input[name=has_alt_member][value="n"]').prop('checked', true);
                $('#sig_alternate').attr('disabled', 'disabled')
                                   .removeClass('required')
                                   .val('');
            } else {
                $('#sig_second_member').removeAttr('disabled')
                                       .addClass('required');
            }
        });

        $("input[name=has_alt_member]").unbind('click').click(function() {
            if ($("input[name=has_sec_member]:checked").val() == 'y') {
                if ($("input[name=has_alt_member]:checked").val() == 'n') {
                    $('#sig_alternate').attr('disabled', 'disabled')
                                       .removeClass('required')
                                       .val('');
                } else {
                    $('#sig_alternate').removeAttr('disabled')
                                       .addClass('required');
                }
            } else {
                $('input[name=has_alt_member][value="n"]').prop('checked', true);
            }
        });

        $('.sel-bidder-count').each(function() {
            $(this).unbind('change').change(function() {
                const bidderCount = $(this).val(),
                      groupKey = $(this).closest('div').find('.grp_key').val(),
                      groupNo = $(this).closest('div').find('.grp_no').val();
                      urlSegment = `${baseURL}/abstract/item-segment/${id}?bidder_count=${bidderCount}
                                    &group_key=${groupKey}&group_no=${groupNo}`;

                $(this).closest('tr').next('tr').find('div')
                                     .html('')
                                     .load(urlSegment, function() {
                    setMultiplyTwoInputs();
                    checkSelectUniqueness();
                });
            });
        });

        setMultiplyTwoInputs();
        checkSelectUniqueness();
    }

    $.fn.showCreate = function(url, id) {
        $('#mdb-preloader').css('background', '#000000ab').fadeIn(300);
        $('#modal-body-create').load(url, function() {
            $('#mdb-preloader').fadeOut(300);
            $('.crud-select').materialSelect();
            $(this).slideToggle(500);

            initInputs(id);
        });
        $("#modal-lg-create").modal({keyboard: false, backdrop: 'static'})
						     .on('shown.bs.modal', function() {
            $('#create-title').html('Create Abstract of Quotation Items');
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

    $.fn.showEdit = function(url, id) {
        $('#mdb-preloader').css('background', '#000000ab').fadeIn(300);
        $('#modal-body-edit').load(url, function() {
            $('#mdb-preloader').fadeOut(300);
            $('.crud-select').materialSelect();
            $(this).slideToggle(500);

            initInputs(id);
        });
        $("#modal-lg-edit").modal({keyboard: false, backdrop: 'static'})
						   .on('shown.bs.modal', function() {
            $('#edit-title').html('Update Abstract of Quotation Items');
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
            $('#delete-title').html('Delete Abstract of Quotation Items');
            $('#form-delete').attr('action', url);
		}).on('hidden.bs.modal', function() {
             $('#modal-delete-body').html('');
             $('#form-delete').attr('action', '#');
		});
    }

    $.fn.delete = function() {
        $('#form-delete').submit();
    }

    $.fn.showApprove = function(url, name) {
		$('#modal-body-approve').html(`Are you sure you want to approve '${name}' for PO/JO?`);
        $("#modal-approve").modal({keyboard: false, backdrop: 'static'})
						  .on('shown.bs.modal', function() {
            $('#approve-title').html('Approve Abstract of Quotation');
            $('#form-approve').attr('action', url);
		}).on('hidden.bs.modal', function() {
             $('#modal-approve-body').html('');
             $('#form-approve').attr('action', '#');
		});
    }

    $.fn.approve = function() {
        $('#form-approve').submit();
    }

    $('.material-tooltip-main').tooltip({
        template: template
    });
});
