<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Install</title>
	<link rel="stylesheet" href="<?php echo base_url('css/install.css'); ?>">
</head>
<body>
	<div class="test-box">
		<form action="check.php" method="post" accept="UTF-8" id="databaseForm">
			<div class="group">
				<label for="host">Host</label>
				<input class="text-form" value="localhost" type="text" name="host" id="host" autocomplete="off" required>
				<span class="help">@hostname</span>
			</div>
			<div class="group">
				<label for="user">user</label>
				<input class="text-form" value="root" type="text" name="user" id="user" autocomplete="off" required>
				<span class="help">@username</span>
			</div>
			<div class="group">
				<label for="password">Password</label>
				<input class="text-form" type="password" name="password" id="password" autocomplete="off">
				<span class="help">@password</span>
			</div>
			<div class="group">
				<label for="database">Database Name</label>
				<input class="text-form" value="something" type="text" name="database" id="database" autocomplete="off" required>
				<span class="help">@examples</span>
			</div>

			<div class="notice notice-error hide" id="InfoMessage"></div>

			<button id="connectBtn" class="button l-s" type="button">Connect</button>
			<button id="nextBtn" class="button l-s kick-right disabled" type="submit">Next</button>
		</form>
	</div>

	<script src="<?php echo base_url('js/jquery-3.1.1.min.js') ?>"></script>
	<script src="<?php echo base_url('js/script.js') ?>"></script>
</body>
</html>