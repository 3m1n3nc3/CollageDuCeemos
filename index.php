<?php
require_once(__DIR__ . '/includes/autoload.php'); 
 
if(isset($_GET['page']) && isset($action[$_GET['page']])) {
	$page_name = $action[$_GET['page']];
} else {
	$page_name = 'introduction';
} 
 
require_once("controller/{$page_name}.php");

$url = $SETT['url']; // 'http://admin.collageduceemos.te';
if (strpos($url, 'admin') && (!$admin || !$user['founder'] || $user['role'] < 4)) {
	if ($page_name != 'moderate') {
		$framework->redirect(cleanUrls($SETT['url'].'/index.php?page=moderate'), 1);
	}
}

$PTMPL['site_title'] = $configuration['site_name']; 
$PTMPL['site_slug'] = $configuration['slug'];
$PTMPL['site_logo'] = getImage($configuration['logo']);
$PTMPL['site_url'] = $SETT['url'];
$PTMPL['template_url'] = $PTMPL['template_url'];
$PTMPL['favicon'] = getImage($configuration['intro_logo']);

$captcha_url = '/includes/vendor/goCaptcha/goCaptcha.php?gocache='.strtotime('now');
$PTMPL['captcha_url'] = $SETT['url'].$captcha_url;

//$PTMPL['token'] = $_SESSION['token_id'];  

$PTMPL['site_copy'] = '&copy; Copyright '.date('Y').' <a href="'.$PTMPL['site_url'].'">'.$configuration['site_name'].'</a> <a href="https://twitter.com/'.$configuration['twitter'].'"><i class="fa fa-twitter"></i></a>';  

$PTMPL['language'] = isset($_COOKIE['lang']) ? $_COOKIE['lang'] : ''; 

$theme = new themer('navigation/carousel');
$PTMPL['carousel'] = $theme->make();
 
$PTMPL['page_sidebar'] = site_sidebar();
$PTMPL['content_footer'] = postFooter();

$adspace = '
<section class="mb-5">
    <div class="card card-body py-0 px-0">
        <div class="single-post">
            <p class="font-weight-bold dark-grey-text text-center spacing grey lighten-4 py-2 my-1">
                <strong>ADVERT</strong>
            </p>
            <div class="pb-0">%s</div>
        </div>
    </div>
</section>';

$PTMPL['advert_unit_two'] = $configuration['ads_off'] == 0 && $configuration['ads_2'] ? sprintf($adspace, $configuration['ads_2']) : '';
$PTMPL['advert_unit_three'] = $configuration['ads_off'] == 0 && $configuration['ads_3'] ? sprintf($adspace, $configuration['ads_3']) : ''; 
$PTMPL['advert_unit_four'] = $configuration['ads_off'] == 0 && $configuration['ads_4'] ? sprintf($adspace, $configuration['ads_4']) : '';  

// Render the page
$PTMPL['content'] = mainContent();

if ($page_name !== 'introduction') {
	$PTMPL['skin'] = ' class="black-skin"'; 
	$scripts = new themer('coder/scripts');
	$PTMPL['body_scripts'] = $scripts->make(); 
	
	// Dynamically included pages
	$PTMPL['header'] = globalTemplate(1);  
	$PTMPL['footer'] = globalTemplate();  
	// End Dynamically included pages
}

$theme = new themer('container'); 

echo $theme->make();
 
?>
