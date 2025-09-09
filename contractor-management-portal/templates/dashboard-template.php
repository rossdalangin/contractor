<?php
/**
 * Template Name: Contractor Dashboard
 *
 * This is the template for the contractor dashboard.
 *
 * @package ContractorManagementPortal
 */

// Redirect non-contractors.
if ( ! current_user_can( 'contractor' ) && ! current_user_can( 'manage_options' ) ) {
    wp_redirect( home_url() );
    exit;
}

get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main" role="main">
        <div class="wrap">
            <h1><?php _e( 'Contractor Dashboard', 'contractor-management-portal' ); ?></h1>
            <p><?php _e( 'Welcome to your dashboard. Here you can view your assigned tasks.', 'contractor-management-portal' ); ?></p>

            <h2><?php _e( 'Your Tasks', 'contractor-management-portal' ); ?></h2>
            <?php
            // We will add the task list here later.
            $current_user_id = get_current_user_id();
            $args = array(
                'post_type'      => 'task',
                'posts_per_page' => -1,
                'post_status'    => 'publish',
                'meta_query'     => array(
                    array(
                        'key'     => '_cmp_assigned_contractor',
                        'value'   => $current_user_id,
                        'compare' => '=',
                    ),
                ),
            );
            $tasks_query = new WP_Query( $args );

            if ( $tasks_query->have_posts() ) :
                echo '<ul>';
                while ( $tasks_query->have_posts() ) : $tasks_query->the_post();
                    $task_id = get_the_ID();

                    // Get Task Details
                    $task_statuses = get_the_terms( $task_id, 'task-status' );
                    $due_date = get_post_meta( $task_id, '_cmp_task_due_date', true );
                    $budget = get_post_meta( $task_id, '_cmp_task_budget', true );

                    // Build details string
                    $details = array();
                    if ( ! empty( $task_statuses ) && ! is_wp_error( $task_statuses ) ) {
                        $details[] = 'Status: ' . esc_html( wp_list_pluck( $task_statuses, 'name' )[0] );
                    }
                    if ( ! empty( $due_date ) ) {
                        $details[] = 'Due: ' . esc_html( $due_date );
                    }
                    if ( ! empty( $budget ) ) {
                        $details[] = 'Budget: ' . esc_html( $budget );
                    }

                    $details_string = ! empty($details) ? ' <span class="task-details">(' . implode( ' | ', $details ) . ')</span>' : '';

                    echo '<li><a href="' . get_permalink() . '">' . get_the_title() . '</a>' . $details_string . '</li>';
                endwhile;
                echo '</ul>';
                wp_reset_postdata();
            else :
                echo '<p>' . __( 'No tasks assigned to you yet.', 'contractor-management-portal' ) . '</p>';
            endif;
            ?>
        </div><!-- .wrap -->
    </main><!-- #main -->
</div><!-- #primary -->

<?php get_footer(); ?>
