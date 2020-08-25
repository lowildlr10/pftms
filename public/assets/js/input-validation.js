function inputValidation(withError) {
    let errorCount = 0;

    try {
        $(".required").not(".mdb-select").each(function() {
            let inputField = $(this).val().replace(/^\s+|\s+$/g, "").length;

            if (inputField == 0) {
                $(this).addClass("input-error-highlighter");
                errorCount++;
            } else {
                $(".input-quantity").each(function() {
                    if ($(this).val() == "0") {
                        $(this).addClass("input-error-highlighter");
                        errorCount++;
                    }
                });
                $(this).removeClass("input-error-highlighter");
            }
        });
        $("select.required").each(function() {
            let inputField = $(this).val(),
                inputSelect2 = $(this).next('span.select2');

            if (!inputField || empty(inputField)) {
                $(this).siblings('.select-dropdown').addClass("input-error-highlighter");
                errorCount++;
            } else {
                $(this).siblings('.select-dropdown').removeClass("input-error-highlighter");
            }

            if (inputSelect2.length) {
                const listCountSelect2 = inputSelect2.find('ul.select2-selection__rendered')
                                            .children().length;

                if (!listCountSelect2) {
                    inputSelect2.find('input').addClass("input-error-highlighter");
                } else {
                    inputSelect2.find('input').removeClass("input-error-highlighter");
                }
            }

        });


    } catch (error) {
    }

    if (errorCount == 0) {
        withError = false;
    } else {
        withError = true;
    }

    return withError;
}
