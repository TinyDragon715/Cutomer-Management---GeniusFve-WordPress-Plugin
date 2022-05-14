<?php

function load_stav_post_types() {
    $labels = array(
        'name'               => _x( 'Stav', 'post type general name', 'bussenschalter' ),
        'singular_name'      => _x( 'Stav', 'post type singular name', 'bussenschalter' ),
        'menu_name'          => _x( 'Stav', 'admin menu', 'bussenschalter' ),
        'name_admin_bar'     => _x( 'Zákazníci - Stav', 'add new on admin bar', 'bussenschalter' ),
        'add_new'            => _x( 'Add New', 'Stav', 'bussenschalter' ),
        'add_new_item'       => __( 'Typ', 'bussenschalter' ),
        'new_item'           => __( 'New Stav', 'bussenschalter' ),
        'edit_item'          => __( 'Edit Stav', 'bussenschalter' ),
        'view_item'          => __( 'View Stav', 'bussenschalter' ),
        'all_items'          => __( 'Stav', 'bussenschalter' ),
        'search_items'       => __( 'Search Stav', 'bussenschalter' ),
        'parent_item_colon'  => __( 'Parent Stav:', 'bussenschalter' ),
        'not_found'          => __( 'No Stav found.', 'bussenschalter' ),
        'not_found_in_trash' => __( 'No Stav found in Trash.', 'bussenschalter' )
    );

    $args = array(
        'labels'             => $labels,
        'description'        => __( 'This is the Violation custom post type.', 'bussenschalter' ),
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'stav' ),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'menu_icon'          => 'dashicons-admin-post',
        'show_in_menu'       => 'my-top-level-slug',
        'supports'           => array( 'title' )
    );

    register_post_type( 'stav', $args );
}
add_action( 'init', 'load_stav_post_types' );

?>