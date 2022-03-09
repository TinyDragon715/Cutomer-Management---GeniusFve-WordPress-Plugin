<?php

function load_stridace_post_types() {
    $labels = array(
        'name'               => _x( 'Střídače', 'post type general name', 'bussenschalter' ),
        'singular_name'      => _x( 'Střídače', 'post type singular name', 'bussenschalter' ),
        'menu_name'          => _x( 'Střídače', 'admin menu', 'bussenschalter' ),
        'name_admin_bar'     => _x( 'Střídače', 'add new on admin bar', 'bussenschalter' ),
        'add_new'            => _x( 'Add New', 'Střídače', 'bussenschalter' ),
        'add_new_item'       => __( 'Typ', 'bussenschalter' ),
        'new_item'           => __( 'New Střídače', 'bussenschalter' ),
        'edit_item'          => __( 'Edit Střídače', 'bussenschalter' ),
        'view_item'          => __( 'View Střídače', 'bussenschalter' ),
        'all_items'          => __( 'Střídače', 'bussenschalter' ),
        'search_items'       => __( 'Search Střídače', 'bussenschalter' ),
        'parent_item_colon'  => __( 'Parent Střídače:', 'bussenschalter' ),
        'not_found'          => __( 'No Střídače found.', 'bussenschalter' ),
        'not_found_in_trash' => __( 'No Střídače found in Trash.', 'bussenschalter' )
    );

    $args = array(
        'labels'             => $labels,
        'description'        => __( 'This is the Violation custom post type.', 'bussenschalter' ),
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'stridace' ),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'menu_icon' => 'dashicons-admin-post',
        'show_in_menu'=> 'my-top-level-slug',
        'supports'           => array( 'title' )
    );

register_post_type( 'stridace', $args );
}

add_action( 'init', 'load_stridace_post_types' );

//custom orders list
add_filter( 'manage_edit-stridace_columns', 'edit_stridace_columns' ) ;

function edit_stridace_columns( $columns ) {
	$columns = array(
		'title' => __( 'Typ' ),
		'vyrobce' => __( 'Výrobce' ),
		'vykon' => __('Výkon'),
        'max_dc_vstupni_vykon' => __('Max DC vstupní výkon'),
        'max_vstupni_proud' => __('Max vstupní proud'),
        'svt' => __( 'SVT' ),
		'pocet_mppt' => __( 'Počet MPPT' ),
		'rozsah_mppt' => __('Rozsah MPPT'),
        'faze' => __('Fáze'),
        'cena_nakup' => __('Cena nákup'),
        'cena_prodej' => __('Cena prodej'),
        'popis' => __('Popis'),
        'akce' => __('Akce'),
	);
	return $columns;
}


// set default column
add_filter( 'list_table_primary_column', 'stridace_list_table_primary_column', 10, 2 );

function stridace_list_table_primary_column( $default, $screen ) {
    if ( 'edit-stridace' === $screen ) {
        $default = 'typ';
    }
     
    return $default;
}

add_action( 'manage_stridace_posts_custom_column', 'manage_stridace_columns', 10, 2 );

function manage_stridace_columns($column, $post_id) {
	global $post;

	switch( $column ) {
		case 'vyrobce' :
            $vyrobci = get_field('vyrobce',$post_id);
            
            $value = $vyrobci->post_title;

			if (empty($value)) echo __('Unknown');
            else printf( __('%s'), $value);

            break;
        case 'vykon' :
            $value = get_post_meta($post_id, 'vykon', true);

			if (empty($value)) echo __('Unknown');
            else printf( __('%s'), $value);
            break;
        case 'max_dc_vstupni_vykon' :
            $value = get_post_meta($post_id, 'max_dc_vstupni_vykon', true);

            if (empty($value)) echo __('');
            else printf( __('%s'), $value);

            break;

        case 'max_vstupni_proud' :
            $value = get_post_meta($post_id, 'max_vstupni_proud', true);

            if (empty($value)) echo __('');
            else printf( __('%s'), $value);

            break;

        case 'svt' :
            $value = get_post_meta($post_id, 'svt', true);

			if (empty($value)) echo __('Unknown');
            else printf( __('%s'), $value);

            break;
		case 'pocet_mppt' :
            $value = get_field("pocet_mppt", $post_id);

			if (empty($value)) echo __('Unknown');
            else printf( __('%s'), $value);

            break;
        case 'rozsah_mppt' :
            $value = get_post_meta($post_id, 'rozsah_mppt', true);

			if (empty($value)) echo __('Unknown');
            else printf( __('%s'), $value);
            break;
        case 'faze' :
            $value = get_post_meta($post_id, 'faze', true);

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

