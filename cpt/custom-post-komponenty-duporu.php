<?php

function load_komponentyDuporu_post_types() {
    $labels = array(
        'name'               => _x( 'doporučených Komponenty', 'post type general name', 'bussenschalter' ),
        'singular_name'      => _x( 'doporučených Komponenty', 'post type singular name', 'bussenschalter' ),
        'menu_name'          => _x( 'doporučených Komponenty', 'admin menu', 'bussenschalter' ),
        'name_admin_bar'     => _x( 'doporučených Komponenty', 'add new on admin bar', 'bussenschalter' ),
        'add_new'            => _x( 'Add New', 'doporučených Komponenty', 'bussenschalter' ),
        'add_new_item'       => __( 'Název', 'bussenschalter' ),
        'new_item'           => __( 'New doporučených Komponenty', 'bussenschalter' ),
        'edit_item'          => __( 'Edit doporučených Komponenty', 'bussenschalter' ),
        'view_item'          => __( 'View doporučených Komponenty', 'bussenschalter' ),
        'all_items'          => __( 'doporučených Komponenty', 'bussenschalter' ),
        'search_items'       => __( 'Search doporučených Komponenty', 'bussenschalter' ),
        'parent_item_colon'  => __( 'Parent doporučených Komponenty:', 'bussenschalter' ),
        'not_found'          => __( 'No doporučených Komponenty found.', 'bussenschalter' ),
        'not_found_in_trash' => __( 'No doporučených Komponenty found in Trash.', 'bussenschalter' )
    );

    $args = array(
        'labels'             => $labels,
        'description'        => __( 'This is the Violation custom post type.', 'bussenschalter' ),
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => false,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'komponentyDuporu' ),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'menu_icon'          => 'dashicons-admin-post',
        // 'show_in_menu'       => 'my-top-level-slug',
        'supports'           => array( 'title' )
    );

register_post_type( 'komponentyDuporu', $args );
}

add_action( 'init', 'load_komponentyDuporu_post_types' );
?>