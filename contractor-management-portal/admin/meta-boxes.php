<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Renders the task details meta box.
 *
 * @param WP_Post $post The post object.
 */
function cmp_render_task_details_meta_box( $post ) {
    $due_date = get_post_meta( $post->ID, '_cmp_task_due_date', true );
    $budget = get_post_meta( $post->ID, '_cmp_task_budget', true );
    ?>
    <p>
        <label for="cmp_task_due_date"><?php _e( 'Due Date:', 'contractor-management-portal' ); ?></label><br>
        <input type="date" id="cmp_task_due_date" name="cmp_task_due_date" value="<?php echo esc_attr( $due_date ); ?>" />
    </p>
    <p>
        <label for="cmp_task_budget"><?php _e( 'Budget:', 'contractor-management-portal' ); ?></label><br>
        <input type="text" id="cmp_task_budget" name="cmp_task_budget" value="<?php echo esc_attr( $budget ); ?>" />
    </p>
    <?php
}

/**
 * Adds a meta box to the task post type.
 */
function cmp_add_task_meta_box() {
    add_meta_box(
        'cmp_task_contractor_meta_box',
        __( 'Assign Contractor', 'contractor-management-portal' ),
        'cmp_render_task_contractor_meta_box',
        'task',
        'side',
        'default'
    );
    add_meta_box(
        'cmp_task_client_meta_box',
        __( 'Assign Client', 'contractor-management-portal' ),
        'cmp_render_task_client_meta_box',
        'task',
        'side',
        'default'
    );
    add_meta_box(
        'cmp_task_channel_meta_box',
        __( 'Assign Channel', 'contractor-management-portal' ),
        'cmp_render_task_channel_meta_box',
        'task',
        'side',
        'default'
    );
    add_meta_box(
        'cmp_task_details_meta_box',
        __( 'Task Details', 'contractor-management-portal' ),
        'cmp_render_task_details_meta_box',
        'task',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'cmp_add_task_meta_box' );

/**
 * Renders the meta box.
 */
function cmp_render_task_contractor_meta_box( $post ) {
    // Get the list of contractors.
    $contractors = get_users( array( 'role' => 'contractor' ) );
    $assigned_contractor_id = get_post_meta( $post->ID, '_cmp_assigned_contractor', true );

    ?>
    <label for="cmp_contractor_select"><?php _e( 'Select a Contractor:', 'contractor-management-portal' ); ?></label>
    <select name="cmp_assigned_contractor" id="cmp_contractor_select" style="width:100%;">
        <option value=""><?php _e( 'Not Assigned', 'contractor-management-portal' ); ?></option>
        <?php foreach ( $contractors as $contractor ) : ?>
            <option value="<?php echo esc_attr( $contractor->ID ); ?>" <?php selected( $assigned_contractor_id, $contractor->ID ); ?>>
                <?php echo esc_html( $contractor->display_name ); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <?php
}

/**
 * Renders the client meta box.
 */
function cmp_render_task_client_meta_box( $post ) {
    // Add nonce for security and authentication.
    wp_nonce_field( 'cmp_save_task_meta', 'cmp_task_meta_nonce' );

    // Get the list of clients.
    $clients = get_posts( array( 'post_type' => 'client', 'posts_per_page' => -1 ) );
    $assigned_client_id = get_post_meta( $post->ID, '_cmp_assigned_client', true );

    ?>
    <label for="cmp_client_select"><?php _e( 'Select a Client:', 'contractor-management-portal' ); ?></label>
    <select name="cmp_assigned_client" id="cmp_client_select" style="width:100%;">
        <option value=""><?php _e( 'Not Assigned', 'contractor-management-portal' ); ?></option>
        <?php foreach ( $clients as $client ) : ?>
            <option value="<?php echo esc_attr( $client->ID ); ?>" <?php selected( $assigned_client_id, $client->ID ); ?>>
                <?php echo esc_html( $client->post_title ); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <?php
}

/**
 * Renders the channel meta box.
 */
function cmp_render_task_channel_meta_box( $post ) {
    // Get the list of channels.
    $channels = get_posts( array( 'post_type' => 'channel', 'posts_per_page' => -1 ) );
    $assigned_channel_id = get_post_meta( $post->ID, '_cmp_assigned_channel', true );

    ?>
    <label for="cmp_channel_select"><?php _e( 'Select a Channel:', 'contractor-management-portal' ); ?></label>
    <select name="cmp_assigned_channel" id="cmp_channel_select" style="width:100%;">
        <option value=""><?php _e( 'Not Assigned', 'contractor-management-portal' ); ?></option>
        <?php foreach ( $channels as $channel ) : ?>
            <option value="<?php echo esc_attr( $channel->ID ); ?>" <?php selected( $assigned_channel_id, $channel->ID ); ?>>
                <?php echo esc_html( $channel->post_title ); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <?php
}

/**
 * Handles the saving of the task meta boxes.
 *
 * @param int $post_id Post ID.
 */
function cmp_save_task_meta( $post_id ) {
    // Check if our nonce is set.
    if ( ! isset( $_POST['cmp_task_meta_nonce'] ) ) {
        return;
    }

    // Verify that the nonce is valid.
    if ( ! wp_verify_nonce( $_POST['cmp_task_meta_nonce'], 'cmp_save_task_meta' ) ) {
        return;
    }

    // If this is an autosave, our form has not been submitted, so we don't want to do anything.
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    // Check the user's permissions.
    if ( isset( $_POST['post_type'] ) && 'task' == $_POST['post_type'] ) {
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }
    }

    // Sanitize and save the assigned contractor.
    if ( isset( $_POST['cmp_assigned_contractor'] ) ) {
        $new_contractor_id = sanitize_text_field( $_POST['cmp_assigned_contractor'] );
        $old_contractor_id = get_post_meta( $post_id, '_cmp_assigned_contractor', true );

        // Only send email if the contractor has changed and is not empty
        if ( $new_contractor_id && $new_contractor_id !== $old_contractor_id ) {
            $user = get_userdata( $new_contractor_id );
            if ( $user ) {
                $to = $user->user_email;
                $subject = 'You have been assigned a new task';
                $task_title = get_the_title( $post_id );
                $task_url = get_edit_post_link( $post_id );
                $message = "Hello " . $user->display_name . ",\n\nYou have been assigned a new task: '" . $task_title . "'.\n\nYou can view the task here: " . $task_url;
                $headers = array('Content-Type: text/plain; charset=UTF-8');

                wp_mail( $to, $subject, $message, $headers );
            }
        }

        // Now update the meta
        update_post_meta( $post_id, '_cmp_assigned_contractor', $new_contractor_id );
    }

    // Sanitize and save the assigned client.
    if ( isset( $_POST['cmp_assigned_client'] ) ) {
        $client_id = sanitize_text_field( $_POST['cmp_assigned_client'] );
        update_post_meta( $post_id, '_cmp_assigned_client', $client_id );
    }

    // Sanitize and save the assigned channel.
    if ( isset( $_POST['cmp_assigned_channel'] ) ) {
        $channel_id = sanitize_text_field( $_POST['cmp_assigned_channel'] );
        update_post_meta( $post_id, '_cmp_assigned_channel', $channel_id );
    }

    // Sanitize and save the due date.
    if ( isset( $_POST['cmp_task_due_date'] ) ) {
        $due_date = sanitize_text_field( $_POST['cmp_task_due_date'] );
        update_post_meta( $post_id, '_cmp_task_due_date', $due_date );
    }

    // Sanitize and save the budget.
    if ( isset( $_POST['cmp_task_budget'] ) ) {
        $budget = sanitize_text_field( $_POST['cmp_task_budget'] );
        update_post_meta( $post_id, '_cmp_task_budget', $budget );
    }
}
add_action( 'save_post', 'cmp_save_task_meta' );


/**
 * Adds a meta box to the invoice post type.
 */
function cmp_add_invoice_meta_box() {
    add_meta_box(
        'cmp_invoice_details_meta_box',
        __( 'Invoice Details', 'contractor-management-portal' ),
        'cmp_render_invoice_meta_box',
        'invoice',
        'normal',
        'high'
    );
    add_meta_box(
        'cmp_invoice_payment_details_meta_box',
        __( 'Payment Details', 'contractor-management-portal' ),
        'cmp_render_invoice_payment_details_meta_box',
        'invoice',
        'normal',
        'default'
    );
}
add_action( 'add_meta_boxes', 'cmp_add_invoice_meta_box' );

/**
 * Renders the payment details meta box.
 *
 * @param WP_Post $post The post object.
 */
function cmp_render_invoice_payment_details_meta_box( $post ) {
    $payment_date = get_post_meta( $post->ID, '_cmp_payment_date', true );
    $payment_method = get_post_meta( $post->ID, '_cmp_payment_method', true );
    $transaction_id = get_post_meta( $post->ID, '_cmp_transaction_id', true );
    ?>
    <p>
        <label for="cmp_payment_date"><?php _e( 'Payment Date:', 'contractor-management-portal' ); ?></label><br>
        <input type="date" id="cmp_payment_date" name="cmp_payment_date" value="<?php echo esc_attr( $payment_date ); ?>" />
    </p>
    <p>
        <label for="cmp_payment_method"><?php _e( 'Payment Method:', 'contractor-management-portal' ); ?></label><br>
        <input type="text" id="cmp_payment_method" name="cmp_payment_method" value="<?php echo esc_attr( $payment_method ); ?>" style="width:100%;" />
    </p>
    <p>
        <label for="cmp_transaction_id"><?php _e( 'Transaction ID:', 'contractor-management-portal' ); ?></label><br>
        <input type="text" id="cmp_transaction_id" name="cmp_transaction_id" value="<?php echo esc_attr( $transaction_id ); ?>" style="width:100%;" />
    </p>
    <?php
}

/**
 * Renders the meta box.
 */
function cmp_render_invoice_meta_box( $post ) {
    // Add nonce for security and authentication.
    wp_nonce_field( 'cmp_save_invoice_meta', 'cmp_invoice_nonce' );

    // Get the linked task ID
    $task_id = get_post_meta( $post->ID, '_cmp_task_id', true );

    // Get existing meta values.
    $amount = get_post_meta( $post->ID, '_cmp_invoice_amount', true );
    $due_date = get_post_meta( $post->ID, '_cmp_invoice_due_date', true );
    $file_id = get_post_meta( $post->ID, '_cmp_invoice_file_id', true );
    ?>
    <?php if ( $task_id ) :
        $task_title = get_the_title( $task_id );
        $task_url = get_edit_post_link( $task_id );
    ?>
    <p>
        <strong><?php _e( 'Associated Task:', 'contractor-management-portal' ); ?></strong>
        <a href="<?php echo esc_url($task_url); ?>"><?php echo esc_html($task_title); ?></a>
    </p>
    <hr>
    <?php endif; ?>
    <p>
        <label for="cmp_invoice_amount"><?php _e( 'Amount:', 'contractor-management-portal' ); ?></label><br>
        <input type="text" id="cmp_invoice_amount" name="cmp_invoice_amount" value="<?php echo esc_attr( $amount ); ?>" />
    </p>
    <p>
        <label for="cmp_invoice_due_date"><?php _e( 'Due Date:', 'contractor-management-portal' ); ?></label><br>
        <input type="date" id="cmp_invoice_due_date" name="cmp_invoice_due_date" value="<?php echo esc_attr( $due_date ); ?>" />
    </p>
    <p>
        <label for="cmp_invoice_file"><?php _e( 'Invoice File:', 'contractor-management-portal' ); ?></label><br>
        <input type="file" id="cmp_invoice_file" name="cmp_invoice_file" value="" />
        <?php
        $gdrive_file_id = get_post_meta( $post->ID, '_cmp_invoice_gdrive_file_id', true );
        $wp_file_id = get_post_meta( $post->ID, '_cmp_invoice_file_id', true );

        if ( $gdrive_file_id ) : ?>
            <br />
            <a href="https://drive.google.com/file/d/<?php echo esc_attr( $gdrive_file_id ); ?>/view" target="_blank"><?php _e( 'View File on Google Drive', 'contractor-management-portal' ); ?></a>
        <?php elseif ( $wp_file_id ) : ?>
            <br />
            <?php echo wp_get_attachment_link( $wp_file_id ); ?>
        <?php endif; ?>
    </p>

    <hr>

    <?php
    // Add Pay with Stripe button
    $status_terms = wp_get_post_terms( $post->ID, 'invoice-status' );
    $status = ! empty( $status_terms ) ? $status_terms[0]->slug : '';

    if ( 'approved' === $status ) {
        $pay_url = add_query_arg( array(
            'action' => 'cmp_pay_with_stripe',
            'invoice_id' => $post->ID,
            '_wpnonce' => wp_create_nonce( 'cmp_pay_with_stripe_' . $post->ID )
        ), admin_url() );

        echo '<a href="' . esc_url( $pay_url ) . '" class="button button-primary">' . __( 'Pay with Stripe', 'contractor-management-portal' ) . '</a>';
    }
    ?>
    <?php
}

/**
 * Handles the saving of the meta box.
 *
 * @param int $post_id Post ID.
 */
function cmp_save_invoice_meta( $post_id ) {
    // Check if our nonce is set.
    if ( ! isset( $_POST['cmp_invoice_nonce'] ) ) {
        return;
    }

    // Verify that the nonce is valid.
    if ( ! wp_verify_nonce( $_POST['cmp_invoice_nonce'], 'cmp_save_invoice_meta' ) ) {
        return;
    }

    // If this is an autosave, our form has not been submitted, so we don't want to do anything.
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    // Check the user's permissions.
    if ( isset( $_POST['post_type'] ) && 'invoice' == $_POST['post_type'] ) {
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }
    }

    // Sanitize and save the amount.
    if ( isset( $_POST['cmp_invoice_amount'] ) ) {
        $amount = sanitize_text_field( $_POST['cmp_invoice_amount'] );
        update_post_meta( $post_id, '_cmp_invoice_amount', $amount );
    }

    // Sanitize and save the due date.
    if ( isset( $_POST['cmp_invoice_due_date'] ) ) {
        $due_date = sanitize_text_field( $_POST['cmp_invoice_due_date'] );
        update_post_meta( $post_id, '_cmp_invoice_due_date', $due_date );
    }

    // Handle the file upload.
    if ( ! empty( $_FILES['cmp_invoice_file']['name'] ) ) {
        $options = get_option( 'cmp_options' );
        // Only attempt Google Drive upload if the class exists and token is set.
        if ( class_exists('Google_Client') && isset( $options['google_access_token']['access_token'] ) ) {
            // Google Drive upload
            $client = cmp_get_google_client();
            $client->setAccessToken( $options['google_access_token'] );

            // Refresh the token if it's expired.
            if ( $client->isAccessTokenExpired() ) {
                $client->fetchAccessTokenWithRefreshToken( $client->getRefreshToken() );
                $options['google_access_token'] = $client->getAccessToken();
                update_option( 'cmp_options', $options );
            }

            $drive_service = new Google_Service_Drive( $client );
            $file_metadata = new Google_Service_Drive_DriveFile( array(
                'name' => basename( $_FILES['cmp_invoice_file']['name'] )
            ) );
            $content = file_get_contents( $_FILES['cmp_invoice_file']['tmp_name'] );
            $file = $drive_service->files->create( $file_metadata, array(
                'data' => $content,
                'mimeType' => $_FILES['cmp_invoice_file']['type'],
                'uploadType' => 'multipart',
                'fields' => 'id'
            ) );

            update_post_meta( $post_id, '_cmp_invoice_gdrive_file_id', $file->id );

        } else {
            // WordPress Media Library upload
            require_once( ABSPATH . 'wp-admin/includes/file.php' );
            $uploaded_file = $_FILES['cmp_invoice_file'];
            $upload_overrides = array( 'test_form' => false );
            $move_file = wp_handle_upload( $uploaded_file, $upload_overrides );

            if ( $move_file && ! isset( $move_file['error'] ) ) {
                $attachment = array(
                    'post_mime_type' => $move_file['type'],
                    'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $move_file['file'] ) ),
                    'post_content'   => '',
                    'post_status'    => 'inherit'
                );
                $attach_id = wp_insert_attachment( $attachment, $move_file['file'], $post_id );
                update_post_meta( $post_id, '_cmp_invoice_file_id', $attach_id );
            }
        }
    }

    // Sanitize and save the payment date.
    if ( isset( $_POST['cmp_payment_date'] ) ) {
        $payment_date = sanitize_text_field( $_POST['cmp_payment_date'] );
        update_post_meta( $post_id, '_cmp_payment_date', $payment_date );
    }

    // Sanitize and save the payment method.
    if ( isset( $_POST['cmp_payment_method'] ) ) {
        $payment_method = sanitize_text_field( $_POST['cmp_payment_method'] );
        update_post_meta( $post_id, '_cmp_payment_method', $payment_method );
    }

    // Sanitize and save the transaction ID.
    if ( isset( $_POST['cmp_transaction_id'] ) ) {
        $transaction_id = sanitize_text_field( $_POST['cmp_transaction_id'] );
        update_post_meta( $post_id, '_cmp_transaction_id', $transaction_id );
    }
}
add_action( 'save_post', 'cmp_save_invoice_meta' );

/**
 * Add enctype to form
 */
function cmp_add_edit_form_multipart_encoding() {
    echo ' enctype="multipart/form-data"';
}
add_action( 'post_edit_form_tag', 'cmp_add_edit_form_multipart_encoding' );

/**
 * Adds a meta box to the client post type.
 */
function cmp_add_client_meta_box() {
    add_meta_box(
        'cmp_client_user_meta_box',
        __( 'Assign User', 'contractor-management-portal' ),
        'cmp_render_client_user_meta_box',
        'client',
        'side',
        'default'
    );
    add_meta_box(
        'cmp_client_details_meta_box',
        __( 'Client Details', 'contractor-management-portal' ),
        'cmp_render_client_details_meta_box',
        'client',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'cmp_add_client_meta_box' );

/**
 * Renders the client user meta box.
 *
 * @param WP_Post $post The post object.
 */
/**
 * Renders the client details meta box.
 *
 * @param WP_Post $post The post object.
 */
function cmp_render_client_details_meta_box( $post ) {
    $email = get_post_meta( $post->ID, '_cmp_client_email', true );
    $phone = get_post_meta( $post->ID, '_cmp_client_phone', true );
    $address = get_post_meta( $post->ID, '_cmp_client_address', true );
    ?>
    <p>
        <label for="cmp_client_email"><?php _e( 'Email:', 'contractor-management-portal' ); ?></label><br>
        <input type="email" id="cmp_client_email" name="cmp_client_email" value="<?php echo esc_attr( $email ); ?>" style="width:100%;" />
    </p>
    <p>
        <label for="cmp_client_phone"><?php _e( 'Phone:', 'contractor-management-portal' ); ?></label><br>
        <input type="text" id="cmp_client_phone" name="cmp_client_phone" value="<?php echo esc_attr( $phone ); ?>" style="width:100%;" />
    </p>
    <p>
        <label for="cmp_client_address"><?php _e( 'Address:', 'contractor-management-portal' ); ?></label><br>
        <textarea id="cmp_client_address" name="cmp_client_address" rows="4" style="width:100%;"><?php echo esc_textarea( $address ); ?></textarea>
    </p>
    <?php
}

function cmp_render_client_user_meta_box( $post ) {
    // Add nonce for security and authentication.
    wp_nonce_field( 'cmp_save_client_user_meta', 'cmp_client_user_nonce' );

    // Get the list of users with the 'client' role.
    $clients = get_users( array( 'role' => 'client' ) );
    $assigned_user_id = get_post_meta( $post->ID, '_cmp_assigned_user_id', true );

    ?>
    <label for="cmp_client_user_select"><?php _e( 'Select a User:', 'contractor-management-portal' ); ?></label>
    <select name="cmp_assigned_user_id" id="cmp_client_user_select" style="width:100%;">
        <option value=""><?php _e( 'Not Assigned', 'contractor-management-portal' ); ?></option>
        <?php foreach ( $clients as $client ) : ?>
            <option value="<?php echo esc_attr( $client->ID ); ?>" <?php selected( $assigned_user_id, $client->ID ); ?>>
                <?php echo esc_html( $client->display_name ); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <?php
}

/**
 * Handles the saving of the client meta boxes.
 *
 * @param int $post_id Post ID.
 */
function cmp_save_client_meta( $post_id ) {
    // Check if our nonce is set.
    if ( ! isset( $_POST['cmp_client_user_nonce'] ) ) {
        return;
    }

    // Verify that the nonce is valid.
    if ( ! wp_verify_nonce( $_POST['cmp_client_user_nonce'], 'cmp_save_client_user_meta' ) ) {
        return;
    }

    // If this is an autosave, our form has not been submitted, so we don't want to do anything.
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    // Check the user's permissions.
    if ( isset( $_POST['post_type'] ) && 'client' == $_POST['post_type'] ) {
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }
    }

    // Sanitize and save the assigned user ID.
    if ( isset( $_POST['cmp_assigned_user_id'] ) ) {
        $user_id = sanitize_text_field( $_POST['cmp_assigned_user_id'] );
        update_post_meta( $post_id, '_cmp_assigned_user_id', $user_id );
    }

    // Sanitize and save the email.
    if ( isset( $_POST['cmp_client_email'] ) ) {
        $email = sanitize_email( $_POST['cmp_client_email'] );
        update_post_meta( $post_id, '_cmp_client_email', $email );
    }

    // Sanitize and save the phone.
    if ( isset( $_POST['cmp_client_phone'] ) ) {
        $phone = sanitize_text_field( $_POST['cmp_client_phone'] );
        update_post_meta( $post_id, '_cmp_client_phone', $phone );
    }

    // Sanitize and save the address.
    if ( isset( $_POST['cmp_client_address'] ) ) {
        $address = sanitize_textarea_field( $_POST['cmp_client_address'] );
        update_post_meta( $post_id, '_cmp_client_address', $address );
    }
}
add_action( 'save_post', 'cmp_save_client_meta' );

/**
 * Adds a meta box to the channel post type.
 */
function cmp_add_channel_meta_box() {
    add_meta_box(
        'cmp_channel_id_meta_box',
        __( 'YouTube Channel ID', 'contractor-management-portal' ),
        'cmp_render_channel_id_meta_box',
        'channel',
        'side',
        'default'
    );
    add_meta_box(
        'cmp_channel_details_meta_box',
        __( 'Channel Details', 'contractor-management-portal' ),
        'cmp_render_channel_details_meta_box',
        'channel',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'cmp_add_channel_meta_box' );

/**
 * Renders the channel ID meta box.
 *
 * @param WP_Post $post The post object.
 */
/**
 * Renders the channel details meta box.
 *
 * @param WP_Post $post The post object.
 */
function cmp_render_channel_details_meta_box( $post ) {
    $platform = get_post_meta( $post->ID, '_cmp_channel_platform', true );
    $url = get_post_meta( $post->ID, '_cmp_channel_url', true );
    ?>
    <p>
        <label for="cmp_channel_platform"><?php _e( 'Platform:', 'contractor-management-portal' ); ?></label><br>
        <select id="cmp_channel_platform" name="cmp_channel_platform" style="width:100%;">
            <option value="youtube" <?php selected( $platform, 'youtube' ); ?>>YouTube</option>
            <option value="twitter" <?php selected( $platform, 'twitter' ); ?>>Twitter</option>
            <option value="facebook" <?php selected( $platform, 'facebook' ); ?>>Facebook</option>
            <option value="instagram" <?php selected( $platform, 'instagram' ); ?>>Instagram</option>
            <option value="other" <?php selected( $platform, 'other' ); ?>>Other</option>
        </select>
    </p>
    <p>
        <label for="cmp_channel_url"><?php _e( 'Channel URL:', 'contractor-management-portal' ); ?></label><br>
        <input type="url" id="cmp_channel_url" name="cmp_channel_url" value="<?php echo esc_url( $url ); ?>" style="width:100%;" />
    </p>
    <?php
}

function cmp_render_channel_id_meta_box( $post ) {
    // Add nonce for security and authentication.
    wp_nonce_field( 'cmp_save_channel_id_meta', 'cmp_channel_id_nonce' );

    $channel_id = get_post_meta( $post->ID, '_cmp_youtube_channel_id', true );
    ?>
    <label for="cmp_youtube_channel_id"><?php _e( 'Enter the YouTube Channel ID:', 'contractor-management-portal' ); ?></label>
    <input type="text" id="cmp_youtube_channel_id" name="cmp_youtube_channel_id" value="<?php echo esc_attr( $channel_id ); ?>" style="width:100%;" />
    <hr>
    <?php
    if ( ! empty( $channel_id ) ) {
        $stats = cmp_get_youtube_channel_stats( $channel_id );
        if ( $stats ) {
            echo '<h4>' . esc_html( $stats['title'] ) . '</h4>';
            echo '<img src="' . esc_url( $stats['thumbnail'] ) . '" />';
            echo '<ul>';
            echo '<li><strong>' . __( 'Subscribers:', 'contractor-management-portal' ) . '</strong> ' . esc_html( number_format_i18n( $stats['subscriberCount'] ) ) . '</li>';
            echo '<li><strong>' . __( 'Total Views:', 'contractor-management-portal' ) . '</strong> ' . esc_html( number_format_i18n( $stats['viewCount'] ) ) . '</li>';
            echo '<li><strong>' . __( 'Total Videos:', 'contractor-management-portal' ) . '</strong> ' . esc_html( number_format_i18n( $stats['videoCount'] ) ) . '</li>';
            echo '</ul>';
        } else {
            echo '<p style="color:red;">' . __( 'Could not retrieve channel stats. Please check the Channel ID and API Key.', 'contractor-management-portal' ) . '</p>';
        }
    }
}

/**
 * Handles the saving of the channel meta boxes.
 *
 * @param int $post_id Post ID.
 */
function cmp_save_channel_meta( $post_id ) {
    // Check if our nonce is set.
    if ( ! isset( $_POST['cmp_channel_id_nonce'] ) ) {
        return;
    }

    // Verify that the nonce is valid.
    if ( ! wp_verify_nonce( $_POST['cmp_channel_id_nonce'], 'cmp_save_channel_id_meta' ) ) {
        return;
    }

    // If this is an autosave, our form has not been submitted, so we don't want to do anything.
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    // Check the user's permissions.
    if ( isset( $_POST['post_type'] ) && 'channel' == $_POST['post_type'] ) {
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }
    }

    // Sanitize and save the channel ID.
    if ( isset( $_POST['cmp_youtube_channel_id'] ) ) {
        $channel_id = sanitize_text_field( $_POST['cmp_youtube_channel_id'] );
        update_post_meta( $post_id, '_cmp_youtube_channel_id', $channel_id );
    }

    // Sanitize and save the platform.
    if ( isset( $_POST['cmp_channel_platform'] ) ) {
        $platform = sanitize_text_field( $_POST['cmp_channel_platform'] );
        update_post_meta( $post_id, '_cmp_channel_platform', $platform );
    }

    // Sanitize and save the URL.
    if ( isset( $_POST['cmp_channel_url'] ) ) {
        $url = esc_url_raw( $_POST['cmp_channel_url'] );
        update_post_meta( $post_id, '_cmp_channel_url', $url );
    }
}
add_action( 'save_post', 'cmp_save_channel_meta' );
