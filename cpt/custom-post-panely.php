<?php

function load_panely_post_types() {
    $labels = array(
        'name'               => _x( 'Panely', 'post type general name', 'bussenschalter' ),
        'singular_name'      => _x( 'Panely', 'post type singular name', 'bussenschalter' ),
        'menu_name'          => _x( 'Panely', 'admin menu', 'bussenschalter' ),
        'name_admin_bar'     => _x( 'Panely', 'add new on admin bar', 'bussenschalter' ),
        'add_new'            => _x( 'Add New', 'Panely', 'bussenschalter' ),
        'add_new_item'       => __( 'Typ', 'bussenschalter' ),
        'new_item'           => __( 'New Panely', 'bussenschalter' ),
        'edit_item'          => __( 'Edit Panely', 'bussenschalter' ),
        'view_item'          => __( 'View Panely', 'bussenschalter' ),
        'all_items'          => __( 'Panely', 'bussenschalter' ),
        'search_items'       => __( 'Search Panely', 'bussenschalter' ),
        'parent_item_colon'  => __( 'Parent Panely:', 'bussenschalter' ),
        'not_found'          => __( 'No Panely found.', 'bussenschalter' ),
        'not_found_in_trash' => __( 'No Panely found in Trash.', 'bussenschalter' )
    );

    $args = array(
        'labels'             => $labels,
        'description'        => __( 'This is the Violation custom post type.', 'bussenschalter' ),
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'panely' ),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'menu_icon' => 'dashicons-admin-post',
        'show_in_menu'=> 'my-top-level-slug',
        'supports'           => array( 'title' )
    );

register_post_type( 'panely', $args );
}

add_action( 'init', 'load_panely_post_types' );


//custom orders list
add_filter( 'manage_edit-panely_columns', 'edit_panely_columns' ) ;

function edit_panely_columns( $columns ) {
	$columns = array(
		'title' => __( 'Typ' ),
		'vyrobce' => __( 'Výrobce' ),
		'cena_nakup' => __('Cena nákup'),
        'cena_prodej' => __('Cena prodej'),
        'vykon' => __('Výkon'),
        'svt' => __('SVT'),
        'material' => __('Materiál'),
        'j_napeti' => __('J. Napětí'),
        'j_proud' => __('J. Proud'),
        'popis' => __('Popis'),
        'akce' => __('Akce')
	);
	return $columns;
}

// set default column
add_filter( 'list_table_primary_column', 'panely_list_table_primary_column', 10, 2 );

function panely_list_table_primary_column( $default, $screen ) {
    if ( 'edit-panely' === $screen ) {
        $default = 'typ';
    }
     
    return $default;
}

add_action( 'manage_panely_posts_custom_column', 'manage_panely_columns', 10, 2 );

function manage_panely_columns($column, $post_id) {
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
            $url = get_post_meta($post_id, 'url', true) ;
            if ($url)
            $value = '
                    <div class="btn btn-edit btn-purple" data-id="'.$post_id.'" data-toggle="tooltip" data-placement="top" title="Editovat"><i class="fas fa-pencil-alt"></i> </div>   
                    <a href="'.$url.'" class="btn btn-link btn-green"  data-id="'.$post_id.'" data-toggle="tooltip" data-placement="top" title="Do obchodu" target="_blank"> <i class="fas fa-cart-plus"></i></a>
                    <div class="btn btn-delete" data-id="'.$post_id.'" data-toggle="tooltip" data-placement="top" title="Smazat"> <i class="fas fa-trash-alt"></i></div>';
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