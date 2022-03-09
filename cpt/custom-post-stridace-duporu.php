<?php

function load_stridaceDuporu_post_types() {
    $labels = array(
        'name'               => _x( 'doporučených Střídače', 'post type general name', 'bussenschalter' ),
        'singular_name'      => _x( 'doporučených Střídače', 'post type singular name', 'bussenschalter' ),
        'menu_name'          => _x( 'doporučených Střídače', 'admin menu', 'bussenschalter' ),
        'name_admin_bar'     => _x( 'doporučených Střídače', 'add new on admin bar', 'bussenschalter' ),
        'add_new'            => _x( 'Add New', 'doporučených Střídače', 'bussenschalter' ),
        'add_new_item'       => __( 'Typ', 'bussenschalter' ),
        'new_item'           => __( 'New doporučených Střídače', 'bussenschalter' ),
        'edit_item'          => __( 'Edit doporučených Střídače', 'bussenschalter' ),
        'view_item'          => __( 'View doporučených Střídače', 'bussenschalter' ),
        'all_items'          => __( 'doporučených Střídače', 'bussenschalter' ),
        'search_items'       => __( 'Search doporučených Střídače', 'bussenschalter' ),
        'parent_item_colon'  => __( 'Parent doporučených Střídače:', 'bussenschalter' ),
        'not_found'          => __( 'No doporučených Střídače found.', 'bussenschalter' ),
        'not_found_in_trash' => __( 'No doporučených Střídače found in Trash.', 'bussenschalter' )
    );

    $args = array(
        'labels'             => $labels,
        'description'        => __( 'This is the Violation custom post type.', 'bussenschalter' ),
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => false,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'stridaceDuporu' ),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'menu_icon'          => 'dashicons-admin-post',
        // 'show_in_menu'       => 'my-top-level-slug',
        'supports'           => array( 'title' )
    );

register_post_type( 'stridaceDuporu', $args );
}

add_action( 'init', 'load_stridaceDuporu_post_types' );
?>