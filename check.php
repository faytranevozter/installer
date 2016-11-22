<?php 

$msg = array();
if (isset($_POST['host'])) {
	$a = @new Mysqli($_POST['host'], $_POST['user'], $_POST['password'], $_POST['database']);
	// jika ada error
	if ($a->connect_errno != 0) {
		$msg['error']['code'] = $a->connect_errno;
		$msg['error']['msg'] = $a->connect_error;
	} else {
		$msg['success'] = true;
	}
} else {
	$msg['error'] = "nothing";
}

echo json_encode($msg);
