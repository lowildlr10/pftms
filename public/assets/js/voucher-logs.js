$(function() {
	var wb;
    var wbout;
    var modalLoadingContent = "<div class='mt-5 mb-5' style='height: 500px;'>"+
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

    function s2ab(s) {
        var buf = new ArrayBuffer(s.length);
        var view = new Uint8Array(buf);
        for (var i=0; i<s.length; i++) view[i] = s.charCodeAt(i) & 0xFF;
        return buf;
    }

	function inputValidation(withError) {
		var errorCount = 0;

        $(".required").each(function() {
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

		if (errorCount == 0) {
			withError = false;
		} else {
			withError = true;
		}

		return withError;
	}

	$.fn.generate = function(toggle) {
		var withError = inputValidation(false);

		if (!withError) {
			var dateFrom = $('#date-from').val();
			var dateTo = $('#date-to').val();
			var search = $('#input-search').val();
			$('#input-search').val('');
			$('#btn-generate').prop('disabled', true);
			$('#btn-generate-table').prop('disabled', true);
			$('#table-generate').html(modalLoadingContent)
								.load('generate-table/' + toggle + '?date_from=' + dateFrom +
									  '&date_to=' + dateTo + '&search=' + search, function() {
									$('#btn-generate').removeAttr('disabled');
                                    $('#btn-generate-table').removeAttr('disabled');
                                    $('[data-toggle="tooltip"]').tooltip();
								});
		}
	}

	$.fn.generateNextPrev = function(url) {
		$('#btn-generate').prop('disabled', true);
		$('#btn-generate-table').prop('disabled', true);
		$('#table-generate').html(modalLoadingContent)
							.load(url, function() {
								$('#btn-generate').removeAttr('disabled');
								$('#btn-generate-table').removeAttr('disabled');
							});
	}

	$.fn.generateExcel = function(toggle) {
		var dateFrom = $('#date-from').val();
		var dateTo = $('#date-to').val();
		var fileName = dateFrom + "_to_" + dateTo + "_" + toggle + ".xlsx";

		wb = XLSX.utils.table_to_book(document.getElementById('table-list'), {sheet:"Sheet JS"});
    	wbout = XLSX.write(wb, {bookType:'xlsx', bookSST:true, type: 'binary'});

    	saveAs(new Blob([s2ab(wbout)],{type:"application/octet-stream"}), fileName);
    }

	$('#date-from').unbind('change').change(function() {
		var dateFrom = $(this).val();

		$('#date-to').attr('min', dateFrom);
	});

	$('#date-to').unbind('change').change(function() {
		var dateFrom = $('#date-from').val();

		$(this).attr('min', dateFrom);
	});
});
