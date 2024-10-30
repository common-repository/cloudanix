<?php defined('ABSPATH') or die('No script kiddies please!');
/**
 * Plugin Name: Cloudanix
 * 
 * Plugin URI: https://www.cloudanix.com/integrations/wordpress
 * 
 * Description: View Website essentials, Uptime check, Speed Test (US, EU & Asia), Privacy Score, DNS Monitoring, OWASP Security Tests and others for your website in WordPress (dashboard).
 * 
 * Version: 1.0
 * 
 * Author: Cloudanix
 * 
 * Author URI:https://www.cloudanix.com
 * 
 * License: GPLv2 or later
 * 
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * 
 * Text Domain: cloudanix
 * 
 */

include_once(plugin_dir_path(__FILE__) . '/includes/constants.php');
include_once(plugin_dir_path(__FILE__) . '/includes/apilayer.php');
include_once(plugin_dir_path(__FILE__) . '/includes/functions.php');

function cloudanix_admin()
{
	if (current_user_can('manage_options')) {
		include_once(plugin_dir_path(__FILE__) . '/includes/dashboard.php');

		// include_once(plugin_dir_path(__FILE__) . '/includes/settings.php');
	}
}

function cloudanix_adminmenu()
{
	if (!current_user_can('manage_options')) {
		wp_die(__('You do not have sufficient permissions to access this page.', 'cloudanix'));
	}

	// add_menu_page(string $page_title, string $menu_title, string $capability, string $menu_slug, callable $function = '', string $icon_url = '', int $position = null)
	add_menu_page('Cloudanix Settings', 'Cloudanix', 'manage_options', cloudanix_menu_slug, 'cloudanix_admin_settings');

	// add_submenu_page( string $parent_slug, string $page_title, string $menu_title, string $capability, string $menu_slug, callable $function = '' )
	// add_submenu_page(cloudanix_menu_slug, 'General', 'cloudanix', 'General', 'cloudanix', 'manage_options', cloudanix_menu_slug, 'cloudanix_admin_settings');
}

function cloudanix_admin_settings()
{
	?>
	<form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
		<input type="hidden" name="action" value="update_cloudanix_settings" />
		<?php wp_nonce_field('update_cloudanix_settings');
		?>

		<h3>Settings</h3>

		<p>
			<label>Domain:</label>
			<input class="" type="text" readonly="readonly" name="cloudanix_domain" value="<?php echo cloudanix_get_blog_url(); ?>" />
		</p>

		<p>
			<label>Email:</label>
			<input class="" type="text" readonly="readonly" name="cloudanix_email" value="<?php echo cloudanix_get_admin_email(); ?>" />
		</p>

		<p>
			<label>Recipes:</label>
		</p>
		<p>
			<input type="checkbox" name="cloudanix_recipe_essentials" checked="true" onclick="return false;" value="essentials">Essentials</input>
			<input type="checkbox" name="cloudanix_recipe[]" checked="true" onclick="return false;" value="speedtests">Speed Tests</input>
			<input type="checkbox" name="cloudanix_recipe[]" checked="true" onclick="return false;" value="dnsinfo">DNS Info</input>
			<input type="checkbox" name="cloudanix_recipe[]" checked="true" onclick="return false;" value="privacyscore">Privacy Score</input>
			<input type="checkbox" name="cloudanix_recipe[]" checked="true" onclick="return false;" value="owasptop10">OWASP Top 10</input>
			<input type="checkbox" name="cloudanix_recipe[]" checked="true" onclick="return false;" value="safeindex">Safe Index</input>
			<input type="checkbox" name="cloudanix_recipe[]" checked="true" onclick="return false;" value="uptimecheck">Uptime Check</input>
		</p>

		<input class="button button-primary" type="submit" value="Update" />

		<?php if (cloudanix_read_request_parameter('status') != '') { ?>
			<p>
				<label><?php echo get_option('cloudanix_message'); ?></label>
			</p>
			<?php delete_option('cloudanix_message');
		}
		?>

		<p style="height:25px;">
		</p>

		<?php

		cloudanix_get_status_text();
		?>

	</form>
<?php
}

function cloudanix_register_shortcodes()
{
	include_once(plugin_dir_path(__FILE__) . '/includes/shortcodes.php');
}

function cloudanix_activate()
{
	include_once(plugin_dir_path(__FILE__) . '/includes/activate.php');
	cloudanix_forceactivate();
}

function cloudanix_deactivate()
{
	include_once(plugin_dir_path(__FILE__) . '/includes/deactivate.php');
	cloudanix_forcedeactivate();
}

register_activation_hook(__FILE__, 'cloudanix_activate');

register_deactivation_hook(__FILE__, 'cloudanix_deactivate');

add_action('admin_init', 'cloudanix_admin');

add_action('admin_menu', 'cloudanix_adminmenu');

add_action('admin_post_update_cloudanix_settings', 'cloudanix_handle_update_settings');

add_action('wp_dashboard_setup', 'cloudanix_dashboard');
