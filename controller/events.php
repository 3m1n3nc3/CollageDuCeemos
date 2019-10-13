<?php

function mainContent() {
	global $PTMPL, $LANG, $SETT, $framework, $marxTime, $collage; 

	$PTMPL['page_title'] = $LANG['homepage'];	 
	
	$PTMPL['site_url'] = $SETT['url']; 

	// This is a holder for the big col-12 cards
    $event_holder = 
    '<div class="row text-center mb-2"> 
        %s
    </div>'; 

    // Fetch the pagination for the posts
    $framework->all_rows = $collage->fetchEvents(1);
    $PTMPL['pagination'] = $framework->pagination(1);

    // Fetch the event
    $all_events = $collage->fetchEvents(1);

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
	$PTMPL['skin'] = ' class="black-skin homepage-v4"';

	$theme = new themer('events/content');
	$PTMPL['page_content'] = $theme->make();

	// Set the active landing page_title 
	$theme = new themer('events/container');
	return $theme->make();
}
?>
