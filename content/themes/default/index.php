<?php the_header(); ?>	
	
	<?php
		hook('testHook');
	?>

	<?php
		$posts = getPosts(); 
		if($posts) { foreach ($posts as $post) { 
	?>
		<h2><?php post('name'); ?></h2>
		<p><?php post('main content'); ?></p>
		
		<h3>This post's (<?php post('name'); ?>) Data</h3>
		<p>
			<b>ID: </b> <?php post('id'); ?><br />
			<b>slug: </b> <?php post('slug'); ?><br />
			<b>date: </b> <?php post('date'); ?><br />
			<b>postType: </b> <?php post('type'); ?><br />
			<?php if(post('taxonomy terms')){ ?><b>terms: </b> <?php print_r(unserialize(post('taxonomy terms', false))); ?><br /><?php } ?>
		</p>
		
		<hr />
		
	<?php }} else { ?>
		No posts by your criteria were found...
	<?php } ?>	
	
		<p>
			<b>Site Info: </b><br />
			<b>Url: </b> <?php siteinfo('url'); ?><br />
			<b>Name: </b> <?php siteinfo('name'); ?><br />
			<b>Desc: </b> <?php siteinfo('description'); ?><br />
			<b>Admin: </b> <?php siteinfo('admin'); ?><br />
			<b>Theme: </b> <?php siteinfo('theme'); ?><br />
		</p>
		<p>
			<b>User Info: </b><br />
			<b>ID: </b> <?php userinfo('id'); ?><br />
			<b>Name: </b> <?php userinfo('name'); ?><br />
			<b>Email: </b> <?php userinfo('email'); ?><br />
			<b>User Level: </b> <?php userinfo('user level'); ?><br />
		</p>
		<p>
			<b>Template: </b> <?php echo $template; ?>
		</p>
		<p>
			<b>URL parser: </b> <?php global $Flex; print_r($Flex->parseUrl()); ?>
		</p>
	
<?php the_footer(); ?>