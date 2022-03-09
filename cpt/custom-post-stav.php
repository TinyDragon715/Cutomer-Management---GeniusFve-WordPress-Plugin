<?php

function load_stav_post_types() {
    $labels = array(
        'name'               => _x( 'Stav', 'post type general name', 'bussenschalter' ),
        'singular_name'      => _x( 'Stav', 'post type singular name', 'bussenschalter' ),
        'menu_name'          => _x( 'Stav', 'admin menu', 'bussenschalter' ),
        'name_admin_bar'     => _x( 'Zákazníci - Stav', 'add new on admin bar', 'bussenschalter' ),
        'add_new'            => _x( 'Add New', 'Stav', 'bussenschalter' ),
        'add_new_item'       => __( 'Typ', 'bussenschalter' ),
        'new_item'           => __( 'New Stav', 'bussenschalter' ),
        'edit_item'          => __( 'Edit Stav', 'bussenschalter' ),
        'view_item'          => __( 'View Stav', 'bussenschalter' ),
        'all_items'          => __( 'Stav', 'bussenschalter' ),
        'search_items'       => __( 'Search Stav', 'bussenschalter' ),
        'parent_item_colon'  => __( 'Parent Stav:', 'bussenschalter' ),
        'not_found'          => __( 'No Stav found.', 'bussenschalter' ),
        'not_found_in_trash' => __( 'No Stav found in Trash.', 'bussenschalter' )
    );

    $args = array(
        'labels'             => $labels,
        'description'        => __( 'This is the Violation custom post type.', 'bussenschalter' ),
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'stav' ),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'menu_icon'          => 'dashicons-admin-post',
        'show_in_menu'       => 'my-top-level-slug',
        'supports'           => array( 'title' )
    );

    register_post_type( 'stav', $args );
}
add_action( 'init', 'load_stav_post_types' );

//custom orders list
add_filter( 'manage_edit-stav_columns', 'edit_stav_columns' ) ;
function edit_stav_columns( $columns ) {
	$columns = array(
        // 'cb',
		'title' => __( 'Typ' ),
        'osoba' => __( 'Odpovědná Osoba' ),
        'barva' => __( 'Barva' ),
	);
	return $columns;
}

// set default column
add_filter( 'list_table_primary_column', 'stav_list_table_primary_column', 10, 2 );
function stav_list_table_primary_column( $default, $screen ) {
    if ( 'edit-stav' === $screen ) {
        $default = 'title';
    }
     
    return $default;
}

add_action( 'manage_stav_posts_custom_column', 'manage_stav_columns', 10, 2 );
function manage_stav_columns($column, $post_id) {
	global $post;

	switch( $column ) {
		case 'osoba' :
            $osoba = get_field('odpovedna_osoba', $post_id);
            $value = $osoba->post_title;

			if (empty($value)) echo __('Unknown');
            else printf( __('%s'), $value);

            break;
        case 'barva':
            $value = get_post_meta($post_id, 'barva', true);

			if (empty($value)) echo __('Unknown');
            else printf( __('%s'), $value);
            
            break;
        default :
			break;
	}
}
?>