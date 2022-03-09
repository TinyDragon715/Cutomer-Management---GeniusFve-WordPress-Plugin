<?php

function load_uzivatele_post_types() {
    $labels = array(
        'name'               => _x( 'Uživatele', 'post type general name', 'bussenschalter' ),
        'singular_name'      => _x( 'Uživatele', 'post type singular name', 'bussenschalter' ),
        'menu_name'          => _x( 'Uživatele', 'admin menu', 'bussenschalter' ),
        'name_admin_bar'     => _x( 'Uživatele', 'add new on admin bar', 'bussenschalter' ),
        'add_new'            => _x( 'Add New', 'Uživatele', 'bussenschalter' ),
        'add_new_item'       => __( 'Add New Uživatele', 'bussenschalter' ),
        'new_item'           => __( 'New Uživatele', 'bussenschalter' ),
        'edit_item'          => __( 'Edit Uživatele', 'bussenschalter' ),
        'view_item'          => __( 'View Uživatele', 'bussenschalter' ),
        'all_items'          => __( 'Uživatele', 'bussenschalter' ),
        'search_items'       => __( 'Search Uživatele', 'bussenschalter' ),
        'parent_item_colon'  => __( 'Parent Uživatele:', 'bussenschalter' ),
        'not_found'          => __( 'No Uživatele found.', 'bussenschalter' ),
        'not_found_in_trash' => __( 'No Uživatele found in Trash.', 'bussenschalter' )
    );

    $args = array(
        'labels'             => $labels,
        'description'        => __( 'This is the Violation custom post type.', 'bussenschalter' ),
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'uzivatele' ),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'menu_icon' => 'dashicons-admin-post',
        'show_in_menu'=> 'my-top-level-slug',
        'supports'           => array( 'title' )
    );

register_post_type( 'uzivatele', $args );
}

add_action( 'init', 'load_uzivatele_post_types' );

//custom orders list
add_filter( 'manage_edit-uzivatele_columns', 'edit_uzivatele_columns' ) ;

function edit_uzivatele_columns( $columns ) {
	$columns = array(
		'cislo_oz' => __( 'Číslo oz.' ),
		'jmeno' => __( 'Jméno' ),
		'role' => __('Role'),
        'telefon' => __('Telefon'),
        'email' => __('Email'),
        'akce' => __('Akce')
	);
	return $columns;
}

add_action( 'manage_uzivatele_posts_custom_column', 'manage_uzivatele_columns', 10, 2 );

function manage_uzivatele_columns($column, $post_id) {
	global $post;

	switch( $column ) {
		case 'cislo_oz' :
            // $value = get_post_meta($post_id, 'customer_id', true);
            $value = '19691236';

			if (empty($value)) echo __('Unknown');
            else printf( __('%s'), $value);

            break;
		case 'jmeno' :
            // $value = get_field("customer_name", $post_id);
            $value = 'Jiří Ledahudec';

			if (empty($value)) echo __('Unknown');
            else printf( __('%s'), $value);

            break;
        case 'role' :
            // $value = get_post_meta($post_id, 'customer_id', true);
            $value = '17.08.2021 18:17:01';

			if (empty($value)) echo __('Unknown');
            else printf( __('%s'), $value);
            break;
        case 'telefon' :
            // $value = get_post_meta($post_id, 'customer_id', true);
            $value = '';

            if (empty($value)) echo __('');
            else printf( __('%s'), $value);

            break;

        case 'email' :
            // $value = get_field("customer_name", $post_id);
            $value = 'Jiří Ledahudec';

            if (empty($value)) echo __('Unknown');
            else printf( __('%s'), $value);

            break;
        case 'akce' :
            // $value = get_post_meta($post_id, 'customer_id', true);
            $value = '  </a> <a href="" class="btn btn-view "><i class="fas fa-user-circle"></i></a>
                        </a> <a href="" class="btn btn-view "><i class="fas fa-file-alt"></i></a>
                        </a> <a href="" class="btn btn-view "><i class="fas fa-pencil-alt"></i></a>
                        <a href="" class="btn btn-delete"> <i class="fas fa-trash-alt"></i></a>';
            if (empty($value)) echo __('Unknown');
            else printf( __('%s'), $value);

            break;
        
		default :
			break;
	}
}
?>