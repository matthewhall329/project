<?php
/*
 * Template name: Page Builder
 */
get_header();
WilokePublic::headerPage();

echo '<div class="container">';
	if ( have_posts() ){
		while (have_posts()){
			the_post();
			the_content();
		}
	}
echo '</div>';
get_footer();