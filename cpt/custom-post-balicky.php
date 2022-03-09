<?php

function load_balicky_post_types() {
    $labels = array(
        'name'               => _x( 'Balíčky', 'post type general name', 'bussenschalter' ),
        'singular_name'      => _x( 'Balíčky', 'post type singular name', 'bussenschalter' ),
        'menu_name'          => _x( 'Balíčky', 'admin menu', 'bussenschalter' ),
        'name_admin_bar'     => _x( 'Balíčky', 'add new on admin bar', 'bussenschalter' ),
        'add_new'            => _x( 'Add New', 'Balíčky', 'bussenschalter' ),
        'add_new_item'       => __( 'Typ', 'bussenschalter' ),
        'new_item'           => __( 'New Balíčky', 'bussenschalter' ),
        'edit_item'          => __( 'Edit Balíčky', 'bussenschalter' ),
        'view_item'          => __( 'View Balíčky', 'bussenschalter' ),
        'all_items'          => __( 'Balíčky', 'bussenschalter' ),
        'search_items'       => __( 'Search Balíčky', 'bussenschalter' ),
        'parent_item_colon'  => __( 'Parent Balíčky:', 'bussenschalter' ),
        'not_found'          => __( 'No Balíčky found.', 'bussenschalter' ),
        'not_found_in_trash' => __( 'No Balíčky found in Trash.', 'bussenschalter' )
    );

    $args = array(
        'labels'             => $labels,
        'description'        => __( 'This is the Violation custom post type.', 'bussenschalter' ),
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'balicky' ),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'menu_icon' => 'dashicons-admin-post',
        'show_in_menu'=> 'my-top-level-slug',
        'supports'           => array( 'title' )
    );

register_post_type( 'balicky', $args );
}

add_action( 'init', 'load_balicky_post_types' );

//custom orders list
add_filter( 'manage_edit-balicky_columns', 'edit_balicky_columns' ) ;

function edit_balicky_columns( $columns ) {
	$columns = array(
		'title' => __( 'Typ' ),
		'panel' => __( 'Panel' ),
		'baterie' => __('Baterie'),
        'stridac' => __('Střídač'),
        'akce' => __('Akce')
	);
	return $columns;
}

add_action( 'manage_balicky_posts_custom_column', 'manage_balicky_columns', 10, 2 );

function manage_balicky_columns($column, $post_id) {
	global $post;

	switch( $column ) {
		case 'panel' :
            $panel = get_field('panel', $post_id);
            $value = $panel->post_title;

			if (empty($value)) echo __('Unknown');
            else printf( __('%s'), $value);

            break;
        case 'baterie' :
            $baterie = get_field('baterie', $post_id);
            $value = $baterie->post_title;
            // $value = get_post_meta($baterie->ID, 'typ', true);

			if (empty($value)) echo __('Unknown');
            else printf( __('%s'), $value);
            break;
        case 'stridac' :
            $stridac = get_field('stridac', $post_id);
            $value = $stridac->post_title;
            // $value = get_post_meta($stridac->ID, 'typ', true);

            if (empty($value)) echo __('');
            else printf( __('%s'), $value);

            break;
        case 'akce' :
            // $value = get_post_meta($post_id, 'customer_id', true);
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