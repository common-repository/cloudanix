<?php defined('ABSPATH') or die('No script kiddies please!');

// if uninstall.php is not called by WordPress, die
if (!defined('WP_UNINSTALL_PLUGIN')) {
	die;
}

include_once(plugin_dir_path(__FILE__) . '/includes/constants.php');
include_once(plugin_dir_path(__FILE__) . '/includes/apilayer.php');
include_once(plugin_dir_path(__FILE__) . '/includes/functions.php');

cloudanix_delete_entity_settings();
