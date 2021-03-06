<?php      

// Set the defult timezone
date_default_timezone_set("Africa/Lagos");

// Set the site configuration here
// Default configuration
// $configuration = array('language' => 'english', 'site_name' => 'Passengine', 'site_phone' => '09031983482'
// 	, 'twillio_phone' => '+1092292922', 'cleanurl' => 0, 'page_limits' => 10, 'sidebar_limit' => 3, 'related_limit' => 5, 'releases_limit' => 1);
// You can pass this configuration information from a database, your database should contain the default
// configuration variables
$configuration = configuration();
// $framework->beautifulBeast();

// Store the theme path and theme name into the CONF and TMPL
$PTMPL['template_path'] = $SETT['template_path'];
$PTMPL['template_name'] = $SETT['template_name'] = 'default';//$settings['template'];
$PTMPL['template_url'] = $SETT['template_url'] = $SETT['template_path'].'/'.$SETT['template_name'];
$PTMPL['full_template_url'] = $SETT['full_template_url'] = $SETT['url'].'/'.$PTMPL['template_url'];

// $_SESSION['username'] = 'davidson';
// Check who is logged in right now
if (isset($_SESSION['username'])) { 
	$user = $framework->userData($_SESSION['username'], 2);
	$user_role = $user['role'];
} elseif (isset($_COOKIE['username'])) {
	$user = $framework->userData($_COOKIE['username'], 2);
}  

if (isset($_SESSION['admin'])) { 
	$admin = $framework->administrator(2, $_SESSION['admin']); 
} elseif (isset($_COOKIE['admin'])) {
	$admin = $framework->administrator(2, $_COOKIE['admin']); 
}

if (isset($_GET['profile']) && isset($_GET['username'])) { 
	$profile = $framework->userData($framework->db_prepare_input($_GET['username']), 2); 
}
