<?php
/*
Plugin Name: Link Rotator
Plugin URI: http://www.automatedtraffic.com
Description: Integrates with www.automatedtraffic.com.
Version: 1.0
Author: http://www.automatedtraffic.com
Author URI: http://www.automatedtraffic.com
*/

register_activation_hook(__FILE__, 'install_link_rotator');
register_deactivation_hook( __FILE__, 'remove_link_rotator' );
function install_link_rotator() {
    add_option('link_rotator_id', '', '', 'yes');
    // Should equal 'sidebar' or 'footer':
    add_option('link_rotator_mode', 'sidebar', '', 'yes');
}
function remove_link_rotator() {
    delete_option('link_rotator_id');
    delete_option('link_rotator_mode');
}

add_filter('the_content', 'add_link_rotator_footer');
function add_link_rotator_footer($content) {
    if (get_option('link_rotator_mode') != 'footer') return $content;
    $id = get_option('link_rotator_id');
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://www.leadevolution.com/linkRotator/pluginGetLinks.php?id=$id");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    $response = curl_exec($ch);
    return $content . $response;
}

add_filter('plugin_action_links', 'link_rotator_plugin_action_links', 10, 2);
function link_rotator_plugin_action_links($links, $file) {
    if (strstr($file, 'link_rotator')) {
        $settings_link = "<a href='options-general.php?page=link_rotator.php'>Settings</a>";
        array_unshift($links, $settings_link);
    }
    return $links;
}

if (is_admin()) {
    add_action('admin_menu', 'link_rotator_admin_menu');
    function link_rotator_admin_menu() {
        add_options_page('Link Rotator', 'Link Rotator', 8, 'link_rotator', 'link_rotator_html');
    }
}

function widget_linkRotator() {
    $id = get_option('link_rotator_id');
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://www.leadevolution.com/linkRotator/pluginGetLinks.php?id=$id");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    echo curl_exec($ch);
}

add_action("plugins_loaded", "link_rotator_init");
function link_rotator_init() {
    if (get_option('link_rotator_mode') != 'sidebar') return;
    register_sidebar_widget('Link Rotator', 'widget_linkRotator');
}

function link_rotator_html() {
    ?>
        <div>
            <h2>Link Rotator Options</h2>

            <form method="post" action="options.php">

                <?php wp_nonce_field('update-options');?>

                <b>Please enter your Link Rotator ID below:</b>

                <p />
                
                <input type="text" name="link_rotator_id" id="link_rotator_id" size="5" value="<?php echo get_option('link_rotator_id'); ?>" />

                <p />

                <b>Display mode:</b>
                <table>
                    <tr>
                        <td>
                            <input type="radio" name="link_rotator_mode" value="sidebar" <?php if (get_option('link_rotator_mode') == 'sidebar') echo 'checked';?>/>
                        </td>
                        <td>
                            Sidebar
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="radio" name="link_rotator_mode" value="footer" <?php if (get_option('link_rotator_mode') == 'footer') echo 'checked';?>/>
                        </td>
                        <td>
                            Footer
                        </td>
                    </tr>
                </table>

                <p />

                <input type="submit" value="Save Changes" />

                <input type="hidden" name="action" value="update" />
                <input type="hidden" name="page_options" value="link_rotator_id,link_rotator_mode" />

            </form>
            
            <p />

            Please note that to use this plugin in Sidebar mode, you must drag the "Link Rotator" widget to your sidebar.
            Click "Dashboard", then "Widgets" to access your widgets.

            <p />


        </div>
    <?php
}

?>