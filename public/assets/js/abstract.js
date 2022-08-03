$(function () {
    const template =
        '<div class="tooltip md-tooltip">' +
        '<div class="tooltip-arrow md-arrow"></div>' +
        '<div class="tooltip-inner md-inner stylish-color"></div></div>';
    let selSupplierBidders = [];

    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    function storeAbstractItems(abstractID, toggle, formData) {
        const storeDataURL =
            toggle == "store"
                ? `${baseURL}/procurement/abstract/store-items/${abstractID}`
                : `${baseURL}/procurement/abstract/update-items/${abstractID}`;
        $.ajax({
            url: storeDataURL,
            type: "POST",
            processData: false,
            contentType: false,
            //async: false,
            data: formData,
            //dataType: 'json',
            success: function (response) {
                console.log(response);
            },
            fail: function (xhr, textStatus, errorThrown) {
                console.log("fail");
                storeAbstractItems(abstractID, toggle, formData);
            },
            error: function (data) {
                console.log("error");
                storeAbstractItems(abstractID, toggle, formData);
            },
        });
    }

    function processData() {
        const abstractID = $("#abstract_id").val(),
            toggle = $("#toggle").val(),
            dateAbstract = $("#date_abstract").val(),
            modeProcurement = $("#mode_procurement").val(),
            sigChairperson = $("#sig_chairperson").val(),
            sigViceChairperson = $("#sig_vice_chairperson").val(),
            sigFirstPerson = $("#sig_first_member").val(),
            sigSecondPerson = $("#sig_second_member").val(),
            sigThirdPerson = $("#sig_third_member").val(),
            sigEndUser = $("#sig_end_user").val();

        $("input[name=date_abstract]").val(dateAbstract);
        $("input[name=mode_procurement]").val(modeProcurement);
        $("input[name=sig_chairperson]").val(sigChairperson);
        $("input[name=sig_vice_chairperson]").val(sigViceChairperson);
        $("input[name=sig_first_member]").val(sigFirstPerson);
        $("input[name=sig_second_member]").val(sigSecondPerson);
        $("input[name=sig_third_member]").val(sigThirdPerson);
        $("input[name=sig_end_user]").val(sigEndUser);

        $("select.sel-bidder-count").each(function (grpKey, elemSelBidder) {
            const bidderCount = parseInt($(elemSelBidder).val());

            if (!empty(bidderCount) && bidderCount > 0) {
                let selectedSuppliers = [];
                const containerID = "#container_" + (grpKey + 1);

                $(containerID)
                    .find(".sel-supplier")
                    .each(function () {
                        const selectedSupplier = $(this).val();
                        selectedSuppliers.push({
                            selected_supplier: selectedSupplier,
                        });
                    });

                $(containerID)
                    .find("tbody.table-data")
                    .each(function (tblIndex, tableBody) {
                        $(tableBody)
                            .find("tr")
                            .each(function (rowCtr, elemRow) {
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

                                prItemID = $(elemRow).find(".item-id").val();
                                jsonData["select_suppliers"] =
                                    JSON.stringify(selectedSuppliers);
                                jsonData["bidder_count"] = bidderCount;
                                jsonData["pr_item_id"] = prItemID;

                                $(elemRow)
                                    .find(".abstract-item-id")
                                    .each(function () {
                                        const abstractitemID = $(this).val();
                                        abstractitemIDs.push({
                                            abs_item_id: abstractitemID,
                                        });
                                    });

                                jsonData["abstract_item_ids"] =
                                    JSON.stringify(abstractitemIDs);

                                $(elemRow)
                                    .find(".unit-cost")
                                    .each(function () {
                                        const unitCost = parseFloat(
                                            $(this).val()
                                        ).toFixed(2);
                                        unitCosts.push({ unit_cost: unitCost });
                                    });

                                jsonData["unit_costs"] =
                                    JSON.stringify(unitCosts);

                                $(elemRow)
                                    .find(".total-cost")
                                    .each(function () {
                                        const totalCost = parseFloat(
                                            $(this).val()
                                        ).toFixed(2);
                                        totalCosts.push({
                                            total_cost: totalCost,
                                        });
                                    });

                                jsonData["total_costs"] =
                                    JSON.stringify(totalCosts);

                                $(elemRow)
                                    .find(".specification")
                                    .each(function () {
                                        const specification = $(this).val();
                                        specifications.push({
                                            specification: specification,
                                        });
                                    });

                                jsonData["specifications"] =
                                    JSON.stringify(specifications);

                                $(elemRow)
                                    .find(".remarks")
                                    .each(function () {
                                        const remark = $(this).val();
                                        remarks.push({ remarks: remark });
                                    });

                                jsonData["remarks"] = JSON.stringify(remarks);

                                awardedTo = $(elemRow)
                                    .find(".awarded-to")
                                    .val();
                                documentType = $(elemRow)
                                    .find(".document-type")
                                    .val();
                                awardedRemark = $(elemRow)
                                    .find(".awarded-remarks")
                                    .val();

                                jsonData["awarded_to"] = awardedTo;
                                jsonData["document_type"] = documentType;
                                jsonData["awarded_remark"] = awardedRemark;

                                formData.append(
                                    "json_data",
                                    JSON.stringify(jsonData)
                                );

                                storeAbstractItems(
                                    abstractID,
                                    toggle,
                                    formData
                                );
                            });
                    });
            } else {
                let jsonData = {},
                    formData = new FormData();

                jsonData["bidder_count"] = 0;
                formData.append("json_data", JSON.stringify(jsonData));
                storeAbstractItems(abstractID, toggle, formData);
            }
        });
    }

    function multiplyInputs(element) {
        const unitCost = parseFloat(element.val()),
            quantity = parseInt(element.siblings(".quantity").val());
        let totalCost = unitCost * quantity;

        if (totalCost == null || totalCost == 0) {
            totalCost = 0.0;
        }

        element.closest("td").find(".total-cost").val(totalCost.toFixed(2));

        element
            .closest("td")
            .find(".total-cost")
            .next("label")
            .addClass("active");
    }

    function setMultiplyTwoInputs() {
        $(".unit-cost").each(function () {
            $(this)
                .unbind("keyup")
                .unbind("change")
                .keyup(function () {
                    multiplyInputs($(this));
                })
                .change(function () {
                    multiplyInputs($(this));
                });
        });
    }

    function checkSelectUniqueness() {
        selectedSupplier = [];

        $(".header-group").each(function (keyGroup, elemGroup) {
            const headerGroup = $(this);

            headerGroup
                .find(`.sel-supplier-${keyGroup}`)
                .each(function (index, elemSupplier) {
                    const bidID = `#sel-bidder-count-${keyGroup}-${index}`;
                    const selectedSupplier = $(bidID);
                    let oldValue = "";

                    selectedSupplier
                        .on("select2:opening", function () {
                            oldValue = selectedSupplier.val();
                        })
                        .on("select2:select", function () {
                            const supplierID = selectedSupplier.val();
                            let selectHtmlValues =
                                    '<option value="" disabled selected>Choose an awardee</option>' +
                                    '<option value="">-- No awardee --</option>',
                                hasDuplicate = false;

                            headerGroup
                                .find(".sel-supplier")
                                .each(function (index2) {
                                    const _supplierID = $(this).val(),
                                        optSelected = $(this)
                                            .find("option:selected")
                                            .text();

                                    if (index != index2) {
                                        if (_supplierID == supplierID) {
                                            selectedSupplier
                                                .val(oldValue)
                                                .trigger("change");
                                            hasDuplicate = true;
                                            alert(
                                                "The selected suppliers must be unique."
                                            );
                                        }
                                    }

                                    selectHtmlValues += `<option value="${_supplierID}">${optSelected}</option>`;
                                });

                            if (!hasDuplicate) {
                                headerGroup
                                    .closest(".table-segment-group")
                                    .find(".awarded-to")
                                    .each(function () {
                                        $(this).html(selectHtmlValues);
                                    });
                            }
                        });
                });
        });
    }

    function initInputs(id) {
        let dropdownParent = "#modal-lg-create";

        if ($("#modal-lg-create").hasClass("show")) {
            dropdownParent = "#modal-lg-create";
        } else {
            dropdownParent = "#modal-lg-edit";
        }

        $(".sel-bidder-count").each(function (bidCount, bidCountElem) {
            $(this).change(function () {
                const bidderCount = $(this).val(),
                    groupKey = $(this)
                        .closest(".grp-group")
                        .find(".grp_key")
                        .val(),
                    groupNo = $(this)
                        .closest(".grp-group")
                        .find(".grp_no")
                        .val();
                urlSegment =
                    `${baseURL}/procurement/abstract/item-segment/${id}` +
                    `?bidder_count=${bidderCount}&group_key=${groupKey}&group_no=${groupNo}`;

                if (!empty(bidderCount)) {
                    $("#mdb-preloader")
                        .css("background", "#000000ab")
                        .fadeIn(300);
                    $(this)
                        .closest("tr")
                        .next("tr")
                        .find("div")
                        .html(
                            '<div class="col p-4"><i class="fas fa-spinner fa-spin"></i> Loading...</div>'
                        );
                    let loadSegment = $(this)
                        .closest("tr")
                        .next("tr")
                        .find("div")
                        .load(urlSegment, function () {
                            $("#mdb-preloader").fadeOut(300);
                            //$('.sel-supplier').materialSelect();
                            //$('.awarded-to').materialSelect();
                            //$('.document-type').materialSelect();
                            setMultiplyTwoInputs();
                            checkSelectUniqueness();

                            $(".sel-supplier").each(function (bidCtr, bid) {
                                const bidID = `sel-bidder-count-${groupKey}-${bidCtr}`;

                                //$(bidID).materialSelect('destroy');
                                //$(bidID).materialSelect();
                                $(`#${bidID}`).select2({
                                    dropdownParent: $(dropdownParent),
                                });
                                //console.log(bidID);
                                $(`#select2-${bidID}-container`).addClass(
                                    "input-error-highlighter"
                                );
                                //$(bidID).siblings('.select-dropdown').addClass("input-error-highlighter");
                            });
                        });

                    loadSegment.onreadystatechange = null;
                    loadSegment.abort = null;
                    loadSegment = null;
                }
            });
        });

        setMultiplyTwoInputs();
        checkSelectUniqueness();
    }

    $.fn.setSupplierHeaderName = function (selElem, elemClass, text) {
        selElem
            .parent()
            .parent()
            .parent()
            .parent()
            .next()
            .find(elemClass)
            .html(text);
    };

    $.fn.showCreate = function (url, id) {
        $("#mdb-preloader").css("background", "#000000ab").fadeIn(300);
        $("#modal-body-create").load(url, function () {
            $("#mdb-preloader").fadeOut(300);
            $(".crud-select").materialSelect();
            $(this).slideToggle(500);

            initInputs(id);
            $(".sel-supplier").select2({
                dropdownParent: $("#modal-lg-create"),
            });
        });
        $("#modal-lg-create")
            .modal({ keyboard: false, backdrop: "static" })
            .on("shown.bs.modal", function () {
                $("#create-title").html("Create Abstract of Quotation Items");
            })
            .on("hidden.bs.modal", function () {
                $("#modal-body-create").html("").css("display", "none");
            });
    };

    $.fn.store = function () {
        const withError = inputValidation(false);

        if (!withError) {
            $("#mdb-preloader")
                .css("background", "#000000ab")
                .fadeIn(300, function () {
                    processData();
                    $(document).ajaxStop(function () {
                        $("#form-store").submit();
                    });
                });
        }
    };

    $.fn.showEdit = function (url, id) {
        $("#mdb-preloader").css("background", "#000000ab").fadeIn(300);
        $("#modal-body-edit").load(url, function () {
            $("#mdb-preloader").fadeOut(300);
            $(".crud-select").materialSelect();
            $(this).slideToggle(500);

            initInputs(id);
            $(".sel-supplier").select2({
                dropdownParent: $("#modal-lg-edit"),
            });
        });
        $("#modal-lg-edit")
            .modal({ keyboard: false, backdrop: "static" })
            .on("shown.bs.modal", function () {
                $("#edit-title").html("Update Abstract of Quotation Items");
            })
            .on("hidden.bs.modal", function () {
                $("#modal-body-edit").html("").css("display", "none");
            });
    };

    $.fn.update = function () {
        const withError = inputValidation(false);

        if (!withError) {
            $("#mdb-preloader")
                .css("background", "#000000ab")
                .fadeIn(300, function () {
                    processData();
                    $(document).ajaxStop(function () {
                        $("#form-update").submit();
                    });
                });
        }
    };

    $.fn.showDelete = function (url, name) {
        $("#modal-body-delete").html(
            `Are you sure you want to delete '${name}'?`
        );
        $("#modal-delete")
            .modal({ keyboard: false, backdrop: "static" })
            .on("shown.bs.modal", function () {
                $("#delete-title").html("Delete Abstract of Quotation Items");
                $("#form-delete").attr("action", url);
            })
            .on("hidden.bs.modal", function () {
                $("#modal-delete-body").html("");
                $("#form-delete").attr("action", "#");
            });
    };

    $.fn.delete = function () {
        $("#form-delete").submit();
    };

    $.fn.showApprove = function (url, name) {
        $("#modal-body-approve").html(
            `Are you sure you want to approve '${name}' for PO/JO?`
        );
        $("#modal-approve")
            .modal({ keyboard: false, backdrop: "static" })
            .on("shown.bs.modal", function () {
                $("#approve-title").html("Approve Abstract of Quotation");
                $("#form-approve").attr("action", url);
            })
            .on("hidden.bs.modal", function () {
                $("#modal-approve-body").html("");
                $("#form-approve").attr("action", "#");
            });
    };

    $.fn.approve = function () {
        $("#form-approve").submit();
    };

    $(".material-tooltip-main").tooltip({
        template: template,
    });
});
