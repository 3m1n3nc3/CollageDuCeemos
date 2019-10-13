<?php

function mainContent() {
	global $PTMPL, $LANG, $SETT, $configuration, $framework, $collage; 

	$PTMPL['page_title'] = $LANG['homepage'];	 
	
	$PTMPL['site_url'] = $SETT['url']; 

	// This is a holder for the big col-12 cards
    $big_card = 
    '<div class="row mb-4"> 
        %s
    </div>'; 

	// This is a holder for the small col-6 cards
    $small_card = '
    <div class="row text-center">  
        %s
    </div>';

    if ($configuration['banner']) {
        $PTMPL['jarallax'] = jarallax($configuration['banner'], 2);
    }

    // Check if there are no featured or non featured (No) post
    $collage->public = 1;
    $yes_posts = $collage->fetchPost(2);
    if (!$yes_posts) {
    	$PTMPL['featured_posts'] = notAvailable('No new post to show you', 'text-info', 2);
    }

    // Show the featured posts
    if (!isset($_GET['archive'])) {
	    $collage->featured = 1;
	    $featured_posts = $collage->fetchPost(2);

	    if ($featured_posts) {
	        $post_card = ''; $top = ' mt-4';
	        $i = 0;
	        foreach ($featured_posts as $pst => $post) {
	            $i++;
	            $post_card .= sprintf($big_card, big_postCard($post['id']));
	        } $post_card .= '<hr>';
	    	$PTMPL['featured_posts'] = $post_card;  
	    }
	} else {
    	// Fetch posts filtered by entry month 
		$collage->archive = $_GET['archive'];
	}

	// Set the argument for non featured posts
    $collage->featured = 0;

    // Fetch the pagination for the posts
    $framework->all_rows = $collage->fetchPost(2);
    $PTMPL['pagination'] = $framework->pagination(1);

    // Fetch the non featured posts
    $all_posts = $collage->fetchPost(2);

    if ($all_posts) {
        $post_card = ''; $top = ' mt-4';
        $i = 0;
        foreach ($all_posts as $ps => $post) {
          //   $i++;
          //   if (count($all_posts) > 1) {
          //   	$post_card .= big_postCard($post['id'], 1);
          //   } else {
        		// // If there is only one result, make it full screen
          //   	$post_card .= big_postCard($post['id']);
          //   }
            $post_card .= big_postCard($post['id']);
        } 
      //   if (count($all_posts) > 1) {
    		// $PTMPL['list_posts'] = sprintf($small_card, $post_card);  
      //   } else {
      //   	// If there is only one result, make it full screen
    		// $PTMPL['list_posts'] = sprintf($big_card, $post_card); 
      //   }
        $PTMPL['list_posts'] = sprintf($big_card, $post_card); 
    }

    // Set the skin for the home page
	$PTMPL['skin'] = ' class="black-skin homepage-v1"'; 

	$theme = new themer('homepage/content');
	$PTMPL['page_content'] = $theme->make(); 

	// Set the active landing page_title 
	$theme = new themer('homepage/container');
	return $theme->make();
}
?>
