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


function save_poke($post_id) {
	// Prevent auto-saves and ensure we are handling 'poytakirjat' post type
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		return;
	}
	if (get_post_type($post_id) !== 'poytakirjat') {
		return;
	}
	if (wp_is_post_revision($post_id)) {
		return;
	}

	// Delay execution to ensure meta fields & taxonomies are saved first
	add_action('wp_insert_post', function ($post_id) {
		update_poytakirja_title($post_id);
	}, 20, 1);
}

function update_poytakirja_title($post_id) {
	// Prevent recursion
	remove_action('save_post', 'save_poke');

	$numero = carbon_get_post_meta($post_id, 'pk_numero');
	$tyyppi = get_the_terms($post_id, 'vuosi');

	if (!empty($numero) && !empty($tyyppi) && is_array($tyyppi)) {
		$new_title = 'Pöytäkirja ' . $numero . '/' . $tyyppi[0]->name;

		// Update post title only if different
		$current_title = get_the_title($post_id);
		if ($current_title !== $new_title) {
			wp_update_post([
				'ID' => $post_id,
				'post_title' => $new_title,
			]);
		}
	}

	// Re-add the action
	add_action('save_post', 'save_poke');
}

add_action('save_post', 'save_poke');