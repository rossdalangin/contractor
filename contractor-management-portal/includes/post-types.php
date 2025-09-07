<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register Custom Post Types
 */
function cmp_register_post_types() {


    // CPT for Clients
    $labels_client = array(
        'name'                  => _x( 'Clients', 'Post Type General Name', 'contractor-management-portal' ),
        'singular_name'         => _x( 'Client', 'Post Type Singular Name', 'contractor-management-portal' ),
        'menu_name'             => __( 'Clients', 'contractor-management-portal' ),
        'name_admin_bar'        => __( 'Client', 'contractor-management-portal' ),
        'archives'              => __( 'Client Archives', 'contractor-management-portal' ),
        'attributes'            => __( 'Client Attributes', 'contractor-management-portal' ),
        'parent_item_colon'     => __( 'Parent Client:', 'contractor-management-portal' ),
        'all_items'             => __( 'All Clients', 'contractor-management-portal' ),
        'add_new_item'          => __( 'Add New Client', 'contractor-management-portal' ),
        'add_new'               => __( 'Add New', 'contractor-management-portal' ),
        'new_item'              => __( 'New Client', 'contractor-management-portal' ),
        'edit_item'             => __( 'Edit Client', 'contractor-management-portal' ),
        'update_item'           => __( 'Update Client', 'contractor-management-portal' ),
        'view_item'             => __( 'View Client', 'contractor-management-portal' ),
        'view_items'            => __( 'View Clients', 'contractor-management-portal' ),
        'search_items'          => __( 'Search Client', 'contractor-management-portal' ),
        'not_found'             => __( 'Not found', 'contractor-management-portal' ),
        'not_found_in_trash'    => __( 'Not found in Trash', 'contractor-management-portal' ),
        'featured_image'        => __( 'Featured Image', 'contractor-management-portal' ),
        'set_featured_image'    => __( 'Set featured image', 'contractor-management-portal' ),
        'remove_featured_image' => __( 'Remove featured image', 'contractor-management-portal' ),
        'use_featured_image'    => __( 'Use as featured image', 'contractor-management-portal' ),
        'insert_into_item'      => __( 'Insert into client', 'contractor-management-portal' ),
        'uploaded_to_this_item' => __( 'Uploaded to this client', 'contractor-management-portal' ),
        'items_list'            => __( 'Clients list', 'contractor-management-portal' ),
        'items_list_navigation' => __( 'Clients list navigation', 'contractor-management-portal' ),
        'filter_items_list'     => __( 'Filter clients list', 'contractor-management-portal' ),
    );
    $args_client = array(
        'label'                 => __( 'Client', 'contractor-management-portal' ),
        'description'           => __( 'Post Type for Clients', 'contractor-management-portal' ),
        'labels'                => $labels_client,
        'supports'              => array( 'title', 'editor', 'thumbnail' ),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => 'contractor-management-portal',
        'menu_position'         => 6,
        'menu_icon'             => 'dashicons-businessman',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'post',
        'show_in_rest'          => true,
    );
    register_post_type( 'client', $args_client );

    // CPT for Channels
    $labels_channel = array(
        'name'                  => _x( 'Channels', 'Post Type General Name', 'contractor-management-portal' ),
        'singular_name'         => _x( 'Channel', 'Post Type Singular Name', 'contractor-management-portal' ),
        'menu_name'             => __( 'Channels', 'contractor-management-portal' ),
        'name_admin_bar'        => __( 'Channel', 'contractor-management-portal' ),
        'archives'              => __( 'Channel Archives', 'contractor-management-portal' ),
        'attributes'            => __( 'Channel Attributes', 'contractor-management-portal' ),
        'parent_item_colon'     => __( 'Parent Channel:', 'contractor-management-portal' ),
        'all_items'             => __( 'All Channels', 'contractor-management-portal' ),
        'add_new_item'          => __( 'Add New Channel', 'contractor-management-portal' ),
        'add_new'               => __( 'Add New', 'contractor-management-portal' ),
        'new_item'              => __( 'New Channel', 'contractor-management-portal' ),
        'edit_item'             => __( 'Edit Channel', 'contractor-management-portal' ),
        'update_item'           => __( 'Update Channel', 'contractor-management-portal' ),
        'view_item'             => __( 'View Channel', 'contractor-management-portal' ),
        'view_items'            => __( 'View Channels', 'contractor-management-portal' ),
        'search_items'          => __( 'Search Channel', 'contractor-management-portal' ),
        'not_found'             => __( 'Not found', 'contractor-management-portal' ),
        'not_found_in_trash'    => __( 'Not found in Trash', 'contractor-management-portal' ),
        'featured_image'        => __( 'Featured Image', 'contractor-management-portal' ),
        'set_featured_image'    => __( 'Set featured image', 'contractor-management-portal' ),
        'remove_featured_image' => __( 'Remove featured image', 'contractor-management-portal' ),
        'use_featured_image'    => __( 'Use as featured image', 'contractor-management-portal' ),
        'insert_into_item'      => __( 'Insert into channel', 'contractor-management-portal' ),
        'uploaded_to_this_item' => __( 'Uploaded to this channel', 'contractor-management-portal' ),
        'items_list'            => __( 'Channels list', 'contractor-management-portal' ),
        'items_list_navigation' => __( 'Channels list navigation', 'contractor-management-portal' ),
        'filter_items_list'     => __( 'Filter channels list', 'contractor-management-portal' ),
    );
    $args_channel = array(
        'label'                 => __( 'Channel', 'contractor-management-portal' ),
        'description'           => __( 'Post Type for Channels', 'contractor-management-portal' ),
        'labels'                => $labels_channel,
        'supports'              => array( 'title', 'editor', 'thumbnail' ),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => 'contractor-management-portal',
        'menu_position'         => 7,
        'menu_icon'             => 'dashicons-video-alt3',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'post',
        'show_in_rest'          => true,
    );
    register_post_type( 'channel', $args_channel );

    // CPT for Tasks
    $labels_task = array(
        'name'                  => _x( 'Tasks', 'Post Type General Name', 'contractor-management-portal' ),
        'singular_name'         => _x( 'Task', 'Post Type Singular Name', 'contractor-management-portal' ),
        'menu_name'             => __( 'Tasks', 'contractor-management-portal' ),
        'name_admin_bar'        => __( 'Task', 'contractor-management-portal' ),
        'archives'              => __( 'Task Archives', 'contractor-management-portal' ),
        'attributes'            => __( 'Task Attributes', 'contractor-management-portal' ),
        'parent_item_colon'     => __( 'Parent Task:', 'contractor-management-portal' ),
        'all_items'             => __( 'All Tasks', 'contractor-management-portal' ),
        'add_new_item'          => __( 'Add New Task', 'contractor-management-portal' ),
        'add_new'               => __( 'Add New', 'contractor-management-portal' ),
        'new_item'              => __( 'New Task', 'contractor-management-portal' ),
        'edit_item'             => __( 'Edit Task', 'contractor-management-portal' ),
        'update_item'           => __( 'Update Task', 'contractor-management-portal' ),
        'view_item'             => __( 'View Task', 'contractor-management-portal' ),
        'view_items'            => __( 'View Tasks', 'contractor-management-portal' ),
        'search_items'          => __( 'Search Task', 'contractor-management-portal' ),
        'not_found'             => __( 'Not found', 'contractor-management-portal' ),
        'not_found_in_trash'    => __( 'Not found in Trash', 'contractor-management-portal' ),
        'featured_image'        => __( 'Featured Image', 'contractor-management-portal' ),
        'set_featured_image'    => __( 'Set featured image', 'contractor-management-portal' ),
        'remove_featured_image' => __( 'Remove featured image', 'contractor-management-portal' ),
        'use_featured_image'    => __( 'Use as featured image', 'contractor-management-portal' ),
        'insert_into_item'      => __( 'Insert into task', 'contractor-management-portal' ),
        'uploaded_to_this_item' => __( 'Uploaded to this task', 'contractor-management-portal' ),
        'items_list'            => __( 'Tasks list', 'contractor-management-portal' ),
        'items_list_navigation' => __( 'Tasks list navigation', 'contractor-management-portal' ),
        'filter_items_list'     => __( 'Filter tasks list', 'contractor-management-portal' ),
    );
    $args_task = array(
        'label'                 => __( 'Task', 'contractor-management-portal' ),
        'description'           => __( 'Post Type for Tasks', 'contractor-management-portal' ),
        'labels'                => $labels_task,
        'supports'              => array( 'title', 'editor' ),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => 'contractor-management-portal',
        'menu_position'         => 8,
        'menu_icon'             => 'dashicons-list-view',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'post',
        'show_in_rest'          => true,
    );
    register_post_type( 'task', $args_task );

    // CPT for Invoices
    $labels_invoice = array(
        'name'                  => _x( 'Invoices', 'Post Type General Name', 'contractor-management-portal' ),
        'singular_name'         => _x( 'Invoice', 'Post Type Singular Name', 'contractor-management-portal' ),
        'menu_name'             => __( 'Invoices', 'contractor-management-portal' ),
        'name_admin_bar'        => __( 'Invoice', 'contractor-management-portal' ),
        'archives'              => __( 'Invoice Archives', 'contractor-management-portal' ),
        'attributes'            => __( 'Invoice Attributes', 'contractor-management-portal' ),
        'parent_item_colon'     => __( 'Parent Invoice:', 'contractor-management-portal' ),
        'all_items'             => __( 'All Invoices', 'contractor-management-portal' ),
        'add_new_item'          => __( 'Add New Invoice', 'contractor-management-portal' ),
        'add_new'               => __( 'Add New', 'contractor-management-portal' ),
        'new_item'              => __( 'New Invoice', 'contractor-management-portal' ),
        'edit_item'             => __( 'Edit Invoice', 'contractor-management-portal' ),
        'update_item'           => __( 'Update Invoice', 'contractor-management-portal' ),
        'view_item'             => __( 'View Invoice', 'contractor-management-portal' ),
        'view_items'            => __( 'View Invoices', 'contractor-management-portal' ),
        'search_items'          => __( 'Search Invoice', 'contractor-management-portal' ),
        'not_found'             => __( 'Not found', 'contractor-management-portal' ),
        'not_found_in_trash'    => __( 'Not found in Trash', 'contractor-management-portal' ),
        'featured_image'        => __( 'Featured Image', 'contractor-management-portal' ),
        'set_featured_image'    => __( 'Set featured image', 'contractor-management-portal' ),
        'remove_featured_image' => __( 'Remove featured image', 'contractor-management-portal' ),
        'use_featured_image'    => __( 'Use as featured image', 'contractor-management-portal' ),
        'insert_into_item'      => __( 'Insert into invoice', 'contractor-management-portal' ),
        'uploaded_to_this_item' => __( 'Uploaded to this invoice', 'contractor-management-portal' ),
        'items_list'            => __( 'Invoices list', 'contractor-management-portal' ),
        'items_list_navigation' => __( 'Invoices list navigation', 'contractor-management-portal' ),
        'filter_items_list'     => __( 'Filter invoices list', 'contractor-management-portal' ),
    );
    $args_invoice = array(
        'label'                 => __( 'Invoice', 'contractor-management-portal' ),
        'description'           => __( 'Post Type for Invoices', 'contractor-management-portal' ),
        'labels'                => $labels_invoice,
        'supports'              => array( 'title', 'editor' ),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => 'contractor-management-portal',
        'menu_position'         => 9,
        'menu_icon'             => 'dashicons-media-text',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'post',
        'show_in_rest'          => true,
        'taxonomies'            => array( 'invoice-status' ),
    );
    register_post_type( 'invoice', $args_invoice );

}
add_action( 'init', 'cmp_register_post_types', 0 );

/**
 * Register Custom Taxonomies
 */
function cmp_register_taxonomies() {
    // Invoice Status Taxonomy
    $labels = array(
        'name'              => _x( 'Invoice Statuses', 'taxonomy general name', 'contractor-management-portal' ),
        'singular_name'     => _x( 'Invoice Status', 'taxonomy singular name', 'contractor-management-portal' ),
        'search_items'      => __( 'Search Invoice Statuses', 'contractor-management-portal' ),
        'all_items'         => __( 'All Invoice Statuses', 'contractor-management-portal' ),
        'parent_item'       => __( 'Parent Invoice Status', 'contractor-management-portal' ),
        'parent_item_colon' => __( 'Parent Invoice Status:', 'contractor-management-portal' ),
        'edit_item'         => __( 'Edit Invoice Status', 'contractor-management-portal' ),
        'update_item'       => __( 'Update Invoice Status', 'contractor-management-portal' ),
        'add_new_item'      => __( 'Add New Invoice Status', 'contractor-management-portal' ),
        'new_item_name'     => __( 'New Invoice Status Name', 'contractor-management-portal' ),
        'menu_name'         => __( 'Invoice Statuses', 'contractor-management-portal' ),
    );
    $args = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'invoice-status' ),
        'show_in_rest'      => true,
    );
    register_taxonomy( 'invoice-status', array( 'invoice' ), $args );
}
add_action( 'init', 'cmp_register_taxonomies', 0 );

/**
 * Insert default invoice statuses.
 */
function cmp_insert_invoice_statuses() {
    $statuses = array(
        'Pending'  => 'pending',
        'Approved' => 'approved',
        'Paid'     => 'paid',
        'Rejected' => 'rejected',
    );

    foreach ( $statuses as $status => $slug ) {
        if ( ! term_exists( $status, 'invoice-status' ) ) {
            wp_insert_term( $status, 'invoice-status', array( 'slug' => $slug ) );
        }
    }
}
