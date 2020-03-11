$(function() {
	// Image Upload
	$('.btn-file-avatar :file').change(function() {
		var input = $(this),
			label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
			input.trigger('fileselect', [label]);
	});

	$('.btn-file-avatar :file').on('fileselect', function(event, label) {
	    var input = $(this).parents('.input-group').find(':text'),
	        log = label;

	    if( input.length ) {
	        input.val(log);
	    } else {
	        if (log) {
	        	alert(log);
	        }
	    }
	});

	$('.btn-file-signature :file').change(function() {
		var input = $(this),
			label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
			input.trigger('fileselect', [label]);
	});

	$('.btn-file-signature :file').on('fileselect', function(event, label) {
	    var input = $(this).parents('.input-group').find(':text'),
	        log = label;

	    if( input.length ) {
	        input.val(log);
	    } else {
	        if (log) {
	        	alert(log);
	        }
	    }

	});

	function readURL(input, toggle) {
	    if (input.files && input.files[0]) {
	        var reader = new FileReader();

	        reader.onload = function (e) {
	        	if (toggle == 'avatar') {
	        		$('#img-upload').attr('src', e.target.result);
	        	} else if (toggle == 'signature') {
	        		$('#sig-upload').attr('src', e.target.result);
	            }
	        }

	        reader.readAsDataURL(input.files[0]);
	    }
    }

    $.fn.register = function() {
        const withError = inputValidation(false);

		if (!withError) {
			$('#form-store').submit();
		}
    }

    $.fn.update = function() {
        const withError = inputValidation(false);

		if (!withError) {
			$('#form-update').submit();
		}
    }

	$("#img-input").change(function(){
	    readURL(this, "avatar");
	});

	$("#sig-input").change(function(){
	    readURL(this, "signature");
    });
    $('.mdb-select').materialSelect();
});
