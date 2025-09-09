<?php
/**
 * Template Name: Client Dashboard
 *
 * This is the template for the client dashboard.
 *
 * @package ContractorManagementPortal
 */

// Redirect non-clients.
if ( ! is_user_logged_in() || ! in_array( 'client', (array) wp_get_current_user()->roles ) ) {
    wp_redirect( home_url() );
    exit;
}

get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main" role="main">
        <div class="wrap">
            <h1><?php _e( 'Client Dashboard', 'contractor-management-portal' ); ?></h1>

            <?php
            $current_user = wp_get_current_user();

            // Find the client CPT post associated with the current user.
            $client_posts = get_posts( array(
                'post_type' => 'client',
                'meta_key' => '_cmp_assigned_user_id',
                'meta_value' => $current_user->ID,
                'posts_per_page' => 1
            ) );

            if ( $client_posts ) :
                $client_id = $client_posts[0]->ID;
            ?>

            <h2><?php _e( 'Your Tasks', 'contractor-management-portal' ); ?></h2>
            <?php
            $task_args = array(
                'post_type' => 'task',
                'posts_per_page' => -1,
                'meta_key' => '_cmp_assigned_client',
                'meta_value' => $client_id
            );
            $tasks_query = new WP_Query( $task_args );

            if ( $tasks_query->have_posts() ) :
                echo '<ul>';
                while ( $tasks_query->have_posts() ) : $tasks_query->the_post();
                    $task_id = get_the_ID();

                    // Get details
                    $task_statuses = get_the_terms( $task_id, 'task-status' );
                    $contractor_id = get_post_meta( $task_id, '_cmp_assigned_contractor', true );
                    $contractor = get_userdata( $contractor_id );

                    // Build details string
                    $details = array();
                    if ( ! empty( $task_statuses ) && ! is_wp_error( $task_statuses ) ) {
                        $details[] = 'Status: ' . esc_html( wp_list_pluck( $task_statuses, 'name' )[0] );
                    }
                    if ( $contractor ) {
                        $details[] = 'Contractor: ' . esc_html( $contractor->display_name );
                    }

                    $details_string = ! empty($details) ? ' <span class="task-details">(' . implode( ' | ', $details ) . ')</span>' : '';

                    echo '<li><a href="' . get_permalink() . '">' . get_the_title() . '</a>' . $details_string . '</li>';
                endwhile;
                echo '</ul>';
                wp_reset_postdata();
            else :
                echo '<p>' . __( 'No tasks found.', 'contractor-management-portal' ) . '</p>';
            endif;
            ?>

            <h2><?php _e( 'Your Invoices', 'contractor-management-portal' ); ?></h2>
            <?php
            $task_ids = wp_list_pluck( $tasks_query->posts, 'ID' );
            if ( ! empty( $task_ids ) ) {
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
                $invoices_query = new WP_Query( $invoice_args );

                if ( $invoices_query->have_posts() ) :
                    echo '<ul>';
                    while ( $invoices_query->have_posts() ) : $invoices_query->the_post();
                        $invoice_id = get_the_ID();
                        $status_terms = wp_get_post_terms( $invoice_id, 'invoice-status' );
                        $amount = get_post_meta( $invoice_id, '_cmp_invoice_amount', true );

                        // Build details string
                        $details = array();
                        if ( ! empty( $status_terms ) && ! is_wp_error( $status_terms ) ) {
                            $details[] = 'Status: ' . esc_html( $status_terms[0]->name );
                        }
                        if ( ! empty( $amount ) ) {
                            $details[] = 'Amount: $' . esc_html( number_format( $amount, 2 ) );
                        }

                        $details_string = ! empty($details) ? ' <span class="invoice-details">(' . implode( ' | ', $details ) . ')</span>' : '';

                        echo '<li>' . get_the_title() . $details_string . '</li>';
                    endwhile;
                    echo '</ul>';
                    wp_reset_postdata();
                else :
                    echo '<p>' . __( 'No invoices found.', 'contractor-management-portal' ) . '</p>';
                endif;
            } else {
                echo '<p>' . __( 'No invoices found.', 'contractor-management-portal' ) . '</p>';
            }
            ?>

            <?php else : ?>
                <p><?php _e( 'Your client profile is not set up correctly. Please contact an administrator.', 'contractor-management-portal' ); ?></p>
            <?php endif; ?>

        </div><!-- .wrap -->
    </main><!-- #main -->
</div><!-- #primary -->

<?php get_footer(); ?>
