<?php

function load_panelyDuporu_post_types() {
    $labels = array(
        'name'               => _x( 'doporučených Panely', 'post type general name', 'bussenschalter' ),
        'singular_name'      => _x( 'doporučených Panely', 'post type singular name', 'bussenschalter' ),
        'menu_name'          => _x( 'doporučených Panely', 'admin menu', 'bussenschalter' ),
        'name_admin_bar'     => _x( 'doporučených Panely', 'add new on admin bar', 'bussenschalter' ),
        'add_new'            => _x( 'Add New', 'doporučených Panely', 'bussenschalter' ),
        'add_new_item'       => __( 'Typ', 'bussenschalter' ),
        'new_item'           => __( 'New doporučených Panely', 'bussenschalter' ),
        'edit_item'          => __( 'Edit doporučených Panely', 'bussenschalter' ),
        'view_item'          => __( 'View doporučených Panely', 'bussenschalter' ),
        'all_items'          => __( 'doporučených Panely', 'bussenschalter' ),
        'search_items'       => __( 'Search doporučených Panely', 'bussenschalter' ),
        'parent_item_colon'  => __( 'Parent doporučených Panely:', 'bussenschalter' ),
        'not_found'          => __( 'No doporučených Panely found.', 'bussenschalter' ),
        'not_found_in_trash' => __( 'No doporučených Panely found in Trash.', 'bussenschalter' )
    );

    $args = array(
        'labels'             => $labels,
        'description'        => __( 'This is the Violation custom post type.', 'bussenschalter' ),
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => false,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'panelyDuporu' ),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'menu_icon'          => 'dashicons-admin-post',
        // 'show_in_menu'       => 'my-top-level-slug',
        'supports'           => array( 'title' )
    );

register_post_type( 'panelyDuporu', $args );
}

add_action( 'init', 'load_panelyDuporu_post_types' );
?>