<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Add admin menu page
 */
function cmp_admin_menu() {
    add_menu_page(
        __( 'Contractor Management', 'contractor-management-portal' ),
        __( 'Contractor Mgmt', 'contractor-management-portal' ),
        'manage_options',
        'contractor-management-portal',
        'cmp_admin_dashboard_page',
        'dashicons-groups',
        3
    );
}
add_action( 'admin_menu', 'cmp_admin_menu' );

/**
 * Add Contractors submenu.
 */
function cmp_add_contractors_submenu() {
    add_submenu_page(
        'contractor-management-portal',
        'Contractors',
        'Contractors',
        'manage_options',
        'users.php?role=contractor'
    );
}
add_action('admin_menu', 'cmp_add_contractors_submenu');

/**
 * Render admin dashboard page
 */
function cmp_admin_dashboard_page() {
    ?>
    <div class="wrap">
        <h1><?php _e( 'Contractor Management Dashboard', 'contractor-management-portal' ); ?></h1>
        <p><?php _e( 'Welcome to the Contractor Management Portal. Here you can manage all your contractors, clients, channels, and tasks.', 'contractor-management-portal' ); ?></p>
    </div>
    <?php
}

/**
 * Add custom columns to the invoice list table.
 *
 * @param array $columns The existing columns.
 * @return array The modified columns.
 */
function cmp_add_invoice_columns( $columns ) {
    $new_columns = array();
    foreach ( $columns as $key => $value ) {
        $new_columns[$key] = $value;
        if ( $key === 'title' ) {
            $new_columns['invoice_amount'] = __( 'Amount', 'contractor-management-portal' );
            $new_columns['invoice_due_date'] = __( 'Due Date', 'contractor-management-portal' );
        }
    }
    return $new_columns;
}
add_filter( 'manage_invoice_posts_columns', 'cmp_add_invoice_columns' );

/**
 * Render the custom column content.
 *
 * @param string $column_name The name of the column.
 * @param int    $post_id     The ID of the post.
 */
function cmp_render_invoice_columns( $column_name, $post_id ) {
    switch ( $column_name ) {
        case 'invoice_amount':
            echo esc_html( get_post_meta( $post_id, '_cmp_invoice_amount', true ) );
            break;
        case 'invoice_due_date':
            echo esc_html( get_post_meta( $post_id, '_cmp_invoice_due_date', true ) );
            break;
    }
}
add_action( 'manage_invoice_posts_custom_column', 'cmp_render_invoice_columns', 10, 2 );
