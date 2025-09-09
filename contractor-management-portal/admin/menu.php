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
 * Add admin submenus.
 */
function cmp_add_submenus() {
    add_submenu_page(
        'contractor-management-portal',
        'Contractors',
        'Contractors',
        'manage_options',
        'users.php?role=contractor'
    );
    add_submenu_page(
        'contractor-management-portal',
        __( 'Reporting', 'contractor-management-portal' ),
        __( 'Reporting', 'contractor-management-portal' ),
        'manage_options',
        'cmp-reporting',
        'cmp_render_reporting_page'
    );
    add_submenu_page(
        'contractor-management-portal',
        __( 'Documents', 'contractor-management-portal' ),
        __( 'Documents', 'contractor-management-portal' ),
        'manage_options',
        'cmp-documents',
        'cmp_render_documents_page'
    );
    add_submenu_page(
        'contractor-management-portal',
        __( 'Settings', 'contractor-management-portal' ),
        __( 'Settings', 'contractor-management-portal' ),
        'manage_options',
        'cmp-settings',
        'cmp_render_settings_page'
    );
}
add_action('admin_menu', 'cmp_add_submenus');

/**
 * Render reporting page.
 */
function cmp_render_reporting_page() {
    ?>
    <div class="wrap">
        <h1><?php _e( 'Reporting & Analytics', 'contractor-management-portal' ); ?></h1>

        <form method="get">
            <input type="hidden" name="page" value="cmp-reporting">
            <?php
            // Get current filter values
            $current_status = isset( $_GET['invoice_status_filter'] ) ? sanitize_text_field( $_GET['invoice_status_filter'] ) : '';
            $start_date = isset( $_GET['start_date_filter'] ) ? sanitize_text_field( $_GET['start_date_filter'] ) : '';
            $end_date = isset( $_GET['end_date_filter'] ) ? sanitize_text_field( $_GET['end_date_filter'] ) : '';

            // Status filter dropdown
            $statuses = get_terms( array( 'taxonomy' => 'invoice-status', 'hide_empty' => false ) );
            echo '<select name="invoice_status_filter">';
            echo '<option value="">' . __( 'All Statuses', 'contractor-management-portal' ) . '</option>';
            foreach ( $statuses as $status ) {
                printf(
                    '<option value="%s"%s>%s</option>',
                    esc_attr( $status->slug ),
                    selected( $current_status, $status->slug, false ),
                    esc_html( $status->name )
                );
            }
            echo '</select>';

            // Date range filters
            echo '<input type="date" name="start_date_filter" value="' . esc_attr( $start_date ) . '">';
            echo '<input type="date" name="end_date_filter" value="' . esc_attr( $end_date ) . '">';

            // Submit button
            submit_button( __( 'Filter Report', 'primary' ), 'primary', 'filter_action', false );
            ?>
        </form>
        <hr/>

        <h2><?php _e( 'Financial Overview', 'contractor-management-portal' ); ?></h2>
        <?php
        $args = array(
            'post_type' => 'invoice',
            'posts_per_page' => -1,
            'post_status' => 'publish',
        );

        // Add tax_query if status is set
        if ( ! empty( $current_status ) ) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'invoice-status',
                    'field'    => 'slug',
                    'terms'    => $current_status,
                ),
            );
        }

        // Add date_query if start or end date is set
        if ( ! empty( $start_date ) || ! empty( $end_date ) ) {
            $args['date_query'] = array(
                'inclusive' => true,
            );
            if ( ! empty( $start_date ) ) {
                $args['date_query']['after'] = $start_date;
            }
            if ( ! empty( $end_date ) ) {
                $args['date_query']['before'] = $end_date;
            }
        }

        $invoices = new WP_Query( $args );

        $financial_data = array(
            'paid' => 0,
            'approved' => 0,
            'pending' => 0,
            'rejected' => 0,
            'total' => 0,
        );

        if ( $invoices->have_posts() ) {
            while ( $invoices->have_posts() ) {
                $invoices->the_post();
                $amount = (float) get_post_meta( get_the_ID(), '_cmp_invoice_amount', true );
                $financial_data['total'] += $amount;

                $statuses = wp_get_post_terms( get_the_ID(), 'invoice-status' );
                if ( ! empty( $statuses ) ) {
                    $status_slug = $statuses[0]->slug;
                    if ( isset( $financial_data[ $status_slug ] ) ) {
                        $financial_data[ $status_slug ] += $amount;
                    }
                }
            }
            wp_reset_postdata();
        }
        ?>
        <table class="widefat fixed" cellspacing="0">
            <thead>
                <tr>
                    <th id="columnname" class="manage-column column-columnname" scope="col"><?php _e( 'Status', 'contractor-management-portal' ); ?></th>
                    <th id="columnname" class="manage-column column-columnname" scope="col"><?php _e( 'Total Amount', 'contractor-management-portal' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?php _e( 'Paid', 'contractor-management-portal' ); ?></td>
                    <td><?php echo esc_html( number_format( $financial_data['paid'], 2 ) ); ?></td>
                </tr>
                <tr class="alternate">
                    <td><?php _e( 'Approved (Unpaid)', 'contractor-management-portal' ); ?></td>
                    <td><?php echo esc_html( number_format( $financial_data['approved'], 2 ) ); ?></td>
                </tr>
                <tr>
                    <td><?php _e( 'Pending', 'contractor-management-portal' ); ?></td>
                    <td><?php echo esc_html( number_format( $financial_data['pending'], 2 ) ); ?></td>
                </tr>
                <tr class="alternate">
                    <td><?php _e( 'Rejected', 'contractor-management-portal' ); ?></td>
                    <td><?php echo esc_html( number_format( $financial_data['rejected'], 2 ) ); ?></td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <th scope="row"><strong><?php _e( 'Total Invoiced', 'contractor-management-portal' ); ?></strong></th>
                    <td><strong><?php echo esc_html( number_format( $financial_data['total'], 2 ) ); ?></strong></td>
                </tr>
            </tfoot>
        </table>

        <h2><?php _e( 'Contractor Productivity', 'contractor-management-portal' ); ?></h2>
        <?php
        $contractors = get_users( array( 'role' => 'contractor' ) );
        ?>
        <table class="widefat fixed" cellspacing="0">
            <thead>
                <tr>
                    <th id="columnname" class="manage-column column-columnname" scope="col"><?php _e( 'Contractor', 'contractor-management-portal' ); ?></th>
                    <th id="columnname" class="manage-column column-columnname" scope="col"><?php _e( 'Total Tasks Assigned', 'contractor-management-portal' ); ?></th>
                    <th id="columnname" class="manage-column column-columnname" scope="col"><?php _e( 'Total Invoiced Amount', 'contractor-management-portal' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $i = 0;
                foreach ( $contractors as $contractor ) :
                    $i++;
                    $class = ( $i % 2 == 0 ) ? 'alternate' : '';

                    // Get total tasks assigned
                    $task_args = array(
                        'post_type' => 'task',
                        'posts_per_page' => -1,
                        'meta_key' => '_cmp_assigned_contractor',
                        'meta_value' => $contractor->ID
                    );
                    $tasks = new WP_Query( $task_args );
                    $total_tasks = $tasks->found_posts;

                    // Get total invoiced amount
                    $total_invoiced = 0;
                    if ( $tasks->have_posts() ) {
                        $task_ids = wp_list_pluck( $tasks->posts, 'ID' );
                        $invoice_args = array(
                            'post_type' => 'invoice',
                            'posts_per_page' => -1,
                            'meta_query' => array(
                                array(
                                    'key' => '_cmp_task_id',
                                    'value' => $task_ids,
                                    'compare' => 'IN'
                                )
                            )
                        );
                        $invoices = new WP_Query( $invoice_args );
                        if ( $invoices->have_posts() ) {
                            while ( $invoices->have_posts() ) {
                                $invoices->the_post();
                                $total_invoiced += (float) get_post_meta( get_the_ID(), '_cmp_invoice_amount', true );
                            }
                            wp_reset_postdata();
                        }
                    }
                    ?>
                    <tr class="<?php echo $class; ?>">
                        <td><?php echo esc_html( $contractor->display_name ); ?></td>
                        <td><?php echo esc_html( $total_tasks ); ?></td>
                        <td><?php echo esc_html( number_format( $total_invoiced, 2 ) ); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    </div>
    <?php
}

/**
 * Render the documents page.
 */
function cmp_render_documents_page() {
    ?>
    <div class="wrap">
        <h1><?php _e( 'Document Management', 'contractor-management-portal' ); ?></h1>
        <p><?php _e( 'Here is a list of all documents uploaded to invoices.', 'contractor-management-portal' ); ?></p>

        <table class="widefat fixed" cellspacing="0">
            <thead>
                <tr>
                    <th><?php _e( 'Invoice', 'contractor-management-portal' ); ?></th>
                    <th><?php _e( 'File Name / Link', 'contractor-management-portal' ); ?></th>
                    <th><?php _e( 'Source', 'contractor-management-portal' ); ?></th>
                    <th><?php _e( 'Date Uploaded', 'contractor-management-portal' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Query for Google Drive files
                $gdrive_invoices = new WP_Query( array(
                    'post_type' => 'invoice',
                    'posts_per_page' => -1,
                    'meta_key' => '_cmp_invoice_gdrive_file_id',
                ) );

                if ( $gdrive_invoices->have_posts() ) {
                    while ( $gdrive_invoices->have_posts() ) {
                        $gdrive_invoices->the_post();
                        $invoice_id = get_the_ID();
                        $gdrive_file_id = get_post_meta( $invoice_id, '_cmp_invoice_gdrive_file_id', true );
                        ?>
                        <tr>
                            <td><a href="<?php echo get_edit_post_link( $invoice_id ); ?>"><?php echo get_the_title(); ?></a></td>
                            <td><a href="https://drive.google.com/file/d/<?php echo esc_attr( $gdrive_file_id ); ?>/view" target="_blank"><?php _e( 'View on Google Drive', 'contractor-management-portal' ); ?></a></td>
                            <td><?php _e( 'Google Drive', 'contractor-management-portal' ); ?></td>
                            <td><?php echo get_the_date(); ?></td>
                        </tr>
                        <?php
                    }
                    wp_reset_postdata();
                }

                // Query for WordPress Media Library files
                $wp_invoices = new WP_Query( array(
                    'post_type' => 'invoice',
                    'posts_per_page' => -1,
                    'meta_key' => '_cmp_invoice_file_id',
                ) );

                if ( $wp_invoices->have_posts() ) {
                    while ( $wp_invoices->have_posts() ) {
                        $wp_invoices->the_post();
                        $invoice_id = get_the_ID();
                        $wp_file_id = get_post_meta( $invoice_id, '_cmp_invoice_file_id', true );
                        $file_link = wp_get_attachment_url( $wp_file_id );
                        $file_name = get_the_title( $wp_file_id );
                        ?>
                        <tr>
                            <td><a href="<?php echo get_edit_post_link( $invoice_id ); ?>"><?php echo get_the_title(); ?></a></td>
                            <td><a href="<?php echo esc_url( $file_link ); ?>" target="_blank"><?php echo esc_html( $file_name ); ?></a></td>
                            <td><?php _e( 'WordPress Media', 'contractor-management-portal' ); ?></td>
                            <td><?php echo get_the_date(); ?></td>
                        </tr>
                        <?php
                    }
                    wp_reset_postdata();
                }

                if ( ! $gdrive_invoices->have_posts() && ! $wp_invoices->have_posts() ) {
                    echo '<tr><td colspan="4">' . __( 'No documents found.', 'contractor-management-portal' ) . '</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
    <?php
}

/**
 * Handle the Stripe payment request.
 */
function cmp_handle_stripe_payment() {
    if ( ! isset( $_GET['action'] ) || 'cmp_pay_with_stripe' !== $_GET['action'] ) {
        return;
    }

    if ( ! isset( $_GET['invoice_id'] ) || ! isset( $_GET['_wpnonce'] ) ) {
        return;
    }

    $invoice_id = intval( $_GET['invoice_id'] );

    if ( ! wp_verify_nonce( $_GET['_wpnonce'], 'cmp_pay_with_stripe_' . $invoice_id ) ) {
        wp_die( 'Invalid nonce.' );
    }

    $options = get_option( 'cmp_options' );
    $secret_key = isset( $options['stripe_secret_key'] ) ? $options['stripe_secret_key'] : '';

    if ( empty( $secret_key ) ) {
        wp_die( 'Stripe API keys are not configured.' );
    }

    $invoice = get_post( $invoice_id );
    $amount = get_post_meta( $invoice_id, '_cmp_invoice_amount', true );
    $amount_in_cents = $amount * 100;

    \Stripe\Stripe::setApiKey( $secret_key );

    try {
        $checkout_session = \Stripe\Checkout\Session::create( [
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => $invoice->post_title,
                    ],
                    'unit_amount' => $amount_in_cents,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => admin_url( 'post.php?post=' . $invoice_id . '&action=edit&payment=success&session_id={CHECKOUT_SESSION_ID}' ),
            'cancel_url' => admin_url( 'post.php?post=' . $invoice_id . '&action=edit&payment=cancelled' ),
            'metadata' => [
                'invoice_id' => $invoice_id,
            ]
        ] );

        wp_redirect( $checkout_session->url );
        exit;

    } catch ( \Stripe\Exception\ApiErrorException $e ) {
        wp_die( 'Error creating Stripe Checkout session: ' . $e->getMessage() );
    }
}
add_action( 'admin_init', 'cmp_handle_stripe_payment' );

/**
 * Handle the Stripe payment success.
 */
function cmp_handle_stripe_payment_success() {
    if ( ! isset( $_GET['payment'] ) || 'success' !== $_GET['payment'] ) {
        return;
    }

    if ( ! isset( $_GET['session_id'] ) ) {
        return;
    }

    $session_id = sanitize_text_field( $_GET['session_id'] );
    $options = get_option( 'cmp_options' );
    $secret_key = isset( $options['stripe_secret_key'] ) ? $options['stripe_secret_key'] : '';

    if ( empty( $secret_key ) ) {
        return;
    }

    \Stripe\Stripe::setApiKey( $secret_key );

    try {
        $session = \Stripe\Checkout\Session::retrieve( $session_id );
        $invoice_id = $session->metadata->invoice_id;

        if ( 'paid' === $session->payment_status ) {
            // Update invoice status to 'Paid'
            wp_set_post_terms( $invoice_id, 'Paid', 'invoice-status' );

            // Store the payment details in the standard meta fields
            update_post_meta( $invoice_id, '_cmp_transaction_id', $session->payment_intent );
            update_post_meta( $invoice_id, '_cmp_payment_method', 'Stripe' );
            update_post_meta( $invoice_id, '_cmp_payment_date', date('Y-m-d') );

            // Add an admin notice
            add_action( 'admin_notices', 'cmp_payment_success_admin_notice' );
        }

    } catch ( \Stripe\Exception\ApiErrorException $e ) {
        // Handle error
        wp_die( 'Error retrieving Stripe Checkout session: ' . $e->getMessage() );
    }
}
add_action( 'admin_init', 'cmp_handle_stripe_payment_success' );

/**
 * Display a success notice.
 */
function cmp_payment_success_admin_notice() {
    ?>
    <div class="notice notice-success is-dismissible">
        <p><?php _e( 'Payment was successful and the invoice status has been updated to Paid.', 'contractor-management-portal' ); ?></p>
    </div>
    <?php
}

/**
 * Render admin dashboard page
 */
function cmp_admin_dashboard_page() {
    ?>
    <div class="wrap">
        <h1><?php _e( 'Contractor Management Dashboard', 'contractor-management-portal' ); ?></h1>
        <p><?php _e( 'Welcome to the Contractor Management Portal. Here you can manage all your contractors, clients, channels, and tasks.', 'contractor-management-portal' ); ?></p>

        <hr>
        <h2><?php _e( 'System Status', 'contractor-management-portal' ); ?></h2>
        <table class="widefat" style="width: 400px;">
            <thead>
                <tr>
                    <th scope="col" style="width: 200px;"><?php _e( 'Component', 'contractor-management-portal' ); ?></th>
                    <th scope="col"><?php _e( 'Status', 'contractor-management-portal' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong><?php _e( 'Wordfence Security', 'contractor-management-portal' ); ?></strong></td>
                    <td>
                        <?php if ( is_plugin_active('wordfence/wordfence.php') ) : ?>
                            <span style="color: green;"><?php _e( 'Active', 'contractor-management-portal' ); ?></span>
                        <?php else : ?>
                            <span style="color: red;"><?php _e( 'Inactive', 'contractor-management-portal' ); ?></span>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr class="alternate">
                    <td><strong><?php _e( 'iThemes Security', 'contractor-management-portal' ); ?></strong></td>
                    <td>
                        <?php if ( is_plugin_active('better-wp-security/better-wp-security.php') ) : ?>
                            <span style="color: green;"><?php _e( 'Active', 'contractor-management-portal' ); ?></span>
                        <?php else : ?>
                            <span style="color: red;"><?php _e( 'Inactive', 'contractor-management-portal' ); ?></span>
                        <?php endif; ?>
                    </td>
                </tr>
            </tbody>
        </table>
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

/**
 * Add filters and export button to the invoice list table.
 */
function cmp_add_invoice_filters_and_export_button( $post_type ) {
    if ( 'invoice' !== $post_type ) {
        return;
    }

    // Status filter
    $statuses = get_terms( array( 'taxonomy' => 'invoice-status', 'hide_empty' => false ) );
    $current_status = isset( $_GET['invoice_status_filter'] ) ? sanitize_text_field( $_GET['invoice_status_filter'] ) : '';

    echo '<select name="invoice_status_filter">';
    echo '<option value="">' . __( 'All Statuses', 'contractor-management-portal' ) . '</option>';
    foreach ( $statuses as $status ) {
        printf(
            '<option value="%s"%s>%s</option>',
            esc_attr( $status->slug ),
            selected( $current_status, $status->slug, false ),
            esc_html( $status->name )
        );
    }
    echo '</select>';

    // Date range filters
    $start_date = isset( $_GET['start_date_filter'] ) ? sanitize_text_field( $_GET['start_date_filter'] ) : '';
    $end_date = isset( $_GET['end_date_filter'] ) ? sanitize_text_field( $_GET['end_date_filter'] ) : '';

    echo '<input type="date" name="start_date_filter" value="' . esc_attr( $start_date ) . '" placeholder="Start Date">';
    echo '<input type="date" name="end_date_filter" value="' . esc_attr( $end_date ) . '" placeholder="End Date">';

    // Export button
    submit_button( __( 'Export to CSV', 'contractor-management-portal' ), 'secondary', 'export_invoices_to_csv', false );
}
add_action( 'restrict_manage_posts', 'cmp_add_invoice_filters_and_export_button' );

/**
 * Handle the CSV export of invoices.
 */
function cmp_export_invoices_to_csv() {
    if ( ! isset( $_GET['export_invoices_to_csv'] ) ) {
        return;
    }

    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    $args = array(
        'post_type'      => 'invoice',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
    );

    $tax_query = array();

    // Status filter
    if ( ! empty( $_GET['invoice_status_filter'] ) ) {
        $tax_query[] = array(
            'taxonomy' => 'invoice-status',
            'field'    => 'slug',
            'terms'    => sanitize_text_field( $_GET['invoice_status_filter'] ),
        );
    }

    // Date range filter
    if ( ! empty( $_GET['start_date_filter'] ) || ! empty( $_GET['end_date_filter'] ) ) {
        $date_query = array();
        if ( ! empty( $_GET['start_date_filter'] ) ) {
            $date_query['after'] = sanitize_text_field( $_GET['start_date_filter'] );
        }
        if ( ! empty( $_GET['end_date_filter'] ) ) {
            $date_query['before'] = sanitize_text_field( $_GET['end_date_filter'] );
        }
        $date_query['inclusive'] = true;
        $args['date_query'] = $date_query;
    }


    if ( ! empty( $tax_query ) ) {
        $args['tax_query'] = $tax_query;
    }

    $invoices_query = new WP_Query( $args );

    if ( $invoices_query->have_posts() ) {
        $filename = 'invoices-' . date( 'Y-m-d' ) . '.csv';

        header( 'Content-Type: text/csv' );
        header( 'Content-Disposition: attachment; filename=' . $filename );

        $output = fopen( 'php://output', 'w' );

        fputcsv( $output, array( 'Invoice Title', 'Task Title', 'Contractor', 'Amount', 'Due Date', 'Status' ) );

        while ( $invoices_query->have_posts() ) {
            $invoices_query->the_post();
            $invoice_id = get_the_ID();
            $task_id = get_post_meta( $invoice_id, '_cmp_task_id', true );
            $task = get_post( $task_id );
            $contractor_id = get_post_meta( $task_id, '_cmp_assigned_contractor', true );
            $contractor = get_user_by( 'ID', $contractor_id );
            $status_terms = wp_get_post_terms( $invoice_id, 'invoice-status' );
            $status = ! empty( $status_terms ) ? $status_terms[0]->name : '';

            $row = array(
                get_the_title(),
                $task ? $task->post_title : '',
                $contractor ? $contractor->display_name : '',
                get_post_meta( $invoice_id, '_cmp_invoice_amount', true ),
                get_post_meta( $invoice_id, '_cmp_invoice_due_date', true ),
                $status,
            );

            fputcsv( $output, $row );
        }

        fclose( $output );
        exit;
    }
}
add_action( 'init', 'cmp_export_invoices_to_csv' );

/**
 * Add custom columns to the task list table.
 *
 * @param array $columns The existing columns.
 * @return array The modified columns.
 */
function cmp_add_task_columns( $columns ) {
    $new_columns = array();
    foreach ( $columns as $key => $value ) {
        $new_columns[$key] = $value;
        if ( $key === 'title' ) {
            $new_columns['task_client'] = __( 'Client', 'contractor-management-portal' );
            $new_columns['task_channel'] = __( 'Channel', 'contractor-management-portal' );
            $new_columns['task_contractor'] = __( 'Contractor', 'contractor-management-portal' );
        }
    }
    return $new_columns;
}
add_filter( 'manage_task_posts_columns', 'cmp_add_task_columns' );

/**
 * Render the custom column content for tasks.
 *
 * @param string $column_name The name of the column.
 * @param int    $post_id     The ID of the post.
 */
function cmp_render_task_columns( $column_name, $post_id ) {
    switch ( $column_name ) {
        case 'task_client':
            $client_id = get_post_meta( $post_id, '_cmp_assigned_client', true );
            if ( $client_id ) {
                echo '<a href="' . get_edit_post_link( $client_id ) . '">' . esc_html( get_the_title( $client_id ) ) . '</a>';
            }
            break;
        case 'task_channel':
            $channel_id = get_post_meta( $post_id, '_cmp_assigned_channel', true );
            if ( $channel_id ) {
                echo '<a href="' . get_edit_post_link( $channel_id ) . '">' . esc_html( get_the_title( $channel_id ) ) . '</a>';
            }
            break;
        case 'task_contractor':
            $contractor_id = get_post_meta( $post_id, '_cmp_assigned_contractor', true );
            if ( $contractor_id ) {
                $user = get_user_by( 'ID', $contractor_id );
                if ( $user ) {
                    echo '<a href="' . get_edit_user_link( $user->ID ) . '">' . esc_html( $user->display_name ) . '</a>';
                }
            }
            break;
    }
}
add_action( 'manage_task_posts_custom_column', 'cmp_render_task_columns', 10, 2 );

/**
 * Add custom columns to the channel list table.
 *
 * @param array $columns The existing columns.
 * @return array The modified columns.
 */
function cmp_add_channel_columns( $columns ) {
    $new_columns = array();
    foreach ( $columns as $key => $value ) {
        $new_columns[$key] = $value;
        if ( $key === 'title' ) {
            $new_columns['youtube_subscribers'] = __( 'Subscribers', 'contractor-management-portal' );
            $new_columns['youtube_views'] = __( 'Views', 'contractor-management-portal' );
            $new_columns['youtube_videos'] = __( 'Videos', 'contractor-management-portal' );
        }
    }
    return $new_columns;
}
add_filter( 'manage_channel_posts_columns', 'cmp_add_channel_columns' );

/**
 * Render the custom column content for channels.
 *
 * @param string $column_name The name of the column.
 * @param int    $post_id     The ID of the post.
 */
function cmp_render_channel_columns( $column_name, $post_id ) {
    $channel_id = get_post_meta( $post_id, '_cmp_youtube_channel_id', true );
    if ( empty( $channel_id ) ) {
        return;
    }

    // To avoid making an API call for every row, we'll store the stats in a transient.
    $transient_key = 'cmp_yt_stats_' . $channel_id;
    $stats = get_transient( $transient_key );

    if ( false === $stats ) {
        $stats = cmp_get_youtube_channel_stats( $channel_id );
        // Cache the result for 1 hour.
        set_transient( $transient_key, $stats, HOUR_IN_SECONDS );
    }

    if ( ! $stats ) {
        return;
    }

    switch ( $column_name ) {
        case 'youtube_subscribers':
            echo esc_html( number_format_i18n( $stats['subscriberCount'] ) );
            break;
        case 'youtube_views':
            echo esc_html( number_format_i18n( $stats['viewCount'] ) );
            break;
        case 'youtube_videos':
            echo esc_html( number_format_i18n( $stats['videoCount'] ) );
            break;
    }
}
add_action( 'manage_channel_posts_custom_column', 'cmp_render_channel_columns', 10, 2 );

/**
 * Render the settings page.
 */
function cmp_render_settings_page() {
    ?>
    <div class="wrap">
        <h1><?php _e( 'Contractor Management Settings', 'contractor-management-portal' ); ?></h1>
        <form action="options.php" method="post">
            <?php
            settings_fields( 'cmp_settings_group' );
            do_settings_sections( 'cmp-settings' );
            submit_button( 'Save Settings' );
            ?>
        </form>
    </div>
    <?php
}
