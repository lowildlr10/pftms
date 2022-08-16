const CSRF_TOKEN = $('meta[name="csrf-token"]').attr("content");

$(function () {
    let orsSerialNos = [],
        msdGSBs = [];

    const template =
        '<div class="tooltip md-tooltip">' +
        '<div class="tooltip-arrow md-arrow"></div>' +
        '<div class="tooltip-inner md-inner stylish-color"></div></div>';

    function setOrsBursDetails(mainElem) {
        let orsData = [];

        mainElem.find("option:selected").each(() => {
            orsData = mainElem.val();
        });

        let formData = new FormData();
        const url = `${baseURL}/payment/lddap/get-ors-burs-details`;

        formData.append("ors_burs_ids", orsData);

        $.ajax({
            url: url,
            type: "POST",
            processData: false,
            contentType: false,
            data: formData,
            success: function (response) {
                const payees = response.payees;
                const uacs = response.uacs;
                const totalAmount = response.total_amount;

                mainElem
                    .closest("tr")
                    .children()
                    .each(function (i, elem) {
                        $(elem).find(".current-creditor-name").text(payees);
                        $(elem).find(".prior-creditor-name").text(payees);
                        $(elem).find(".current-gross-amount").val(totalAmount);
                        $(elem).find(".prior-gross-amount").val(totalAmount);

                        $(elem)
                            .find(".allot-class-tokenizer")
                            .val(null)
                            .trigger("change");

                        $.each(uacs, function (i, mooe) {
                            const allotClassElem = $(elem).find(
                                ".allot-class-tokenizer"
                            );
                            const mooeId = mooe.id;
                            const mooeTitle = mooe.title;

                            if (
                                allotClassElem.find(
                                    "option[value='" + mooeId + "']"
                                ).length
                            ) {
                                allotClassElem.val(mooeId).trigger("change");
                            } else {
                                // Create a DOM Option and pre-select by default
                                const newOption = new Option(
                                    mooeTitle,
                                    mooeId,
                                    true,
                                    true
                                );
                                // Append it to the select
                                allotClassElem
                                    .append(newOption)
                                    .trigger("change");
                            }
                        });
                    });

                $(this).computeGrossTotal("current");
                $(this).computeGrossTotal("prior");
                computeGrandTotal();
            },
            fail: function (xhr, textStatus, errorThrown) {
                alert("Try selecting ORS/BURS again.");
            },
            error: function (data) {
                alert("Try selecting ORS/BURS again.");
            },
        });
    }

    function initializeSelect2() {
        $(".ors-tokenizer")
            .select2({
                tokenSeparators: [","],
                placeholder: "Value...",
                width: "100%",
                maximumSelectionSize: 4,
                allowClear: true,
                ajax: {
                    url: `${baseURL}/payment/lddap/get-ors-burs`,
                    type: "post",
                    dataType: "json",
                    delay: 250,
                    data: function (params) {
                        return {
                            _token: CSRF_TOKEN,
                            search: params.term,
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: $.map(data, function (item) {
                                return {
                                    text: item.serial_no,
                                    id: item.id,
                                };
                            }),
                            pagination: {
                                more: true,
                            },
                        };
                    },
                    cache: true,
                },
                //theme: "material"
            })
            .on("select2:select", function (e) {
                setOrsBursDetails($(this));
            })
            .on("select2:unselect", function (e) {
                setOrsBursDetails($(this));
            })
            .on("select2:clear", function (e) {
                setOrsBursDetails($(this));
            });

        $(".allot-class-tokenizer").select2({
            tokenSeparators: [","],
            placeholder: "Value...",
            width: "100%",
            maximumSelectionSize: 4,
            allowClear: true,
            ajax: {
                url: `${baseURL}/payment/lddap/get-mooe-title`,
                type: "post",
                dataType: "json",
                delay: 250,
                data: function (params) {
                    return {
                        _token: CSRF_TOKEN,
                        search: params.term,
                    };
                },
                processResults: function (data) {
                    return {
                        results: $.map(data, function (item) {
                            return {
                                text: `${item.uacs_code} : ${item.account_title}`,
                                id: item.id,
                            };
                        }),
                        pagination: {
                            more: true,
                        },
                    };
                },
                cache: true,
            },
            //theme: "material"
        });

        $(".mds-gsb-tokenizer").select2({
            tokenSeparators: [","],
            placeholder:
                "For adding a new data, use '/' to separate MDS-GSB BRANCH and MDS SUB ACCOUNT NO.",
            width: "100%",
            tags: true,
            maximumSelectionSize: 4,
            allowClear: true,
            dropdownParent: $(".mds-gsb-tokenizer").parent(),
            ajax: {
                url: `${baseURL}/payment/lddap/get-mds-gsb`,
                type: "post",
                dataType: "json",
                delay: 250,
                data: function (params) {
                    return {
                        _token: CSRF_TOKEN,
                        search: params.term,
                    };
                },
                processResults: function (data) {
                    return {
                        results: $.map(data, function (item) {
                            return {
                                text: `${item.branch} / ${item.sub_account_no}`,
                                id: item.id,
                            };
                        }),
                        pagination: {
                            more: true,
                        },
                    };
                },
                cache: true,
            },
            //theme: "material"
        });
    }

    function filterNaN(inputVal) {
        let outputVal = isNaN(inputVal) ? 0 : inputVal;

        return outputVal;
    }

    function computeGrandTotal() {
        let currentTotalGross = parseFloat($("#current-total-gross").val());
        let currentTotalWithholding = parseFloat(
            $("#current-total-withholdingtax").val()
        );
        let currentTotalNet = parseFloat($("#current-total-netamount").val());
        let priorTotalGross = parseFloat($("#prior-total-gross").val());
        let priorTotalWithholding = parseFloat(
            $("#prior-total-withholdingtax").val()
        );
        let priorTotalNet = parseFloat($("#prior-total-netamount").val());

        let grandTotalGross = currentTotalGross + priorTotalGross;
        let grandTotalWithholding =
            currentTotalWithholding + priorTotalWithholding;
        let grandTotalNet = currentTotalNet + priorTotalNet;

        $("#total-gross-amount").val(parseFloat(grandTotalGross, 2).toFixed(2));
        $("#total-withholding-tax").val(
            parseFloat(grandTotalWithholding, 2).toFixed(2)
        );
        $("#total-net-amount").val(parseFloat(grandTotalNet, 2).toFixed(2));

        $("#total-amount").val(parseFloat(grandTotalNet, 2).toFixed(2));
        $("#total-amount-words").val(
            toWordsconvert(parseFloat(grandTotalNet).toFixed(2))
        );
        $("#total-amount-words").siblings("label").addClass("active");
    }

    $.fn.computeGrossTotal = function (type) {
        let totalGrossAmount = 0;
        let totalNetAmount = 0;
        let classGross, classWithholding, classNet, idTotalGross, idtotalNet;

        if (type == "current") {
            classGross = ".current-gross-amount";
            classWithholding = ".current-withold-tax";
            classNet = ".current-net-amount";
            idTotalGross = "#current-total-gross";
            idtotalNet = "#current-total-netamount";
        } else if (type == "prior") {
            classGross = ".prior-gross-amount";
            classWithholding = ".prior-withold-tax";
            classNet = ".prior-net-amount";
            idTotalGross = "#prior-total-gross";
            idtotalNet = "#prior-total-netamount";
        }

        $(classGross).each(function () {
            let grossAmount = filterNaN(parseFloat($(this).val()));
            let withholdingTax = filterNaN(
                parseFloat(
                    $(this)
                        .parent()
                        .parent()
                        .next()
                        .find(classWithholding)
                        .val()
                )
            );
            let netAmount = filterNaN(grossAmount - withholdingTax);

            $(this)
                .parent()
                .parent()
                .next()
                .next()
                .find(classNet)
                .val(parseFloat(netAmount, 2));

            totalGrossAmount += grossAmount;
            totalNetAmount += netAmount;
        });

        $(idTotalGross).val(parseFloat(totalGrossAmount, 2));
        $(idtotalNet).val(parseFloat(totalNetAmount, 2));

        computeGrandTotal();
    };

    $.fn.computeWithholdingTaxTotal = function (type) {
        let totalWithholdingTax = 0,
            totalNetAmount = 0;
        let classGross,
            classWithholding,
            classNet,
            idTotalWitholding,
            idtotalNet;

        if (type == "current") {
            classGross = ".current-gross-amount";
            classWithholding = ".current-withold-tax";
            classNet = ".current-net-amount";
            idTotalWitholding = "#current-total-withholdingtax";
            idtotalNet = "#current-total-netamount";
        } else if (type == "prior") {
            classGross = ".prior-gross-amount";
            classWithholding = ".prior-withold-tax";
            classNet = ".prior-net-amount";
            idTotalWitholding = "#prior-total-withholdingtax";
            idtotalNet = "#prior-total-netamount";
        }

        $(classWithholding).each(function () {
            let withholdingTax = filterNaN(parseFloat($(this).val()));
            let grossAmount = filterNaN(
                parseFloat(
                    $(this).parent().parent().prev().find(classGross).val()
                )
            );
            let netAmount = filterNaN(parseFloat(grossAmount - withholdingTax));

            $(this)
                .parent()
                .parent()
                .next()
                .find(classNet)
                .val(parseFloat(netAmount, 2));

            totalWithholdingTax += withholdingTax;
            totalNetAmount += netAmount;
        });

        $(idTotalWitholding).val(parseFloat(totalWithholdingTax, 2));
        $(idtotalNet).val(parseFloat(totalNetAmount, 2));

        computeGrandTotal();
    };

    $.fn.computeNetAmountTotal = function (type) {
        let totalNetAmount = 0;
        let classGross,
            classWithholding,
            classNet,
            idTotalGross,
            idTotalWitholding,
            idtotalNet;

        if (type == "current") {
            classGross = ".current-gross-amount";
            classWithholding = ".current-withold-tax";
            classNet = ".current-net-amount";
            idTotalGross = "#current-total-gross";
            idTotalWitholding = "#current-total-withholdingtax";
            idtotalNet = "#current-total-netamount";
        } else if (type == "prior") {
            classGross = ".prior-gross-amount";
            classWithholding = ".prior-withold-tax";
            classNet = ".prior-net-amount";
            idTotalGross = "#prior-total-gross";
            idTotalWitholding = "#prior-total-withholdingtax";
            idtotalNet = "#prior-total-netamount";
        }

        $(classNet).each(function () {
            let netAmount = filterNaN(parseFloat($(this).val()));

            totalNetAmount += netAmount;
        });

        $(idtotalNet).val(parseFloat(totalNetAmount, 2));

        computeGrandTotal();
    };

    $.fn.addRow = function (rowClass, type) {
        let lastRow = $(rowClass).last();
        let lastRowID =
            lastRow.length > 0 ? lastRow.attr("id") : type + "-row-0";
        let _lastRowID = lastRowID.split("-");
        let newID = parseInt(_lastRowID[2]) + 1;

        let creditorName = `<td><div class="md-form form-sm my-0">
                            <textarea name="${_lastRowID[0]}_creditor_name[]" placeholder=" Value..."
                            class="${_lastRowID[0]}-creditor-name md-textarea required form-control-sm w-100 py-1"></textarea>
                            </div></td>`;
        let creditorAccntNo = `<td><div class="md-form form-sm my-0">
                               <textarea name="${_lastRowID[0]}_creditor_acc_no[]" placeholder=" Value..."
                               class="md-textarea required form-control-sm w-100 py-1"></textarea>
                               </div></td>`;
        let orsNo = `<td><div class="md-form my-0">
                    <select class="mdb-select required ors-tokenizer" multiple="multiple"
                    name="${_lastRowID[0]}_ors_no[${
            newID - 1
        }][]"></select></div></td>`;
        let allotClassUacs = `<td><div class="md-form my-0">
                            <select class="mdb-select required allot-class-tokenizer" multiple="multiple"
                            name="${_lastRowID[0]}_allot_class_uacs[${
            newID - 1
        }][]"></select></div></td>`;
        let grossAmmount =
            '<td><div class="md-form form-sm my-0">' +
            '<input type="number" class="form-control required form-control-sm ' +
            _lastRowID[0] +
            "-gross-amount" +
            '" ' +
            'placeholder=" Value..." name="' +
            _lastRowID[0] +
            '_gross_amount[]" ' +
            `id="${_lastRowID[0]}-gross-amount-${newID - 1}" ` +
            'onkeyup="$(this).computeGrossTotal(' +
            "'" +
            _lastRowID[0] +
            "'" +
            ')" ' +
            'onchange="$(this).computeGrossTotal(' +
            "'" +
            _lastRowID[0] +
            "'" +
            ')" ' +
            'onclick="$(this).showCalc(' +
            `'#${_lastRowID[0]}-gross-amount-${newID - 1}', '${
                _lastRowID[0]
            }'` +
            ')">' +
            "</div></td>";
        let withholdingTax =
            '<td><div class="md-form form-sm my-0">' +
            '<input type="number" class="form-control required form-control-sm ' +
            _lastRowID[0] +
            "-withold-tax" +
            '" ' +
            'placeholder=" Value..." name="' +
            _lastRowID[0] +
            '_withold_tax[]" ' +
            'onkeyup="$(this).computeWithholdingTaxTotal(' +
            "'" +
            _lastRowID[0] +
            "'" +
            ')" ' +
            'onchange="$(this).computeWithholdingTaxTotal(' +
            "'" +
            _lastRowID[0] +
            "'" +
            ')">' +
            "</div></td>";
        let netAmount =
            '<td><div class="md-form form-sm my-0">' +
            '<input type="number" class="form-control required form-control-sm ' +
            _lastRowID[0] +
            "-net-amount" +
            '" ' +
            'placeholder=" Value..." name="' +
            _lastRowID[0] +
            '_net_amount[]" ' +
            'onkeyup="$(this).computeNetAmountTotal(' +
            "'" +
            _lastRowID[0] +
            "'" +
            ')" ' +
            'onchange="$(this).computeNetAmountTotal(' +
            "'" +
            _lastRowID[0] +
            "'" +
            ')">' +
            "</div></td>";
        let remarks = `<td><div class="md-form form-sm my-0">
                       <textarea name="${_lastRowID[0]}_remarks[]" placeholder=" Value..."
                       class="md-textarea form-control-sm w-100 py-1"></textarea>
                       </div></td>`;
        let deleteButton =
            '<td><a onclick="' +
            "$(this).deleteRow('#" +
            _lastRowID[0] +
            "-row-" +
            newID +
            "');" +
            '"' +
            'class="btn btn-outline-red px-1 py-0">' +
            '<i class="fas fa-minus-circle"></i></a></td>';

        let rowOutput =
            '<tr id="' +
            _lastRowID[0] +
            "-row-" +
            newID +
            '" class="' +
            _lastRowID[0] +
            '-row">' +
            creditorName +
            creditorAccntNo +
            orsNo +
            allotClassUacs +
            grossAmmount +
            withholdingTax +
            netAmount +
            remarks +
            deleteButton +
            "</tr>";

        $(rowOutput).insertAfter("#" + lastRowID);
        initializeSelect2();
    };

    $.fn.deleteRow = function (row) {
        if (confirm("Are you sure you want to delete this row?")) {
            let _row = row.split("-");
            let type = _row[0].replace("#", "");
            let rowClass = "." + type + "-" + _row[1];
            let rowCount = $(rowClass).length;

            if (type == "prior") {
                $(row).fadeOut(300, function () {
                    let grossAmount = parseFloat(
                        $(this).find(".prior-gross-amount").val()
                    );
                    let withholding = parseFloat(
                        $(this).find(".prior-withold-tax").val()
                    );
                    let netAmmount = parseFloat(
                        $(this).find(".prior-net-amount").val()
                    );

                    let totalGross = parseFloat($("#prior-total-gross").val());
                    let totalWithholding = parseFloat(
                        $("#prior-total-withholdingtax").val()
                    );
                    let totalNet = parseFloat(
                        $("#prior-total-netamount").val()
                    );

                    totalGross = parseFloat(totalGross - grossAmount, 2);
                    totalWithholding = parseFloat(
                        totalWithholding - withholding,
                        2
                    );
                    totalNet = parseFloat(totalNet - netAmmount, 2);
                    $("#prior-total-gross").val(totalGross);
                    $("#prior-total-withholdingtax").val(totalWithholding);
                    $("#prior-total-netamount").val(totalNet);

                    computeGrandTotal();

                    $(this).remove();
                });
            } else {
                if (rowCount > 1) {
                    $(row).fadeOut(300, function () {
                        let grossAmount = parseFloat(
                            $(this).find(".current-gross-amount").val()
                        );
                        let withholding = parseFloat(
                            $(this).find(".current-withold-tax").val()
                        );
                        let netAmmount = parseFloat(
                            $(this).find(".current-net-amount").val()
                        );

                        let totalGross = parseFloat(
                            $("#current-total-gross").val()
                        );
                        let totalWithholding = parseFloat(
                            $("#current-total-withholdingtax").val()
                        );
                        let totalNet = parseFloat(
                            $("#current-total-netamount").val()
                        );

                        totalGross = parseFloat(totalGross - grossAmount, 2);
                        totalWithholding = parseFloat(
                            totalWithholding - withholding,
                            2
                        );
                        totalNet = parseFloat(totalNet - netAmmount, 2);

                        $("#current-total-gross").val(totalGross);
                        $("#current-total-withholdingtax").val(
                            totalWithholding
                        );
                        $("#current-total-netamount").val(totalNet);

                        computeGrandTotal();

                        $(this).remove();
                    });
                } else {
                    alert("Cannot delete all row.");
                }
            }
        }
    };

    $.fn.showCalc = function (inputID, type) {
        $("#modal-calculator")
            .modal({ keyboard: false, backdrop: "static" })
            .on("shown.bs.modal", function () {
                let result = 0.0;

                $("#input-calc")
                    .unbind("keyup")
                    .keyup(function () {
                        const inputText = $(this).val().split("+");

                        $.each(inputText, function (ctr, value) {
                            result += parseFloat(value);
                        });

                        $(inputID).val(result.toFixed(2));
                        result = 0;
                    })
                    .focus();
            })
            .on("hidden.bs.modal", function () {
                $("#input-calc").val("");
                $(this).computeGrossTotal(type);
            });
    };

    $.fn.createUpdateDoc = function () {
        let withError = inputValidation(false);

        if (!withError) {
            $("#form-create").submit();
            $("#form-edit").submit();
        }
    };

    $.fn.showCreate = function (url) {
        $("#mdb-preloader").css("background", "#000000ab").fadeIn(300);
        $("#modal-body-create").load(url, function () {
            $("#mdb-preloader").fadeOut(300);
            $(".crud-select").materialSelect();
            $(this).slideToggle(500);
            initializeSelect2();

            $("#sig-cert-correct option:eq(2)").attr("selected", "selected");

            $("#sig-approval-1 option:eq(4)").attr("selected", "selected");
            $("#sig-approval-2 option:eq(2)").attr("selected", "selected");
            $("#sig-approval-3 option:eq(3)").attr("selected", "selected");

            $("#sig-agency-auth-1 option:eq(5)").attr("selected", "selected");
            $("#sig-agency-auth-2 option:eq(4)").attr("selected", "selected");
            $("#sig-agency-auth-3 option:eq(2)").attr("selected", "selected");
            $("#sig-agency-auth-4 option:eq(3)").attr("selected", "selected");
        });
        $("#modal-lg-create")
            .modal({ keyboard: false, backdrop: "static" })
            .on("shown.bs.modal", function () {
                $("#create-title").html("Create LDDAP");
            })
            .on("hidden.bs.modal", function () {
                $("#modal-body-create").html("").css("display", "none");
            });
    };

    $.fn.store = function () {
        const withError = inputValidation(false);

        if (!withError) {
            $("#form-store").submit();
        }
    };

    $.fn.showEdit = function (url) {
        $("#mdb-preloader").css("background", "#000000ab").fadeIn(300);
        $("#modal-body-edit").load(url, function () {
            $("#mdb-preloader").fadeOut(300);
            $(".crud-select").materialSelect();
            $(this).slideToggle(500);
            initializeSelect2();
        });
        $("#modal-lg-edit")
            .modal({ keyboard: false, backdrop: "static" })
            .on("shown.bs.modal", function () {
                $("#edit-title").html("Update LDDAP");
            })
            .on("hidden.bs.modal", function () {
                $("#modal-body-edit").html("").css("display", "none");
            });
    };

    $.fn.update = function () {
        const withError = inputValidation(false);

        if (!withError) {
            $("#form-update").submit();
        }
    };

    $.fn.showDelete = function (url, name) {
        $("#modal-body-delete").html(
            `Are you sure you want to delete this ${name} ` + `document?`
        );
        $("#modal-delete")
            .modal({ keyboard: false, backdrop: "static" })
            .on("shown.bs.modal", function () {
                $("#delete-title").html("Delete LDDAP");
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

    $.fn.showApproval = function (url, name) {
        $("#modal-body-approval").html(`Are you sure you want to set this
                                       document to 'For Approval'?`);
        $("#modal-approval")
            .modal({ keyboard: false, backdrop: "static" })
            .on("shown.bs.modal", function () {
                $("#approval-title").html("Approval LDDAP");
                $("#form-approval").attr("action", url);
            })
            .on("hidden.bs.modal", function () {
                $("#modal-approval-body").html("");
                $("#form-approval").attr("action", "#");
            });
    };

    $.fn.approval = function () {
        $("#form-approval").submit();
    };

    $.fn.showApprove = function (url, name) {
        $("#modal-body-approve").html(`Are you sure you want to set this
                                       document to 'Approved'?`);
        $("#modal-approve")
            .modal({ keyboard: false, backdrop: "static" })
            .on("shown.bs.modal", function () {
                $("#approve-title").html("Approve LDDAP");
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

    $.fn.showSummary = function (url, name) {
        $("#modal-body-summary").html(`Are you sure you want to set this
                                       document to 'For Summary'?`);
        $("#modal-summary")
            .modal({ keyboard: false, backdrop: "static" })
            .on("shown.bs.modal", function () {
                $("#summary-title").html("Summary LDDAP");
                $("#form-summary").attr("action", url);
            })
            .on("hidden.bs.modal", function () {
                $("#modal-summary-body").html("");
                $("#form-summary").attr("action", "#");
            });
    };

    $.fn.summary = function () {
        $("#form-summary").submit();
    };

    $(".material-tooltip-main").tooltip({
        template: template,
    });
});
