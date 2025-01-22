<?php
wp_redirect(get_post_type_archive_link('poytakirjat'));

// No singular view for pöke



/**
 * Template Name: Pöytäkirjat-single
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

	<div class="pk-single">
		<script>
			jQuery(function($) {
				$(window).load(function() {
					$('#loadOverlay').fadeOut('slow');
				});
			});
		</script>
<?php

/* Loop joka hakee tiedot */

if (have_posts()) : while (have_posts()) : the_post();
	global $post;

	/* Tallennetaan tiedot muuttujiin kannasta */
	$pdf_id = (int) carbon_get_post_meta($post->ID, 'custom_pdf_data');
	$pdfurl = wp_get_attachment_url($pdf_id);
	$slug = get_permalink();
	$pm = carbon_get_post_meta($post->ID, 'pk_paivamaara', true);
	$jn = carbon_get_post_meta($post->ID, 'pk_numero', true);
	$tyyppi = get_the_terms($post->ID, 'tyyppi');

	/* Generoidaan HTML */
	echo '<a class="hvr-grow" href="' . $pdfurl . '" download>Lataa pöytäkirja <i class="fa fa-paperclip"></i></a>';

	echo '<div class="pk-single-meta-content">';
	echo '<table>';
	echo '<tr>';
	echo '<td><strong>Nimi</strong></td><td>' . apply_filters('the_title', $post->post_title) . '</td>';
	echo '</tr><tr>';
	echo '<td><strong>Tyyppi</strong></td><td>' . $tyyppi[0]->name . '</td>';
	echo '</tr><tr>';
	echo '<td><strong>Päivämäärä</strong></td><td>' . $pm . '</td>';
	echo '</tr><tr>';
	echo '<td><strong>Järjestysnumero</strong></td><td>' . $jn . '</td>';
	echo '</tr>';
	echo '</table>';
	echo '</div>';

	echo '<div class="pk-pagination">';
	echo '<div class="pk-buttons-left">';
	echo previous_post_link('%link', '<i class="fa fa-chevron-left"></i> Edellinen');
	echo '</div>';
	echo '<div class="pk-buttons-right">';
	echo next_post_link('%link', 'Seuraava <i class="fa fa-chevron-right"></i>');
	echo '</div>';
	echo '</div>';

	echo '<div class="pk-buttons">';
	echo '<a href="' . get_site_url() . '/' . 'poytakirjat' . '"><i class="fa fa-arrow-circle-left" aria-hidden="true"></i> Takaisin selailuun</a>';
	echo '</div>';

	echo '</div>';
	echo '</div>';

endwhile; endif;

get_footer();