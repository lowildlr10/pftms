$(function() {
	var percentCompleted = 0;
	var totalProcess = 15;
	var multiplier = 100 / totalProcess;
	var ajaxRequest;
	var elapsedTime;
	var time = "[" + moment().format('H:mm:ss') + "] : ";
	var logs = "[" + moment().format('H:mm:ss') + "] : " + "Ready.\n";

	$('#txt-logs').text(logs);
	$('#txt-logs').scrollTop($('#txt-logs')[0].scrollHeight);

	function logDisplay(_logs) {
		$('#txt-logs').text(logs);
		$('#txt-logs').scrollTop($('#txt-logs')[0].scrollHeight);
	}

	function btnToggleAbort() {
		$('#btn-migrate').html('<i class="fas fa-ban text-danger"></i> Abort')
						 .attr('onclick', '$(this).abort();');
		$('#btn-icon').removeClass('fas fa-database')
					  .addClass('fas fa-spinner faa-spin fa-spin');
	}

	function btnToggleMigrate() {
		$('#btn-migrate').html('<i class="fas fa-database"></i> Migrate')
						 .attr('onclick', '$(this).migrate();');
		$('#btn-icon').removeClass('fas fa-spinner faa-spin fa-spin')
					  .addClass('fas fa-database');
	}

	function importTempPIS(file, servername, username, password) {
		var postData = new FormData();

		btnToggleAbort();

		logs += "[" + moment().format('H:mm:ss') + "] : " + "Migration from PIS to PFMS has started...\n";
		logDisplay(logs);

		percentCompleted = 1 * multiplier;
		$('#migrate-progress').css('width', percentCompleted.toFixed(2) + '%')
							  .text(percentCompleted.toFixed(2) + '%');

	    postData.append('_token', $('meta[name=csrf-token]').attr('content'));
	    postData.append('file', file);
	    postData.append('servername', servername);
	    postData.append('username', username);
	    postData.append('password', password);

	    ajaxRequest = $.ajax({
	        url: 'migrator/temp-pis-import',
	        type: 'POST',
	        async: true,
	        contentType: false,
	        processData: false,
	        data: postData,
	        success: function(result) {
	        	logs += "[" + moment().format('H:mm:ss') + "] : Successfully created the PIS temporary database.\n";
				logDisplay(logs);

				migrateEmployee();
	        },
	        error: function(xhr, result, errorThrown){
	            logs += "[" + moment().format('H:mm:ss') + "] : Encountered an error on the creation of PIS temporary database.\n";
	            logs += "[" + moment().format('H:mm:ss') + "] : Aborted.\n";
				logDisplay(logs);

				$('#migrate-progress').removeClass('progress-bar-striped active')
									  .addClass('bg-danger')
							  	      .css('width', '100%')
							  	      .text('Aborted.');

				btnToggleMigrate();
	        }
        });
	}

	function migrateEmployee() {
		var postData = new FormData();
		postData.append('_token', $('meta[name=csrf-token]').attr('content'));

		logs += "[" + moment().format('H:mm:ss') + "] : " + "Migrating the Employee module data from PIS to PFMS.\n";
		logDisplay(logs);

		percentCompleted = 2 * multiplier;
		$('#migrate-progress').css('width', percentCompleted.toFixed(2) + '%')
							  .text(percentCompleted.toFixed(2) + '%');

		ajaxRequest = $.ajax({
	        url: 'migrator/migrate-data-modules/employee',
	        type: 'POST',
	        async: true,
	        contentType: false,
	        processData: false,
	        data: postData,
	        success: function(result) {
	        	logs += "[" + moment().format('H:mm:ss') + "] : " + "Successfully migrated the Employee module data.\n";
				logDisplay(logs);

				migrateSignatory();
	        },
	        error: function(xhr, result, errorThrown){
	            logs += "[" + moment().format('H:mm:ss') + "] : " + "Encountered error on the migration of Employee module data.\n";
	            logs += "[" + moment().format('H:mm:ss') + "] : Aborted.\n";
				logDisplay(logs);

				$('#migrate-progress').removeClass('progress-bar-striped active')
									  .addClass('bg-danger')
							  	      .css('width', '100%')
							  	      .text('Aborted.');

				btnToggleMigrate();
	        }
        });
	}

	function migrateSignatory() {
		var postData = new FormData();
		postData.append('_token', $('meta[name=csrf-token]').attr('content'));

		logs += "[" + moment().format('H:mm:ss') + "] : " + "Migrating the Signatory module data from PIS to PFMS.\n";
		logDisplay(logs);

		percentCompleted = 3 * multiplier;
		$('#migrate-progress').css('width', percentCompleted.toFixed(2) + '%')
							  .text(percentCompleted.toFixed(2) + '%');

		ajaxRequest = $.ajax({
	        url: 'migrator/migrate-data-modules/signatory',
	        type: 'POST',
	        async: true,
	        contentType: false,
	        processData: false,
	        data: postData,
	        success: function(result) {
	        	logs += "[" + moment().format('H:mm:ss') + "] : " + "Successfully migrated the Signatory module data.\n";
				logDisplay(logs);

				migrateSupplierClass();
	        },
	        error: function(xhr, result, errorThrown){
	            logs += "[" + moment().format('H:mm:ss') + "] : " + "Encountered error on the migration of Signatory module data.\n";
	            logs += "[" + moment().format('H:mm:ss') + "] : Aborted.\n";
				logDisplay(logs);

				$('#migrate-progress').removeClass('progress-bar-striped active')
									  .addClass('bg-danger')
							  	      .css('width', '100%')
							  	      .text('Aborted.');

				btnToggleMigrate();
	        }
        });
	}

	function migrateSupplierClass() {
		var postData = new FormData();
		postData.append('_token', $('meta[name=csrf-token]').attr('content'));

		logs += "[" + moment().format('H:mm:ss') + "] : " + "Migrating the Supplier Classification module data from PIS to PFMS.\n";
		logDisplay(logs);

		percentCompleted = 4 * multiplier;
		$('#migrate-progress').css('width', percentCompleted.toFixed(2) + '%')
							  .text(percentCompleted.toFixed(2) + '%');

		ajaxRequest = $.ajax({
	        url: 'migrator/migrate-data-modules/supplier-classification',
	        type: 'POST',
	        async: true,
	        contentType: false,
	        processData: false,
	        data: postData,
	        success: function(result) {
	        	logs += "[" + moment().format('H:mm:ss') + "] : " + "Successfully migrated the Supplier Classification module data.\n";
				logDisplay(logs);

				migrateSupplier();
	        },
	        error: function(xhr, result, errorThrown){
	            logs += "[" + moment().format('H:mm:ss') + "] : " + "Encountered error on the migration of Supplier Classification module data.\n";
	            logs += "[" + moment().format('H:mm:ss') + "] : Aborted.\n";
				logDisplay(logs);

				$('#migrate-progress').removeClass('progress-bar-striped active')
									  .addClass('bg-danger')
							  	      .css('width', '100%')
							  	      .text('Aborted.');

				btnToggleMigrate();
	        }
        });
	}

	function migrateSupplier() {
		var postData = new FormData();
		postData.append('_token', $('meta[name=csrf-token]').attr('content'));

		logs += "[" + moment().format('H:mm:ss') + "] : " + "Migrating the Supplier module data from PIS to PFMS.\n";
		logDisplay(logs);

		percentCompleted = 5 * multiplier;
		$('#migrate-progress').css('width', percentCompleted.toFixed(2) + '%')
							  .text(percentCompleted.toFixed(2) + '%');

		ajaxRequest = $.ajax({
	        url: 'migrator/migrate-data-modules/supplier',
	        type: 'POST',
	        async: true,
	        contentType: false,
	        processData: false,
	        data: postData,
	        success: function(result) {
	        	logs += "[" + moment().format('H:mm:ss') + "] : " + "Successfully migrated the Supplier module data.\n";
				logDisplay(logs);

				migrateUnitIssue();
	        },
	        error: function(xhr, result, errorThrown){
	            logs += "[" + moment().format('H:mm:ss') + "] : " + "Encountered error on the migration of Supplier module data.\n";
	            logs += "[" + moment().format('H:mm:ss') + "] : Aborted.\n";
				logDisplay(logs);

				$('#migrate-progress').removeClass('progress-bar-striped active')
									  .addClass('bg-danger')
							  	      .css('width', '100%')
							  	      .text('Aborted.');

				btnToggleMigrate();
	        }
        });
	}

	function migrateUnitIssue() {
		var postData = new FormData();
		postData.append('_token', $('meta[name=csrf-token]').attr('content'));

		logs += "[" + moment().format('H:mm:ss') + "] : " + "Migrating the Unit issue module data from PIS to PFMS.\n";
		logDisplay(logs);

		percentCompleted = 6 * multiplier;
		$('#migrate-progress').css('width', percentCompleted.toFixed(2) + '%')
							  .text(percentCompleted.toFixed(2) + '%');

		ajaxRequest = $.ajax({
	        url: 'migrator/migrate-data-modules/unit-issue',
	        type: 'POST',
	        async: true,
	        contentType: false,
	        processData: false,
	        data: postData,
	        success: function(result) {
	        	logs += "[" + moment().format('H:mm:ss') + "] : " + "Successfully migrated the Unit issue module data.\n";
				logDisplay(logs);

				migratePR();
	        },
	        error: function(xhr, result, errorThrown){
	            logs += "[" + moment().format('H:mm:ss') + "] : " + "Encountered error on the migration of Unit issue module data.\n";
	            logs += "[" + moment().format('H:mm:ss') + "] : Aborted.\n";
				logDisplay(logs);

				$('#migrate-progress').removeClass('progress-bar-striped active')
									  .addClass('bg-danger')
							  	      .css('width', '100%')
							  	      .text('Aborted.');

				btnToggleMigrate();
	        }
        });
	}

	function migratePR() {
		var postData = new FormData();
		postData.append('_token', $('meta[name=csrf-token]').attr('content'));

		logs += "[" + moment().format('H:mm:ss') + "] : " + "Migrating the PR module data from PIS to PFMS.\n";
		logDisplay(logs);

		percentCompleted = 7 * multiplier;
		$('#migrate-progress').css('width', percentCompleted.toFixed(2) + '%')
							  .text(percentCompleted.toFixed(2) + '%');

		ajaxRequest = $.ajax({
	        url: 'migrator/migrate-data-modules/pr',
	        type: 'POST',
	        async: true,
	        contentType: false,
	        processData: false,
	        data: postData,
	        success: function(result) {
	        	logs += "[" + moment().format('H:mm:ss') + "] : " + "Successfully migrated the PR module data.\n";
				logDisplay(logs);

				migrateRFQ();
	        },
	        error: function(xhr, result, errorThrown){
	            logs += "[" + moment().format('H:mm:ss') + "] : " + "Encountered error on the migration of PR module data.\n";
	            logs += "[" + moment().format('H:mm:ss') + "] : Aborted.\n";
				logDisplay(logs);

				$('#migrate-progress').removeClass('progress-bar-striped active')
									  .addClass('bg-danger')
							  	      .css('width', '100%')
							  	      .text('Aborted.');

				btnToggleMigrate();
	        }
        });
	}

	function migrateRFQ() {
		var postData = new FormData();
		postData.append('_token', $('meta[name=csrf-token]').attr('content'));

		logs += "[" + moment().format('H:mm:ss') + "] : " + "Migrating the RFQ module data from PIS to PFMS.\n";
		logDisplay(logs);

		percentCompleted = 8 * multiplier;
		$('#migrate-progress').css('width', percentCompleted.toFixed(2) + '%')
							  .text(percentCompleted.toFixed(2) + '%');

		ajaxRequest = $.ajax({
	        url: 'migrator/migrate-data-modules/rfq',
	        type: 'POST',
	        async: true,
	        contentType: false,
	        processData: false,
	        data: postData,
	        success: function(result) {
	        	logs += "[" + moment().format('H:mm:ss') + "] : " + "Successfully migrated the RFQ module data.\n";
				logDisplay(logs);

				migrateAbstract();
	        },
	        error: function(xhr, result, errorThrown){
	            logs += "[" + moment().format('H:mm:ss') + "] : " + "Encountered error on the migration of RFQ module data.\n";
	            logs += "[" + moment().format('H:mm:ss') + "] : Aborted.\n";
				logDisplay(logs);

				$('#migrate-progress').removeClass('progress-bar-striped active')
									  .addClass('bg-danger')
							  	      .css('width', '100%')
							  	      .text('Aborted.');

				btnToggleMigrate();
	        }
        });
	}

	function migrateAbstract() {
		var postData = new FormData();
		postData.append('_token', $('meta[name=csrf-token]').attr('content'));

		logs += "[" + moment().format('H:mm:ss') + "] : " + "Migrating the Abstract module data from PIS to PFMS.\n";
		logDisplay(logs);

		percentCompleted = 9 * multiplier;
		$('#migrate-progress').css('width', percentCompleted.toFixed(2) + '%')
							  .text(percentCompleted.toFixed(2) + '%');

		ajaxRequest = $.ajax({
	        url: 'migrator/migrate-data-modules/abstract',
	        type: 'POST',
	        async: true,
	        contentType: false,
	        processData: false,
	        data: postData,
	        success: function(result) {
	        	logs += "[" + moment().format('H:mm:ss') + "] : " + "Successfully migrated the Abstract module data.\n";
				logDisplay(logs);

				migratePO_JO();
	        },
	        error: function(xhr, result, errorThrown){
	            logs += "[" + moment().format('H:mm:ss') + "] : " + "Encountered error on the migration of Abstract module data.\n";
	            logs += "[" + moment().format('H:mm:ss') + "] : Aborted.\n";
				logDisplay(logs);

				$('#migrate-progress').removeClass('progress-bar-striped active')
									  .addClass('bg-danger')
							  	      .css('width', '100%')
							  	      .text('Aborted.');

				btnToggleMigrate();
	        }
        });
	}

	function migratePO_JO() {
		var postData = new FormData();
		postData.append('_token', $('meta[name=csrf-token]').attr('content'));

		logs += "[" + moment().format('H:mm:ss') + "] : " + "Migrating the PO/JO module data from PIS to PFMS.\n";
		logDisplay(logs);

		percentCompleted = 10 * multiplier;
		$('#migrate-progress').css('width', percentCompleted.toFixed(2) + '%')
							  .text(percentCompleted.toFixed(2) + '%');

		ajaxRequest = $.ajax({
	        url: 'migrator/migrate-data-modules/po-jo',
	        type: 'POST',
	        async: true,
	        contentType: false,
	        processData: false,
	        data: postData,
	        success: function(result) {
	        	logs += "[" + moment().format('H:mm:ss') + "] : " + "Successfully migrated the PO/JO module data.\n";
				logDisplay(logs);

				migrateORS_BURS();
	        },
	        error: function(xhr, result, errorThrown){
	            logs += "[" + moment().format('H:mm:ss') + "] : " + "Encountered error on the migration of PO/JO module data.\n";
	            logs += "[" + moment().format('H:mm:ss') + "] : Aborted.\n";
				logDisplay(logs);

				$('#migrate-progress').removeClass('progress-bar-striped active')
									  .addClass('bg-danger')
							  	      .css('width', '100%')
							  	      .text('Aborted.');

				btnToggleMigrate();
	        }
        });
	}

	function migrateORS_BURS() {
		var postData = new FormData();
		postData.append('_token', $('meta[name=csrf-token]').attr('content'));

		logs += "[" + moment().format('H:mm:ss') + "] : " + "Migrating the ORS/BURS module data from PIS to PFMS.\n";
		logDisplay(logs);

		percentCompleted = 11 * multiplier;
		$('#migrate-progress').css('width', percentCompleted.toFixed(2) + '%')
							  .text(percentCompleted.toFixed(2) + '%');

		ajaxRequest = $.ajax({
	        url: 'migrator/migrate-data-modules/ors-burs',
	        type: 'POST',
	        async: true,
	        contentType: false,
	        processData: false,
	        data: postData,
	        success: function(result) {
	        	logs += "[" + moment().format('H:mm:ss') + "] : " + "Successfully migrated the ORS/BURS module data.\n";
				logDisplay(logs);

				migrateIAR();
	        },
	        error: function(xhr, result, errorThrown){
	            logs += "[" + moment().format('H:mm:ss') + "] : " + "Encountered error on the migration of ORS/BURS module data.\n";
	            logs += "[" + moment().format('H:mm:ss') + "] : Aborted.\n";
				logDisplay(logs);

				$('#migrate-progress').removeClass('progress-bar-striped active')
									  .addClass('bg-danger')
							  	      .css('width', '100%')
							  	      .text('Aborted.');

				btnToggleMigrate();
	        }
        });
	}

	function migrateIAR() {
		var postData = new FormData();
		postData.append('_token', $('meta[name=csrf-token]').attr('content'));

		logs += "[" + moment().format('H:mm:ss') + "] : " + "Migrating the IAR module data from PIS to PFMS.\n";
		logDisplay(logs);

		percentCompleted = 12 * multiplier;
		$('#migrate-progress').css('width', percentCompleted.toFixed(2) + '%')
							  .text(percentCompleted.toFixed(2) + '%');

		ajaxRequest = $.ajax({
	        url: 'migrator/migrate-data-modules/iar',
	        type: 'POST',
	        async: true,
	        contentType: false,
	        processData: false,
	        data: postData,
	        success: function(result) {
	        	logs += "[" + moment().format('H:mm:ss') + "] : " + "Successfully migrated the IAR module data.\n";
				logDisplay(logs);

				migrateDV();
	        },
	        error: function(xhr, result, errorThrown){
	            logs += "[" + moment().format('H:mm:ss') + "] : " + "Encountered error on the migration of IAR module data.\n";
	            logs += "[" + moment().format('H:mm:ss') + "] : Aborted.\n";
				logDisplay(logs);

				$('#migrate-progress').removeClass('progress-bar-striped active')
									  .addClass('bg-danger')
							  	      .css('width', '100%')
							  	      .text('Aborted.');

				btnToggleMigrate();
	        }
        });
	}

	function migrateDV() {
		var postData = new FormData();
		postData.append('_token', $('meta[name=csrf-token]').attr('content'));

		logs += "[" + moment().format('H:mm:ss') + "] : " + "Migrating the DV module data from PIS to PFMS.\n";
		logDisplay(logs);

		percentCompleted = 13 * multiplier;
		$('#migrate-progress').css('width', percentCompleted.toFixed(2) + '%')
							  .text(percentCompleted.toFixed(2) + '%');

		ajaxRequest = $.ajax({
	        url: 'migrator/migrate-data-modules/dv',
	        type: 'POST',
	        async: true,
	        contentType: false,
	        processData: false,
	        data: postData,
	        success: function(result) {
	        	logs += "[" + moment().format('H:mm:ss') + "] : " + "Successfully migrated the DV module data.\n";
				logDisplay(logs);

				migrateStock();
	        },
	        error: function(xhr, result, errorThrown){
	            logs += "[" + moment().format('H:mm:ss') + "] : " + "Encountered error on the migration of DV module data.\n";
	            logs += "[" + moment().format('H:mm:ss') + "] : Aborted.\n";
				logDisplay(logs);

				$('#migrate-progress').removeClass('progress-bar-striped active')
									  .addClass('bg-danger')
							  	      .css('width', '100%')
							  	      .text('Aborted.');

				btnToggleMigrate();
	        }
        });
	}

	function migrateStock() {
		var postData = new FormData();
		postData.append('_token', $('meta[name=csrf-token]').attr('content'));

		logs += "[" + moment().format('H:mm:ss') + "] : " + "Migrating the Inventory Stock module data from PIS to PFMS.\n";
		logDisplay(logs);

		percentCompleted = 14 * multiplier;
		$('#migrate-progress').css('width', percentCompleted.toFixed(2) + '%')
							  .text(percentCompleted.toFixed(2) + '%');

		ajaxRequest = $.ajax({
	        url: 'migrator/migrate-data-modules/stock',
	        type: 'POST',
	        async: true,
	        contentType: false,
	        processData: false,
	        data: postData,
	        success: function(result) {
	        	logs += "[" + moment().format('H:mm:ss') + "] : " + "Successfully migrated the Inventory Stock module data.\n";
				logDisplay(logs);

				deleteTemp();
	        },
	        error: function(xhr, result, errorThrown){
	            logs += "[" + moment().format('H:mm:ss') + "] : " + "Encountered error on the migration of Inventory Stock module data.\n";
	            logs += "[" + moment().format('H:mm:ss') + "] : Aborted.\n";
				logDisplay(logs);

				$('#migrate-progress').removeClass('progress-bar-striped active')
									  .addClass('bg-danger')
							  	      .css('width', '100%')
							  	      .text('Aborted.');

				btnToggleMigrate();
	        }
        });
	}

	function deleteTemp() {
		var postData = new FormData();
		var servername = $('#servername').val();
		var username = $('#username').val();
		var password = $('#password').val();

		postData.append('_token', $('meta[name=csrf-token]').attr('content'));
		postData.append('servername', servername);
	    postData.append('username', username);
	    postData.append('password', password);

		logs += "[" + moment().format('H:mm:ss') + "] : " + "Deleting temporary database.\n";
		logDisplay(logs);

		percentCompleted = 15 * multiplier;
		$('#migrate-progress').css('width', percentCompleted.toFixed(2) + '%')
							  .text(percentCompleted.toFixed(2) + '%');

		ajaxRequest = $.ajax({
	        url: 'migrator/migrate-data-modules/temp',
	        type: 'POST',
	        async: true,
	        contentType: false,
	        processData: false,
	        data: postData,
	        success: function(result) {
	        	logs += "[" + moment().format('H:mm:ss') + "] : " + "Successfully deleted temporary database.\n";
	        	logs += "[" + moment().format('H:mm:ss') + "] : " + "Migration from PIS to PFMS completed.\n";
				logDisplay(logs);

				//alert('Migration from PIS to PFMS completed.');

				$('#migrate-progress').removeClass('progress-bar-striped active')
							  	      .css('width', percentCompleted.toFixed(2) + '%')
							  	      .text('Complete');

				btnToggleMigrate();
	        },
	        error: function(xhr, result, errorThrown){
	            logs += "[" + moment().format('H:mm:ss') + "] : " + "Encountered error on the deletion of temporary database.\n";
	            logs += "[" + moment().format('H:mm:ss') + "] : Aborted.\n";
				logDisplay(logs);

				$('#migrate-progress').removeClass('progress-bar-striped active')
									  .addClass('bg-danger')
							  	      .css('width', '100%')
							  	      .text('Aborted.');

				btnToggleMigrate();
	        }
        });
	}

	$.fn.migrate = function() {
		var file = $('#db-file')[0].files;
		var servername = $('#servername').val();
		var username = $('#username').val();
		var password = $('#password').val();

		if (file.length > 0) {
			percentCompleted = 0;
			logs += "[" + moment().format('H:mm:ss') + "] : " + "Initializing sql file...\n";
			$('#txt-logs').text(logs);
			$('#txt-logs').scrollTop($('#txt-logs')[0].scrollHeight);

			$('#migrate-progress').addClass('progress-bar-striped active')
								  .removeClass('bg-danger')
								  .css('width', '100%')
							  	  .text('Starting...');		

			importTempPIS(file[0], servername, username, password);
		} else {
			alert('Insert a sql file.');
		}
	}

	$.fn.abort = function() {
		btnToggleMigrate();
		ajaxRequest.abort();
	}

	// Upload
	$('.btn-file-database :file').change(function() {
		var input = $(this),
			label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
			input.trigger('fileselect', [label]);
	});

	$('.btn-file-database :file').on('fileselect', function(event, label) {
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

	function readURL(input) {
	    if (input.files && input.files[0]) {
	        var reader = new FileReader();
	        
	        reader.readAsDataURL(input.files[0]);
	    }
	}

	$("#db-file").change(function(){
	    readURL(this);
	});
});