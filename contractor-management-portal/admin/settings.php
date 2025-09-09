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

    // Payment Gateways Section
    add_settings_section(
        'cmp_gateways_section',
        'Payment Gateways',
        'cmp_gateways_section_callback',
        'cmp-settings'
    );
    add_settings_field(
        'cmp_cod_enable',
        'Cash on Delivery (COD)',
        'cmp_cod_enable_callback',
        'cmp-settings',
        'cmp_gateways_section'
    );
    add_settings_field(
        'cmp_stripe_enable',
        'Stripe',
        'cmp_stripe_enable_callback',
        'cmp-settings',
        'cmp_gateways_section'
    );
    add_settings_field(
        'cmp_paypal_enable',
        'PayPal',
        'cmp_paypal_enable_callback',
        'cmp-settings',
        'cmp_gateways_section'
    );
    add_settings_field(
        'cmp_gcash_enable',
        'GCash',
        'cmp_gcash_enable_callback',
        'cmp-settings',
        'cmp_gateways_section'
    );

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
 * Payment Gateways section callback.
 */
function cmp_gateways_section_callback() {
    echo '<p>Enable and configure the payment gateways you want to offer.</p>';
}

/**
 * Gateway field callbacks.
 */
function cmp_cod_enable_callback() {
    $options = get_option( 'cmp_options' );
    $checked = isset( $options['cod_enable'] ) ? checked( $options['cod_enable'], 1, false ) : '';
    echo '<input type="checkbox" id="cmp_cod_enable" name="cmp_options[cod_enable]" value="1" ' . $checked . ' />';
}

function cmp_stripe_enable_callback() {
    $options = get_option( 'cmp_options' );
    $checked = isset( $options['stripe_enable'] ) ? checked( $options['stripe_enable'], 1, false ) : '';
    echo '<input type="checkbox" id="cmp_stripe_enable" name="cmp_options[stripe_enable]" value="1" ' . $checked . ' />';
}

function cmp_paypal_enable_callback() {
    $options = get_option( 'cmp_options' );
    $checked = isset( $options['paypal_enable'] ) ? checked( $options['paypal_enable'], 1, false ) : '';
    echo '<input type="checkbox" id="cmp_paypal_enable" name="cmp_options[paypal_enable]" value="1" ' . $checked . ' />';
}

function cmp_gcash_enable_callback() {
    $options = get_option( 'cmp_options' );
    $checked = isset( $options['gcash_enable'] ) ? checked( $options['gcash_enable'], 1, false ) : '';
    echo '<input type="checkbox" id="cmp_gcash_enable" name="cmp_options[gcash_enable]" value="1" ' . $checked . ' />';
}

/**
 * Sanitize the options.
 */
function cmp_sanitize_options( $input ) {
    $output = get_option( 'cmp_options' );

    // Sanitize checkboxes
    $output['cod_enable'] = isset( $input['cod_enable'] ) ? 1 : 0;
    $output['stripe_enable'] = isset( $input['stripe_enable'] ) ? 1 : 0;
    $output['paypal_enable'] = isset( $input['paypal_enable'] ) ? 1 : 0;
    $output['gcash_enable'] = isset( $input['gcash_enable'] ) ? 1 : 0;

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
