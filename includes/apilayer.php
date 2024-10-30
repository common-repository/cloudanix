<?php defined('ABSPATH') or die("No script kiddies please!");

function cloudanix_create_entity($site_id, $wp_version, $plugin_version, $url, $email, $recipes)
{
	$params = [
		'site_id' => $site_id,
		'wp_version' => $wp_version,
		'plugin_version' => $plugin_version,
		'url' => $url,
		'email' => $email,
		'recipes' => $recipes
	];

	$res = cloudanix_post_request(cloudanix_init_url, $params);

	return $res['body'];
}

function cloudanix_update_entity($site_id, $wp_version, $plugin_version, $account_string, $entity_string, $url, $email, $recipes)
{
	$params = [
		'site_id' => $site_id,
		'wp_version' => $wp_version,
		'plugin_version' => $plugin_version,
		'account_string' => $account_string,
		'entity_string' => $entity_string,
		'url' => $url,
		'email' => $email,
		'recipes' => $recipes
	];

	$res = cloudanix_post_request(cloudanix_update_url, $params);

	return $res['body'];
}

function cloudanix_entity_status_check($site_id, $wp_version, $plugin_version, $account_string, $entity_string)
{
	$params = [
		'site_id' => $site_id,
		'wp_version' => $wp_version,
		'plugin_version' => $plugin_version,
		'account_string' => $account_string,
		'entity_string' => $entity_string
	];

	$res = cloudanix_post_request(cloudanix_status_check_url, $params);

	return $res['body'];
}

function cloudanix_delete_entity($site_id, $wp_version, $plugin_version, $entity_string, $account_string)
{
	$params = [
		'site_id' => $site_id,
		'wp_version' => $wp_version,
		'plugin_version' => $plugin_version,
		'account_string' => $account_string,
		'entity_string' => $entity_string
	];

	$res = cloudanix_post_request(cloudanix_delete_url, $params);

	return $res['body'];
}

function cloudanix_post_request($url, $json_values)
{
	$res = wp_remote_post($url, array(
		'headers'     => array('Content-Type' => 'application/json; charset=utf-8'),
		'body'        => json_encode($json_values),
		'method'      => 'POST',
		'data_format' => 'body',
	));

	return $res;
}
