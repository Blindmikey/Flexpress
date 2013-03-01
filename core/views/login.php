<?php
/*
 *	Login for Flex.
 */
if ($_SERVER["PHP_SELF"] == $_SERVER['REQUEST_URI']) die ("Tsk Tsk. Nice Try.");
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Flex</title>
	<link rel="stylesheet" href="<?php echo $this->siteinfo('url'); ?>core/styles/admin.css" />
	<script src="<?php echo $this->siteinfo('url'); ?>core/styles/js/jquery.js"></script>
	<script>
		$(function() {
			$('form').css('opacity', 0)<?php if ($this->submit) {?>.css('marginTop', '10px')<?php } ?>.animate({opacity: 1, marginTop: '20px'}, 500, function() {
				$('#next').click(function(e){
					e.preventDefault();
					$('form').animate({opacity: 0, marginTop: '10px'}, 500, function() {
						$('form').submit();
					});
				});
			});
		});
	</script>
</head>
<body class="install">
	<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
	<fieldset>
		<legend>Flex Login</legend>
		
		<input type="hidden" name="loginSubmit" value="yes" />
		
		<label for="username">Username </label>
		<input type="text" name="username" <?php if ($this->submit) { if (!$username) echo 'class="req" '; else echo 'value="'.$_POST['username'].'" '; }?>/>
		
		<label for="userpass">Password</label>
		<input type="password" name="userpass" <?php if ($this->submit) { if (!$userpass) echo 'class="req" '; else echo 'value="'.$_POST['userpass'].'" '; }?>/>
		
		<input id="next" type="submit" value="Login" />
		
		<?php if ($this->submit) {?>
			<?php if ($this->loginInvalid == true) {?>	
				<p class="error">ERROR. Not Valid Login Credentials.</p>
			<?php } else { ?>
				<p class="error">ERROR. Please fill out form elements outlined in red.</p>
			<?php }?>
		<?php }?>

	</fieldset>
</form>
</body>
</html>