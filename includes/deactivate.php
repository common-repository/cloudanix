<?php defined('ABSPATH') or die("No script kiddies please!");

function cloudanix_forcedeactivate()
{
	delete_option('cloudanix_activated');

	// $status = cloudanix_success_key;
	// update_option("cloudanix_message", cloudanix_success_message, TRUE);

	// wp_redirect(cloudanix_settings_url($status));
	// exit();
}
