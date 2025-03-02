<?php

/* Custom post type "Pöytäkirjat" rekisteröinti */

function wpark_pk_register_post_type(): void {

	$singular = 'Pöytäkirja';
	$plural = 'Pöytäkirjat';
	$slug = 'poytakirjat';

	$labels = [
		'name' => $plural,
		'singular_name' => $singular,
		'add_name' => 'Lisää uusi',
		'add_new_item' => 'Lisää uusi ' . $singular,
		'edit' => 'Muokkaa',
		'edit_item' => 'Muokkaa asiakirjaa',
		'new_item' => 'Uusi ' . $singular,
		'view' => 'Näytä ' . $singular,
		'view_item' => 'Näytä ' . $singular,
		'search_term' => 'Etsi asiakirjoja',
		'parent' => 'Vanhempi ' . $singular,
		'not_found' => 'Asiakirjoja ei löytynyt',
		'not_found_in_trash' => 'Asiakirjoja ei löytynyt roskakorista',
	];

	$args = [
		'labels' => $labels,
		'public' => true,
		'publicly_queryable' => true,
		'exclude_from_search' => false,
		'show_in_nav_menus' => true,
		'show_ui' => true,
		'show_in_menu' => true,
		'show_in_admin_bar' => true,
		'menu_position' => 10,
		'menu_icon' => 'dashicons-media-text',
		'can_export' => true,
		'delete_with_user' => false,
		'hierarchical' => false,
		'has_archive' => true,
		'query_var' => true,
		'capability_type' => 'post',
		'map_meta_cap' => true,
		// 'capabilities'       => array(),
		'taxonomies' => ['vuosi', 'tyyppi',],
		'rewrite' => [
			'slug' => $slug,
			'with_front' => true,
			'pages' => true,
			'feeds' => false,
		],
		'supports' => [
			'title',
		],
	];

	register_post_type($slug, $args);
}

add_action('init', 'wpark_pk_register_post_type');

/* Custom taxonomyn "Vuodet" rekisteröinti pöyräkirjoille */
function wpark_pk_register_taxonomy_vuosi(): void {

	$plural = 'Vuodet';
	$singular = 'Vuosi';
	$slug = 'vuosi';

	$labels = [
		'name' => $singular,
		'singular_name' => $singular,
		'search_items' => 'Etsi vuotta',
		'popular_items' => 'Suositut vuodet',
		'all_items' => 'Kaikki vuodet',
		'parent_item' => null,
		'parent_item_colon' => null,
		'edit_item' => 'Muokkaa vuotta',
		'update_item' => 'Päivitä ' . $singular,
		'add_new_item' => 'Lisää uusi ' . $singular,
		'new_item_name' => 'Nimeä ' . $singular,
		'separate_items_with_commas' => 'Erottele ' . $plural . ' pilkuilla',
		'add_or_remove_items' => 'Lisää tai poista vuosia',
		'choose_from_most_used' => 'Valitse suosituimmista vuosista',
		'not_found' => 'Vuosia ei löytynyt',
		'menu_name' => $plural,
	];

	/** @link wpark_pk_taxonomy_meta_box */
	$args = [
		'public' => false,
		'hierarchical' => true,
		'labels' => $labels,
		'show_ui' => true,
		'show_admin_column' => true,
		'update_count_callback' => '_update_post_term_count',
		'query_var' => true,
		'rewrite' => ['slug' => $slug],
		'meta_box_cb' => 'wpark_pk_taxonomy_meta_box',
	];
	register_taxonomy($slug, 'poytakirjat', $args);
}

add_action('init', 'wpark_pk_register_taxonomy_vuosi');

/* Custom taxonomyn "Tyyppi" rekisteröinti pöyräkirjoille */

function wpark_pk_register_taxonomy_tyyppi(): void {

	$plural = 'Tyypit';
	$singular = 'Tyyppi';
	$slug = 'tyyppi';

	$labels = [
		'name' => $singular,
		'singular_name' => $singular,
		'search_items' => 'Etsi tyyppiä',
		'popular_items' => 'Suositut tyypit',
		'all_items' => 'Kaikki tyypit',
		'parent_item' => null,
		'parent_item_colon' => null,
		'edit_item' => 'Muokkaa tyyppiä',
		'update_item' => 'Päivitä ' . $singular,
		'add_new_item' => 'Lisää uusi ' . $singular,
		'new_item_name' => 'Nimeä ' . $singular,
		'separate_items_with_commas' => 'Erottele ' . $plural . ' pilkuilla',
		'add_or_remove_items' => 'Lisää tai poista tyyppejä',
		'choose_from_most_used' => 'Valitse suosituimmista tyypeistä',
		'not_found' => 'Tyyppejä ei löytynyt',
		'menu_name' => $plural,
	];

	/** @link wpark_pk_taxonomy_meta_box */
	$args = [
		'public' => false,
		'hierarchical' => true,
		'labels' => $labels,
		'show_ui' => true,
		'show_admin_column' => true,
		'update_count_callback' => '_update_post_term_count',
		'query_var' => true,
		'rewrite' => ['slug' => $slug],
		'meta_box_cb' => 'wpark_pk_taxonomy_meta_box',
	];
	register_taxonomy($slug, 'poytakirjat', $args);
}

add_action('init', 'wpark_pk_register_taxonomy_tyyppi');

/* Lisäyssivun taxonomioiden meta boxit */
function wpark_pk_taxonomy_meta_box($post, $meta_box_properties): void {
	$taxonomy = $meta_box_properties['args']['taxonomy'];
	$tax = get_taxonomy($taxonomy);
	$terms = get_terms($taxonomy, ['hide_empty' => 0]);
	$postterms = get_the_terms($post->ID, $taxonomy);
	$current = ($postterms ? array_pop($postterms) : false);
	$current = ($current ? $current->term_id : 0);

	if ($taxonomy === 'vuosi' && is_array($terms)) {
		$terms = array_reverse($terms);
	}
	?>

	<div id="taxonomy-<?php echo $taxonomy; ?>" class="categorydiv">
		<ul id="<?php echo $taxonomy; ?>-tabs" class="category-tabs">
			<li class="tabs"><a href="#<?php echo $taxonomy; ?>-all"><?php echo $tax->labels->all_items; ?></a></li>
		</ul>

		<div id="<?php echo $taxonomy; ?>-all" class="tabs-panel">
			<input name="tax_input[<?php echo $taxonomy; ?>][]" value="0" type="hidden">
			<ul id="<?php echo $taxonomy; ?>checklist" data-wp-lists="list:symbol" class="categorychecklist form-no-clear">

				<?php
				$first = false;
				foreach ($terms as $term) {
					$id = $taxonomy . '-' . $term->term_id; ?>

					<li id="<?php echo $id ?>">
						<label class="selectit">
							<?php if (!$first && !$current):
							$first = true;
							?>
							<input required value="<?php echo $term->term_id; ?>" name="tax_input[<?php echo $taxonomy; ?>][]"
							       id="in-<?php echo $id; ?>"
								   checked="checked"
							       type="radio"> <?php echo $term->name; ?></label>
						<?php else : ?>
							<input required value="<?php echo $term->term_id; ?>" name="tax_input[<?php echo $taxonomy; ?>][]"
							       id="in-<?php echo $id; ?>"
								   <?php if ($current === $term->term_id){ ?>checked="checked"<?php } ?>
							       type="radio"> <?php echo $term->name; ?></label>
						<?php endif; ?>
					</li>
				<?php } ?>
			</ul>
		</div>
	</div>
	<?php
}

/* Asiakirjojen lisäyssivun meta boxit (Järjestysnum, pvm, helppi) */
function wpark_pk_add_metabox(): void {
	add_meta_box(
		'wpark_pk_help',
		'Tiedote',
		'wpark_pk_help_callback',
		'poytakirjat',
		'normal',
		'high'
	);
}

add_action('add_meta_boxes', 'wpark_pk_add_metabox');

function wpark_pk_help_callback($post): void {
	echo '<div class="meta-help">Jos et ole ihan varma mitä teet, katso <a href="' . admin_url('edit.php?post_type=poytakirjat&page=pk-ohjeet') . '">ohjeet</a></div>';
}

/* Templojen lataus */
function wpark_load_templates($original_template) {
	if (get_query_var('post_type') !== 'poytakirjat') {
		return $original_template;
	}
	if (is_archive() || is_search()) {
		return plugin_dir_path(__FILE__) . 'templates/poytakirjat-archive.php';
	} elseif (is_singular('poytakirjat')) {
		return plugin_dir_path(__FILE__) . 'templates/poytakirjat-single.php';
	} else {
		return get_page_template();
	}
}

add_action('template_include', 'wpark_load_templates');

/* Ohjeet-sivu */

function wpark_pk_add_help_page(): void {

	add_submenu_page(
		'edit.php?post_type=poytakirjat',
		'Asiakirjojen ohjeet',
		'Ohjeet ja asetukset',
		'manage_options',
		'pk-ohjeet',
		'wpark_pk_help_cb'
	);
}

function wp_poytakirjat_settings_theme_cb(): void {
	?>
	<select id="wp_poytakirjat_settings_theme" name="wp_poytakirjat_settings_theme">
		<option value="default" <?php selected('default', get_option('wp_poytakirjat_settings_theme')); ?>>Oletus</option>
		<option value="asteriski" <?php selected('asteriski', get_option('wp_poytakirjat_settings_theme')); ?>>Asteriski</option>
		<option value="technica" <?php selected('technica', get_option('wp_poytakirjat_settings_theme')); ?>>Technica</option>
	</select>
	<?php
}

add_action('admin_menu', 'wpark_pk_add_help_page');

function wpark_pk_add_settings() {
	add_settings_section(
		'wp_poytakirjat_settings',
		'Asetukset',
		'wp_poytakirjat_settings_cb',
		'pk-ohjeet'
	);
	add_settings_field('wp_poytakirjat_settings_theme',
		'Teema',
		'wp_poytakirjat_settings_theme_cb',
		'pk-ohjeet',
		'wp_poytakirjat_settings'
	);
	register_setting('pk-ohjeet', 'wp_poytakirjat_settings_theme');
}

add_action('admin_init', 'wpark_pk_add_settings');
function wp_poytakirjat_settings_cb() {
	echo '<p>Kaikki teemaan liittyvät asetukset</p>';
}

function wpark_pk_help_cb(): void {
	?>
	<div class="help-page">
		<h1>Ohjeet asiakirjojen hallintaan</h1>
		<h3>Pöytäkirjan lisääminen</h3>
		<p>Sinulla tulisi olla asiakirjan PDF-tiedosto tietokoneellasi ennen aloitusta</p>
		<ol type="1">
			<li>Valitse järjestysnumero</li>
			<li>Valitse "Kokouksen päivämäärä" kentästä avautuvasta kalenterista kokouksen päivämäärä</li>
			<li>Valitse vuosi (jona kokous pidettiin) kategorisointia varten</li>
			<li>Valitse asiakirjan tyyppi (kts. kohta "Tyypeistä")</li>
			<li>Paina "Julkaise"</li>
			<li>Valmista! Käy vielä tarkistamassa että lisäämäsi asiakirja näkyy oikein</li>
		</ol>

		<h3>Tyyppien ja vuosien hallinta</h3>
		<p>Tyyppejä voi lisätä tarpeen mukaan Pöytäkirjat->Tyypit -valikosta. Vuosia vastaavasta. Ainoa täytettävä kenttä on "Nimi". Nimen
			täyttämisen jälkeen paina "Lisää uusi
			Tyyppi/Vuosi"-näppäintä ja uusi vuosi/tyyppi on käytettävissä asiakirjan lisäyssivulla</p>

		<h3>Tyypeistä</h3>
		<p>Hallituksen kokousten asiakirja tyyppi on "Hallitus"</p>
		<p>Syyskokouksella ja kevätkokouksella on omat tyyppinsä, muut yhdistyksen kokoukset tyyppiin "Yhdistys"</p>
		<p>Toimikuntien kokousten asiakirjojen tyyppi on toimikunnan nimi, esim. "WWW-toimikunta"</p>
	</div>
	<form method='POST' action='options.php'>
		<?php
		settings_fields('pk-ohjeet');
		do_settings_sections('pk-ohjeet');
		submit_button();
		?>
	</form>
	<?php
}

/* Adminin listauksen dataa */

function wpark_pk_columns($columns) {
	$columns['kokouksen_pvm'] = 'Kokouksen päivämäärä';

	return $columns;
}

add_filter('manage_edit-poytakirjat_columns', 'wpark_pk_columns');

function wpark_pk_populate_columns($column): void {
	if ('kokouksen_pvm' == $column) {
		$kok_pvm = esc_html(carbon_get_post_meta(get_the_ID(), 'pk_paivamaara'));
		echo $kok_pvm;
	}
}

add_action('manage_posts_custom_column', 'wpark_pk_populate_columns');