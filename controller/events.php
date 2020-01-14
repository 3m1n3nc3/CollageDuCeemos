<?php

function mainContent() {
	global $PTMPL, $LANG, $SETT, $configuration, $framework, $marxTime, $collage; 

    $collage->parent = 'events'; 
    $collage->priority = '3';
    $intro =  $collage->fetchStatic(null, 1)[0];  
    
    $PTMPL['site_url'] = $SETT['url'];

    $PTMPL['page_title'] = $intro['title'];  
    $PTMPL['seo_meta'] = seo_plugin(getImage($intro['jarallax'], 1), $intro['content'], $intro['title']);

    $PTMPL['main_title'] = $intro['title'];
    $PTMPL['main_content'] = $framework->rip_tags($intro['content']);
    $PTMPL['jarallax'] = $intro['jarallax'] ? jarallax($intro['jarallax']) : '';  

	// This is a holder for the big col-12 cards
    $event_holder = 
    '<div class="row text-center mb-2"> 
        %s
    </div>'; 

    // Fetch the pagination for the posts
    $framework->all_rows = $collage->fetchSpecialPosts(1, 'event');
    $PTMPL['pagination'] = $framework->pagination(1);

    // Fetch the event
    $all_events = $collage->fetchSpecialPosts(1, 'event');

    if ($all_events) {
        $event_card = ''; $top = ' mt-4';
        $i = 0;
        foreach ($all_events as $ev => $event) { 
        	$image = getImage($event['image'], 1);
        	$link = cleanUrls($SETT['url'].'/index.php?page=post&post_id='.$event['id']);
        	$date = $marxTime->dateFormat($event['event_date'], 2);
           	$event_card .= eventsCard($image, $event['title'], $event['details'], $date, $link); 
        }  
    	$PTMPL['list_events'] = sprintf($event_holder, $event_card);  
    }

    // Set the skin for the home page
	$PTMPL['skin'] = ' class="'.$configuration['skin'].' homepage-v4"';

	$theme = new themer('events/content');
	$PTMPL['page_content'] = $theme->make();

	// Set the active landing page_title 
	$theme = new themer('events/container');
	return $theme->make();
}
?>
