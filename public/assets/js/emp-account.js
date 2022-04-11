$(function() {
    const template = '<div class="tooltip md-tooltip">' +
                     '<div class="tooltip-arrow md-arrow"></div>' +
                     '<div class="tooltip-inner md-inner stylish-color"></div></div>';

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.fn.showCreate = function(url) {
        $('#mdb-preloader').css('background', '#000000ab').fadeIn(300);
        $('#modal-body-create').load(url,function() {
            $('#mdb-preloader').fadeOut(300);
            $('.mdb-select').materialSelect();
            $('#sel-region').change(() => {
                const regionID = $('#sel-region').val();
                getProvinces(regionID);
            });
            $(this).slideToggle(500);
        });
        $("#modal-sm-create").modal({keyboard: false, backdrop: 'static'})
						     .on('shown.bs.modal', function() {
            $('#create-title').html('Create Account');
		}).on('hidden.bs.modal', function() {
		    $('#modal-body-create').html('').css('display', 'none');
		});
    }

    $.fn.store = function() {
        const withError = inputValidation(false);

		if (!withError) {
			$('#form-store').submit();
		}
    }

    $.fn.showEdit = function(url) {
        $('#mdb-preloader').css('background', '#000000ab').fadeIn(300);
        $('#modal-body-edit').load(url, function() {
            $('#mdb-preloader').fadeOut(300);
            $('.mdb-select').materialSelect();
            $('#sel-region').change(() => {
                const regionID = $('#sel-region').val();
                getProvinces(regionID);
            });
            $(this).slideToggle(500);
        });
        $("#modal-sm-edit").modal({keyboard: false, backdrop: 'static'})
						   .on('shown.bs.modal', function() {
            $('#edit-title').html('Update Account');
		}).on('hidden.bs.modal', function() {
		    $('#modal-body-edit').html('').css('display', 'none');
		});
    }

    $.fn.update = function() {
        const withError = inputValidation(false);

		if (!withError) {
			$('#form-update').submit();
		}
    }

    $.fn.showDelete = function(url, name) {
		$('#modal-body-delete').html(`Are you sure you want to delete '${name}'?`);
        $("#modal-delete").modal({keyboard: false, backdrop: 'static'})
						  .on('shown.bs.modal', function() {
            $('#delete-title').html('Delete Account');
            $('#form-delete').attr('action', url);
		}).on('hidden.bs.modal', function() {
             $('#modal-delete-body').html('');
             $('#form-delete').attr('action', '#');
		});
    }

    $.fn.delete = function() {
        $('#form-delete').submit();
    }

    $('.material-tooltip-main').tooltip({
        template: template
    });
    $('.mdb-select-filter').materialSelect();

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

    function getProvinces(regionID) {
        const url = `${baseURL}/profile/get-province/${regionID}`;
        $.ajax({
		    url: url,
            type: 'POST',
            processData: false,
            contentType: false,
            //async: false,
            //data: formData,
            //dataType: 'json',
		    success: function(response) {
                let selProvince = `
                <select id="sel-province" class="mdb-select md-form required" searchable="Search here.."
                        name="province">
                    <option value="" disabled selected>Choose a province *</option>
                `;

                if (response.length > 0) {
                    $.each(response, (key, json) => {
                        selProvince += `<option value="${json.id}">${json.province_name}</option>`;
                    });
                } else {
                    selProvince += `<option value="" disabled>No data</option>`;
                }

                selProvince += `</select>`;
                $('#province-section').html(selProvince);
                $('#sel-province').materialSelect();

                console.log(response);
            },
            fail: function(xhr, textStatus, errorThrown) {
                getProvinces(regionID);
		    },
		    error: function(data) {
                getProvinces(regionID);
		    }
        });
    }

	$("#img-input").change(function(){
	    readURL(this, "avatar");
	});

	$("#sig-input").change(function(){
	    readURL(this, "signature");
    });
});
