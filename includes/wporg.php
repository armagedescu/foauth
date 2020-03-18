<?php
#error_log("wporg start");
function wporg_options_page_html() {
    error_log(__FUNCTION__ . "()");
    ?>
    <div class="wrap">
      <h1><?php esc_html( get_admin_page_title() ); ?></h1>
      <form action="options.php" method="post">
        <?php
        // output security fields for the registered setting "wporg_options"
        settings_fields( 'wporg_options' );
        // output setting sections and their fields
        // (sections are registered for "wporg", each field is registered to a specific section)
        do_settings_sections( 'wporg' );
        // output save settings button
        submit_button( 'Save Settings' );
        ?>
      </form>
    </div>
    <?php
}
add_action( 'admin_menu', 'wporg_options_page' );
function wporg_options_page() {
    error_log(__FUNCTION__ . "()");
    add_menu_page(
        'WPOrg',
        'WPOrg Optionz',
        'manage_options',
        'wporg',
        'wporg_options_page_html',
        '', //plugin_dir_url(__FILE__) . 'images/icon_wporg.png',
        20
    );
}

?>