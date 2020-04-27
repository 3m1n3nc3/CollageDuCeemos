<?php

function mainContent() {
	global $PTMPL, $LANG, $SETT, $configuration, $framework, $collage, $marxTime;  
	
	$PTMPL['site_url'] = $SETT['url']; 

	$PTMPL['skin'] = ' class="'.$configuration['skin'].'"'; 

	if (isset($_GET['view'])) {
		if ($_GET['view'] == 'about' || $_GET['view'] == 'contact') {

			// Show the empty banner
			$PTMPL['main_title'] = $PTMPL['main_content'] = 
			$PTMPL['features'] = $PTMPL['more_info'] = notAvailable($LANG['no_static_notice'], 'text-info', 2);

			if ($_GET['view'] == 'about') {
				// Prepare the about page

				$collage->parent = 'about'; 
				$collage->priority = 3;
				$main_about =  $collage->fetchStatic(null, 1)[0]; 
				if ($main_about) { 
					$PTMPL['page_title'] = 'About '.$configuration['site_name'];	
					$PTMPL['seo_meta'] = seo_plugin(getImage($main_about['jarallax'], 1), $main_about['content'], $main_about['title']);

					$PTMPL['main_title'] = '<h1 class="font-weight-bold text-center h1 my-5">'.$main_about['title'].'</h1>';
					$PTMPL['main_content'] = '<div class="text-center grey-text mb-5 mx-auto w-responsive lead">'.$main_about['content'].'</div>';
					$PTMPL['jarallax'] = $main_about['jarallax'] ? jarallax($main_about['jarallax']) : '';
				}

 				$collage->priority = '2';
				$features =  $collage->fetchStatic(null, 1);  
				if ($features) {
					$feature = '';
					$i = 0;
					foreach ($features as $row) {
						$i++;
						$feature .= '
						<div class="col-md-4 mb-4">
							<i class="fa fa-4x '.$row['icon'].' '.$framework->mdbColors($i).'"></i>
							<h4 class="font-weight-bold my-4">'.$row['title'].'</h4>
							<div class="grey-text">'.$row['content'].'</div>
						</div>';
					}
					$PTMPL['features'] = $feature;
				}

 				$collage->priority = '1';
				$more_info = $collage->fetchStatic(null, 1);
				if ($more_info) {
					$info = '';
					$i = 0;
					foreach ($more_info as $row) {  
						$i++;
						$content = $framework->auto_template(str_ireplace('{$texp-&gt;', '{$texp->', $row['content']), 1); 
						$class = stripos($content, 'team-activator') ? 'team-section pb-4 ' : '';  
						$info .= ' 
							<hr class="my-5"> 
							<section id="info-'.$i.'" class="section '.$class.'wow fadeIn" data-wow-delay="0.3s"> 
								<h1 class="font-weight-bold text-center h1 my-5">'.$row['title'].'</h1> 
								<div class="text-center grey-text mb-5 mx-auto w-responsive">'.$content.'</div> 
							</section>';
					}
					$PTMPL['more_info'] = $info;
				}
			} else {
				// Prepare the contacts page
				$collage->parent = 'contact'; 
				$collage->priority = '3';
				$intro =  $collage->fetchStatic(null, 1)[0];  

				$PTMPL['page_title'] = 'Contact '.$configuration['site_name'];	
				$PTMPL['seo_meta'] = seo_plugin(getImage($intro['jarallax'], 1), $intro['content'], $intro['title']);

				$PTMPL['main_title'] = $intro['title'];
				$PTMPL['main_content'] = $intro['content'];
				$PTMPL['jarallax'] = $intro['jarallax'] ? jarallax($intro['jarallax']) : '';
				$PTMPL['main_address'] = $configuration['site_office'];
				$PTMPL['social_links'] = fetchSocialInfo($configuration);
			}
			$theme = new themer('static/'.$_GET['view']);
			$PTMPL['page_content'] = $theme->make();
		} else { 
			$more_info = $collage->fetchStatic($_GET['view'])[0];   
			if ($more_info) {

				$PTMPL['page_title'] = $more_info['title'];	
				$PTMPL['seo_meta'] = seo_plugin(getImage($more_info['jarallax'], 1), $more_info['content'], $more_info['title']);

				$class = $founder = '';
				if (stripos($more_info['title'], 'Founder') || stripos($more_info['title'], 'Creator') || stripos($more_info['title'], 'Admin')) {
					$class = 'team-section pb-4 ';
					$founder = userCard($collage->fetchFounder()['uid'], 1); 
				} 
				$PTMPL['more_info'] = ' 
					<hr class="my-5"> 
					<section id="info-'.$more_info['id'].'" class="section '.$class.'wow fadeIn" data-wow-delay="0.3s"> 
						<h1 class="font-weight-bold text-center h1 my-5">'.$more_info['title'].'</h1> 
						<div class="text-center grey-text mb-5 mx-auto w-responsive">'.$more_info['content'].'</div>
						'.$founder.'
					</section>'; 

				$PTMPL['jarallax'] = $more_info['jarallax'] ? jarallax($more_info['jarallax']) : ''; 
			} else {
				$PTMPL['more_info'] = '<div class="m-5">'.notAvailable('', '', 404).'</div>';
			}
				
			$theme = new themer('static/about');

			$PTMPL['page_content'] = $theme->make();
		}
	} 
 
	$PTMPL['page_sidebar'] = site_sidebar();

	// Set the active landing page_title 
	$theme = new themer('static/container');
	return $theme->make();
}
?> 
