<?php defined('ABSPATH') or die("No script kiddies please!");

if (!current_user_can('manage_options')) {
	wp_die(__('You do not have sufficient permissions to access this page.', 'cloudanix'));
}

function cloudanix_dashboard()
{
	wp_add_dashboard_widget('dashboard_cloudanix', 'Cloudanix', 'cloudanix_admin_dashboard');
}

function cloudanix_admin_dashboard()
{
	cloudanix_get_status_text();
}
