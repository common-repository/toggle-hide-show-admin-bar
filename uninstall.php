<?php
// If is has not invoked from Wordpress: EXIT
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit();
}

// Delete plugin options
delete_option( 'thsab_tab_position' );
delete_option( 'thsab_tab_behavior' );
delete_option( 'thsab_tab_background_color' );
delete_option( 'thsab_tab_text_color' );
delete_option( 'thsab_tab_persistence' );

// Id is multisite, delete plugin option for each site
global $wpdb;
if ( is_multisite() ) {
    $sites = get_sites();
    foreach ( $sites as $site ) {
        switch_to_blog( $site->blog_id );
        delete_option( 'thsab_tab_position' );
        delete_option( 'thsab_tab_behavior' );
        delete_option( 'thsab_tab_background_color' );
        delete_option( 'thsab_tab_text_color' );
        delete_option( 'thsab_tab_persistence' );
        restore_current_blog();
    }
}

?>
