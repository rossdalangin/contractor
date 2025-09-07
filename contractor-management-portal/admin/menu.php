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
