<?php

function load_vyrobci_post_types() {
    $labels = array(
        'name'               => _x( 'Výrobci', 'post type general name', 'bussenschalter' ),
        'singular_name'      => _x( 'Výrobci', 'post type singular name', 'bussenschalter' ),
        'menu_name'          => _x( 'Výrobci', 'admin menu', 'bussenschalter' ),
        'name_admin_bar'     => _x( 'Výrobci', 'add new on admin bar', 'bussenschalter' ),
        'add_new'            => _x( 'Add New', 'Výrobci', 'bussenschalter' ),
        'add_new_item'       => __( 'Název', 'bussenschalter' ),
        'new_item'           => __( 'New Výrobci', 'bussenschalter' ),
        'edit_item'          => __( 'Edit Výrobci', 'bussenschalter' ),
        'view_item'          => __( 'View Výrobci', 'bussenschalter' ),
        'all_items'          => __( 'Výrobci', 'bussenschalter' ),
        'search_items'       => __( 'Search Výrobci', 'bussenschalter' ),
        'parent_item_colon'  => __( 'Parent Výrobci:', 'bussenschalter' ),
        'not_found'          => __( 'No Výrobci found.', 'bussenschalter' ),
        'not_found_in_trash' => __( 'No Výrobci found in Trash.', 'bussenschalter' )
    );

    $args = array(
        'labels'             => $labels,
        'description'        => __( 'This is the Violation custom post type.', 'bussenschalter' ),
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'vyrobci' ),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'menu_icon' => 'dashicons-admin-post',
        'show_in_menu'=> 'my-top-level-slug',
        'supports'           => array( 'title' )
    );

register_post_type( 'vyrobci', $args );
}

// set default column
add_filter( 'list_table_primary_column', 'vyrobci_list_table_primary_column', 10, 2 );

function vyrobci_list_table_primary_column( $default, $screen ) {
    if ( 'edit-vyrobci' === $screen ) {
        $default = 'nazev';
    }
     
    return $default;
}

add_action( 'init', 'load_vyrobci_post_types' );

//custom orders list
add_filter( 'manage_edit-vyrobci_columns', 'edit_vyrobci_columns' ) ;

function edit_vyrobci_columns( $columns ) {
	$columns = array(
		'title' => __( 'Název' ),
		'sortiment' => __( 'Sortiment' ),
        'akce' => __('Akce')
	);
	return $columns;
}

add_action( 'manage_vyrobci_posts_custom_column', 'manage_vyrobci_columns', 10, 2 );

function manage_vyrobci_columns($column, $post_id) {
	global $post;

	switch( $column ) {
		case 'sortiment' :
            $value = get_field("sortiment", $post_id);

			if (empty($value)) echo __('Unknown');
            else printf( __('%s'), $value);

            break;
        case 'akce' :
            $value = '<div class="btn btn-delete" data-id="'.$post_id.'" data-toggle="tooltip" data-placement="top" title="Smazat"> <i class="fas fa-trash-alt"></i></button> ';

            if (empty($value)) echo __('Unknown');
            else printf( __('%s'), $value);

            break;
        
		default :
			break;
	}
}
?>