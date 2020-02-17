function inputValidation(withError) {
    var errorCount = 0;
    try {
        $(".required").not(".mdb-select").each(function() {
            var inputField = $(this).val().replace(/^\s+|\s+$/g, "").length;
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
            var inputField = $(this).val();
            if (!inputField) {
                $(this).siblings('.select-dropdown').addClass("input-error-highlighter");
                errorCount++;
            } else {
                $(this).siblings('.select-dropdown').removeClass("input-error-highlighter");
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
