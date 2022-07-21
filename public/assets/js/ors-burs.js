$(function () {
    const template =
        '<div class="tooltip md-tooltip">' +
        '<div class="tooltip-arrow md-arrow"></div>' +
        '<div class="tooltip-inner md-inner stylish-color"></div></div>';
    const payeeData = {};

    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    $.fn.showRemarks = function (url) {
        $("#mdb-preloader").css("background", "#000000ab").fadeIn(300);
        $("#modal-body-show").load(url, function () {
            $("#mdb-preloader").fadeOut(300);
            $(this).slideToggle(500);
        });
        $("#modal-show")
            .modal({ keyboard: false, backdrop: "static" })
            .on("shown.bs.modal", function () {
                $("#show-title").html("View Remarks");
            })
            .on("hidden.bs.modal", function () {
                $("#modal-body-show").html("").css("display", "none");
            });
    };

    function initializeSelect2() {
        $(".payee-tokenizer").select2({
            placeholder: "Select a payee...",
            width: "100%",
            allowClear: true,
            tags: true,
            dropdownParent: $(".payee-tokenizer").parent(),
            ajax: {
                url: `${baseURL}/cadv-reim-liquidation/ors-burs/get-custom-payees`,
                type: "post",
                dataType: "json",
                delay: 250,
                data: function (params) {
                    return {
                        //_token: CSRF_TOKEN,
                        search: params.term,
                    };
                },
                processResults: function (data) {
                    return {
                        results: $.map(data, (item) => {
                            let jsonData = {};
                            jsonData["name"] = item.payee_name;
                            payeeData[item.id] = jsonData;

                            return {
                                text: `${item.payee_name}`,
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
            //theme: "material",
        });
    }

    function sendRemarks(url, refreshURL, formData) {
        $.ajax({
            url: url,
            type: "POST",
            processData: false,
            contentType: false,
            data: formData,
            success: function (response) {
                $("#modal-body-show").load(refreshURL, function () {
                    $("#mdb-preloader").fadeOut(300);
                });
            },
            fail: function (xhr, textStatus, errorThrown) {
                sendRemarks(url, refreshURL, formData);
            },
            error: function (data) {
                sendRemarks(url, refreshURL, formData);
            },
        });
    }

    function computeRemainingUacs() {
        $(".uacs_amount").on("keyup change", function () {
            const amount = parseFloat($("#remaining-original").val());
            let remaining = amount;

            $(".uacs_amount").each(function () {
                const valAmount = !isNaN(parseFloat($(this).val()))
                    ? parseFloat($(this).val())
                    : 0.0;
                remaining -= valAmount;
            });

            $("#remaining").val(remaining.toFixed(2));

            if (parseFloat($("#remaining").val()) < 0) {
                $("#remaining")
                    .addClass("input-error-highlighter")
                    .tooltip("show");
            } else {
                $("#remaining")
                    .removeClass("input-error-highlighter")
                    .tooltip("hide");
            }
        });
    }

    function initializeInputs() {
        $("#sel-uacs-code").change(function () {
            const uacsVals = $(this).val();
            const uacsDescs = $(this)
                .find("option:selected")
                .not("option:disabled")
                .map(function () {
                    return $(this).text().trim();
                })
                .get();
            let uacsHTML = "";

            if (!empty(uacsVals) && !empty(uacsDescs)) {
                uacsVals.forEach((uacsID, uacsIndex) => {
                    const uacsDescCode = uacsDescs[uacsIndex];
                    const uacsCode = uacsDescCode.split(" : ")[0];
                    const uacsDesc = uacsDescCode.split(" : ")[1];

                    let description = $(`#uacs_description_${uacsID}`).val();
                    let _uacsID = $(`#uacs_id_${uacsID}`).val();
                    let orsUacsID = $(`#ors_uacs_id_${uacsID}`).val();
                    let amount = $(`#uacs_amount_${uacsID}`).val();

                    description = !empty(description) ? description : uacsDesc;
                    _uacsID = !empty(_uacsID) ? _uacsID : uacsID;
                    orsUacsID = !empty(orsUacsID) ? orsUacsID : "";
                    amount = !empty(amount) ? amount : 0;

                    uacsHTML += `
                    <div class="row" id="uacs_description_{{ $itemCtr }}">
                        <div class="col-md-10 border">
                            <div class="md-form form-sm" id="uacs_description_${uacsIndex}">
                                <input type="text" id="uacs_description_${uacsID}" name="uacs_description[]"
                                    class="form-control uacs_description required" placeholder="Item Description for '${uacsDesc}'"
                                    value="${description}">
                                <input type="hidden" id="uacs_id_${uacsID}" class="uacs_id" name="uacs_id[]" value="${_uacsID}">
                                <input type="hidden" id="ors_uacs_id_${uacsID}" class="ors_uacs_id" name="ors_uacs_id[]" value="${orsUacsID}">
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
                                    <strong>Amount for ${uacsDesc}</strong>
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
                $("#remaining-amount-segment").show();
            } else {
                $("#remaining-amount-segment").hide();
                uacsHTML = "";
            }

            $("#uacs-description-segment").html(uacsHTML);

            computeRemainingUacs();
        });

        $("#amount").change(function () {
            const amount = $(this).val();
            $("#total").val(amount);
        });

        computeRemainingUacs();
    }

    $.fn.deleteUacsItem = function (elemDescID, elemAmountID, uacsID) {
        $(elemDescID).remove();
        $(elemAmountID).remove();

        //$(`#sel-uacs-code option:selected`).removeAttr('selected');
    };

    $.fn.refreshRemarks = function (refreshURL) {
        $("#mdb-preloader").css("background", "#000000ab").fadeIn(300);
        $("#modal-body-show").load(refreshURL, function () {
            $("#mdb-preloader").fadeOut(300);
        });
    };

    $.fn.storeRemarks = function (url, refreshURL) {
        let formData = new FormData();
        const message = $("#message").val(),
            withError = inputValidation(false);

        if (!withError) {
            $("#mdb-preloader").css("background", "#000000ab").fadeIn(300);
            formData.append("message", message);
            sendRemarks(url, refreshURL, formData);
        }
    };

    $.fn.showCreate = function (url) {
        $("#mdb-preloader").css("background", "#000000ab").fadeIn(300);
        $("#modal-body-create").load(url, function () {
            $("#mdb-preloader").fadeOut(300);
            $(".crud-select").materialSelect();
            initializeSelect2();
            $(this).slideToggle(500);

            initializeInputs();
        });
        $("#modal-lg-create")
            .modal({ keyboard: false, backdrop: "static" })
            .on("shown.bs.modal", function () {
                $("#create-title").html("Create ORS/BURS");
            })
            .on("hidden.bs.modal", function () {
                $("#modal-body-create").html("").css("display", "none");
            });
    };

    $.fn.showCreateDV = function (url) {
        $("#mdb-preloader").css("background", "#000000ab").fadeIn(300);
        $("#modal-body-create").load(url, function () {
            $("#mdb-preloader").fadeOut(300);
            $(".crud-select").materialSelect();
            initializeSelect2();
            $(this).slideToggle(500);

            initializeInputs();
        });
        $("#modal-lg-create")
            .modal({ keyboard: false, backdrop: "static" })
            .on("shown.bs.modal", function () {
                $("#create-title").html("Create DV from ORS/BURS");
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
            initializeSelect2();
            $(this).slideToggle(500);

            initializeInputs();
        });
        $("#modal-lg-edit")
            .modal({ keyboard: false, backdrop: "static" })
            .on("shown.bs.modal", function () {
                $("#edit-title").html("Update ORS/BURS");
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
                $("#delete-title").html("Delete ORS/BURS");
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

    $.fn.showIssue = function (url) {
        $("#mdb-preloader").css("background", "#000000ab").fadeIn(300);
        $("#modal-body-issue").load(url, function () {
            $("#mdb-preloader").fadeOut(300);
            $(this).slideToggle(500);
        });
        $("#modal-issue")
            .modal({ keyboard: false, backdrop: "static" })
            .on("shown.bs.modal", function () {
                $("#issue-title").html("Submit ORS/BURS");
                $(this)
                    .find(".btn-orange")
                    .html('<i class="fas fa-paper-plane"></i> Submit');
            })
            .on("hidden.bs.modal", function () {
                $("#modal-body-issue").html("").css("display", "none");
            });
    };

    $.fn.issue = function () {
        $("#form-issue").submit();
    };

    $.fn.showReceive = function (url) {
        $("#mdb-preloader").css("background", "#000000ab").fadeIn(300);
        $("#modal-body-receive").load(url, function () {
            $("#mdb-preloader").fadeOut(300);
            $(this).slideToggle(500);
        });
        $("#modal-receive")
            .modal({ keyboard: false, backdrop: "static" })
            .on("shown.bs.modal", function () {
                $("#receive-title").html("Receive ORS/BURS");
            })
            .on("hidden.bs.modal", function () {
                $("#modal-body-receive").html("").css("display", "none");
            });
    };

    $.fn.receive = function () {
        $("#form-receive").submit();
    };

    $.fn.showIssueBack = function (url) {
        $("#mdb-preloader").css("background", "#000000ab").fadeIn(300);
        $("#modal-body-issue-back").load(url, function () {
            $("#mdb-preloader").fadeOut(300);
            $(this).slideToggle(500);
        });
        $("#modal-issue-back")
            .modal({ keyboard: false, backdrop: "static" })
            .on("shown.bs.modal", function () {
                $("#issue-back-title").html("Submit Back ORS/BURS");
            })
            .on("hidden.bs.modal", function () {
                $("#modal-body-issue-back").html("").css("display", "none");
            });
    };

    $.fn.issueBack = function () {
        $("#form-issue-back").submit();
    };

    $.fn.showReceiveBack = function (url) {
        $("#mdb-preloader").css("background", "#000000ab").fadeIn(300);
        $("#modal-body-receive-back").load(url, function () {
            $("#mdb-preloader").fadeOut(300);
            $(this).slideToggle(500);
        });
        $("#modal-receive-back")
            .modal({ keyboard: false, backdrop: "static" })
            .on("shown.bs.modal", function () {
                $("#receive-back-title").html("Receive Back ORS/BURS");
            })
            .on("hidden.bs.modal", function () {
                $("#modal-body-receive-back").html("").css("display", "none");
            });
    };

    $.fn.receiveBack = function () {
        $("#form-receive-back").submit();
    };

    $.fn.showObligate = function (url) {
        $("#mdb-preloader").css("background", "#000000ab").fadeIn(300);
        $("#modal-body-obligate").load(url, function () {
            let amount = $("#remaining").val();

            $("#mdb-preloader").fadeOut(300);
            $(".crud-select").materialSelect();
            $("#type").change(function () {
                $("#serial_no")
                    .val($(this).val().split("-")[1])
                    .siblings("label")
                    .addClass("active");
            });
            $("#sel-uacs-code").select2({
                tokenSeparators: [","],
                placeholder: "Choose the MOOE account titles",
                width: "100%",
                maximumSelectionSize: 4,
                allowClear: true,
            });

            $("#remaining-original").val(amount);
            $("#uacs-description-segment")
                .find(".uacs_amount")
                .each((key, elem) => {
                    const uacsAmount = parseFloat($(elem).val()).toFixed(2);
                    amount -= uacsAmount;
                });
            $("#remaining").val(amount);
            $(this).slideToggle(500);
            initializeInputs();
        });
        $("#modal-obligate")
            .modal({ keyboard: false, backdrop: "static" })
            .on("shown.bs.modal", function () {
                $("#obligate-title").html("Obligate ORS/BURS");
            })
            .on("hidden.bs.modal", function () {
                $("#modal-body-obligate").html("").css("display", "none");
            });
    };

    $.fn.obligate = function () {
        const withError = inputValidation(false);

        if (!withError) {
            $("#form-obligate").submit();
        }
    };

    $.fn.showUacsItems = function (url) {
        const totalAmount = $("#amount").val();

        if (!empty(totalAmount) && totalAmount > 0) {
            $("#mdb-preloader").css("background", "#000000ab").fadeIn(300);
            $("#modal-body-uacs").load(url, function () {
                let amount = $("#amount").val();

                $("#mdb-preloader").fadeOut(300);
                //$('.crud-select').materialSelect();
                $("#sel-uacs-code").select2({
                    tokenSeparators: [","],
                    placeholder: "Choose the MOOE account titles",
                    width: "100%",
                    maximumSelectionSize: 4,
                    allowClear: true,
                });

                $("#remaining-original").val(amount);
                $("#uacs-description-segment")
                    .find(".uacs_amount")
                    .each((key, elem) => {
                        const uacsAmount = parseFloat($(elem).val()).toFixed(2);
                        amount -= uacsAmount;
                    });
                $("#remaining").val(amount);
                $(this).slideToggle(500);
                initializeInputs();

                $("#amount")
                    .removeClass("input-error-highlighter")
                    .tooltip("hide");
            });
            $("#modal-uacs")
                .modal({ keyboard: false, backdrop: "static" })
                .on("shown.bs.modal", function () {
                    $("#uacs-title").html("Update ORS/BURS UACS Items");
                })
                .on("hidden.bs.modal", function () {
                    $("#modal-body-uacs").html("").css("display", "none");
                });
        } else {
            $("#amount").addClass("input-error-highlighter").tooltip("show");
        }
    };

    $.fn.updateUacsItems = function () {
        let formElem = "",
            uacsDisplay = "",
            listDesc = [],
            listAmount = [];

        $("#uacs-items-segment").html("");
        //$('#uacs-code-display').val($('#sel-uacs-code').find(':selected').text().trim());
        $("#uacs-code").val($("#sel-uacs-code").val());

        $("#uacs-description-segment")
            .find(".uacs_id")
            .each((key, elem) => {
                const uacsID = $(elem).val();
                formElem += `<input type="hidden" name="uacs_id[${key}]" value="${uacsID}">`;
            });

        if ($("#uacs-description-segment").find(".ors_uacs_id").length > 0) {
            $("#uacs-description-segment")
                .find(".ors_uacs_id")
                .each((key, elem) => {
                    const orsUacsID = $(elem).val();
                    formElem += `<input type="hidden" name="ors_uacs_id[${key}]" value="${orsUacsID}">`;
                });
        } else {
            const uacsIdCount = $("#uacs-description-segment").find(
                ".uacs_id"
            ).length;

            for (let uacsCtr = 0; uacsCtr < uacsIdCount; uacsCtr++) {
                formElem += `<input type="hidden" name="ors_uacs_id[${uacsCtr}]" value="">`;
            }
        }

        $("#uacs-description-segment")
            .find(".uacs_description")
            .each((key, elem) => {
                const uacsDesc = $(elem).val();
                formElem += `<input type="hidden" name="uacs_description[${key}]" value="${uacsDesc}">`;
                listDesc.push(uacsDesc);
            });

        $("#uacs-description-segment")
            .find(".uacs_amount")
            .each((key, elem) => {
                const uacsAmount = $(elem).val();
                formElem += `<input type="hidden" name="uacs_amount[${key}]" value="${uacsAmount}">`;
                listAmount.push(uacsAmount);
            });

        $.each(listDesc, (key, desc) => {
            const amount = parseFloat(listAmount[key]).toFixed(2);
            uacsDisplay += `${desc} = ${amount}\n\n`;
        });

        $("#uacs-code-display").val(uacsDisplay);
        $("#uacs-items-segment").html(formElem);
        $("#modal-uacs").modal("hide");
    };

    $(".material-tooltip-main").tooltip({
        template: template,
    });
});
