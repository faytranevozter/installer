<h1 class="step-title"><?php echo $title ?></h1>
<form action="<?php echo $nextUrl ?>" connect-action="<?php echo $checkUrl ?>" method="post" accept="UTF-8" id="databaseForm">
	
	<?php echo $form ?>
	
	<div class="notice notice-error hide" id="InfoMessage"></div>

	<button id="connectBtn" class="button l-s" type="button">Connect</button>
	<a href="<?php echo $nextUrl ?>" id="nextBtn" class="button l-s kick-right disabled" type="submit">Next</a>
</form>