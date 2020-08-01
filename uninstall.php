<?php
/**
*Triggered on uninstall of the plugin
*
*@package prizedai-mobile-payments
**/

defined('WP_UNINSTALL_PLUGIN') or die("Forbidden!");

//clear database of all stored data
global $wpdb;

$wpdb->query( "DELETE FROM wp_posts WHERE post_type = 'mobile_payments' " );
$wpdb->query( "DELETE FROM wp_postmeta WHERE post_id NOT IN ( SELECT id FROM wp_posts)" );
$wpdb->query( "DELETE FROM wp_term_relationships WHERE object_id NOT IN ( SELECT id FROM wp_posts)" );
