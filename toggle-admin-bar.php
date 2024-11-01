<?php
/*
Plugin Name: Toggle Hide/Show Admin Bar
Description: A simple and customizable real-time toggle button to hide/show admin bar in front-end.
Version: 1.1.0
Author: Jorge del Campo
Author URI: https://www.linkedin.com/in/jorgedelcampoandrade/
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: toggle-hide-show-admin-bar
*/

// Ensure the code does not run if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Enqueue scripts and styles
function toggle_admin_bar_enqueue_scripts() {
    if ( is_user_logged_in() && is_admin_bar_showing() ) {
        // Enqueue Font Awesome
        wp_enqueue_style( 'font-awesome', plugin_dir_url( __FILE__ ) . 'libs/font-awesome/5.15.4/css/all.min.css', array(), '5.15.4' );

        // Enqueue plugin scripts and styles
        wp_enqueue_script( 'toggle-admin-bar-script', plugin_dir_url( __FILE__ ) . 'toggle-admin-bar.js', array('jquery'), '1.1.0', true );
        wp_enqueue_style( 'toggle-admin-bar-style', plugin_dir_url( __FILE__ ) . 'toggle-admin-bar.css', array(), '1.1.0' );

        // Pass options to JavaScript
        wp_localize_script( 'toggle-admin-bar-script', 'tabOptions', array(
            'position' => get_option('thsab_tab_position', 'bottom-left'),
            'behavior' => get_option('thsab_tab_behavior', 'always-visible'),
            'background_color' => get_option('thsab_tab_background_color', '#000000'),
            'text_color' => get_option('thsab_tab_text_color', '#ffffff')
        ));
    }
}
add_action( 'wp_enqueue_scripts', 'toggle_admin_bar_enqueue_scripts' );

// Add toggle button to the front-end
function add_toggle_admin_bar_button() {
    if ( is_user_logged_in() && is_admin_bar_showing() ) {
        // Determine initial state based on admin bar visibility
        $admin_bar_visible = is_admin_bar_showing() ? 'show' : 'hide';
        ?>
        <div id="thsabToggleAdminBar" class="' . esc_attr($admin_bar_visible) . '">
            <input type="checkbox" id="thsabToggleAdminBarCheckbox">
            <label for="thsabToggleAdminBarCheckbox">
                <i class="fa fa-eye"></i>
                <i class="fa fa-eye-slash"></i>
            </label>
        </div>
        <?php
    }
}
add_action( 'wp_footer', 'add_toggle_admin_bar_button' );

// Add settings menu
function toggle_admin_bar_settings_menu() {
    add_options_page(
        'Toggle Admin Bar Settings',
        'Toggle Admin Bar',
        'manage_options',
        'toggle-admin-bar-settings',
        'toggle_admin_bar_settings_page'
    );
}
add_action( 'admin_menu', 'toggle_admin_bar_settings_menu' );

// Register settings
function thsab_register_settings() {
    register_setting( 'thsab_settings', 'thsab_tab_position', 'sanitize_text_field' );
    register_setting( 'thsab_settings', 'thsab_tab_behavior', 'sanitize_text_field' );
    register_setting( 'thsab_settings', 'thsab_tab_background_color', 'sanitize_hex_color' );
    register_setting( 'thsab_settings', 'thsab_tab_text_color', 'sanitize_hex_color' );
}
add_action( 'admin_init', 'thsab_register_settings' );

// Settings page
function toggle_admin_bar_settings_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'toggle-hide-show-admin-bar' ) );
    }

    $user_id = get_current_user_id();    
    // Get 'show_admin_bar_front' option value from wp_usermeta
    $show_admin_bar_front = get_user_meta( $user_id, 'show_admin_bar_front', true );

    // Check if value is false to show a warning
    if ( $show_admin_bar_front === 'false' ) {
        ?>
        <div class="notice notice-warning is-dismissible">
        <p>Important! Your profile is not configured to show admin bar in front-end. If you want to enjoy this plugin, <a href="/wp-admin/profile.php">enable this option</a> and comeback to this page.</p>
        </div>
        <?php
    }
    ?>
    <div class="wrap">
        <h1>Toggle Admin Bar Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields( 'thsab_settings' );
            do_settings_sections( 'toggle-admin-bar-settings' );
            wp_nonce_field( 'toggle_admin_bar_settings_verify', 'toggle_admin_bar_settings_nonce' );
            ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Position</th>
                    <td>
                        <select name="thsab_tab_position">
                            <option value="bottom-left" <?php selected( get_option('thsab_tab_position'), 'bottom-left' ); ?>>Bottom-left corner</option>
                            <option value="bottom-right" <?php selected( get_option('thsab_tab_position'), 'bottom-right' ); ?>>Bottom-right corner</option>
                        </select>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Behavior</th>
                    <td>
                        <select name="thsab_tab_behavior">
                            <option value="always-visible" <?php selected( get_option('thsab_tab_behavior'), 'always-visible' ); ?>>Always visible</option>
                            <option value="hide-partially" <?php selected( get_option('thsab_tab_behavior'), 'hide-partially' ); ?>>Show when hover</option>
                        </select>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Background color</th>
                    <td><input type="text" name="thsab_tab_background_color" value="<?php echo esc_attr( get_option('thsab_tab_background_color', '#000000') ); ?>" class="color-picker" data-default-color="#000000" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Text color</th>
                    <td><input type="text" name="thsab_tab_text_color" value="<?php echo esc_attr( get_option('thsab_tab_text_color', '#ffffff') ); ?>" class="color-picker" data-default-color="#ffffff" /></td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

// Enqueue color picker
function toggle_admin_bar_enqueue_color_picker( $hook_suffix ) {
    if ( 'settings_page_toggle-admin-bar-settings' !== $hook_suffix ) {
        return;
    }
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( 'toggle-admin-bar-color-picker', plugin_dir_url( __FILE__ ) . 'toggle-admin-bar-color-picker.js', array( 'wp-color-picker' ), '1.1.0', true );
}
add_action( 'admin_enqueue_scripts', 'toggle_admin_bar_enqueue_color_picker' );
