<?php defined('ABSPATH') or die("No script kiddies please!");

if (!current_user_can('manage_options')) {
	wp_die(__('You do not have sufficient permissions to access this page.', 'cloudanix'));
}
