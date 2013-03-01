to do notes:

- enable user to change admin directory to whatever they'd like

- add catch for {postType}-{slug}.php for all single template calls

- add post_get() function as a wrapper for post($var, true);

- replace all taxonomy functionality with ability to create child postTypes. 

	- replace 'taxonomies' column in 'post_types' database with 'child_of'

	- keep taxonomy language for front-end, user functions & templates

		- when creating a post type, add 'use as taxonomy for post type' option

	- rewrite functions from controller Flex.php

		- taxonomy_exists()

		- term_exists()

		- 

- add functions for front-page settings & catch for front-page.php

- add search functions (including searchform echo function) & search.php template

- add core templates as fallbacks for missing templates in a theme

- add time/date archive functionality? {postType}-archive.php ???

- replace Database Class with PHP built-in PDO functions

- add simple tour. 

before launching:

- change htaccess so that the local /flex/ directory isn't included. (use php to create the file - thereby adding the correct path automatically)

- change function Flex->login() so that $uri is secure (currently handicapped for local developement // OR detect localhost and serve appropriately.)