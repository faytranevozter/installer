$(document).ready(function() {
	var msg, allowStep2=false;
	/* DISABLE CLICK "DISABLED" CLASS */
	$('body').on('click', 'a.disabled', function(e) {
		e.preventDefault();
		return false;
	});

	/* CHECK CONNECTION DATABASE */
	$('#connectBtn').click(function(){
		$.ajax({
			url: $('#databaseForm').attr('connect-action'),
			type: $('#databaseForm').attr('method'),
			data: $('#databaseForm').serialize(),
			beforeSend: function() {
				$('#connectBtn').addClass('disabled');
				$('#InfoMessage').removeClass('notice-error notice-success').addClass('notice-info').text('Loading...').fadeIn(500);
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
					msg = data.error.msg;
					$('#InfoMessage').addClass('notice-error').removeClass('notice-info notice-success');
					$('#InfoMessage').fadeIn(500, function(){
						$('#InfoMessage').html("<strong>Error :</strong> <br>" + msg);
					});
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

	$('#host, #user, #password, #database').keydown(function(){
		allowStep2 = false;
		$('#nextBtn').addClass('disabled');
		$('#InfoMessage').fadeOut(500);
	});

	$('#databaseForm').submit(function(){
		if (!allowStep2) {
			return false;
		}
	});
});