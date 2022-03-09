<?php

function load_hotove_post_types() {
    $labels = array(
        'name'               => _x( 'Hotové zakázky', 'post type general name', 'bussenschalter' ),
        'singular_name'      => _x( 'Hotové zakázky', 'post type singular name', 'bussenschalter' ),
        'menu_name'          => _x( 'Hotové zakázky', 'admin menu', 'bussenschalter' ),
        'name_admin_bar'     => _x( 'Hotové zakázky', 'add new on admin bar', 'bussenschalter' ),
        'add_new'            => _x( 'Add New', 'Hotové zakázky', 'bussenschalter' ),
        'add_new_item'       => __( 'Add New Hotové zakázky', 'bussenschalter' ),
        'new_item'           => __( 'New Hotové zakázky', 'bussenschalter' ),
        'edit_item'          => __( 'Edit Hotové zakázky', 'bussenschalter' ),
        'view_item'          => __( 'View Hotové zakázky', 'bussenschalter' ),
        'all_items'          => __( 'Hotové zakázky', 'bussenschalter' ),
        'search_items'       => __( 'Search Hotové zakázky', 'bussenschalter' ),
        'parent_item_colon'  => __( 'Parent Hotové zakázky:', 'bussenschalter' ),
        'not_found'          => __( 'No Hotové zakázky found.', 'bussenschalter' ),
        'not_found_in_trash' => __( 'No Hotové zakázky found in Trash.', 'bussenschalter' )
    );

    $args = array(
        'labels'             => $labels,
        'description'        => __( 'Zde jsou zobrazeny všechny nabídky, které jsou ve stavu dokončeno. Ostatní nabídky naleznete v kartě nabídky.', 'bussenschalter' ),
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'hotove' ),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'menu_icon' => 'dashicons-admin-post',
        'show_in_menu'=> 'my-top-level-slug',
        'supports'           => array( 'title' )
    );

    register_post_type( 'hotove', $args );
}

add_action( 'init', 'load_hotove_post_types' );

//custom orders list
add_filter( 'manage_edit-hotove_columns', 'edit_hotove_columns' ) ;

function edit_hotove_columns( $columns ) {
	$columns = array(
		'c' => __( 'Č.' ),
		'zakaznik' => __( 'zákazník' ),
		'datum' => __('Datum'),
        'stav' => __('Stav procesu'),
        'adresa' => __('Adresa instalace'),
        'telefon' => __('Telefon'),
        'mail' => __('E-mail'),
        'akce' => __('Akce')
	);
	return $columns;
}

// set default column
add_filter( 'list_table_primary_column', 'hotove_list_table_primary_column', 10, 2 );

function hotove_list_table_primary_column( $default, $screen ) {
    if ( 'edit-hotove' === $screen ) {
        $default = 'c';
    }
     
    return $default;
}

add_action( 'manage_hotove_posts_custom_column', 'manage_hotove_columns', 10, 2 );

function manage_hotove_columns($column, $post_id) {
	global $post;

	switch( $column ) {
		case 'c' :
            $value = get_post_meta($post_id, 'c', true);

			if (empty($value)) echo __('Unknown');
            else printf( __('%s'), $value);

            break;
		case 'zakaznik' :
            $value = get_field("zakaznik", $post_id);
            // $value = 'Jiří Ledahudec';

			if (empty($value)) echo __('Unknown');
            else printf( __('%s'), $value);

            break;
        case 'datum' :
            $value = get_post_meta($post_id, 'datum', true);
            // $value = '17.08.2021 18:17:01';

			if (empty($value)) echo __('Unknown');
            else printf( __('%s'), $value);
            break;
        case 'stav' :
            // $value = get_post_meta($post_id, 'customer_id', true);
            $value = '';

            if (empty($value)) echo __('');
            else printf( __('%s'), $value);

            break;
        case 'adresa' :
            $value = get_post_meta($post_id, 'adresa', true);
            // $value = 'Pod lesem 499, 270 61 Lány';

            if (empty($value)) echo __('Unknown');
            else printf( __('%s'), $value);

            break;
        case 'telefon' :
            $value = get_post_meta($post_id, 'telefon', true);
            // $value = '602640202';

            if (empty($value)) echo __('Unknown');
            else printf( __('%s'), $value);

            break;
            
        case 'mail' :
            $value = get_post_meta($post_id, 'mail', true);
            // $value = 'ledahudec@gmail.com';

            if (empty($value)) echo __('Unknown');
            else printf( __('%s'), $value);

            break;    
        case 'akce' :
            // $value = get_post_meta($post_id, 'customer_id', true);
            $value = ' <div class="btn btn-view btn-purple"><i class="fas fa-eye"></i> </div> 
            <div class="btn btn-timetable "> <i class="fas fa-calendar-day"></i></div> 
            <div class="btn btn-delete" data-id="'.$post_id.'"> <i class="fas fa-trash-alt"></i></button> ';

            if (empty($value)) echo __('Unknown');
            else printf( __('%s'), $value);

            break;

		default :
			break;
	}
}
?>