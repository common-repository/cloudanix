<?php defined('ABSPATH') or die("No script kiddies please!");

function cloudanix_forceactivate()
{
	if (get_option('cloudanix_account_string') != "") {
		update_option("cloudanix_activated", true, TRUE);
	} else {
		$domain = cloudanix_get_blog_url();
		$email = cloudanix_get_admin_email();
		$recipes = cloudanix_default_recipes;

		$status = cloudanix_init_entity_settings($domain, $email, $recipes);
	}

	// exit(wp_redirect(admin_url('options-general.php?page=cloudanix_settings')));

	// wp_redirect(cloudanix_settings_url($status));
	// exit();
}
