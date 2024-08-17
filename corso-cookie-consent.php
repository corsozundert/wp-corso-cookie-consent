<?php
/*
Plugin Name: Corso Cookie Consent Plugin
Description: A simple plugin to display a cookie consent bar and load user tracking scripts. Initially developped for corsozundert.nl
Version: 1.0
Author: Marc Mathijssen
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Enqueue the CSS and JavaScript files
function corso_cookie_consent_enqueue_scripts() {
    wp_enqueue_style('corso-cookie-consent-style', plugin_dir_url(__FILE__) . 'styles.css', array(), null);
    wp_enqueue_script('corso-cookie-consent-script', plugin_dir_url(__FILE__) . 'script.js', array('jquery'), null, true);

    // Get color settings
    $background_color = get_option('corso_cookie_consent_background_color', '#2c3e50');
    $button_color = get_option('corso_cookie_consent_button_color', '#e74c3c');
    $button_hover_color = get_option('corso_cookie_consent_button_hover_color', '#c0392b');
    $text_color = get_option('corso_cookie_consent_text_color', '#ffffff');

    // Inject CSS variables
    $custom_css = "
        :root {
            --corso-cookie-consent-background-color: {$background_color};
            --corso-cookie-consent-button-color: {$button_color};
            --corso-cookie-consent-button-hover-color: {$button_hover_color};
            --corso-cookie-consent-text-color: {$text_color};
        }
    ";

    wp_add_inline_style('corso-cookie-consent-style', $custom_css);

    
}
add_action('wp_enqueue_scripts', 'corso_cookie_consent_enqueue_scripts');

// Add the cookie consent HTML to the footer
function corso_cookie_consent_bar_html() {
    $consent_policy = get_option('corso_cookie_consent_policy_nl', '');
    $consent_text = get_option('corso_cookie_consent_text_nl', 'Deze website maakt gebruik van cookies');
    $consent_button = get_option('corso_cookie_consent_button_nl', 'Accepteren');

    $gtm_id =  get_option('corso_cookie_consent_tracking_gtm_id', '');
    $ga_id =  get_option('corso_cookie_consent_tracking_ga_id', '');

    $div = '<div id="corso-cookie-consent-bar" class="corso-cookie-consent" data-gtm-id="'. $gtm_id  .'" data-ga-id="' . $ga_id . '">
            ' . $consent_text . '. <a href="' . $consent_policy . '">Lees meer</a>.
            <button id="corso-cookie-consent-accept-cookies">' . $consent_button . '</button>
          </div>';
    
    echo $div;
}
add_action('wp_footer', 'corso_cookie_consent_bar_html');

// Create admin menu item
function corso_cookie_consent_create_menu() {
    add_options_page(
        'Corso Cookie Consent Settings',
        'Corso Cookie Consent',
        'manage_options',
        'corso-cookie-consent-settings',
        'corso_cookie_consent_settings_page'
    );
}
add_action('admin_menu', 'corso_cookie_consent_create_menu');

// Display settings page
function corso_cookie_consent_settings_page() {
    ?>
    <div class="wrap">
        <h1>Corso Cookie Consent Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('corso_cookie_consent_options_group');
            do_settings_sections('corso-cookie-consent-tracking');
            do_settings_sections('corso-cookie-consent-labels');
            do_settings_sections('corso-cookie-consent-styling');
           
            submit_button();
            ?>
        </form>
    </div>
    <?php
}


// Register and define GTM settings
function corso_cookie_consent_register_tracking_settings() {
    register_setting('corso_cookie_consent_options_group', 'corso_cookie_consent_tracking_gtm_id');
    register_setting('corso_cookie_consent_options_group', 'corso_cookie_consent_tracking_ga_id');

    add_settings_section(
        'corso_cookie_consent_tracking_section',
        'Tracking',
        null,
        'corso-cookie-consent-tracking'
    );

    add_settings_field(
        'corso_cookie_consent_tracking_gtm_id',
        'Google Tag Manager',
        'corso_cookie_consent_tracking_gtm_id_callback',
        'corso-cookie-consent-tracking',
        'corso_cookie_consent_tracking_section'
    );

    add_settings_field(
        'corso_cookie_consent_tracking_ga_id',
        'Google Analytics',
        'corso_cookie_consent_tracking_ga_id_callback',
        'corso-cookie-consent-tracking',
        'corso_cookie_consent_tracking_section'
    );


}

// Register and define label settings
function corso_cookie_consent_register_labels_settings() {
    register_setting('corso_cookie_consent_options_group', 'corso_cookie_consent_text_nl');
    register_setting('corso_cookie_consent_options_group', 'corso_cookie_consent_button_nl');
    register_setting('corso_cookie_consent_options_group', 'corso_cookie_consent_policy_nl');

    add_settings_section(
        'corso_cookie_consent_labels_section',
        'Settings',
        null,
        'corso-cookie-consent-labels'
    );

    add_settings_field(
        'corso_cookie_consent_text_nl',
        'Text',
        'corso_cookie_consent_text_nl_callback',
        'corso-cookie-consent-labels',
        'corso_cookie_consent_labels_section'
    );

    add_settings_field(
        'corso_cookie_consent_button_nl',
        'Button',
        'corso_cookie_consent_button_nl_callback',
        'corso-cookie-consent-labels',
        'corso_cookie_consent_labels_section'
    );

    add_settings_field(
        'corso_cookie_consent_policy_nl',
        'Policy Url',
        'corso_cookie_consent_policy_nl_callback',
        'corso-cookie-consent-labels',
        'corso_cookie_consent_labels_section'
    );

}


// Register and define style settings
function corso_cookie_consent_register_style_settings() {
    register_setting('corso_cookie_consent_options_group', 'corso_cookie_consent_background_color');
    register_setting('corso_cookie_consent_options_group', 'corso_cookie_consent_button_color');
    register_setting('corso_cookie_consent_options_group', 'corso_cookie_consent_button_hover_color');
    register_setting('corso_cookie_consent_options_group', 'corso_cookie_consent_text_color');

    add_settings_section(
        'corso_cookie_consent_styling_section',
        'Styling',
        null,
        'corso-cookie-consent-styling'
    );

    add_settings_field(
        'corso_cookie_consent_background_color',
        'Background Color',
        'corso_cookie_consent_background_color_callback',
        'corso-cookie-consent-styling',
        'corso_cookie_consent_styling_section'
    );

    add_settings_field(
        'corso_cookie_consent_button_color',
        'Button Color',
        'corso_cookie_consent_button_color_callback',
        'corso-cookie-consent-styling',
        'corso_cookie_consent_styling_section'
    );

    add_settings_field(
        'corso_cookie_consent_button_hover_color',
        'Button Hover Color',
        'corso_cookie_consent_button_hover_color_callback',
        'corso-cookie-consent-styling',
        'corso_cookie_consent_styling_section'
    );

    add_settings_field(
        'corso_cookie_consent_text_color',
        'Text Color',
        'corso_cookie_consent_text_color_callback',
        'corso-cookie-consent-styling',
        'corso_cookie_consent_styling_section'
    );
}

add_action('admin_init', 'corso_cookie_consent_register_labels_settings');
add_action('admin_init', 'corso_cookie_consent_register_style_settings');
add_action('admin_init', 'corso_cookie_consent_register_tracking_settings');

// Callback functions for settings fields
function corso_cookie_consent_text_nl_callback() {
    $value = get_option('corso_cookie_consent_text_nl', 'Deze website maakt gebruik van cookies');
    echo '<input type="text" name="corso_cookie_consent_text_nl" value="' . esc_attr($value) . '" />';
}

function corso_cookie_consent_button_nl_callback() {
    $value = get_option('corso_cookie_consent_button_nl', 'Accepteren');
    echo '<input type="text" name="corso_cookie_consent_button_nl" value="' . esc_attr($value) . '" />';
}

function corso_cookie_consent_policy_nl_callback() {
    $value = get_option('corso_cookie_consent_policy_nl', '');
    echo '<input type="text" name="corso_cookie_consent_policy_nl" value="' . esc_attr($value) . '" />';
}


function corso_cookie_consent_background_color_callback() {
    $color = get_option('corso_cookie_consent_background_color', '#2c3e50');
    echo '<input type="text" name="corso_cookie_consent_background_color" value="' . esc_attr($color) . '" class="color-field" />';
}

function corso_cookie_consent_button_color_callback() {
    $color = get_option('corso_cookie_consent_button_color', '#e74c3c');
    echo '<input type="text" name="corso_cookie_consent_button_color" value="' . esc_attr($color) . '" class="color-field" />';
}

function corso_cookie_consent_button_hover_color_callback() {
    $color = get_option('corso_cookie_consent_button_hover_color', '#c0392b');
    echo '<input type="text" name="corso_cookie_consent_button_hover_color" value="' . esc_attr($color) . '" class="color-field" />';
}

function corso_cookie_consent_text_color_callback() {
    $color = get_option('corso_cookie_consent_text_color', '#ffffff');
    echo '<input type="text" name="corso_cookie_consent_text_color" value="' . esc_attr($color) . '" class="color-field" />';
}

function corso_cookie_consent_tracking_gtm_id_callback() {
    $value = get_option('corso_cookie_consent_tracking_gtm_id', '');
    echo '<input type="text" name="corso_cookie_consent_tracking_gtm_id" value="' . esc_attr($value) . '"  />';
}

function corso_cookie_consent_tracking_ga_id_callback() {
    $value = get_option('corso_cookie_consent_tracking_ga_id', '');
    echo '<input type="text" name="corso_cookie_consent_tracking_ga_id" value="' . esc_attr($value) . '"  />';
}