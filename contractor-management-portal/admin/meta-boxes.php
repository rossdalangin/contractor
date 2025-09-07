<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
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
        $contractor_id = sanitize_text_field( $_POST['cmp_assigned_contractor'] );
        update_post_meta( $post_id, '_cmp_assigned_contractor', $contractor_id );
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
}
add_action( 'add_meta_boxes', 'cmp_add_invoice_meta_box' );

/**
 * Renders the meta box.
 */
function cmp_render_invoice_meta_box( $post ) {
    // Add nonce for security and authentication.
    wp_nonce_field( 'cmp_save_invoice_meta', 'cmp_invoice_nonce' );

    // Get existing meta values.
    $amount = get_post_meta( $post->ID, '_cmp_invoice_amount', true );
    $due_date = get_post_meta( $post->ID, '_cmp_invoice_due_date', true );
    $file_id = get_post_meta( $post->ID, '_cmp_invoice_file_id', true );
    ?>
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
        <?php if ( $file_id ) : ?>
            <br />
            <?php echo wp_get_attachment_link( $file_id ); ?>
        <?php endif; ?>
    </p>
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
        // Include the necessary file for `wp_handle_upload`.
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
add_action( 'save_post', 'cmp_save_invoice_meta' );

/**
 * Add enctype to form
 */
function cmp_add_edit_form_multipart_encoding() {
    echo ' enctype="multipart/form-data"';
}
add_action( 'post_edit_form_tag', 'cmp_add_edit_form_multipart_encoding' );
