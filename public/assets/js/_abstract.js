$(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

	function multiplyInputs(element) {
		unitCost = parseFloat(element.val());
				   		quantity = parseInt(element.prev('input').val());
				   		totalCost = unitCost * quantity;

				   		if (totalCost == null || totalCost == 0) {
				   			totalCost = 0.00;
				   		}

				   		element.closest('td')
				   			   .find('.total-cost')
				   			   .val(totalCost.toFixed(2));
	}

	function setMultiplyTwoInputs() {
		$('.unit-cost').each(function() {
			var unitCost = 0;
			var totalCost = 0;
			var quantity = 0;

			$(this).unbind('keyup').unbind('change')
				   .keyup(function() {
				   		multiplyInputs($(this));
				   })
				   .change(function() {
				   		multiplyInputs($(this));
				   });
		});
	}

	function getSelectedSupplier(element) {
		var supplierListID = [];
    }

	function checkSelectUniqueness() {
		$('.header-group').each(function(keyGroup) {
			var headerGroup = $(this);

			headerGroup.find('.sel-supplier').each(function(index) {
				var selectedSupplier = $(this);
				var oldValue = 0;

				selectedSupplier.unbind('click').click(function() {
				    oldValue = selectedSupplier.val();
				}).unbind('change').change(function() {
					var supplierID = selectedSupplier.val();
					var hasDuplicate = false;
					var selectHtmlValues = '<option value="">-- No awardee --</option>';

					headerGroup.find('.sel-supplier').each(function(index2) {
						var _supplierID = $(this).val();

						if (index != index2) {
							if (_supplierID == supplierID) {
								selectedSupplier.val(oldValue);
								hasDuplicate = true;
								alert('The selected suppliers must be unique.');
							}
						}

						selectHtmlValues += '<option value="' + _supplierID + '">' +
												$(this).find('option:selected').text() +
											'</option>';
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

    function storeAbstractItems(prID, formData) {
        var storeDataURL = baseURL +
            '/procurement/abstract/store-update-items/' + prID;

        $.ajax({
		    url: storeDataURL,
            type: 'POST',
            processData: false,
            contentType: false,
            async: false,
            data: formData,
            //dataType: 'json',
		    success: function(response) {
                //console.log(response);
                console.log('success');
            },
            fail: function(xhr, textStatus, errorThrown) {
                storeAbstractItems(prID, formData);
                console.log('fail');
		    },
		    error: function(data) {
                console.log('error');
                storeAbstractItems(prID, formData);
		    }
        });
    }

    function processData() {
        var prID = $('#pr_id').val();
        var prNo = $('#pr_no').val();
        var toggle = $('#toggle').val();

        var dateAbstract = $('#date_abstract').val();
        var modeProcurement = $('#mode_procurement').val();
        var sigChairperson = $('#sig_chairperson').val();
        var sigViceChairperson = $('#sig_vice_chairperson').val();
        var sigFirstPerson = $('#sig_first_member').val();
        var sigSecondPerson = $('#sig_second_member').val();
        var sigThirdPerson = $('#sig_third_member').val();
        var sigEndUser = $('#sig_end_user').val();

        $("input[name=date_abstract]").val(dateAbstract);
        $("input[name=mode_procurement]").val(modeProcurement);
        $("input[name=sig_chairperson]").val(sigChairperson);
        $("input[name=sig_vice_chairperson]").val(sigViceChairperson);
        $("input[name=sig_first_member]").val(sigFirstPerson);
        $("input[name=sig_second_member]").val(sigSecondPerson);
        $("input[name=sig_third_member]").val(sigThirdPerson);
        $("input[name=sig_end_user]").val(sigEndUser);

        $('.sel-bidder-count').each(function(grpKey, elemSelBidder) {
            var bidderCount = parseInt($(elemSelBidder).val());

            if (bidderCount > 0) {
                var formData = new FormData();
                var containerID = '#container_' + (grpKey + 1);
                var listSelSupplier = [];

                formData.append('pr_id', prID);
                formData.append('pr_no', prNo);
                formData.append('toggle', toggle);
                formData.append('bidder_count', bidderCount);

                $(containerID).find('tr').each(function(rowCtr, elemRow) {
                    var listAbstractID = [];
                    var prItemID = "";
                    var listUnitCost = [];
                    var listTotalCost = [];
                    var listSpecification = [];
                    var listRemarks = [];
                    var awardedTo = 0;
                    var documentType = "";
                    var awardedRemarks = "";

                    if (rowCtr == 0) {
                        $(elemRow).find('.sel-supplier').each(function() {
                            var selectedSupplier = parseInt($(this).val());
                            listSelSupplier.push(selectedSupplier);
                        });
                    } else {
                        prItemID = $(elemRow).find('.item-id').val();
                        $(elemRow).find('.abstract-id').each(function() {
                            var abstractID = $(this).val();
                            listAbstractID.push(abstractID);
                        });
                        $(elemRow).find('.unit-cost').each(function() {
                            var unitCost = parseFloat($(this).val());
                            listUnitCost.push(unitCost);
                        });
                        $(elemRow).find('.total-cost').each(function() {
                            var totalCost = parseFloat($(this).val());
                            listTotalCost.push(totalCost);
                        });
                        $(elemRow).find('.specification').each(function() {
                            var specification = $(this).val();
                            listSpecification.push(specification);
                        });
                        $(elemRow).find('.remarks').each(function() {
                            var remarks = $(this).val();
                            listRemarks.push(remarks);
                        });
                        awardedTo = parseInt($(elemRow).find('.awarded-to').val());
                        documentType = $(elemRow).find('.document-type').val();
                        awardedRemarks = $(elemRow).find('.awarded-remarks').val();

                        formData.append('list_selected_supplier', listSelSupplier);
                        formData.append('list_abstract_id', listAbstractID);
                        formData.append('pr_item_id', prItemID);
                        formData.append('list_unit_cost', listUnitCost);
                        formData.append('list_total_cost', listTotalCost);
                        formData.append('list_specification', listSpecification);
                        formData.append('list_remarks', listRemarks);
                        formData.append('awarded_to', awardedTo);
                        formData.append('document_type', documentType);
                        formData.append('awarded_remarks', awardedRemarks);

                        /*// For debugging
                        console.log('prID = ' + prID,
                                    'prNo = ' + prNo,
                                    'toggle = ' + toggle,
                                    'bidderCount = ' + bidderCount,
                                    'listAbstractID = ' + listAbstractID,
                                    'prItemID = ' + prItemID,
                                    'listUnitCost = ' + listUnitCost,
                                    'listTotalCost = ' + listTotalCost,
                                    'listSpecification = ' + listSpecification,
                                    'listRemarks = ' + listRemarks,
                                    'awardedTo = ' + awardedTo,
                                    'documentType = ' + documentType,
                                    'awardedRemarks = ' + awardedRemarks
                            );*/

                        storeAbstractItems(prID, formData);
                    }
                });
            }

        });
    }

	$.fn.showCreate = function(id, prNo, toggle) {
		var elementBodyID = "#modal-body-create";
		var elementModalID = "#central-create-modal";

		if (toggle == "create") {
			elementModalID = "#central-create-modal";
			elementBodyID = "#modal-body-create";
		} else {
			elementModalID = "#central-edit-modal";
			elementBodyID = "#modal-body-edit";
		}

		$.ajaxSetup({
            async: true
        });

        $('#mdb-preloader').css('background', '#000000ab').fadeIn(300);
		$(elementBodyID).load('abstract/show-create/' + id +
							  '?pr_no=' + prNo + '&toggle=' + toggle,
										function() {
            $('#mdb-preloader').fadeOut(300);
			$('#form-update').attr('action', 'abstract/store-update/' + id);

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
					var bidderCount = $(this).val();
					var groupKey = $(this).closest('div').find('.grp_key').val();
					var groupNo = $(this).closest('div').find('.grp_no').val();

					$(this).closest('tr').next('tr').find('div')
										 .html('')
										 .html(modalLoadingContent)
										 .load('abstract/segment/' + id + '?bidder_count=' + bidderCount +
										 	   '&group_key=' + groupKey + '&group_no=' + groupNo,
					function() {
						setMultiplyTwoInputs();
						checkSelectUniqueness();
					});
				});
			});

			setMultiplyTwoInputs();
			checkSelectUniqueness();
		});

		if (toggle == "create") {
			$(elementModalID).modal()
							.on('shown.bs.modal', function() {

					   		}).on('hidden.bs.modal', function() {
						        $(elementBodyID).html(modalLoadingContent);
						    });
		} else {
			$(elementModalID).modal()
							.on('shown.bs.modal', function() {

					   		}).on('hidden.bs.modal', function() {
						        $(elementBodyID).html(modalLoadingContent);
						    });
		}

	}

	/*
	$.fn.viewItems = function(id) {
		$('#modal-body-content-2').load('abstract/show/' + id);
		$("#view-modal").modal({keyboard: false, backdrop: 'static'})
						.on('shown.bs.modal', function() {

				   		}).on('hidden.bs.modal', function() {
					        $('#modal-body-content-2').html(modalLoadingContent);
					    });
	}
	*/

	$.fn.createUpdateDoc = function() {
		var withError = inputValidation(false);

		if (!withError) {
            $('#mdb-preloader').css('background', '#000000ab')
                               .fadeIn(300, function() {
                processData();
                $('#form-update-2').submit();
            });

            /*
            $(document).ajaxStop(function() {
                $('#form-update-2').submit();
            });*/
		}
	}

	$.fn.receive = function(poNo) {
		if (confirm('receive RFQ document?')) {
			$('#form-validation').attr('action', 'abstract/receive/' + poNo).submit();
		}
	}

	$.fn.approve = function(poNo) {
		if (confirm('Approve the abstract for PO/JO?')) {
			$('#form-validation').attr('action', 'abstract/approve/' + poNo).submit();
		}
	}

	$.fn.delete = function(poNo) {
		if (confirm('Are you sure you want to delete the abstract items?')) {
			$('#form-validation').attr('action', 'abstract/delete/' + poNo).submit();
		}
	}

});
