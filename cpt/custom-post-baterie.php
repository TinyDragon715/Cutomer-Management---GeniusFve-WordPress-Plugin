<?php

function load_baterie_post_types() {
    $labels = array(
        'name'               => _x( 'Baterie', 'post type general name', 'bussenschalter' ),
        'singular_name'      => _x( 'Baterie', 'post type singular name', 'bussenschalter' ),
        'menu_name'          => _x( 'Baterie', 'admin menu', 'bussenschalter' ),
        'name_admin_bar'     => _x( 'Baterie', 'add new on admin bar', 'bussenschalter' ),
        'add_new'            => _x( 'Add New', 'Baterie', 'bussenschalter' ),
        'add_new_item'       => __( 'Typ', 'bussenschalter' ),
        'new_item'           => __( 'New Baterie', 'bussenschalter' ),
        'edit_item'          => __( 'Edit Baterie', 'bussenschalter' ),
        'view_item'          => __( 'View Baterie', 'bussenschalter' ),
        'all_items'          => __( 'Baterie', 'bussenschalter' ),
        'search_items'       => __( 'Search Baterie', 'bussenschalter' ),
        'parent_item_colon'  => __( 'Parent Baterie:', 'bussenschalter' ),
        'not_found'          => __( 'No Baterie found.', 'bussenschalter' ),
        'not_found_in_trash' => __( 'No Baterie found in Trash.', 'bussenschalter' )
    );

    $args = array(
        'labels'             => $labels,
        'description'        => __( 'This is the Violation custom post type.', 'bussenschalter' ),
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'baterie' ),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'menu_icon' => 'dashicons-admin-post',
        'show_in_menu'=> 'my-top-level-slug',
        'supports'           => array( 'title' )
    );

register_post_type( 'baterie', $args );
}

add_action( 'init', 'load_baterie_post_types' );

//custom orders list
add_filter( 'manage_edit-baterie_columns', 'edit_baterie_columns' ) ;

function edit_baterie_columns( $columns ) {
	$columns = array(
		'title' => __( 'Typ' ),
		'vyrobce' => __( 'Výrobce' ),
		'kapacita' => __('Kapacita'),
        'kryti' => __('Krytí'),
        'cena_nakup' => __('Cena nákup'),
        'cena_prodej' => __('Cena prodej'),
        'popis' => __('Popis'),
        'akce' => __('Akce')
	);
	return $columns;
}

// set default column
add_filter( 'list_table_primary_column', 'baterie_list_table_primary_column', 10, 2 );

function baterie_list_table_primary_column( $default, $screen ) {
    if ( 'edit-baterie' === $screen ) {
        $default = 'typ';
    }
     
    return $default;
}

add_action( 'manage_baterie_posts_custom_column', 'manage_baterie_columns', 10, 2 );
function manage_baterie_columns($column, $post_id) {
	global $post;

	switch( $column ) {
		case 'vyrobce' :
            $vyrobci = get_field('vyrobce', $post_id);
            $value = $vyrobci->post_title;

			if (empty($value)) echo __('Unknown');
            else printf( __('%s'), $value);

            break;
        case 'kapacita' :
            $value = get_post_meta($post_id, 'kapacita', true);

			if (empty($value)) echo __('Unknown');
            else printf( __('%s'), $value);
            break;
        case 'kryti' :
            $value = get_post_meta($post_id, 'kryti', true);

            if (empty($value)) echo __('');
            else printf( __('%s'), $value);

            break;

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
        case 'popis' :
            $value = get_post_meta($post_id, 'popis', true);

            if (empty($value)) echo __('');
            else printf( __('%s'), $value);

            break;
        case 'akce' :
            $url = get_post_meta($post_id, 'url', true) ;
            if ($url)
            $value = '<div class="btn btn-edit btn-purple" data-id="'.$post_id.'" data-toggle="tooltip" data-placement="top" title="Editovat"><i class="fas fa-pencil-alt"></i> </div>   
            <a href="'.$url.'" class="btn btn-link btn-green"  data-id="'.$post_id.'" data-toggle="tooltip" data-placement="top" title="Do obchodu" target="_blank"> <i class="fas fa-cart-plus"></i></a>    
            <div class="btn btn-delete" data-id="'.$post_id.'" data-toggle="tooltip" data-placement="top" title="Smazat"> <i class="fas fa-trash-alt"></i></button>';
            else $value = '
                    <div class="btn btn-edit btn-purple" data-id="'.$post_id.'" data-toggle="tooltip" data-placement="top" title="Editovat"><i class="fas fa-pencil-alt"></i> </div>   
                    <div class="btn btn-delete" data-id="'.$post_id.'" data-toggle="tooltip" data-placement="top" title="Smazat"> <i class="fas fa-trash-alt"></i></div>';

            if (empty($value)) echo __('Unknown');
            else printf( __('%s'), $value);

            break;
        
		default :
			break;
	}
}
?>