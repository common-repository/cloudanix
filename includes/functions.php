<?php defined('ABSPATH') or die('No script kiddies please!');

if (!function_exists('cloudanix_write_log')) {
	function cloudanix_write_log($log)
	{
		if (is_array($log) || is_object($log)) {
			error_log(print_r($log, true));
		} else {
			error_log($log);
		}
	}
}

/*
* Gets the request parameter.
*
* @param string $key The query parameter
* @param string $default The default value to return if not found
*
* @return string The request parameter.
*/

function cloudanix_read_request_parameter($key, $default = '')
{
	// If not request set
	if (!isset($_REQUEST[$key]) || empty($_REQUEST[$key])) {
		return $default;
	}

	// Set so process it
	return strip_tags((string)wp_unslash($_REQUEST[$key]));
}

function cloudanix_log($res)
{
	cloudanix_write_log("response content-type { $res->getHeader('content-type')[0] }");
	cloudanix_write_log("response status code { $res->getStatusCode() }");
	cloudanix_write_log("response body { $res->getBody() }");
}

function cloudanix_init_entity_settings($domain, $email, $recipes)
{
	$body = cloudanix_create_entity(cloudanix_siteid(), cloudanix_wordpress_version(), cloudanix_version(), $domain, $email, $recipes);

	cloudanix_write_log("API call response from cloudanix_init_entity_settings->cloudanix_create_entity -  { $body }");

	$res = json_decode($body, true);

	$msg = "";
	$status = "success";

	if ($res['status'] == cloudanix_success_key) {
		update_option("cloudanix_account_string", $res['account_string'], TRUE);
		update_option("cloudanix_entity_string", $res['entity_string'], TRUE);
		update_option("cloudanix_dashboard_url", esc_url($res['sitedashboard_url']), TRUE);
		update_option("cloudanix_activated", true, TRUE);
		// $msg =  $res['message'];
		$msg = cloudanix_activation_success_message;
	} else {
		$msg = cloudanix_activation_failure_message;
		$status = "failure";
	}

	update_option("cloudanix_domain", $domain, TRUE);
	update_option("cloudanix_email", $email, TRUE);
	update_option("cloudanix_recipes", $recipes, TRUE);
	update_option("cloudanix_message", $msg, TRUE);

	return $status;
}

function cloudanix_update_entity_settings($domain, $email, $recipes)
{
	$account_string = get_option('cloudanix_account_string');
	$entity_string = get_option("cloudanix_entity_string");

	$body =  cloudanix_update_entity(cloudanix_siteid(), cloudanix_wordpress_version(), cloudanix_version(), $account_string, $entity_string, $domain, $email, $recipes);
	cloudanix_write_log("API call response from cloudanix_update_entity_settings->cloudanix_update_entity -  { $body }");

	$res = json_decode($body, true);

	$msg = "";
	$status = $res['status'];

	if ($res['status'] == cloudanix_success_key) {
		update_option("cloudanix_account_string", $res['account_string'], TRUE);
		update_option("cloudanix_entity_string", $res['entity_string'], TRUE);
		update_option("cloudanix_dashboard_url", esc_url($res['sitedashboard_url']), TRUE);
		update_option("cloudanix_activated", true, TRUE);

		$msg = $res['message'];
	} else {
		$status = "failure";
		$msg = cloudanix_failure_message;
	}

	update_option("cloudanix_domain", $domain, TRUE);
	update_option("cloudanix_email", $email, TRUE);
	update_option("cloudanix_recipes", $recipes, TRUE);
	update_option("cloudanix_message", $msg, TRUE);
	return $status;
}

function cloudanix_check_entity_status()
{
	$account_string = get_option('cloudanix_account_string');
	$entity_string = get_option("cloudanix_entity_string");

	$body = cloudanix_entity_status_check(cloudanix_siteid(), cloudanix_wordpress_version(), cloudanix_version(), $account_string, $entity_string);
	cloudanix_write_log("API call response from cloudanix_check_entity_status->cloudanix_entity_status_check -  { $body }");

	$res = json_decode($body, true);

	$response = [
		'status' => $res['status'],
		'display_status_string' => $res['display_status_string'],
		'display_status_html' => $res['display_status_html']
	];

	return $response;
}

function cloudanix_delete_entity_settings()
{
	$account_string = get_option('cloudanix_account_string');
	$entity_string = get_option("cloudanix_entity_string");

	if ($account_string == "" || $entity_string == "") {
		return;
	}

	$body = cloudanix_delete_entity(cloudanix_siteid(), cloudanix_wordpress_version(), cloudanix_version(), $account_string, $entity_string);
	cloudanix_write_log("API call response from cloudanix_delete_entity_settings->cloudanix_delete_entity -  { $body }");

	$res = json_decode($body, true);

	$msg = "";
	$status = $res['status'];

	if ($res['status'] == cloudanix_success_key) {
		delete_option('cloudanix_account_string');
		delete_option('cloudanix_entity_string');
		delete_option('cloudanix_dashboard_url');
		delete_option('cloudanix_domain');
		delete_option('cloudanix_email');
		delete_option('cloudanix_recipes');
		delete_option('cloudanix_message');
		delete_option('cloudanix_activated');
		delete_option('wp_version');
		delete_option('cloudanix_version');

		$msg = cloudanix_success_message;
	} else {
		$status = "failure";
		$msg = cloudanix_failure_message;
	}

	return $status;
}

function cloudanix_handle_update_settings()
{
	check_admin_referer('update_cloudanix_settings');

	// Get the options that were sent
	$domain = (!empty($_POST["cloudanix_domain"])) ? sanitize_text_field($_POST["cloudanix_domain"]) : NULL;
	$email = (!empty($_POST["cloudanix_email"])) ? sanitize_email($_POST["cloudanix_email"]) : NULL;

	$recipes = "";
	foreach ($_POST['cloudanix_recipe'] as $selected) {
		$recipes = $recipes . $selected . ",";
	}

	// Validation would go here
	if ($email == "" || $domain == ""  || $recipes == "") {
		update_option("cloudanix_message", "Required parameters are missing", TRUE);

		wp_redirect(cloudanix_settings_url('failure'));
		exit();
	}

	// Call Cloudanix API
	$cloudanix_activated = get_option('cloudanix_activated');
	$status = '';
	if ($cloudanix_activated != true) {
		$status = cloudanix_init_entity_settings($domain, $email, $recipes);
	} else {
		$status = cloudanix_update_entity_settings($domain, $email, $recipes);
	}

	wp_redirect(cloudanix_settings_url($status));
	exit();
}

function cloudanix_get_status_text()
{
	$res = cloudanix_check_entity_status();

	$msg = str_replace('{DASHBOARD_URL}', '<a href="' . esc_url(cloudanix_dashboard_url()) . '" target="_blank" style="text-decoration: underline;">here</a>', $res['display_status_html']);

	echo '<h3>' . $msg  . '</h3>';

	// if ($res['status'] == cloudanix_status_active_key) {
	// 	echo '<h3>' . $msg  . '</h3>';

	// 	echo '<div>	<p>
	// 		<h3>Click ' . alink . 'to view your Cloudanix dashboard</h3>
	// 	</p></div>';
	// } else if ($status == cloudanix_status_expired_key) {
	// 	echo '<div>	<p>
	// 		<h3>Your Trial period has expired. Click <a href="' . cloudanix_dashboard_url() . '" target="_blank" style="text-decoration: underline;">here</a> to activate and view your Cloudanix dashboard</h3>
	// 	</p></div>'; 
	// }
}

function cloudanix_settings_url($status)
{
	// http://localhost/wordpress/wp-admin/admin.php?page=cloudanix-settings

	// Redirect back to settings page
	// The ?page=cloudanix-settings corresponds to the "slug" 
	// set in the fourth parameter of add_submenu_page() above.
	if ($status != "") {
		return admin_url("admin.php") . "?page=" . cloudanix_menu_slug . "&status=" . $status;
	} else {
		return admin_url("admin.php") . "?page=" . cloudanix_menu_slug;
	}
}

function cloudanix_siteid()
{
	return get_current_blog_id();
}

function cloudanix_wordpress_version()
{
	return get_bloginfo('version');

	// $version = get_option('wp_version');

	// if ($version == "") {
	// 	$version = get_bloginfo('version');
	// 	update_option("wp_version", $version, TRUE);
	// }

	// return $version;
}

function cloudanix_version()
{
	$version = get_option('cloudanix_version');

	if ($version == "") {
		$version = cloudanix_version;
		update_option("cloudanix_version", $version, TRUE);
	}

	return $version;
}

function cloudanix_dashboard_url()
{
	return get_option('cloudanix_dashboard_url');
}

function cloudanix_get_blog_url()
{
	$url = get_option('cloudanix_domain');

	if ($url == "") {
		$url = get_bloginfo("url");
	}

	return $url;
}

function cloudanix_get_admin_email()
{
	$email = get_option('cloudanix_email');

	if ($email == "") {
		$email = get_bloginfo("admin_email");
	}

	return $email;
}
