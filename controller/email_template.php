<?php

function returnContent($data = array())
{
	global $PTMPL, $LANG, $SETT, $configuration, $framework, $admin, $user; 

	$PTMPL['page_title'] = $LANG['homepage'];	 
	
	$PTMPL['site_url'] = $SETT['url']; 

	// Page styling
	$PTMPL['logo_image'] 	= getImage($configuration['intro_logo']);  
	$PTMPL['rounder_up'] 	= getImage('rounder-up.png');  
	$PTMPL['rounder_dwn'] 	= getImage('rounder-dwn.png');  

	$PTMPL['light_note'] 	= 'Email notifications from '.$configuration['site_name'];  

	 //'David';  
	$PTMPL['receiver'] 		= isset($data['receiver']) ? $data['receiver'] : '';

	// 'New store order received';  
	$PTMPL['subject'] 		= isset($data['subject']) ? $data['subject'] : ''; 

	// 'Name placed an order on a store item, please login to the dashboard to view the full order details';  
	$PTMPL['message_body'] 	= isset($data['message']) ? $data['message'] : ''; 

	// strtoupper('Click the button below to view orders in your admin dashboard');  
	$PTMPL['more_info'] 	= strtoupper(isset($data['more_info']) ? $data['more_info'] : '');

	// 'VIEW ORDER';
	$PTMPL['btn_label']		= strtoupper(isset($data['btn'][1]) ? $data['btn'][1] : 'DETAILS'); 

	$PTMPL['btn_link']		= isset($data['btn'][0]) ? $data['btn'][0] : ''; 

	// Set the active landing page_title 
	$theme = new themer('store/email_template');
	return $theme->make();
}

?>
