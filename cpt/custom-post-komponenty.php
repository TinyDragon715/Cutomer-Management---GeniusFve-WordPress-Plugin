<?php

function load_komponenty_post_types() {
    $labels = array(
        'name'               => _x( 'Komponenty', 'post type general name', 'bussenschalter' ),
        'singular_name'      => _x( 'Komponenty', 'post type singular name', 'bussenschalter' ),
        'menu_name'          => _x( 'Komponenty', 'admin menu', 'bussenschalter' ),
        'name_admin_bar'     => _x( 'Komponenty', 'add new on admin bar', 'bussenschalter' ),
        'add_new'            => _x( 'Add New', 'Komponenty', 'bussenschalter' ),
        'add_new_item'       => __( 'Název', 'bussenschalter' ),
        'new_item'           => __( 'New Komponenty', 'bussenschalter' ),
        'edit_item'          => __( 'Edit Komponenty', 'bussenschalter' ),
        'view_item'          => __( 'View Komponenty', 'bussenschalter' ),
        'all_items'          => __( 'Komponenty', 'bussenschalter' ),
        'search_items'       => __( 'Search Komponenty', 'bussenschalter' ),
        'parent_item_colon'  => __( 'Parent Komponenty:', 'bussenschalter' ),
        'not_found'          => __( 'No Komponenty found.', 'bussenschalter' ),
        'not_found_in_trash' => __( 'No Komponenty found in Trash.', 'bussenschalter' )
    );

    $args = array(
        'labels'             => $labels,
        'description'        => __( 'This is the Violation custom post type.', 'bussenschalter' ),
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'komponenty' ),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'menu_icon' => 'dashicons-admin-post',
        'show_in_menu'=> 'my-top-level-slug',
        'supports'           => array( 'title' )
    );

register_post_type( 'komponenty', $args );
}

add_action( 'init', 'load_komponenty_post_types' );

//custom orders list
add_filter( 'manage_edit-komponenty_columns', 'edit_komponenty_columns' ) ;

function edit_komponenty_columns( $columns ) {
	$columns = array(
		'title' => __( 'Název' ),
		'cena_nakup' => __( 'Cena nákup' ),
		'cena_prodej' => __('Cena prodej'),
        'staticka' => __('Statická'),
        'akce' => __('Akce')
	);
	return $columns;
}

// set default column
add_filter( 'list_table_primary_column', 'komponenty_list_table_primary_column', 10, 2 );

function komponenty_list_table_primary_column( $default, $screen ) {
    if ( 'edit-komponenty' === $screen ) {
        $default = 'nazev';
    }
     
    return $default;
}

add_action( 'manage_komponenty_posts_custom_column', 'manage_komponenty_columns', 10, 2 );

function manage_komponenty_columns($column, $post_id) {
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
            $value = '<div class="btn btn-edit btn-purple" data-id="'.$post_id.'" data-toggle="tooltip" data-placement="top" title="Editovat"><i class="fas fa-pencil-alt"></i> </div>   
                    <div class="btn btn-delete" data-id="'.$post_id.'" data-toggle="tooltip" data-placement="top" title="Smazat"> <i class="fas fa-trash-alt"></i></button>';
                    
            if (empty($value)) echo __('Unknown');
            else printf( __('%s'), $value);

            break;
        
		default :
			break;
	}
}
?>