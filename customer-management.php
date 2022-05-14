<?php
/**
 * Plugin Name: Customer Mangement
 * Plugin URI: https://geniusfve.cz
 * Description: Manage customers : collect information and assign status to the customer
 * Version: 1.0
 * Author: GeniusFVE
 * Author URI: https://geniusfve.cz
 * License: GPLv2 or later
 * Text Domain: customer-management
*/

/**
 * 
 */
register_activation_hook(__FILE__, 'geniusfvecm_activate');
function geniusfvecm_activate() {
    if ( ! wp_next_scheduled( 'check_nabidky' ) ) {
        wp_schedule_event( time(), 'daily', 'check_nabidky' );
    }
}

register_deactivation_hook( __FILE__, 'geniusfvecm_deactivate' );
function geniusfvecm_deactivate() {
    if (wp_next_scheduled( 'check_nabidky' ) ) {
        $timestamp = wp_next_scheduled('check_nabidky');
        wp_unschedule_event($timestamp, 'check_nabidky');
    }
    remove_action('check_nabidky', 'check_nabidky_exec');
}

add_action( 'check_nabidky', 'check_nabidky_exec' );
function check_nabidky_exec() {
	$args = array(
		'post_type'     => 'zakaznik',
		'post_status'   => 'publish',
		'numberposts'   => -1,
		'orderby'       => 'date',
		'order'         => 'DESC',
	);
	$posts = get_posts($args);

	$args = array(
		'post_type' => 'stav',
		'post_status' => 'publish',
		'numberposts' => -1,
		'orderby' => 'id',
		'order' => 'ASC'
	);
	$stavs = get_posts($args);

	foreach($posts as $post) {
		$status = get_post_meta($post->ID, 'status', true);
		if (!$status || $status < 20) {
			$status = $stavs[0]->ID;
		}

		$termin = get_post_meta($post->ID, 'nabidky_created_date', true);
		$nabidky_date = get_post_meta($status, 'nabidky_date', true);
		if ($termin && $nabidky_date) {
			$termin = date('Y-m-d', strtotime($termin . '+ ' . $nabidky_date . ' days'));

			$today = date('Y-m-d');
			// if (strtotime($termin) < strtotime($today)) {
				$to = 'holub@genius-web.cz';
				$subject = 'Je zde nabídka, která termín skončilo.';

				$body = 'Zákazníci: ' . $post->post_title . '<br><br>
				Uzávěrka nabídky této zákazníci vypršela.<br>
				Tým Genius FVE.';
				$headers = array('Content-Type: text/html; charset=UTF-8', 'From: Genius FVE <info@geniusfve.cz>');
				// wp_mail($to, $subject, $body, $headers);

				// $to = 'jiriandr@genius-web.cz';
				// wp_mail($to, $subject, $body, $headers);

				$to = 'vladimir715.han@gmail.com';
				wp_mail($to, $subject, $body, $headers);
			// }
		}
	}
}

/**
 * Const variables
 */
$kraj_arr = [
    ['wrong' => 'Hlavn msto Praha', 'right' => 'Hlavní město Praha'],       ['wrong' => 'Jihoesk kraj', 'right' => 'Jihočeský kraj'],               ['wrong' => 'Jihomoravsk kraj', 'right' => 'Jihomoravský kraj'],
    ['wrong' => 'Karlovarsk kraj', 'right' => 'Karlovarský kraj'],          ['wrong' => 'Krlovehradeck kraj', 'right' => 'Královehradecký kraj'],   ['wrong' => 'Libereck kraj', 'right' => 'Liberecký kraj'],
    ['wrong' => 'Moravskoslezsk kraj', 'right' => 'Moravskoslezský kraj'],  ['wrong' => 'Olomouck kraj', 'right' => 'Olomoucký kraj'],              ['wrong' => 'Pardubick kraj', 'right' => 'Pardubický kraj'],
    ['wrong' => 'Plzesk kraj', 'right' => 'Plzeňský kraj'],                 ['wrong' => 'Stedoesk kraj', 'right' => 'Středočeský kraj'],            ['wrong' => 'steck kraj', 'right' => 'Ústecký kraj'],
    ['wrong' => 'Vysoina', 'right' => 'Vysočina'],                          ['wrong' => 'Zlnsk kraj', 'right' => 'Zlínský kraj'],
];

/**
 * Register a custom menu page.
 */
function wpdocs_register_my_custom_menu_page(){
	add_menu_page(
		'Správa zákazníků a nabídky', 
		'Správa zákazníků a nabídky', 
		'manage_options', 
		'my-top-level-slug'
	);
}
add_action( 'admin_menu', 'wpdocs_register_my_custom_menu_page' );

// add custom post types
require_once plugin_dir_path(__FILE__) . 'cpt/custom-post-nabidky.php';
require_once plugin_dir_path(__FILE__) . 'cpt/custom-post-zakaznici.php';
require_once plugin_dir_path(__FILE__) . 'cpt/custom-post-stav.php';
require_once plugin_dir_path(__FILE__) . 'cpt/custom-post-osoba.php';
require_once plugin_dir_path(__FILE__) . 'cpt/custom-post-balicky.php';
require_once plugin_dir_path(__FILE__) . 'cpt/custom-post-komponenty.php';
require_once plugin_dir_path(__FILE__) . 'cpt/custom-post-komponenty-duporu.php';
require_once plugin_dir_path(__FILE__) . 'cpt/custom-post-stridace.php';
require_once plugin_dir_path(__FILE__) . 'cpt/custom-post-stridace-duporu.php';
require_once plugin_dir_path(__FILE__) . 'cpt/custom-post-baterie.php';
require_once plugin_dir_path(__FILE__) . 'cpt/custom-post-baterie-duporu.php';
require_once plugin_dir_path(__FILE__) . 'cpt/custom-post-panely.php';
require_once plugin_dir_path(__FILE__) . 'cpt/custom-post-panely-duporu.php';
require_once plugin_dir_path(__FILE__) . 'cpt/custom-post-dotace.php';
require_once plugin_dir_path(__FILE__) . 'cpt/custom-post-vyrobci.php';
require_once plugin_dir_path(__FILE__) . 'cpt/generate-download-pdf.php';

/**
 * Add 'Unread' post status.
 */
function wpdocs_custom_post_status(){
    register_post_status( 'unread', array(
        'label'                     => _x( 'Unread', 'post' ),
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop( 'Unread <span class="count">(%s)</span>', 'Unread <span class="count">(%s)</span>' ),
    ) );
}
add_action( 'admin_init', 'wpdocs_custom_post_status' );

add_action('acf/save_post', 'my_custom_publish_baterie');
function my_custom_publish_baterie($post_id) {
	$post = get_post($post_id);
	if ($post->post_type != 'baterie' && $post->post_type != 'panely' && $post->post_type != 'komponenty' && $post->post_type != 'stridace') return;

	$doporucenych = get_post_meta($post_id, 'doporucenych', true);
	if (!$doporucenych) return;

	if ($post->post_type == 'baterie') {
		$vyrobce = get_post_meta($post_id, 'vyrobce', true);
		$kapacita = get_post_meta($post_id, 'kapacita', true);
		$kryti = get_post_meta($post_id, 'kryti', true);
		$url = get_post_meta($post_id, 'url', true);
		$popis = get_post_meta($post_id, 'popis', true);
		$new_post_id = wp_insert_post( array(
			'post_status' => 'publish',
			'post_type' => 'balickyDuporu',
			'post_title' => $post->post_title,
			'post_content' => $post->post_content,
		) );
		update_post_meta($new_post_id, 'vyrobce', $vyrobce);
		update_post_meta($new_post_id, 'kapacita', $kapacita);
		update_post_meta($new_post_id, 'kryti', $kryti);
		update_post_meta($new_post_id, 'url', $url);
		update_post_meta($new_post_id, 'popis', $popis);
	} else if ($post->post_type == 'panely') {
		$vyrobce = get_post_meta($post_id, 'vyrobce', true);
		$vykon = get_post_meta($post_id, 'vykon', true);
		$j_napeti = get_post_meta($post_id, 'j_napeti', true);
		$j_proud = get_post_meta($post_id, 'j_proud', true);
		$svt = get_post_meta($post_id, 'svt', true);
		$material = get_post_meta($post_id, 'material', true);
		$url = get_post_meta($post_id, 'url', true);
		$popis = get_post_meta($post_id, 'popis', true);
		$new_post_id = wp_insert_post( array(
			'post_status' => 'publish',
			'post_type' => 'panelyDuporu',
			'post_title' => $post->post_title,
			'post_content' => $post->post_content,
		) );
		update_post_meta($new_post_id, 'vyrobce', $vyrobce);
		update_post_meta($new_post_id, 'vykon', $vykon);
		update_post_meta($new_post_id, 'j_napeti', $j_napeti);
		update_post_meta($new_post_id, 'j_proud', $j_proud);
		update_post_meta($new_post_id, 'svt', $svt);
		update_post_meta($new_post_id, 'material', $material);
		update_post_meta($new_post_id, 'url', $url);
		update_post_meta($new_post_id, 'popis', $popis);
	} else if ($post->post_type == 'komponenty') {
		$staticka = get_post_meta($post_id, 'staticka', true);
		$new_post_id = wp_insert_post( array(
			'post_status' => 'publish',
			'post_type' => 'komponentyDuporu',
			'post_title' => $post->post_title,
			'post_content' => $post->post_content,
		) );
		update_post_meta($new_post_id, 'staticka', $staticka);
	} else if ($post->post_type == 'stridace') {
		$vyrobce = get_post_meta($post_id, 'vyrobce', true);
		$vykon = get_post_meta($post_id, 'vykon', true);
		$max_vstupni_proud = get_post_meta($post_id, 'max_vstupni_proud', true);
		$faze = get_post_meta($post_id, 'faze', true);
		$max_dc_vstupni_vykon = get_post_meta($post_id, 'max_dc_vstupni_vykon', true);
		$pocet_mppt = get_post_meta($post_id, 'pocet_mppt', true);
		$rozsah_mppt = get_post_meta($post_id, 'rozsah_mppt', true);
		$svt = get_post_meta($post_id, 'svt', true);
		$url = get_post_meta($post_id, 'url', true);
		$popis = get_post_meta($post_id, 'popis', true);
		$new_post_id = wp_insert_post( array(
			'post_status' => 'publish',
			'post_type' => 'stridaceDuporu',
			'post_title' => $post->post_title,
			'post_content' => $post->post_content,
		) );
		update_post_meta($new_post_id, 'vyrobce', $vyrobce);
		update_post_meta($new_post_id, 'vykon', $vykon);
		update_post_meta($new_post_id, 'max_vstupni_proud', $max_vstupni_proud);
		update_post_meta($new_post_id, 'faze', $faze);
		update_post_meta($new_post_id, 'max_dc_vstupni_vykon', $max_dc_vstupni_vykon);
		update_post_meta($new_post_id, 'pocet_mppt', $pocet_mppt);
		update_post_meta($new_post_id, 'rozsah_mppt', $rozsah_mppt);
		update_post_meta($new_post_id, 'svt', $svt);
		update_post_meta($new_post_id, 'url', $url);
		update_post_meta($new_post_id, 'popis', $popis);
	}
	wp_delete_post($post_id);
}

add_action('admin_init', 'fontawesome_dashboard');
function fontawesome_dashboard() {
	wp_register_style( 'custom_wp_admin_css', get_template_directory_uri() . '/assets/css/admin/style.css', false, '1.0.0' );
}

add_action( 'admin_enqueue_scripts', 'wpdocs_enqueue_custom_admin_style' );
function wpdocs_enqueue_custom_admin_style() {
    wp_enqueue_style( 'custom_wp_admin_css' );
	wp_enqueue_style('fontawesome', 'https://use.fontawesome.com/releases/v5.8.1/css/all.css', '', '5.8.1', 'all');
	wp_enqueue_style('custom_bootstrap_style', 'https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css', '', '4.0.0', 'all');
}

add_action('admin_enqueue_scripts', 'my_enqueue');
function my_enqueue($hook) {
    if ('edit.php' !== $hook) {
        return;
    }

    wp_enqueue_script('custom_wp_admin_script', get_template_directory_uri() . '/assets/js/admin/admin-js.js');
	wp_enqueue_script('custom_jquery_script', "https://code.jquery.com/jquery-3.2.1.min.js");
	wp_enqueue_script('custom_popper_script', "https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js");
	wp_enqueue_script('custom_bootstrap_script', "https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js");
}

function enqueue_scripts_back_end(){
	wp_enqueue_script( 'ajax-script', get_template_directory_uri() . '/assets/js/admin/change-select.js', array('jquery'));
	
	wp_localize_script( 'ajax-script', 'ajax_object',
            array( 'ajax_url' => admin_url( 'admin-ajax.php' )) );
	
}
add_action('admin_enqueue_scripts','enqueue_scripts_back_end');

add_action( 'init', function() use ( &$wp_post_statuses )
{
    $wp_post_statuses['draft']->show_in_admin_all_list = false;

}, 1 );

add_action( 'wp_ajax_get_panel_action', 'get_panel_action' );
function get_panel_action() {
	global $wpdb;
	
	$panel_id = intval( $_POST['panel'] );

	$vyrobce = get_field( 'vyrobce', $panel_id ); 
	$vykon = get_post_meta( $panel_id, 'vykon', true); 
	$j_napeti = get_post_meta( $panel_id, 'j_napeti', true); 
	$j_proud = get_post_meta( $panel_id, 'j_proud', true); 
	$svt = get_post_meta( $panel_id, 'svt', true); 
	$material = get_post_meta( $panel_id, 'material', true); 
	$popis = get_post_meta( $panel_id, 'popis', true); 
	$title = get_the_title($panel_id);
	$url = get_post_meta( $panel_id, 'url', true);
	
	echo json_encode(array('title' => $title,'vyrobce' => $vyrobce,'vykon'=> $vykon, 'j_napeti'=> $j_napeti, 'j_proud'=> $j_proud, 'svt'=> $svt,'material'=> $material,'popis'=> $popis, 'url'=>$url));
	wp_die();
}

add_action( 'wp_ajax_get_baterie_action', 'get_baterie_action' );
function get_baterie_action() {
	global $wpdb;
	
	$baterie_id = intval( $_POST['baterie'] );
	
	$vyrobce = get_post_meta( $baterie_id, 'vyrobce', true); 
	$kapacita = get_post_meta( $baterie_id, 'kapacita', true); 
	$kryti = get_post_meta( $baterie_id, 'kryti', true); 
	$popis = get_post_meta( $baterie_id, 'popis', true); 
	$title = get_the_title($baterie_id);
	$url = get_post_meta( $baterie_id, 'url', true);

	echo json_encode(array('title' => $title,'vyrobce' => $vyrobce,'kapacita'=> $kapacita, 'kryti'=> $kryti, 'popis'=> $popis , 'url'=>$url));
	wp_die();
}

add_action( 'wp_ajax_get_stridace_action', 'get_stridace_action' );
function get_stridace_action() {
	global $wpdb;
	
	$strodace_id = intval( $_POST['stridace'] );
	
	$vyrobce_id = get_post_meta( $strodace_id, 'vyrobce', true); 
	$vyrobce = get_the_title($vyrobce_id);
	$vykon = get_post_meta( $strodace_id, 'vykon', true); 
	$max_dc_vstupni_vykon = get_post_meta( $strodace_id, 'max_dc_vstupni_vykon', true); 
	$max_vstupni_proud = get_post_meta( $strodace_id, 'max_vstupni_proud', true);
	$svt = get_post_meta( $strodace_id, 'svt', true);
	$pocet_mppt = get_post_meta( $strodace_id, 'pocet_mppt', true);
	$rozsah_mppt = get_post_meta( $strodace_id, 'rozsah_mppt', true);
	$faze = get_post_meta( $strodace_id, 'faze', true);
	$popis = get_post_meta( $strodace_id, 'popis', true); 
	$title = get_the_title($strodace_id);
	$url = get_post_meta($strodace_id, 'url', true);

	echo json_encode(array('title' => $title, 'vyrobce' => $vyrobce, 'vykon' => $vykon, 'max_dc_vstupni_vykon'=> $max_dc_vstupni_vykon, 'max_vstupni_proud'=> $max_vstupni_proud, 'svt'=> $svt, 'pocet_mppt'=> $pocet_mppt, 'rozsah_mppt'=> $rozsah_mppt, 'faze'=> $faze, 'popis'=> $popis, 'url'=> $url));
	wp_die();
}

add_action( 'wp_ajax_get_komponenty_action', 'get_komponenty_action' );
function get_komponenty_action() {
	global $wpdb;
	
	$komponenty_id = intval( $_POST['komponenty'] );
	
	$title = get_the_title($komponenty_id);
	$staticka = get_post_meta( $komponenty_id, 'staticka', true);

	echo json_encode(array('title' => $title, 'staticka' => $staticka));
	wp_die();
}

// $customer_id = filter_input( INPUT_GET, "customer_id", FILTER_SANITIZE_STRING );
// $customer_meta = get_post_meta($customer_id);

add_action( 'wp_ajax_get_nabidky_action', 'get_nabidky_action' );
function get_nabidky_action() {
	$customer_id = intval( $_POST['customer_id']);
	$post = get_post($customer_id);

	$datetime = date('Y-m-d H:i:s');
	
	list($date, $time) = explode(" ", $datetime);
	$time = str_replace(':', '', $time);
	$value = $customer_id . $time;

	$email = get_post_meta($customer_id, 'e-mail', true);
	$telefon = get_post_meta($customer_id, 'telefon', true);
	$adresa_realizace = get_post_meta($customer_id, 'adresa_realizace', true);
	$formular = get_post_meta($customer_id, 'formular', true);
	$kraj = get_post_meta($formular, '_field_63', true);
	$kraj = $kraj != null ? $kraj : 'Hlavn msto Praha';
	$args = array(
		'post_type' => 'stav',
		'post_status' => 'publish',
		'numberposts' => -1,
		'orderby' => 'id',
		'order' => 'ASC'
	);
	$stavs = get_posts($args);
	$status = get_post_meta($customer_id, 'status', true);
    $status = $status ? $status : $stavs[0]->ID;
	for ($j = 0; $j < count($stavs); $j++) {
		if ($stavs[$j]->ID == $status)
			$status = $stavs[$j]->post_title;
	}
	if ($status == 1) $status = $stavs[0]->post_title;
	$material_krytiny = get_post_meta($formular, '_field_21', true);
	$rozmer_strechy = get_post_meta($formular, '_field_24', true);

	echo json_encode(
		array(
			'title' => $post->post_title,
			'cislo' => $value,
			'datum' => $post->post_date_gmt,
			'email' => $email,
			'telefon' => $telefon,
			'adresa_instalace' => $adresa_realizace,
			'kraj' => $kraj,
			'status' => $status,
			'material_krytiny' => $material_krytiny,
			'rozmer_strechy' => $rozmer_strechy,
		)
	);
	wp_die();
}

add_action( 'wp_ajax_get_package_action', 'get_package_action' );
function get_package_action() {
	$customer_id = intval( $_POST['customer_id']);
	$formular = get_post_meta($customer_id, 'formular', true);
	$region = get_post_meta($formular, '_field_63', true);
	$region_bonus_flag = 0;
	if ($region == "Moravskoslezsk kraj" || $region == "steck kraj" || $region == "Karlovarsk kraj") {
		$region_bonus_flag = 1;
	}

	$balicy_id = intval( $_POST['package']);
	$balicy_panel_id = get_post_meta( $balicy_id, 'panel', true);
	$balicy_baterie_id = get_post_meta( $balicy_id, 'baterie', true);
	$balicy_stridac_id = get_post_meta( $balicy_id, 'stridac', true);
	
	$balicy_panel_n = get_post_meta( $balicy_id, 'defaultni_pocet_panelu', true);
	$balicy_baterie_n = get_post_meta( $balicy_id, 'defaultni_pocet_baterii', true);
	$balicy_stridac_n = get_post_meta( $balicy_id, 'defaultni_pocet_stridacu', true);

	$balicy_panel_cena = get_post_meta( $balicy_panel_id, 'cena_nakup', true);
	$balicy_panel_performance = get_post_meta( $balicy_panel_id, 'vykon', true);
	$balicy_baterie_cena = get_post_meta( $balicy_baterie_id, 'cena_nakup', true);
	$balicy_battery_capacity = get_post_meta( $balicy_baterie_id, 'kapacita', true);
	$balicy_stridac_cena = get_post_meta( $balicy_stridac_id, 'cena_nakup', true);

	$balicek_komponenty = get_field('komponenty', $balicy_id);

	$komponenty_price = 0;
	foreach ($balicek_komponenty as $key => $value) {
		$tmp = (int)get_post_meta($value->ID, 'cena_prodej', true);
		$komponenty_price += $tmp;
	}
	// $package_price = $komponenty_price + (int)$balicy_panel_cena * (int)$balicy_panel_n + (int)$balicy_baterie_cena * (int)$balicy_baterie_n + (int)$balicy_stridac_cena * (int)$balicy_stridac_n;
	$package_price = $komponenty_price;
	echo json_encode(
		array(
			'package_price' => (int)$package_price,
			'panel_price' => (int)$balicy_panel_cena,
			'battery_price' => (int)$balicy_baterie_cena,
			'inverter_price' => (int)$balicy_stridac_cena,
			'panel_cnt' => (int)$balicy_panel_n,
			'battery_cnt' => (int)$balicy_baterie_n,
			'inverter_cnt' => (int)$balicy_stridac_n,
			'panel_performance' => (float)$balicy_panel_performance,
			'battery_capacity' => (float)$balicy_battery_capacity,
			'region_bonus_flag' => (int)$region_bonus_flag,
		)
	);
	wp_die();
}

add_action( 'wp_ajax_get_dotace_action', 'get_dotace_action' );
function get_dotace_action() {
	$dotace_id = intval( $_POST['dotace']);
	$dotace_price = get_post_meta( $dotace_id, 'vyse', true);
	echo json_encode(array('dotace_price' => $dotace_price));
	wp_die();
}

add_action( 'save_post', 'save_post_callback', 10, 3);
function save_post_callback($post_id,$post,$update) {
    if ($post->post_type != 'nabidky') {
        return;
    }

    //if you get here then it's your post type so do your thing....
	if ($update) {
		download_pdf($post_id, 1);
		download_contrac_pdf($post_id, 1);
		download_zakaznic_pdf($post_id, 1);
		download_technical_pdf($post_id, 1);
	}
}