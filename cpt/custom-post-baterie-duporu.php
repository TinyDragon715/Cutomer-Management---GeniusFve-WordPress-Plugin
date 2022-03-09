<?php

function load_balickyDuporu_post_types() {
    $labels = array(
        'name'               => _x( 'doporučených Baterie', 'post type general name', 'bussenschalter' ),
        'singular_name'      => _x( 'doporučených Baterie', 'post type singular name', 'bussenschalter' ),
        'menu_name'          => _x( 'doporučených Baterie', 'admin menu', 'bussenschalter' ),
        'name_admin_bar'     => _x( 'doporučených Baterie', 'add new on admin bar', 'bussenschalter' ),
        'add_new'            => _x( 'Add New', 'doporučených Baterie', 'bussenschalter' ),
        'add_new_item'       => __( 'Typ', 'bussenschalter' ),
        'new_item'           => __( 'New doporučených Baterie', 'bussenschalter' ),
        'edit_item'          => __( 'Edit doporučených Baterie', 'bussenschalter' ),
        'view_item'          => __( 'View doporučených Baterie', 'bussenschalter' ),
        'all_items'          => __( 'doporučených Baterie', 'bussenschalter' ),
        'search_items'       => __( 'Search doporučených Baterie', 'bussenschalter' ),
        'parent_item_colon'  => __( 'Parent doporučených Baterie:', 'bussenschalter' ),
        'not_found'          => __( 'No doporučených Baterie found.', 'bussenschalter' ),
        'not_found_in_trash' => __( 'No doporučených Baterie found in Trash.', 'bussenschalter' )
    );

    $args = array(
        'labels'             => $labels,
        'description'        => __( 'This is the Violation custom post type.', 'bussenschalter' ),
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => false,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'balickyDuporu' ),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'menu_icon'          => 'dashicons-admin-post',
        // 'show_in_menu'       => 'my-top-level-slug',
        'supports'           => array( 'title' )
    );

register_post_type( 'balickyDuporu', $args );
}

add_action( 'init', 'load_balickyDuporu_post_types' );
?>