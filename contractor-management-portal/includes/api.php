<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Get YouTube channel stats.
 *
 * @param string $channel_id The YouTube channel ID.
 * @return array|false The channel stats or false on failure.
 */
function cmp_get_youtube_channel_stats( $channel_id ) {
    $options = get_option( 'cmp_options' );
    $api_key = isset( $options['youtube_api_key'] ) ? $options['youtube_api_key'] : '';

    if ( empty( $api_key ) ) {
        return false;
    }

    $url = add_query_arg( array(
        'part' => 'snippet,statistics',
        'id'   => $channel_id,
        'key'  => $api_key,
    ), 'https://www.googleapis.com/youtube/v3/channels' );

    $response = wp_remote_get( $url );

    if ( is_wp_error( $response ) ) {
        return false;
    }

    $body = wp_remote_retrieve_body( $response );
    $data = json_decode( $body, true );

    if ( ! isset( $data['items'][0] ) ) {
        return false;
    }

    $stats = $data['items'][0]['statistics'];
    $snippet = $data['items'][0]['snippet'];

    return array(
        'subscriberCount' => $stats['subscriberCount'],
        'viewCount'       => $stats['viewCount'],
        'videoCount'      => $stats['videoCount'],
        'title'           => $snippet['title'],
        'thumbnail'       => $snippet['thumbnails']['default']['url'],
    );
}
