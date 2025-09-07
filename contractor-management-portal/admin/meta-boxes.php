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
