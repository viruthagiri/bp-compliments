<?php
/*
Plugin Name: BuddyPress Compliments
Plugin URI: http://wpgeodirectory.com/
Description: Compliments module for BuddyPress.
Version: 0.1
Author: GeoDirectory
Author URI: http://wpgeodirectory.com
*/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Only load the plugin code if BuddyPress is activated.
 */
function bp_compliments_init() {
    // some pertinent defines
    define( 'BP_COMPLIMENTS_DIR', dirname( __FILE__ ) );
    define( 'BP_COMPLIMENTS_URL', plugin_dir_url( __FILE__ ) );

    // only supported in BP 1.5+
    if ( version_compare( BP_VERSION, '1.3', '>' ) ) {
        require( constant( 'BP_COMPLIMENTS_DIR' ) . '/bp-compliments-core.php' );

        // show admin notice for users on BP 1.2.x
    } else {
        $older_version_notice = __( "Hey! BP Compliments requires BuddyPress 1.5 or higher.", 'bp-compliments' );

        add_action( 'admin_notices', create_function( '', "
			echo '<div class=\"error\"><p>' . $older_version_notice . '</p></div>';
		" ) );

        return;
    }
}
add_action( 'bp_include', 'bp_compliments_init' );

function bp_compliments_activate() {
    global $bp, $wpdb;

    $charset_collate = !empty( $wpdb->charset ) ? "DEFAULT CHARACTER SET $wpdb->charset" : '';
    if ( !$table_prefix = $bp->table_prefix )
        $table_prefix = apply_filters( 'bp_core_get_table_prefix', $wpdb->base_prefix );

    $sql[] = "CREATE TABLE IF NOT EXISTS {$table_prefix}bp_compliments (
			id bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
			term_id int(10) NOT NULL,
			receiver_id bigint(20) NOT NULL,
			sender_id bigint(20) NOT NULL,
			message varchar(1000) NULL DEFAULT NULL,
			created_at datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		        KEY compliments (receiver_id, sender_id)
		) {$charset_collate};";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
}
register_activation_hook( __FILE__, 'bp_compliments_activate' );