$(function () {
    const template =
        '<div class="tooltip md-tooltip">' +
        '<div class="tooltip-arrow md-arrow"></div>' +
        '<div class="tooltip-inner md-inner stylish-color"></div></div>';
    const claimantData = {};

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
        $(".claimant-tokenizer").select2({
            placeholder: "Select a claimant...",
            width: "100%",
            allowClear: true,
            tags: true,
            dropdownParent: $(".claimant-tokenizer").parent(),
            ajax: {
                url: `${baseURL}/cadv-reim-liquidation/liquidation/get-custom-claimants`,
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
                            claimantData[item.id] = jsonData;

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
        });
        $("#modal-lg-create")
            .modal({ keyboard: false, backdrop: "static" })
            .on("shown.bs.modal", function () {
                $("#create-title").html("Create Liquidation Report");
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
        });
        $("#modal-lg-edit")
            .modal({ keyboard: false, backdrop: "static" })
            .on("shown.bs.modal", function () {
                $("#edit-title").html("Update Liquidation Report");
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
                $("#delete-title").html("Delete Liquidation Report");
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
                $("#issue-title").html("Submit Liquidation Report");
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
                $("#receive-title").html("Receive Liquidation Report");
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
                $("#issue-back-title").html("Submit Back Liquidation Report");
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
                $("#receive-back-title").html(
                    "Receive Back Liquidation Report"
                );
            })
            .on("hidden.bs.modal", function () {
                $("#modal-body-receive-back").html("").css("display", "none");
            });
    };

    $.fn.receiveBack = function () {
        $("#form-receive-back").submit();
    };

    $.fn.showLiquidate = function (url) {
        $("#mdb-preloader").css("background", "#000000ab").fadeIn(300);
        $("#modal-body-liquidate").load(url, function () {
            $("#mdb-preloader").fadeOut(300);
            $(this).slideToggle(500);
        });
        $("#modal-liquidate")
            .modal({ keyboard: false, backdrop: "static" })
            .on("shown.bs.modal", function () {
                $("#liquidate-title").html("Liquidate Liquidation Report");
            })
            .on("hidden.bs.modal", function () {
                $("#modal-body-liquidate").html("").css("display", "none");
            });
    };

    $.fn.liquidate = function () {
        const withError = inputValidation(false);

        if (!withError) {
            $("#form-liquidate").submit();
        }
    };

    $(".material-tooltip-main").tooltip({
        template: template,
    });
});
