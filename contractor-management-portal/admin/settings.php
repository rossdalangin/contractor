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

    // YouTube Section
    add_settings_section(
        'cmp_youtube_section',
        'YouTube API Settings',
        'cmp_youtube_section_callback',
        'cmp-settings'
    );
    add_settings_field(
        'cmp_youtube_api_key',
        'YouTube API Key',
        'cmp_youtube_api_key_callback',
        'cmp-settings',
        'cmp_youtube_section'
    );
}
add_action( 'admin_init', 'cmp_register_settings' );

/**
 * Sanitize the options.
 */
function cmp_sanitize_options( $input ) {
    $output = get_option( 'cmp_options' );

    if ( isset( $input['youtube_api_key'] ) ) {
        $output['youtube_api_key'] = sanitize_text_field( $input['youtube_api_key'] );
    }

    return $output;
}

/**
 * YouTube section callback.
 */
function cmp_youtube_section_callback() {
    echo '<p>Enter your YouTube Data API Key below. You can get this from the <a href="https://console.developers.google.com/" target="_blank">Google API Console</a>.</p>';
}

/**
 * YouTube field callbacks.
 */
function cmp_youtube_api_key_callback() {
    $options = get_option( 'cmp_options' );
    $api_key = isset( $options['youtube_api_key'] ) ? $options['youtube_api_key'] : '';
    echo '<input type="text" id="cmp_youtube_api_key" name="cmp_options[youtube_api_key]" value="' . esc_attr( $api_key ) . '" size="50" />';
}
