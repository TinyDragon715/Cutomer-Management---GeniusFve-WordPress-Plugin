<?php

function load_osoba_post_types() {
    $labels = array(
        'name'               => _x( 'Odpovědná Osoba', 'post type general name', 'bussenschalter' ),
        'singular_name'      => _x( 'Odpovědná Osoba', 'post type singular name', 'bussenschalter' ),
        'menu_name'          => _x( 'Odpovědná Osoba', 'admin menu', 'bussenschalter' ),
        'name_admin_bar'     => _x( 'Odpovědná Osoba', 'add new on admin bar', 'bussenschalter' ),
        'add_new'            => _x( 'Add New', 'Odpovědná Osoba', 'bussenschalter' ),
        'add_new_item'       => __( 'Typ', 'bussenschalter' ),
        'new_item'           => __( 'New Odpovědná Osoba', 'bussenschalter' ),
        'edit_item'          => __( 'Edit Odpovědná Osoba', 'bussenschalter' ),
        'view_item'          => __( 'View Odpovědná Osoba', 'bussenschalter' ),
        'all_items'          => __( 'Odpovědná Osoba', 'bussenschalter' ),
        'search_items'       => __( 'Search Odpovědná Osoba', 'bussenschalter' ),
        'parent_item_colon'  => __( 'Parent Odpovědná Osoba:', 'bussenschalter' ),
        'not_found'          => __( 'No Odpovědná Osoba found.', 'bussenschalter' ),
        'not_found_in_trash' => __( 'No Odpovědná Osoba found in Trash.', 'bussenschalter' )
    );

    $args = array(
        'labels'             => $labels,
        'description'        => __( 'This is the Violation custom post type.', 'bussenschalter' ),
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'osoba' ),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'menu_icon' => 'dashicons-admin-post',
        'show_in_menu'=> 'my-top-level-slug',
        'supports'           => array( 'title' )
    );

    register_post_type( 'osoba', $args );
}
add_action( 'init', 'load_osoba_post_types' );

//custom orders list
// add_filter( 'manage_edit-osoba_columns', 'edit_osoba_columns' ) ;
// function edit_osoba_columns( $columns ) {
// 	$columns = array(
// 		'title' => __( 'Typ' ),
// 	);
// 	return $columns;
// }

// set default column
add_filter( 'list_table_primary_column', 'osoba_list_table_primary_column', 10, 2 );

function osoba_list_table_primary_column( $default, $screen ) {
    if ( 'edit-osoba' === $screen ) {
        $default = 'typ';
    }
     
    return $default;
}

add_action( 'manage_osoba_posts_custom_column', 'manage_osoba_columns', 10, 2 );

function manage_osoba_columns($column, $post_id) {
	global $post;

	switch( $column ) {
		case 'vyrobce' :

            $vyrobci = get_field('vyrobce', $post_id);
            $value = $vyrobci->post_title;

			if (empty($value)) echo __('Unknown');
            else printf( __('%s'), $value);

            break;
        case 'cena_nakup' :
            $value = get_post_meta($post_id, 'cena_nakup', true);

			if (empty($value)) echo __('Unknown');
            else printf( __('%s'), $value);
            break;
        case 'cena_prodej' :
            $value = get_post_meta($post_id, 'cena_prodej', true);

            if (empty($value)) echo __('');
            else printf( __('%s'), $value);

            break;
        case 'vykon' :
            $value = get_post_meta($post_id, 'vykon', true);

			if (empty($value)) echo __('Unknown');
            else printf( __('%s'), $value);

            break;
		case 'svt' :
            $value = get_field("svt", $post_id);

			if (empty($value)) echo __('Unknown');
            else printf( __('%s'), $value);

            break;
        case 'material' :
            $value = get_post_meta($post_id, 'material', true);

			if (empty($value)) echo __('Unknown');
            else printf( __('%s'), $value);
            break;
        case 'j_napeti' :
            $value = get_post_meta($post_id, 'j_napeti', true);

            if (empty($value)) echo __('');
            else printf( __('%s'), $value);

            break;
        case 'j_proud' :
            $value = get_post_meta($post_id, 'j_proud', true);

			if (empty($value)) echo __('Unknown');
            else printf( __('%s'), $value);
            break;
        case 'popis' :
            $value = get_post_meta($post_id, 'popis', true);

            if (empty($value)) echo __('');
            else printf( __('%s'), $value);

            break;
        case 'akce' :
            // $value = get_post_meta($post_id, 'customer_id', true);
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