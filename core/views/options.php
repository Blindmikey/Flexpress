<?php
/*
 *	Installation for Flex.
 */
if ($_SERVER["PHP_SELF"] == $_SERVER['REQUEST_URI']) die ("Tsk Tsk. Nice Try.");
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Flex</title>
	<link rel="stylesheet" href="<?php echo $_SERVER['REQUEST_URI']; ?>core/styles/admin.css" />
	<script src="<?php echo $_SERVER['REQUEST_URI']; ?>core/styles/js/jquery.js"></script>
	<script>
		$(function() {
			<?php if (!$this->submit) {?>
			$('form').css('display','none');
			$('#spinner').css('display','block').css('opacity', 0).animate({opacity: 1}, 500).delay(1500).animate({opacity: 0}, 500, function() {
				$(this).css('display','none');
				$('form').css('display','block');
			});
			<?php } ?>
			$('form').css('opacity', 0)<?php if (!$this->submit) {?>.delay(2800)<?php } else {?>.css('marginTop', '10px')<?php } ?>.animate({opacity: 1, marginTop: '20px'}, 500, function() {
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
	<div id="spinner"></div>
	<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
	<fieldset>
		<legend>Flex Configuration Options</legend>
		
		<?php if (!$this->submit) {?>
			<p class="success">Database Connection Successful</p>
		<?php }?>
		
		<input type="hidden" name="optionsSubmit" value="yes" />
		
		<label for="sitename">Site Name</label>
		<input type="text" name="sitename" <?php if ($this->submit) { if (!$sitename) echo 'class="req" '; else echo 'value="'.$_POST['sitename'].'" '; }?>/>
		
		<label for="sitedesc">Site Description</label>
		<input type="text" name="sitedesc" <?php if ($this->submit) { if (!$sitedesc) echo 'class="req" '; else echo 'value="'.$_POST['sitedesc'].'" '; }?>/>
		
		<label for="adminname">Your Name</label>
		<input type="text" name="adminname" <?php if ($this->submit) { if (!$adminname) echo 'class="req" '; else echo 'value="'.$_POST['adminname'].'" '; }?>/>
		
		<label for="adminemail">Your Email</label>
		<input type="text" name="adminemail" <?php if ($this->submit) { if (!$adminemail) echo 'class="req" '; else echo 'value="'.$_POST['adminemail'].'" '; }?>/>
		
		<label for="adminlogin">Admin Login Username</label>
		<input type="text" name="adminlogin" <?php if ($this->submit) { if (!$adminlogin) echo 'class="req" '; else echo 'value="'.$_POST['adminlogin'].'" '; }?>/>
		
		<label for="adminpass">Admin Password</label>
		<input type="password" name="adminpass" <?php if ($this->submit) { if (!$adminpass || $this->passcheck == false) echo 'class="req" '; }?>/>
		
		<label for="adminpasscheck">Admin Password Verify</label>
		<input type="password" name="adminpasscheck" <?php if ($this->submit) { if (!$adminpasscheck || $this->passcheck == false) echo 'class="req" '; }?>/>
		
		<input id="next" type="submit" value="Finish" />
		
		<?php if ($this->submit) {?>
			<?php if ($this->passcheck == false) {?>	
				<p class="error">ERROR. Your 'Admin Password Verify' does not match.</p>
			<?php } else { ?>
				<p class="error">ERROR. Please fill out form elements outlined in red.</p>
			<?php }?>
		<?php }?>

	</fieldset>
</form>
</body>
</html>