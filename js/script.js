$(document).ready(function() {
	/* CHECK CONNECTION DATABASE */
	$('#databaseForm').submit(function(){
		$.ajax({
			url: $(this).attr('action'),
			type: $(this).attr('method'),
			data: $(this).serialize(),
			beforeSend: function() {
				$('#connectBtn').addClass('disabled');
				$('#InfoMessage').addClass('hide');
			},
			dataType: 'json',
			cache: false,
			success: function(data) {
				$('#connectBtn').removeClass('disabled');
				// alert(data);
				if (data.success) {
					alert('success');
				} else {
					$('#InfoMessage').removeClass('hide').text(data.error.msg);
					// alert(data.error.code + ' = ' + data.error.msg);
				}

			// {"error":"1049 = Unknown database 'ada'"}
			// {"error":"1045 = Access denied for user 'root'@'localhost' (using password: YES)"} pss salah
			// {"error":"1045 = Access denied for user 'fafa'@'localhost' (using password: YES)"}
			// {"error":"1045 = Access denied for user 'fafa'@'localhost' (using password: NO)"}
			// {"error":"2002 = php_network_getaddresses: getaddrinfo failed: Name or service not known"}
			}
		});
		return false;
	});	
});