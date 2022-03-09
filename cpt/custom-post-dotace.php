<?php

function load_dotace_post_types() {
    $labels = array(
        'name'               => _x( 'Dotace', 'post type general name', 'bussenschalter' ),
        'singular_name'      => _x( 'Dotace', 'post type singular name', 'bussenschalter' ),
        'menu_name'          => _x( 'Dotace', 'admin menu', 'bussenschalter' ),
        'name_admin_bar'     => _x( 'Dotace', 'add new on admin bar', 'bussenschalter' ),
        'add_new'            => _x( 'Add New', 'Dotace', 'bussenschalter' ),
        'add_new_item'       => __( 'Add New Dotace', 'bussenschalter' ),
        'new_item'           => __( 'New Dotace', 'bussenschalter' ),
        'edit_item'          => __( 'Edit Dotace', 'bussenschalter' ),
        'view_item'          => __( 'View Dotace', 'bussenschalter' ),
        'all_items'          => __( 'Dotace', 'bussenschalter' ),
        'search_items'       => __( 'Search Dotace', 'bussenschalter' ),
        'parent_item_colon'  => __( 'Parent Dotace:', 'bussenschalter' ),
        'not_found'          => __( 'V tabulce zatím nejsou žádná data.', 'bussenschalter' ),
        'not_found_in_trash' => __( 'No Dotace found in Trash.', 'bussenschalter' )
    );

    $args = array(
        'labels'             => $labels,
        'description'        => __( 'This is the Violation custom post type.', 'bussenschalter' ),
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'dotace' ),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'menu_icon' => 'dashicons-admin-post',
        'show_in_menu'=> 'my-top-level-slug',
        'supports'           => array( 'title' )
    );

register_post_type( 'dotace', $args );
}

add_action( 'init', 'load_dotace_post_types' );

//custom orders list
add_filter( 'manage_edit-dotace_columns', 'edit_dotace_columns' ) ;

function edit_dotace_columns( $columns ) {
	$columns = array(
		'nazev' => __( 'Název' ),
		'vyse' => __( 'Výše' ),
		'procenta' => __('Procenta'),
        'akce' => __('Akce')
	);
	return $columns;
}


// set default column
add_filter( 'list_table_primary_column', 'dotace_list_table_primary_column', 10, 2 );

function dotace_list_table_primary_column( $default, $screen ) {
    if ( 'edit-dotace' === $screen ) {
        $default = 'nazev';
    }
     
    return $default;
}

add_action( 'manage_dotace_posts_custom_column', 'manage_dotace_columns', 10, 2 );

function manage_dotace_columns($column, $post_id) {
	global $post;

	switch( $column ) {
		case 'nazev' :
            $value = get_post_meta($post_id, 'nazev', true);

			if (empty($value)) echo __('Unknown');
            else printf( __('%s'), $value);

            break;
		case 'vyse' :
            $value = get_field("vyse", $post_id);

			if (empty($value)) echo __('Unknown');
            else printf( __('%s'), $value);

            break;
        case 'procenta' :
            $value = get_post_meta($post_id, 'procenta', true);

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