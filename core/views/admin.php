<?php 
/*
 *	Admin Dashboard for Flex.
 */
if ($_SERVER["PHP_SELF"] == $_SERVER['REQUEST_URI']) die ("Tsk Tsk. Nice Try.");
global $Flex; 
?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>FLEX | Dashboard</title>
	<link rel="stylesheet" href="<?php echo $this->siteinfo('url'); ?>core/styles/admin.css" />
	<script src="<?php echo $this->siteinfo('url'); ?>core/styles/js/jquery.js"></script>
	<script src="<?php echo $this->siteinfo('url'); ?>core/styles/js/jquery.dataTables.min.js"></script>
	<script src="<?php echo $this->siteinfo('url'); ?>core/styles/js/ColVis.min.js"></script>
	<script src="<?php echo $this->siteinfo('url'); ?>core/styles/js/ColReorder.min.js"></script>
	<style type="text/css">
		html {height:100%; overflow-y: hidden;}
	</style>
	<?php if(isset($args['type']) && isset($args['action'])){ ?>
	<script type="text/javascript">
		$(function() {
			$('nav .<?php echo $args['type']; ?>').addClass('nav-current');
			$('nav .flex-<?php echo $args['type']; ?>').addClass('nav-current');
			$('nav .<?php echo $args['action']; ?>-<?php echo $args['type']; ?>').addClass('nav-current');
			$('#sort').dataTable( {
				"sDom": 'C<"clear">lfrtip',
				"sDom": 'Rlfrtip',
				"bStateSave": true
			});
		});
	</script>
	<?php } ?>
</head>
<body class="dashboard <?php if(isset($args['action'])){ echo ' ' . $args['action']; } ?>">
	<header>
		<hgroup>
			<h1><a href="<?php echo $this->siteinfo('url'); ?>admin/">FLEX</a></h1>
			<h2><?php if(!isset($args['action']) || $args['action'] == 'default') { echo 'Dashboard'; } elseif(isset($args['action']) && $args['action'] != 'default') { echo str_replace('-', ' ', $args['action']); } if(isset($args['type'])){ echo ' <span>:</span> ' . str_replace('-', ' ', $args['type']); } ?></h2>
		</hgroup>
	</header>
	<nav>
		<ul>
			<?php 
				$types = $Flex->get_postTypes();
				foreach ($types as $type) { if($type->post_type_name != 'user') { ?>
					<li class="<?php echo $type->post_type_uri; ?> <?php echo $type->post_type_name; ?>">
						<a href="<?php echo $this->siteinfo('url'); ?>admin/view/<?php echo $type->post_type_uri; ?>"><?php echo $type->post_type_uri; ?></a>
						<ul>
							<li class="add-<?php echo $type->post_type_name; ?>"><a href="<?php echo $this->siteinfo('url'); ?>admin/add/<?php echo $type->post_type_name; ?>">Add New <?php echo $type->post_type_name; ?></a></li>
							<li class="view-<?php echo $type->post_type_uri; ?>"><a href="<?php echo $this->siteinfo('url'); ?>admin/view/<?php echo $type->post_type_uri; ?>">View All <?php echo $type->post_type_uri; ?></a></li>
						</ul>
					</li>
				<?php }}
			?>
		</ul>
		<ul>
			<li class="flex-post-types flex-post-type">
				<a href="<?php echo $this->siteinfo('url'); ?>admin/view/post-types">Post Types</a>
				<ul>
					<li class="add-post-type"><a href="<?php echo $this->siteinfo('url'); ?>admin/add/post-type">Add New Post Type</a></li>
					<li class="view-post-types"><a href="<?php echo $this->siteinfo('url'); ?>admin/view/post-types">View All Post Types</a></li>
				</ul>
			</li>
			<li class="flex-data-templates flex-data-template">
				<a href="<?php echo $this->siteinfo('url'); ?>admin/view/data-templates">Data Templates</a>
				<ul>
					<li class="add-data-template"><a href="<?php echo $this->siteinfo('url'); ?>admin/add/data-template">Add New Data Template</a></li>
					<li class="view-data-templates"><a href="<?php echo $this->siteinfo('url'); ?>admin/view/data-templates">View All Data Templates</a></li>
				</ul>
			</li>
			<li class="flex-data-types flex-data-type">
				<a href="<?php echo $this->siteinfo('url'); ?>admin/view/data-types">Data Types</a>
				<ul>
					<li class="add-data-type"><a href="<?php echo $this->siteinfo('url'); ?>admin/add/data-type">Add New Data Type</a></li>
					<li class="view-data-types"><a href="<?php echo $this->siteinfo('url'); ?>admin/view/data-types">View All Data Types</a></li>
				</ul>
			</li>
		</ul>
		<ul>
			<li class="flex-menus"><a href="<?php echo $this->siteinfo('url'); ?>admin/edit/menus">Menus</a></li>
			<li class="flex-themes"><a href="<?php echo $this->siteinfo('url'); ?>admin/edit/themes">Themes</a></li>
			<li class="flex-users flex-user">
				<a href="<?php echo $this->siteinfo('url'); ?>admin/view/users">Users</a>
				<ul>
					<li class="add-user"><a href="<?php echo $this->siteinfo('url'); ?>admin/add/user">Add New User</a></li>
					<li class="view-users"><a href="<?php echo $this->siteinfo('url'); ?>admin/view/users">View All Users</a></li>
				</ul>
			</li>
			<li class="flex-options"><a href="<?php echo $this->siteinfo('url'); ?>admin/edit/options">Site Options</a></li>
			<!--<li class="flex-____"><a href="<?php echo $this->siteinfo('url'); ?>admin/edit/____">____</a></li>-->
		</ul>
	</nav>
	<div id="content">
		
		<!--<p>URL: <?php echo'<pre>';print_r($Flex->parseUrl(null, true));echo'</pre>'; ?></p>-->
		
		<?php 
			//
			// VIEW
			//
			if(isset($args['action']) && isset($args['type']) && $args['action'] == 'view') { 
		?>
	
			<div id="main" class="view">
			
				<?php
					if($Flex->postType_exists($args['type'])) {
				?>
				<table id="sort" class="<?php echo $args['type']; ?>">
					<thead>
						<tr>
							<th>ID</th>
							<th>Name</th>
							<th>Slug</th>	<?php //NOTE: SLUG - IS NOT APPLICABLE FOR LISTING POST-TYPES OR DATA-TEMPLATES OR DATA-TYPES ?>
							<th>Date Created</th>	<?php //NOTE: DATE - IS NOT APPLICABLE FOR LISTING POST-TYPES OR DATA-TEMPLATES OR DATA-TYPES ?>
							<?php 
								$templates = $Flex->get_dataTemplates($Flex->postTypeNameFromURI($args['type']));
								foreach($templates as $template) {
							?>
								<th class="hidden"><?php echo $template; ?></th>
							<?php 
								}
							?>
						</tr>
					</thead>
					<tbody>
					<?php	
						$results = $Flex->fetch($args['type']);
						if(is_array($results)) { foreach ($results as $result) { ?>
							<tr>
								<td>
									<span><?php echo $result['id']; ?></span>
								</td>
								<td>
									<a href="<?php echo $this->siteinfo('url'); ?>admin/edit/<?php echo $result['type']; ?>/<?php echo $result['id']; ?>"><?php echo $result['name']; ?></a>
								</td>
								<td>
									<span><?php echo $result['slug']; ?></span>
								</td>
								<td>
									<span><?php echo $result['date']; ?></span>
								</td>
								
								<?php 
									foreach($templates as $template) { // WILL NEED TO REPLACE THIS WITH FUNCTION THAT RETURNS DATA ASSOCIATED WITH TEMPLATE
								?>
									<td>
										
									</td>
								<?php 
									}
								?>
							</tr>
					<?php }} ?>
					</tbody>
				</table>
				<?php } else { ?>
					<p>Listing for '<?php echo $args['type']; ?>' go here.</p>
				<?php } ?>
			</div>
		
		<?php }
			//
			// ADD
			//
			if(isset($args['action']) && isset($args['type']) && $args['action'] == 'add') { 
		?>
	
			<div id="main" class="add">
				
				<p>Add Forms for '<?php echo $args['type']; ?>' go here.</p>
				
			</div>
		
		<?php }
			//
			// EDIT
			//
			if(isset($args['action']) && isset($args['type']) && $args['action'] == 'edit') { 
		?>
			
	
			<div id="main" class="edit">
				<?php 
					//
					// POSTS
					//
					if(isset($args['id']) && $Flex->postType_exists($args['type'])) {
					$post = $Flex->get_post($args['id']);  
				?>
				
					<p>Edit form for '<?php echo $args['type']; ?>' : '<?php echo $post['name']; ?>' goes here.</p>
					<?php echo'<pre>';print_r($post);echo'</pre>'; ?>
			
			<?php } else {
					//
					// OPTIONS 		
					//
			?>
				
					<p>Edit Options for '<?php echo $args['type']; ?>' go here.</p>
				
			<?php }} ?>
				
			</div>
		
		
	</div>
	<footer>
		
	</footer>
</body>
</html>