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
		<legend>Flex Database Setup &amp; Installation</legend>
		
		<input type="hidden" name="setupSubmit" value="yes" />
		
		<label for="dbname">Database Name <small>for Flex Installation</small></label>
		<input type="text" name="dbname" <?php if ($this->submit) { if (!$dbname) echo 'class="req" '; else echo 'value="'.$_POST['dbname'].'" '; }?>/>
		
		<label for="dbprfx">Table Prefix</label>
		<input type="text" name="dbprfx" <?php if ($this->submit) { if (!$dbprfx) echo 'class="req" '; else echo 'value="'.$_POST['dbprfx'].'" '; } else {echo 'value="flx_" ';}?>/>
		
		<label for="dbuser">Database User</label>
		<input type="text" name="dbuser" <?php if ($this->submit) { if (!$dbuser) echo 'class="req" '; else echo 'value="'.$_POST['dbuser'].'" '; }?>/>
		
		<label for="dbpass">Database Password</label>
		<input type="password" name="dbpass" <?php if ($this->submit) { echo 'value="'.$_POST['dbpass'].'" '; }?>/>
		
		<label for="dbhost">Database HostName</label>
		<input type="text" name="dbhost" <?php if ($this->submit) { if (!$dbhost) echo 'class="req" '; else echo 'value="'.$_POST['dbhost'].'" '; }?>/>
		
		<label for="dbchar">Database Charset</label>
		<input type="text" name="dbchar" <?php if ($this->submit) { if (!$dbchar) echo 'class="req" '; else echo 'value="'.$_POST['dbchar'].'" '; } else {echo 'value="utf8" ';}?>/>
		
		<label for="dbcoll">Database Collate type <small>(typically blank)</small></label>
		<input type="text" name="dbcoll" <?php if ($this->submit) { echo 'value="'.$_POST['dbcoll'].'" '; }?>/>
		
		<input id="next" type="submit" value="Install" />
		
		<?php if ($this->submit) {?>
			<?php if ($this->dberror == true) {?>	
				<p class="error">ERROR. Could not connect to Database. Verify Info.</p>
			<?php } else { ?>
				<p class="error">ERROR. Please fill out form elements outlined in red.</p>
			<?php }?>
		<?php }?>

	</fieldset>
</form>
</body>
</html>