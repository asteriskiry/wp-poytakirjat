<?php

/**
 * Template Name: Pöytäkirjat-archive
 **/

get_header();

/**
 * Asteriski WP teemaa varten
 */
?>
	
	<header class="page-header">
		<div class="overlay-dark"></div>
		<div class="container breadcrumbs-wrapper">
			<div class="breadcrumbs d-flex flex-column justify-content-center">
				<h3><?php wp_title(''); ?></h3>
			</div>
		</div>
	</header>
<?php
include_once(dirname(__FILE__) . '/poytakirjat-shortcode.php');
get_footer();