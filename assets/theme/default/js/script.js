$(document).ready(function() {
	var msg, allowStep2=false;
	/* CHECK CONNECTION DATABASE */
	$('#connectBtn').click(function(){
		$.ajax({
			url: $('#databaseForm').attr('action'),
			type: $('#databaseForm').attr('method'),
			data: $('#databaseForm').serialize(),
			beforeSend: function() {
				$('#connectBtn').addClass('disabled');
				$('#InfoMessage').removeClass('notice-error notice-success hide').addClass('notice-info').text('Loading...');
			},
			dataType: 'json',
			cache: false,
			success: function(data) {
				$('#connectBtn').removeClass('disabled');
				// alert(data);
				if (data.success) {
					allowStep2 = true;
					$('#nextBtn').removeClass('disabled');
					$('#InfoMessage').removeClass('notice-error notice-info').addClass('notice-success').text('Success');
				} else {
					if(data.error.code==1049) { 
						msg = data.error.msg;
					} else if(data.error.code==1045) {
						msg = "Wrong Username or Password";
					} else if(data.error.code==2002) {
						msg = "Name or service host not known";
					} else {
						msg = "Unknown Error.";
					}
					$('#InfoMessage').addClass('notice-error').removeClass('notice-info notice-success');
					$('#InfoMessage').removeClass('hide').html("<strong>Error :</strong> <br>" + msg);
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

	$('#databaseForm').submit(function(){
		if (!allowStep2) {
			return false;
		}
	});
});