<?php
/**
 * The template for displaying all single tasks.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package ContractorManagementPortal
 */

get_header(); ?>

    <div id="primary" class="content-area">
        <main id="main" class="site-main" role="main">

            <?php
            while ( have_posts() ) : the_post();

                the_title( '<h1 class="entry-title">', '</h1>' );
                the_content();

                $client_id = get_post_meta( get_the_ID(), '_cmp_assigned_client', true );
                $channel_id = get_post_meta( get_the_ID(), '_cmp_assigned_channel', true );

                if ( $client_id ) {
                    echo '<p><strong>' . __( 'Client:', 'contractor-management-portal' ) . '</strong> <a href="' . get_permalink( $client_id ) . '">' . esc_html( get_the_title( $client_id ) ) . '</a></p>';
                }
                if ( $channel_id ) {
                    echo '<p><strong>' . __( 'Channel:', 'contractor-management-portal' ) . '</strong> <a href="' . get_permalink( $channel_id ) . '">' . esc_html( get_the_title( $channel_id ) ) . '</a></p>';
                }

                // Logic to show the "Submit Invoice" button.
                // 1. The current user must be the assigned contractor for this task.
                // 2. An invoice for this task must not have been submitted yet.
                $current_user_id = get_current_user_id();
                $assigned_contractor_id = get_post_meta( get_the_ID(), '_cmp_assigned_contractor', true );

                if ( $current_user_id == $assigned_contractor_id ) {
                    // Check if an invoice for this task already exists.
                    $args = array(
                        'post_type' => 'invoice',
                        'posts_per_page' => 1,
                        'meta_query' => array(
                            array(
                                'key' => '_cmp_task_id',
                                'value' => get_the_ID()
                            )
                        )
                    );
                    $invoice_query = new WP_Query( $args );

                    if ( ! $invoice_query->have_posts() ) {
                        ?>
                        <form method="post" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="cmp_submit_invoice">
                            <input type="hidden" name="task_id" value="<?php echo get_the_ID(); ?>">
                            <?php wp_nonce_field( 'cmp_submit_invoice_nonce', '_wpnonce' ); ?>

                            <p>
                                <label for="cmp_invoice_amount"><?php _e( 'Amount:', 'contractor-management-portal' ); ?></label><br>
                                <input type="text" id="cmp_invoice_amount" name="cmp_invoice_amount" value="" required />
                            </p>
                            <p>
                                <label for="cmp_invoice_due_date"><?php _e( 'Due Date:', 'contractor-management-portal' ); ?></label><br>
                                <input type="date" id="cmp_invoice_due_date" name="cmp_invoice_due_date" value="" required />
                            </p>
                            <p>
                                <label for="cmp_invoice_file"><?php _e( 'Invoice File:', 'contractor-management-portal' ); ?></label><br>
                                <input type="file" id="cmp_invoice_file" name="cmp_invoice_file" value="" />
                            </p>

                            <input type="submit" value="<?php _e( 'Submit Invoice', 'contractor-management-portal' ); ?>">
                        </form>
                        <?php
                    } else {
                        // Display invoice status.
                        $invoice_query->the_post();
                        $status = wp_get_post_terms( get_the_ID(), 'invoice-status' );
                        if ( ! empty( $status ) ) {
                            echo '<p><strong>' . __( 'Invoice Status:', 'contractor-management-portal' ) . '</strong> ' . $status[0]->name . '</p>';
                        }
                        wp_reset_postdata();
                    }
                }

            endwhile; // End of the loop.
            ?>

        </main><!-- #main -->
    </div><!-- #primary -->

<?php
get_footer();
