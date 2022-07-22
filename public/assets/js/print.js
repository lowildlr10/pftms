$(function () {
    let key, documentType, otherParam;

    $("#paper-size")
        .unbind("change")
        .change(function () {
            const otherParam = $("#other_param").val(),
                paperSize = $("#paper-size").val(),
                fontSize = $("#font-size").val(),
                url = `${baseURL}/print/${key}/?document_type=${documentType}&preview_toggle=preview
                  &font_scale=${fontSize}&paper_size=${paperSize}&other_param=${otherParam}`,
                urlPost = `${baseURL}/print/${key}`;

            $("#inp-document-type").val(documentType);
            $("#inp-preview-toggle").val("download");
            $("#inp-font-scale").val(fontSize);
            $("#inp-paper-size").val(paperSize);
            $("#inp-other-param").val(otherParam);

            printDoc(url, documentType, urlPost);
        });

    $("#font-size")
        .unbind("change")
        .change(function () {
            const otherParam = $("#other_param").val(),
                paperSize = $("#paper-size").val(),
                fontSize = $("#font-size").val(),
                url = `${baseURL}/print/${key}/?document_type=${documentType}&preview_toggle=preview
                     &font_scale=${fontSize}&paper_size=${paperSize}&other_param=${otherParam}`,
                urlPost = `${baseURL}/print/${key}`;

            $("#inp-document-type").val(documentType);
            $("#inp-preview-toggle").val("download");
            $("#inp-font-scale").val(fontSize);
            $("#inp-paper-size").val(paperSize);
            $("#inp-other-param").val(otherParam);

            printDoc(url, documentType, urlPost);
        });

    $.fn.showPrint = function (_key, _documentType, _otherParam = "") {
        key = _key;
        documentType = _documentType;
        otherParam = _otherParam;

        switch (documentType) {
            case "proc_pr":
                $("#print-title").html("Generate Purchase Request");
                setPaperSize("A4");
                break;
            case "proc_rfq":
                $("#print-title").html("Generate Request for Quotation");
                setPaperSize("A4");
                break;
            case "proc_abstract":
                $("#print-title").html(
                    "Generate Abstract of Bids and Quotation"
                );
                setPaperSize("Long");
                break;
            case "proc_po":
                $("#print-title").html("Generate Purchase Order");
                setPaperSize("A4");
                break;
            case "proc_jo":
                $("#print-title").html("Generate Job Order");
                setPaperSize("A4");
                break;
            case "proc_ors":
                $("#print-title").html("Generate Obligation Request Status");
                setPaperSize("A4");
                break;
            case "proc_burs":
                $("#print-title").html(
                    "Generate Budget Utilization Request Status"
                );
                setPaperSize("A4");
                break;
            case "ca_ors":
                $("#print-title").html("Generate Obligation Request Status");
                setPaperSize("A4");
                break;
            case "ca_burs":
                $("#print-title").html(
                    "Generate Budget Utilization Request Status"
                );
                setPaperSize("A4");
                break;
            case "proc_iar":
                $("#print-title").html(
                    "Generate Inspection and Acceptance Report"
                );
                setPaperSize("A4");
                break;
            case "proc_dv":
                $("#print-title").html("Generate Disbursement Voucher");
                setPaperSize("A4");
                break;
            case "ca_dv":
                $("#print-title").html("Generate Disbursement Voucher");
                setPaperSize("A4");
                break;
            case "ca_lr":
                $("#print-title").html("Generate Liquidation Report");
                setPaperSize("A4");
                break;
            case "pay_lddap":
                $("#print-title").html(
                    "Generate List of Due and Demandable Accounts Payable"
                );
                setPaperSize("A4");
                break;
            case "pay_summary":
                $("#print-title").html(
                    "Generate Summary of LDDAP-ADAs Issued and Invalidated ADA Entries"
                );
                setPaperSize("A4");
                break;
            case "inv_par":
                $("#print-title").html(
                    "Generate Property Acknowledgement Reciept"
                );
                setPaperSize("A4");
                break;
            case "inv_ris":
                $("#print-title").html("Generate Requisition and Issue Slip");
                setPaperSize("A4");
                break;
            case "inv_ics":
                $("#print-title").html("Generate Inventory Custodian Slip");
                setPaperSize("A4");
                break;
            case "inv_label":
                $("#print-title").html("Generate Property Label Tag");
                setPaperSize("A4");
                break;
            case "fund_lib":
                $("#print-title").html("Generate Line-Item-Budget");
                setPaperSize("Long");
                break;
            case "fund_lib_realignment":
                $("#print-title").html("Generate Line-Item-Budget Realignment");
                setPaperSize("Long");
                break;
            case "report_obligation":
                $("#print-title").html("Generate Obligation Ledger");
                setPaperSize("Long");
                break;
            case "report_disbursement":
                $("#print-title").html("Generate Disbursement Ledger");
                setPaperSize("Long");
                break;
            case "report_raod":
                $("#print-title").html(
                    "Generate Registry of Allotments, Obligations and Disbursements"
                );
                setPaperSize("Long");

                if (otherParam == "multiple") {
                    _key = "";
                    $(".chk").each(function () {
                        if ($(this).is(":checked")) {
                            _key += $(this).val() + ";";
                            key = _key;
                        }
                    });

                    if (!key || !_key) {
                        _key = "id";
                        key = _key;
                    }
                }
                break;

            default:
                break;
        }

        const paperSize = $("#paper-size").val(),
            fontSize = $("#font-size").val();
        let url =
            `${baseURL}/print/${_key}/?document_type=${_documentType}&preview_toggle=preview` +
            `&font_scale=${fontSize}&paper_size=${paperSize}&test=true&other_param=${_otherParam}`;

        $.ajax({
            url: url,
            success: function (data) {
                $("#print-modal")
                    .modal({ keyboard: false, backdrop: "static" })
                    .on("shown.bs.modal", function () {
                        const urlPost = `${baseURL}/print/${key}`;
                        url = `${baseURL}/print/${key}/?document_type=${documentType}&preview_toggle=preview
                          &font_scale=${fontSize}&paper_size=${paperSize}&other_param=${otherParam}`;

                        printDoc(url, documentType, urlPost);

                        $("#other_param").val(otherParam);

                        $("#inp-document-type").val(documentType);
                        $("#inp-preview-toggle").val("download");
                        $("#inp-font-scale").val(fontSize);
                        $("#inp-paper-size").val(paperSize);
                        $("#inp-other-param").val(otherParam);
                    })
                    .on("hidden.bs.modal", function () {
                        $("#modal-print-content").html(
                            $("#modal-print-content").html()
                        );
                        $("#modal-print-content object")
                            .removeAttr("data")
                            .removeAttr("url");
                        $("#modal-print-content object form").removeAttr(
                            "target"
                        );

                        $("#document-type").val("");
                        $("#preview-toggle").val("");
                        $("#font-size").val(0);
                        $("#paper-size").val("0");
                        $("#other-param").val("");

                        $("#inp-document-type").val("");
                        $("#inp-preview-toggle").val("");
                        $("#inp-font-scale").val("");
                        $("#inp-paper-size").val("");
                        $("#inp-other-param").val("");
                    });
            },
            error: function (xhr, error) {
                alert(
                    "Please fill-up all the required(*) fields first before printing the document."
                );
            },
        });
    };

    $.fn.downloadPDF = function () {
        const paperSize = $("#paper-size").val(),
            fontSize = $("#font-size").val(),
            otherParam = $("#other_param").val(),
            url = (urlPost = `${baseURL}/print/${key}`);

        $("#inp-document-type").val(documentType);
        $("#inp-preview-toggle").val("download");
        $("#inp-font-scale").val(fontSize);
        $("#inp-paper-size").val(paperSize);
        $("#inp-other-param").val(otherParam);
        $("#modal-print-content object form").attr("action", url).submit();
    };

    $.fn.downloadImage = function () {
        const paperSize = $("#paper-size").val(),
            fontSize = $("#font-size").val(),
            otherParam = $("#other_param").val(),
            url = (urlPost = `${baseURL}/print/${key}`);

        $("#inp-document-type").val(documentType);
        $("#inp-preview-toggle").val("download-image");
        $("#inp-font-scale").val(fontSize);
        $("#inp-paper-size").val(paperSize);
        $("#inp-other-param").val(otherParam);
        $("#modal-print-content object form").attr("action", url).submit();
    };

    function setPaperSize(paperType) {
        paperType = paperType.toLowerCase();

        $("select#paper-size")
            .find("option")
            .each(function () {
                const text = $(this).text();

                if (text.toLowerCase().indexOf(paperType) >= 0) {
                    const value = $(this).val();
                    $("#paper-size").val(value);
                    return false;
                }
            });
    }

    function printDoc(url, documentType, urlPost = "") {
        $("#mdb-preloader")
            .css("background", "#000000ab")
            .fadeIn(300, function () {
                $.ajax({
                    url: encodeURI(url),
                    success: function (data) {
                        $("#modal-print-content object").attr("data", url);
                        $("#modal-print-content object form").attr(
                            "action",
                            urlPost
                        );
                        $("#mdb-preloader").fadeOut(300);
                    },
                    error: function (xhr, error) {
                        $("#mdb-preloader").fadeOut(300);
                        alert(
                            "Please fill-up all the required(*) fields first before printing the document."
                        );
                    },
                });
            });
    }

    $("#paper-size").materialSelect();
});
