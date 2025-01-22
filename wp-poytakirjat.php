<?php

/**
 * Plugin Name: WP Pöytäkirjat
 * Description: Pöytäkirja-arkisto
 * Plugin URI: https://asteriski.fi
 * Author: Maks Turtiainen, Asteriski ry
 * Version: 1.5
 * Author URI: https://github.com/asteriskiry
 * License: MIT
 * Collaboration: Roosa Virta, Asteriski ry
 **/

use Carbon_Fields\Container;
use Carbon_Fields\Field;

if (!defined('ABSPATH')) {
    exit;
}
const POKE_VERSION = '1.0.0'; // For forcing css updates

require_once(plugin_dir_path(__FILE__) . 'wp-arkisto-poytakirjat.php');
require_once(plugin_dir_path(__FILE__) . 'wp-arkisto-enqueue.php');

/**
 * Removing Quick edit functionality from the list.
 *
 * @param $actions
 *
 * @return mixed
 */
function wppoyt_remove_quick_edit($actions)
{
    global $typenow;
    if ($typenow == 'poytakirjat') {
        unset($actions['inline hide-if-no-js']);
    }

    return $actions;
}

add_filter('post_row_actions', 'wppoyt_remove_quick_edit');

/**
 * Adding a shortcode for the documents.
 *
 * @return false|string
 */
function asteriski_poytakirja_shortcode()
{

    ob_start();
    include(plugin_dir_path(__FILE__) . 'templates/poytakirjat-shortcode.php');

    return ob_get_clean();
}

/**
 * Registering the shortcode.
 */
add_shortcode('poytakirja_taulukko', 'asteriski_poytakirja_shortcode');

add_action('carbon_fields_register_fields', 'pk_attach_theme_options');
function pk_attach_theme_options() {
	Container::make('post_meta', 'Pöytäkirjan tiedot')
		->where('post_type', '=', 'poytakirjat')
		->add_fields([
			Field::make( 'text', 'pk_numero', 'Järjestysnumero' )
				->set_attribute('type', 'number'),
			Field::make( 'date', 'pk_paivamaara', 'Pöytäkirjan päivämäärä' ),
			Field::make( 'file', 'custom_pdf_data', 'Pöytäkirjan tiedosto' )
			->set_type(['text/plain', 'text/richtext', 'text/html', 'application/pdf', 'application/msword']),
		]);
}

add_action('init', function () {
	if(get_option('pk_pvm_migration_done')){
		return;
	}
	$args = [
		'post_type' => 'poytakirjat',
		'post_status' => 'any',
		'posts_per_page' => - 1,
	];

	$query = new WP_Query($args);

	if ($query->have_posts()) {
		while ($query->have_posts()) {
			$query->the_post();
			$post_id = get_the_ID();
			$num_value = get_post_meta($post_id, 'pk_numero', true);
			update_post_meta($post_id, 'bu_pk_numero', $num_value);
			update_post_meta($post_id, '_pk_numero', $num_value);

			// Get the current meta value
			$meta_value = get_post_meta($post_id, 'pk_paivamaara', true);
			update_post_meta($post_id, 'bu_pk_paivamaara', $meta_value);

			// Check if the value matches the d.m.Y format
			if (preg_match('/^\d{1,2}\.\d{1,2}\.\d{4}$/', $meta_value)) {
				// Convert d.m.Y to Y-m-d
				$parts = explode('.', $meta_value);
				$normalized_date = sprintf('%04d-%02d-%02d', $parts[2], $parts[1], $parts[0]);

				// Update the meta value
				update_post_meta($post_id, '_pk_paivamaara', $normalized_date);
			}
		}
		wp_reset_postdata();
		update_option('pk_pvm_migration_done', 1);
	}
});

add_action('init', function () {
	if(get_option('pk_file_migration_done')){
		return;
	}

	$args = [
		'post_type' => 'poytakirjat',
		'post_status' => 'any',
		'posts_per_page' => - 1,
	];

	$query = new WP_Query($args);

	if ($query->have_posts()) {
		while ($query->have_posts()) {
			$query->the_post();
			$post_id = get_the_ID();

			// Get the current meta value
			$meta_value = get_post_meta($post_id, 'custom_pdf_data', true);
			update_post_meta($post_id, 'bu_custom_pdf_data', $meta_value);

			// Check if the id exists
			if (!empty($meta_value['id'])) {

				// Update the meta value
				update_post_meta($post_id, '_custom_pdf_data', $meta_value['id']);
			}
		}
		wp_reset_postdata();
		update_option('pk_file_migration_done', 1);
	}
});

