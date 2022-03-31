<?php

// add html block for downloading pdf
function admin_violation_pdf_form() {
    global $pagenow;

    if (( $pagenow == 'edit.php' ) && ($_GET['post_type'] == 'nabidky')) {
        echo '
            <form id="generate_pdf_form" method="post" target="generate_pdf_frame" action="'.esc_url(admin_url('admin-post.php')).'">
                <input name="action" type="hidden" value="download_pdf_action">
                <input type="hidden" name="selected_post_id" id="selected_post_id" />
            </form>
            <iframe name="generate_pdf_frame" id="generate_pdf_frame" style="display: none;"></iframe>
        ';

        echo '
            <form id="generate_contrac_pdf_form" method="post" target="generate_contrac_pdf_frame" action="'.esc_url(admin_url('admin-post.php')).'">
                <input name="action" type="hidden" value="download_contrac_pdf_action">
                <input type="hidden" name="selected_contrac_post_id" id="selected_contrac_post_id" />
            </form>
            <iframe name="generate_contrac_pdf_frame" id="generate_contrac_pdf_frame" style="display: none;"></iframe>
        ';

        echo '
            <form id="generate_zakaznic_pdf_form" method="post" target="generate_zakaznic_pdf_frame" action="'.esc_url(admin_url('admin-post.php')).'">
                <input name="action" type="hidden" value="download_zakaznic_pdf_action">
                <input type="hidden" name="selected_zakaznic_post_id" id="selected_zakaznic_post_id" />
            </form>
            <iframe name="generate_zakaznic_pdf_frame" id="generate_zakaznic_pdf_frame" style="display: none;"></iframe>
        ';

        echo '
            <form id="generate_technical_pdf_form" method="post" target="generate_technical_pdf_frame" action="'.esc_url(admin_url('admin-post.php')).'">
                <input name="action" type="hidden" value="download_technical_pdf_action">
                <input type="hidden" name="selected_technical_post_id" id="selected_technical_post_id" />
            </form>
            <iframe name="generate_technical_pdf_frame" id="generate_technical_pdf_frame" style="display: none;"></iframe>
        ';
    }
}
add_action('admin_notices', 'admin_violation_pdf_form');

function load_nabidky_post_types() {
    $labels = array(
        'name'               => _x( 'Nabídky', 'post type general name', 'bussenschalter' ),
        'singular_name'      => _x( 'Nabídky', 'post type singular name', 'bussenschalter' ),
        'menu_name'          => _x( 'Nabídky', 'admin menu', 'bussenschalter' ),
        'name_admin_bar'     => _x( 'Nabídky', 'add new on admin bar', 'bussenschalter' ),
        'add_new'            => _x( 'Add New', 'Nabídky', 'bussenschalter' ),
        'add_new_item'       => __( 'Add New Nabídky', 'bussenschalter' ),
        'new_item'           => __( 'New Nabídky', 'bussenschalter' ),
        'edit_item'          => __( 'Edit Nabídky', 'bussenschalter' ),
        'view_item'          => __( 'View Nabídky', 'bussenschalter' ),
        'all_items'          => __( 'Nabídky', 'bussenschalter' ),
        'search_items'       => __( 'Search Nabídky', 'bussenschalter' ),
        'parent_item_colon'  => __( 'Parent Nabídky:', 'bussenschalter' ),
        'not_found'          => __( 'V tabulce zatím nejsou žádná data.', 'bussenschalter' ),
        'not_found_in_trash' => __( 'No Nabídky found in Trash.', 'bussenschalter' )
    );

    $args = array(
        'labels'             => $labels,
        'description'        => __( 'Zde jsou zobrazeny všechny nabídky, které nejsou ve stavu dokončeno. Dokončené nabídky naleznete v kartě hotové zakázky.
                                    * Při změně stavu na dokončeno, se nebudou do zkompletované nabídky propisovat změny cen jednotlivých komponent (ceny se změnou stavu zafixují).' ),
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'nabidky' ),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'menu_icon' => 'dashicons-admin-post',
        'show_in_menu'=> 'my-top-level-slug',
        'supports'           => array( 'title' )
    );

    register_post_type( 'nabidky', $args );
}

add_action( 'init', 'load_nabidky_post_types' );

//custom orders list
add_filter( 'manage_edit-nabidky_columns', 'edit_nabidky_columns' ) ;

function edit_nabidky_columns( $columns ) {
	$columns = array(
		'c' => __( 'Č.' ),
		'zakaznik' => __( 'zákazník' ),
		'datum' => __('Datum'),
        'stav' => __('Stav procesu'),
        'adresa_instalace' => __('Adresa instalace'),
        'telefon' => __('Telefon'),
        'e-mail' => __('E-mail'),
        'akce' => __('Akce')
	);
	return $columns;
}

// set default column
add_filter( 'list_table_primary_column', 'nabidky_list_table_primary_column', 10, 2 );

function nabidky_list_table_primary_column( $default, $screen ) {
    if ( 'edit-nabidky' === $screen ) {
        $default = 'c';
    }
    return $default;
}


add_action( 'manage_nabidky_posts_custom_column', 'manage_nabidky_columns', 10, 2 );

function manage_nabidky_columns($column, $post_id) {
	global $post;

	switch ($column) {
		case 'c':
            $value = get_post_meta($post_id, 'c', true);

			if (empty($value)) echo __('Unknown');
            else printf( __('%s'), $value);

            break;
		case 'zakaznik':
            $value = get_field("zakaznik", $post_id);

			if (empty($value)) echo __('Unknown');
            else printf( __('%s'), $value);

            break;
        case 'datum':
            $value = get_post_meta($post_id, 'datum', true);

            echo __(get_the_date( 'Y-m-d H:i:s' )); 
			// if (empty($value)) echo __(get_the_date( 'Y-m-d H:i:s' ));
            // else printf( __('%s'), $value);
            break;

        case 'adresa_instalace' :
            $value = get_post_meta($post_id, 'adresa_instalace', true);
                
            if (empty($value)) echo __('');
            else printf( __('%s'), $value);

            break;

        case 'stav' :
            $value = get_post_meta($post_id, 'stav_procesu', true);
                
            if (empty($value)) echo __('Unknown');
            else printf( __('%s'), $value);

            break;
        case 'telefon' :
            $value = get_post_meta($post_id, 'telefon', true);

            if (empty($value)) echo __('Unknown');
            else printf( __('%s'), $value);

            break;
            
        case 'e-mail' :

            $value = get_post_meta($post_id, 'e-mail', true);

            if (empty($value)) echo __('Unknown');
            else printf( __('%s'), $value);

            break;    
        case 'akce' :
            // $value = get_post_meta($post_id, 'customer_id', true);
            $name = get_post_meta($post_id, 'zakaznik', true);
            $post = get_page_by_title($name, OBJECT, 'zakaznik');
            $zakaznici_post_id = $post->ID;
            $obhlidka_post_id = get_post_meta($zakaznici_post_id, 'obhlidka', true);
            $value = '  <button class="btn btn-calendar btn-green" data-id="'.$post_id.'" data-toggle="tooltip" data-placement="top" title="Vygenerovat PDF nabídku"> <i class="fas fa-calendar-alt"></i></button> 
                        <button class="btn btn-contrac btn-purple" data-id="'.$post_id.'" data-toggle="tooltip" data-placement="top" title="Stáhnout smlouvu"><i class="fas fa-copy"></i> </button>';
            if ($obhlidka_post_id) {
                $value .= '<button class="btn btn-zakaznic btn-purple" data-id="'.$post_id.'" data-toggle="tooltip" data-placement="top" title="Stáhnout obhlídkový formulář"><i class="fas fa-address-card"></i> </button>';
            } else {
                $value .= '<button class="btn btn-zakaznic btn-purple" data-id="'.$post_id.'" data-toggle="tooltip" data-placement="top" title="Stáhnout obhlídkový formulář" disabled><i class="fas fa-address-card"></i> </button>';
            }
            $value .= ' <button class="btn btn-technical btn-purple" data-id="'.$post_id.'" data-toggle="tooltip" data-placement="top" title="Vygenerovat Rozpočtovou tabulku"><i class="fas fa-calculator"></i> </button> 
                        <button class="btn btn-edit btn-purple" data-id="'.$post_id.'" data-toggle="tooltip" data-placement="top" title="Editovat"><i class="fas fa-pencil-alt"></i> </button> 
                        <button class="btn btn-delete" data-id="'.$post_id.'" data-toggle="tooltip" data-placement="top" title="Smazat"> <i class="fas fa-trash-alt"></i></button> ';
            if (empty($value)) echo __('Unknown');
            else printf( __('%s'), $value);

            break;

		default :
			break;
	}
}
