<?php
/**
 * Plugin Name:       Contractor Management Portal
 * Plugin URI:        https://example.com/
 * Description:       A custom plugin to manage contractors, clients, channels, tasks, invoicing, and reporting.
 * Version:           1.0.0
 * Author:            Jules
 * Author URI:        https://example.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       contractor-management-portal
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

// Plugin version.
define( 'CMP_VERSION', '1.0.0' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/post-types.php';
require plugin_dir_path( __FILE__ ) . 'includes/roles.php';

if ( is_admin() ) {
    require plugin_dir_path( __FILE__ ) . 'admin/menu.php';
    require plugin_dir_path( __FILE__ ) . 'admin/meta-boxes.php';
}

/**
 * The function to run on plugin activation.
 */
function cmp_plugin_activate() {
    cmp_add_roles();
    cmp_create_dashboard_page();
    cmp_insert_invoice_statuses();
}
register_activation_hook( __FILE__, 'cmp_plugin_activate' );
register_deactivation_hook( __FILE__, 'cmp_remove_roles' );

/**
 * Add our custom template to the page templates list.
 *
 * @param array $templates The list of page templates.
 * @return array The modified list of page templates.
 */
function cmp_add_dashboard_template_to_select( $templates ) {
    // Add our template
    $templates['templates/dashboard-template.php'] = __( 'Contractor Dashboard', 'contractor-management-portal' );

    return $templates;
}
add_filter( 'theme_page_templates', 'cmp_add_dashboard_template_to_select' );

/**
 * Load the template file from the plugin directory.
 *
 * @param string $template The path to the template file.
 * @return string The path to the template file.
 */
function cmp_load_dashboard_template( $template ) {
    if ( get_page_template_slug() === 'templates/dashboard-template.php' ) {
        return plugin_dir_path( __FILE__ ) . 'templates/dashboard-template.php';
    }
    if ( is_singular( 'task' ) ) {
        return plugin_dir_path( __FILE__ ) . 'templates/single-task.php';
    }
    return $template;
}
add_filter( 'template_include', 'cmp_load_dashboard_template' );

/**
 * Create the contractor dashboard page on plugin activation.
 */
function cmp_create_dashboard_page() {
    // Check if the page already exists
    $dashboard_page = get_page_by_path( 'contractor-dashboard' );

    if ( ! $dashboard_page ) {
        // Create post object
        $page = array(
            'post_title'    => __( 'Contractor Dashboard', 'contractor-management-portal' ),
            'post_name'     => 'contractor-dashboard',
            'post_status'   => 'publish',
            'post_type'     => 'page',
            'post_author'   => 1, // The admin user
            'page_template' => 'templates/dashboard-template.php'
        );

        // Insert the post into the database
        wp_insert_post( $page );
    }
}

/**
 * Handle invoice submission from single task page.
 */
function cmp_handle_invoice_submission() {
    if ( isset( $_POST['action'] ) && $_POST['action'] === 'cmp_submit_invoice' && isset( $_POST['_wpnonce'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'cmp_submit_invoice_nonce' ) ) {
        $task_id = intval( $_POST['task_id'] );
        $task = get_post( $task_id );

        // Create a new invoice post.
        $invoice_id = wp_insert_post( array(
            'post_title' => 'Invoice for ' . $task->post_title,
            'post_type' => 'invoice',
            'post_status' => 'publish',
        ) );

        if ( $invoice_id ) {
            // Assign the task ID to the invoice.
            update_post_meta( $invoice_id, '_cmp_task_id', $task_id );
            // Set the invoice status to 'Pending'.
            wp_set_object_terms( $invoice_id, 'Pending', 'invoice-status' );
            // Redirect to the same page to prevent form resubmission.
            wp_redirect( get_permalink( $task_id ) );
            exit;
        }
    }
}
add_action( 'init', 'cmp_handle_invoice_submission' );
