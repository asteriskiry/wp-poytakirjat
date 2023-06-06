
	<div id="pk">
	
	<div id="pk-content">
<?php

/* Parametrit Loopeille */

$args_by_year = array(
	'post_type' => 'poytakirjat',
	'posts_per_page' => -1,
	'tax_query' => array(
		array(
			'taxonomy' => 'vuosi',
			'field' => 'slug',
			'terms' => htmlspecialchars($_GET['vuosi'] ?? null),
		),
	),
);

$args_recent = array(
	'numberposts' => '4',
	'post_type' => 'poytakirjat',
);

/* Jos vuotta ei valittu, näytetään tämän vuoden pöytäkirjat  */
if ($args_by_year['tax_query'][0]['terms'] == '') {
	
	$args_by_year = array(
		'post_type' => 'poytakirjat',
		'posts_per_page' => -1,
		'tax_query' => array(
			array(
				'taxonomy' => 'vuosi',
				'field' => 'slug',
				'terms' => date('Y'),
			),
		),
	);
}
$pk_by_year = new WP_Query($args_by_year);
if ($pk_by_year->have_posts()) :
	
	/* HTML: taulukon staattiset kentät */
	echo '<h1 class="customtitle">' . $args_by_year['tax_query'][0]['terms'] . '</h1>';
	?>
	<div id='pk-dropdown'>
		<?php
		
		/* Dropdown-valikko */
		
		global $wp;
		$current_url = home_url($wp->request);
		?>
		<li id="pk-dropdown-li">
			<?php wp_dropdown_categories('show_option_none=' . __('Valitse vuosi', 'wp-poytakirjat') . '&taxonomy=vuosi&value_field=slug'); ?>
			<script type="text/javascript">
				
				let dropdown = document.getElementById('cat');
				
				function onCatChange() {
					if (dropdown.options[dropdown.selectedIndex].value > 0) {
						location.href = "<?= $current_url ?>?vuosi=" + dropdown.options[dropdown.selectedIndex].value;
					}
				}
				
				dropdown.onchange = onCatChange;
			
			</script>
		</li>
	</div>
	<table id="pk-taulukko" class="row-border">
	<thead>
	<tr class="pk-rivi">
		<th class="pk-indeksit"><?= __('Nimi', 'wp-poytakirjat') ?></th>
		<th class="pk-indeksit"><?= __('Jnro', 'wp-poytakirjat') ?></th>
		<th class="pk-indeksit"><?= __('Päivämäärä', 'wp-poytakirjat') ?></th>
		<th class="pk-indeksit"><?= __('Tyyppi', 'wp-poytakirjat') ?></th>
	</tr>
	</thead>
	<tbody>
	<?php
	
	/* Haetaan tiedot ja tallennetaan muuttujiin */
	
	while ($pk_by_year->have_posts()) : $pk_by_year->the_post();
		
		global $post;
		$title = get_the_title();
		$custom_pdf_data = get_post_meta($post->ID, 'custom_pdf_data');
		$pdfurl = $custom_pdf_data[0]['src'];
		$slug = get_permalink();
		$pm = get_post_meta($post->ID, 'pk_paivamaara', true);
		$jn = get_post_meta($post->ID, 'pk_numero', true);
		$tyyppi = get_the_terms($post->ID, 'tyyppi');
		$thumbnail = $custom_pdf_data[0]['tnSmall'];
		
		/* HTML: dynaamiset kentät*/
		
		echo '<tr class="item">';
		echo '<td><a class="hvr-grow pdf-link" href="' . $pdfurl . '">' . $title . '</a></td>';
		echo '<td> ' . $jn . '</td>';
		echo '<td> ' . $pm . '</td>';
		echo '<td> ' . $tyyppi[0]->name . '</td>';
		echo '</tr>';
	endwhile;
	echo '</tbody>';
	echo '</table>';
endif;
?>
	<div id='dialog' style='display:none'>
		<div>
			<iframe id="riski-pdf" width='100%' height='100%' src=''></iframe>
		</div>
	</div>
<?php
echo '</div>';
echo '</div>';