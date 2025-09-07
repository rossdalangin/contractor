<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Add custom user roles on plugin activation.
 */
function cmp_add_roles() {
    add_role( 'contractor', 'Contractor', [ 'read' => true ] );
    add_role( 'client', 'Client', [ 'read' => true ] );
}

/**
 * Remove custom user roles on plugin deactivation.
 */
function cmp_remove_roles() {
    remove_role( 'contractor' );
    remove_role( 'client' );
}
