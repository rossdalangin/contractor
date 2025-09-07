<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register settings, sections, and fields.
 */
function cmp_register_settings() {
    // Register a setting group.
    register_setting( 'cmp_settings_group', 'cmp_options', 'cmp_sanitize_options' );

    // Add a section.
    add_settings_section(
        'cmp_google_drive_section',
        'Google Drive API Settings',
        'cmp_google_drive_section_callback',
        'cmp-settings'
    );

    // Add fields.
    add_settings_field(
        'cmp_google_client_id',
        'Google Client ID',
        'cmp_google_client_id_callback',
        'cmp-settings',
        'cmp_google_drive_section'
    );
    add_settings_field(
        'cmp_google_client_secret',
        'Google Client Secret',
        'cmp_google_client_secret_callback',
        'cmp-settings',
        'cmp_google_drive_section'
    );
}
add_action( 'admin_init', 'cmp_register_settings' );

/**
 * Sanitize the options.
 */
function cmp_sanitize_options( $input ) {
    $output = get_option( 'cmp_options' );

    if ( isset( $input['google_client_id'] ) ) {
        $output['google_client_id'] = sanitize_text_field( $input['google_client_id'] );
    }
    if ( isset( $input['google_client_secret'] ) ) {
        $output['google_client_secret'] = sanitize_text_field( $input['google_client_secret'] );
    }

    return $output;
}

/**
 * Get the Google API client.
 *
 * @return Google_Client
 */
function cmp_get_google_client() {
    $options = get_option( 'cmp_options' );
    $client_id = isset( $options['google_client_id'] ) ? $options['google_client_id'] : '';
    $client_secret = isset( $options['google_client_secret'] ) ? $options['google_client_secret'] : '';

    $client = new Google_Client();
    $client->setClientId( $client_id );
    $client->setClientSecret( $client_secret );
    $client->setRedirectUri( admin_url( 'admin.php?page=cmp-settings' ) );
    $client->setScopes( 'https://www.googleapis.com/auth/drive.file' );
    $client->setAccessType( 'offline' );
    $client->setPrompt( 'consent' );

    return $client;
}

/**
 * Handle the Google OAuth callback.
 */
function cmp_handle_google_oauth_callback() {
    if ( ! isset( $_GET['page'] ) || 'cmp-settings' !== $_GET['page'] ) {
        return;
    }

    if ( ! isset( $_GET['code'] ) ) {
        return;
    }

    $client = cmp_get_google_client();
    $token = $client->fetchAccessTokenWithAuthCode( $_GET['code'] );

    if ( isset( $token['error'] ) ) {
        // Handle error
        wp_die( 'Error fetching access token: ' . esc_html( $token['error_description'] ) );
    } else {
        // Store the token
        $options = get_option( 'cmp_options' );
        $options['google_access_token'] = $token;
        update_option( 'cmp_options', $options );

        // Redirect back to the settings page without the code
        wp_redirect( admin_url( 'admin.php?page=cmp-settings' ) );
        exit;
    }
}
add_action( 'admin_init', 'cmp_handle_google_oauth_callback' );

/**
 * Section callback.
 */
function cmp_google_drive_section_callback() {
    echo '<p>Enter your Google API credentials below. You can get these from the <a href="https://console.developers.google.com/" target="_blank">Google API Console</a>.</p>';

    $options = get_option( 'cmp_options' );
    if ( isset( $options['google_access_token']['access_token'] ) ) {
        echo '<p class="description" style="color: green;">' . __( 'Successfully authorized with Google.', 'contractor-management-portal' ) . '</p>';
    } else {
        $client = cmp_get_google_client();
        $auth_url = $client->createAuthUrl();
        echo '<a href="' . esc_url( $auth_url ) . '" class="button button-primary">' . __( 'Authorize with Google', 'contractor-management-portal' ) . '</a>';
    }
}

/**
 * Field callbacks.
 */
function cmp_google_client_id_callback() {
    $options = get_option( 'cmp_options' );
    $client_id = isset( $options['google_client_id'] ) ? $options['google_client_id'] : '';
    echo '<input type="text" id="cmp_google_client_id" name="cmp_options[google_client_id]" value="' . esc_attr( $client_id ) . '" size="50" />';
}

function cmp_google_client_secret_callback() {
    $options = get_option( 'cmp_options' );
    $client_secret = isset( $options['google_client_secret'] ) ? $options['google_client_secret'] : '';
    echo '<input type="text" id="cmp_google_client_secret" name="cmp_options[google_client_secret]" value="' . esc_attr( $client_secret ) . '" size="50" />';
}
