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
}
add_action( 'add_meta_boxes', 'cmp_add_task_meta_box' );

/**
 * Renders the meta box.
 */
function cmp_render_task_contractor_meta_box( $post ) {
    // Add nonce for security and authentication.
    wp_nonce_field( 'cmp_save_task_contractor_meta', 'cmp_task_contractor_nonce' );

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
 * Handles the saving of the meta box.
 *
 * @param int $post_id Post ID.
 */
function cmp_save_task_contractor_meta( $post_id ) {
    // Check if our nonce is set.
    if ( ! isset( $_POST['cmp_task_contractor_nonce'] ) ) {
        return;
    }

    // Verify that the nonce is valid.
    if ( ! wp_verify_nonce( $_POST['cmp_task_contractor_nonce'], 'cmp_save_task_contractor_meta' ) ) {
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

    // Sanitize user input.
    $my_data = isset( $_POST['cmp_assigned_contractor'] ) ? sanitize_text_field( $_POST['cmp_assigned_contractor'] ) : '';

    // Update the meta field in the database.
    update_post_meta( $post_id, '_cmp_assigned_contractor', $my_data );
}
add_action( 'save_post', 'cmp_save_task_contractor_meta' );


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
