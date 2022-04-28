/*
$.ajaxSetup({
    headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
});*/

$(function () {
    var wb;
    var wbout;
    var modalLoadingContent =
        "<div class='mt-5 mb-5' style='height: 500px;'>" +
        "<center>" +
        "<div class='preloader-wrapper big active crazy'>" +
        "<div class='spinner-layer spinner-blue-only'>" +
        "<div class='circle-clipper left'>" +
        "<div class='circle'></div>" +
        "</div>" +
        "<div class='gap-patch'>" +
        "<div class='circle'></div>" +
        "</div>" +
        "<div class='circle-clipper right'>" +
        "<div class='circle'></div>" +
        "</div>" +
        "</div>" +
        "</div><br>" +
        "</center>" +
        "</div>";
    const modules = [
        "pr-rfq",
        "rfq-abstract",
        "abstract-po",
        "po-ors",
        "po-iar",
        "iar-stock",
        "iar-dv",
        "ors-dv",
        "dv-lddap",
        "lddap-summary",
        "summary-bank",
    ];

    function getSearch(module) {
        const keyword = $("#keyword").val();
        const getURL = encodeURI(
            `${baseURL}/v-track/get-search?keyword=${keyword}&module=${module}`
        );
        const formData = new FormData();

        //formData.append("keyword", keyword);
        //formData.append("module", module);

        let loadSegment = $(`#table-generate-${module}`)
            .html(modalLoadingContent)
            .load(getURL, (response, status, xhr) => {
                if (status == "error") {
                    getSearch(module);
                }

                $(`#${module}`).find(".download-section")
                    .html(`<div class="col-md-12 mb-2 mt-2">
                        <button class="btn btn-outline-primary btn-block"
                                onclick="$(this).generateExcel('${module}', '${module}');"
                                id="btn-generate">
                            <i class="fas fa-file-excel text-success"></i> Download as Excel
                        </button>
                    </div>`);
            });

        loadSegment.onreadystatechange = null;
        loadSegment.abort = null;
        loadSegment = null;
    }

    function s2ab(s) {
        var buf = new ArrayBuffer(s.length);
        var view = new Uint8Array(buf);
        for (var i = 0; i < s.length; i++) view[i] = s.charCodeAt(i) & 0xff;
        return buf;
    }

    function inputValidation(withError) {
        var errorCount = 0;

        $(".required").each(function () {
            var inputField = $(this)
                .val()
                .replace(/^\s+|\s+$/g, "").length;

            if (inputField == 0) {
                $(this).addClass("input-error-highlighter");
                errorCount++;
            } else {
                $(".input-quantity").each(function () {
                    if ($(this).val() == "0") {
                        $(this).addClass("input-error-highlighter");
                        errorCount++;
                    }
                });

                $(this).removeClass("input-error-highlighter");
            }
        });

        if (errorCount == 0) {
            withError = false;
        } else {
            withError = true;
        }

        return withError;
    }

    $.fn.generate = function (toggle) {
        const withError = inputValidation(false);

        if (!withError) {
            const dateFrom = $("#date-from").val(),
                dateTo = $("#date-to").val(),
                search = $("#input-search").val();
            //$('#input-search').val('');
            $("#btn-generate").prop("disabled", true);
            $("#btn-generate-table").prop("disabled", true);
            $("#table-generate")
                .html(modalLoadingContent)
                .load(
                    encodeURI(
                        "generate-table/" +
                            toggle +
                            "?date_from=" +
                            dateFrom +
                            "&date_to=" +
                            dateTo +
                            "&search=" +
                            search
                    ),
                    function () {
                        $("#btn-generate").removeAttr("disabled");
                        $("#btn-generate-table").removeAttr("disabled");
                        $('[data-toggle="tooltip"]').tooltip();
                    }
                );
        }
    };

    $.fn.generateNextPrev = function (url, customID = "") {
        const tableID = !empty(customID)
            ? `#table-generate-${customID}`
            : "#table-generate";
        $("#btn-generate").prop("disabled", true);
        $("#btn-generate-table").prop("disabled", true);

        let loadSegment = $(tableID)
            .html(modalLoadingContent)
            .load(url, function () {
                $("#btn-generate").removeAttr("disabled");
                $("#btn-generate-table").removeAttr("disabled");
            });

        loadSegment.onreadystatechange = null;
        loadSegment.abort = null;
        loadSegment = null;
    };

    $.fn.generateExcel = function (toggle, customID = "table-list") {
        const dateFrom = $("#date-from").val();
        const dateTo = $("#date-to").val();
        let fileName = dateFrom + "_to_" + dateTo + "_" + toggle + ".xlsx";

        if (empty(dateFrom) || empty(dateTo)) {
            fileName = toggle + ".xlsx";
        }

        wb = XLSX.utils.table_to_book(document.getElementById(customID), {
            sheet: "Sheet JS",
        });
        wbout = XLSX.write(wb, {
            bookType: "xlsx",
            bookSST: true,
            type: "binary",
        });

        saveAs(
            new Blob([s2ab(wbout)], { type: "application/octet-stream" }),
            fileName
        );
    };

    $("#date-from")
        .unbind("change")
        .change(function () {
            var dateFrom = $(this).val();

            $("#date-to").attr("min", dateFrom);
        });

    $("#date-to")
        .unbind("change")
        .change(function () {
            var dateFrom = $("#date-from").val();

            $(this).attr("min", dateFrom);
        });

    if ($("#search").val() == "1") {
        $.each(modules, (index, module) => {
            getSearch(module);
        });
    }
});
