<?php

function mainContent() {
	global $PTMPL, $LANG, $SETT, $configuration, $framework; 

	$PTMPL['page_title'] = $LANG['homepage'];	 
	
	$PTMPL['site_url'] = $SETT['url']; 

	// Page styling
	$PTMPL['logo_image'] = getImage($configuration['intro_logo']);
	$PTMPL['bg_image'] = getImage($configuration['intro_banner']);

	$PTMPL['about_page_url'] = cleanUrls($SETT['url'] . '/index.php?page=static&view=about');
	$PTMPL['contact_page_url'] = cleanUrls($SETT['url'] . '/index.php?page=static&view=contact');
	$PTMPL['home_page_url'] = cleanUrls($SETT['url'] . '/index.php?page=homepage');
	$PTMPL['portfolio_page_url'] = cleanUrls($SETT['url'] . '/index.php?page=listing&sorting=portfolio');
	$PTMPL['artists_page_url'] = cleanUrls($SETT['url'] . '/index.php?page=listing&sorting=catalog&type=artist');
	$PTMPL['events_page_url'] = cleanUrls($SETT['url'] . '/index.php?page=events');

	if (isset($_GET['logout'])) {
		if ($_GET['logout'] == 'user') {
			$framework->sign_out(1);
		} elseif ($_GET['logout'] == 'admin') {
			$framework->sign_out(1, 1);
		}
		$framework->redirect(cleanUrls($SETT['url'].'/index.php?page=introduction'), 1);
	}

	$scripts = new themer('coder/introduction-styles');
	$PTMPL['header_scripts'] = $scripts->make(); 

	$PTMPL['skin'] = ' class="text-center cover large-cover"'; 

	// Set the active landing page_title 
	$theme = new themer('homepage/introduction');
	return $theme->make();
}
?>
