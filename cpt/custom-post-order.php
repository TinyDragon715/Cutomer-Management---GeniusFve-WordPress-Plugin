<?php

function load_objednat_post_types() {
    $labels = array(
        'name'               => _x( 'Objednat', 'post type general name', 'bussenschalter' ),
        'singular_name'      => _x( 'Objednat', 'post type singular name', 'bussenschalter' ),
        'menu_name'          => _x( 'Objednat', 'admin menu', 'bussenschalter' ),
        'name_admin_bar'     => _x( 'Objednat', 'add new on admin bar', 'bussenschalter' ),
        'add_new'            => _x( 'Add New', 'Objednat', 'bussenschalter' ),
        'add_new_item'       => __( 'Název', 'bussenschalter' ),
        'new_item'           => __( 'New Objednat', 'bussenschalter' ),
        'edit_item'          => __( 'Edit Objednat', 'bussenschalter' ),
        'view_item'          => __( 'View Objednat', 'bussenschalter' ),
        'all_items'          => __( 'Objednat', 'bussenschalter' ),
        'search_items'       => __( 'Search Objednat', 'bussenschalter' ),
        'parent_item_colon'  => __( 'Parent Objednat:', 'bussenschalter' ),
        'not_found'          => __( 'No Objednat found.', 'bussenschalter' ),
        'not_found_in_trash' => __( 'No Objednat found in Trash.', 'bussenschalter' )
    );

    $args = array(
        'labels'             => $labels,
        'description'        => __( 'This is the Violation custom post type.', 'bussenschalter' ),
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'objednat' ),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'menu_icon' => 'dashicons-admin-post',
        'show_in_menu'=> 'my-top-level-slug',
        'supports'           => array( 'title' )
    );

register_post_type( 'objednat', $args );
}

add_action( 'init', 'load_objednat_post_types' );

//custom objednats list
add_filter( 'manage_edit-objednat_columns', 'edit_objednat_columns' ) ;

function edit_objednat_columns( $columns ) {
	$columns = array(
		'title' => __( 'Název' ),
		'adresa_instalace' => __( 'Adresa instalace' ),
		'kraj' => __('kraj'),
        'akumulace_do:' => __('Akumulace do:'),
        'vyberte_balicek' => __('Vyberte balíček'),
        'vyberte_dotaci' => __('Vyberte dotaci'),
        'sleva_v_%' => __('Sleva v %'),
        'cena_konstrukce' => __('Cena Konstrukce'),
        'vice_prace' => __('Více práce'),
        'vlastni_oznaceni_nabidky' => __('Vlastní označení nabídky'),
        'typ_strechy' => __('Typ střechy'),
        'rozmer_strechy_-_v_metrech' => __('Rozměr střechy - v metrech'),
        'material_krytiny' => __('Materiál krytiny'),
        'orientace_strechy' => __('Orientace střechy'),
        'elektromer_stav' => __('Elektroměr stav'),
        'elektromer_umisteni' => __('Elektroměr umístění'),
        'elektromer_vyska_od_zeme' => __('Elektroměr výška od země'),   
        'hlavni_jistic_faze' => __('Hlavní jistič Fáze'),
        'hlavni_jistic_proud' => __('Hlavní jistič proud'),
        'hlavni_jistic_char' => __('Hlavní jistič char'),
        'hlavni_jistic_zkrat' => __('Hlavní jistič zkrat'),
        'hlavni_jistic_vyberte_obrazek' => __('Hlavní jistič Vyberte obrázek'),
        'hlavni_domovni_rozvadec' => __('Hlavní domovní rozvaděč'),
        'hlavni_domovni_rozvadec_volne_moduly' => __('Hlavní domovní rozvaděč Volné moduly'),
        'hlavni_domovni_rozvadec_vyberte_obrazek' => __('Hlavní domovní rozvaděč Vyberte obrázek'),
        'elektro_spotreba_v_mwh' => __('Elektro Spotřeba v MWh'),
        'cena_za_kw' => __('Cena za kW'),
        'distributor' => __('Distributor'),
        'cislo_mista_spotreby_ean' => __('Číslo místa spotřeby (EAN)'),
        'vyuctovani' => __('Vyúčtování'),
        'objem_bojleru' => __('Objem bojleru'),
        'pripojeni_k_siti' => __('Připojení k síti'),
        'pocet_osob' => __('Počet osob'),
        'poznamky' => __('Poznámky')
	);
	return $columns;
}

// set default column
add_filter( 'list_table_primary_column', 'objednat_list_table_primary_column', 10, 2 );

function objednat_list_table_primary_column( $default, $screen ) {
    if ( 'edit-objednat' === $screen ) {
        $default = 'nazev';
    }
     
    return $default;
}

add_action( 'manage_objednat_posts_custom_column', 'manage_objednat_columns', 10, 2 );

function manage_objednat_columns($column, $post_id) {
	global $post;

	switch( $column ) {
		case 'cena_nakup' :
            $value = get_field("cena_nakup", $post_id);

			if (empty($value)) echo __('Unknown');
            else printf( __('%s'), $value);

            break;
        case 'cena_prodej' :
            $value = get_post_meta($post_id, 'cena_prodej', true);

			if (empty($value)) echo __('Unknown');
            else printf( __('%s'), $value);
            break;
        case 'staticka' :
            $value = get_post_meta($post_id, 'staticka', true);

            if (empty($value)) echo __('');
            else printf( __('%s'), $value);

            break;
        case 'akce' :
            $value = '<div class="btn btn-edit btn-purple" data-id="'.$post_id.'"><i class="fas fa-pencil-alt"></i> </div>   
                    <div class="btn btn-delete" data-id="'.$post_id.'"> <i class="fas fa-trash-alt"></i></button>';
                    
            if (empty($value)) echo __('Unknown');
            else printf( __('%s'), $value);

            break;
        
		default :
			break;
	}
}
?>