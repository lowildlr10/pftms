$(function() {
    var parentID;
    var template = `<div class="tooltip md-tooltip">
                    <div class="tooltip-arrow md-arrow"></div>
                    <div class="tooltip-inner md-inner stylish-color"></div></div>`;
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function baseName(str) {
        var base = new String(str).substring(str.lastIndexOf('/') + 1);

        if(base.lastIndexOf(".") != -1) {
            base = base.substring(0, base.lastIndexOf("."));
        }

        return base;
    }

    function countAttachment() {
        var counter = 0;

        $('#tree-attachments').find('li').each(function() {
            counter++;
        });

        return counter;
    }

    function uploadFile(file, type) {
        var storeDataURL = `${baseURL}/attachment/store`;
        var formData = new FormData();
        formData.append('parent_id', parentID);
        formData.append('type', type);
        formData.append('attachment', file);

        $.ajax({
		    url: storeDataURL,
            type: 'POST',
            processData: false,
            contentType: false,
		    data: formData,
            dataType: 'json',
		    success: function(response) {
                $('#body-new-attachments').append(
                    `<a href="${response.directory}" class="dark-grey-text" target="_blank">
                        <i class="fas fa-file-pdf"></i> ${response.filename}
                        <i class="fas fa-check-circle green-text"></i>
                    </a><br>`
                );
		    },
		    fail: function(xhr, textStatus, errorThrown){
                $('#body-new-attachments').append(
                    `<a href="#" class="dark-grey-text">
                        <i class="fas fa-file-pdf"></i> ${file.name}
                        <i class="fas fa-times red-text"></i>
                    </a><br>`
                );
		    },
		    error: function(data) {
                $('#body-new-attachments').append(
                    `<a href="#" class="dark-grey-text">
                        <i class="fas fa-file-pdf"></i> ${file.name}
                        <i class="fas fa-times red-text"></i>
                    </a><br>`
                );
		    }
        });
    }

    $.fn.showAttachment = function(_parentID) {
        parentID = _parentID;
		$('#modal-body-attachment').load(`${baseURL}/attachment/get/${_parentID}`, function() {
            $('.treeview-animated').mdbTreeview();
            $('.material-tooltip-main').tooltip({
                template: template
            });
        });
		$("#modal-attachment").modal()
						      .on('shown.bs.modal', function() {
		}).on('hidden.bs.modal', function() {
            parentID = '';
            $('#modal-body-attachment').html('');
            $('#btn-attachments').removeClass('active').addClass('active');
            $('#btn-add-attachments').removeClass('active');
            $('#new-attachments').collapse('hide');
            $('#body-new-attachments').html('');
            $('#body-attachments').addClass('active show');
            $('#add-attachment').removeClass('active show');

            $('#attachment').val('');
            $('.file-path').val('');
		});
    }

    $.fn.deleteAttachment = function(id, elementID, directory) {
        const deleteURL = `${baseURL}/attachment/destroy/${id}`;
        const filename = baseName(directory);
        let formData = new FormData();
        formData.append('id', id);
        formData.append('directory', directory);

        $(elementID).html('<i class="fas fa-spinner fa-spin"></i> Deleting...')
					.removeClass('red-text')
                    .addClass('grey-text');

        $.ajax({
		    url: deleteURL,
            type: 'POST',
            processData: false,
            contentType: false,
		    data: formData,
		    success: function(response) {
		    	$(elementID).html(`<i class="fas fa-check"></i> ${response}`)
						  	.fadeOut(500, function() {
                    $(this).remove();
                    if (countAttachment() == 0) {
                        $('#tree-attachments ul').append(
                            `<li>
                                <div class="treeview-animated-element">
                                    <h6 class="red-text">No attachment found.</h6>
                                </div>
                            </li>`
                        );
                    }
                });
		    },
		    fail: function(xhr, textStatus, errorThrown){
		       	$(elementID).html(`Click again to delete "${filename}"`)
		       				.removeClass('dark-grey-text')
						  	.addClass('red-text');
		    },
		    error: function(data) {
		    	$(elementID).html(`Click again to delete "${filename}"`)
		       				.removeClass('dark-grey-text')
						  	.addClass('red-text');
		    }
        });
	}

    $.fn.toggleModalBody = function(bodyID, type) {
        if (type == 'attachment') {
            $(bodyID).html('');
            $(bodyID).load(`${baseURL}/attachment/get/${parentID}`, function() {
                $('.treeview-animated').mdbTreeview();
                $('.material-tooltip-main').tooltip({
                    template: template
                });
            });
        } else if (type == 'add') {

        }
    }

    $.fn.initUpload = function(type) {
        const files = $('#attachment')[0].files;
        $('#new-attachments').collapse('show');

        $.each(files, function(i, file) {
            uploadFile(file, type);
        });
    }
});
