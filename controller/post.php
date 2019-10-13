<?php

function mainContent() {
	global $PTMPL, $LANG, $SETT, $framework, $collage, $marxTime, $user, $user_role; 

	$PTMPL['page_title'] = $LANG['homepage'];	 
	
	$PTMPL['site_url'] = $SETT['url']; 
 
	$post_id = isset($_GET['post_id']) ? $_GET['post_id'] : null;
	$posts = $collage->fetchPost(1, $post_id)[0];

	if ($posts) {
        $date = $marxTime->dateFormat($posts['event_date'], 4);

        if ($user_role < 4 || $user['founder'] != 1) { 
        	$collage->countViews($posts['id']);
        }

	    if ($posts['category'] == 'event') {
	    	$PTMPL['post_title'] = $posts['title'].' - <span class="grey-text">'.ucfirst($posts['category']).'<small> ('.$date.')</small></span>';
	    	$sub_title = $date;
	    } else {
	    	$PTMPL['post_title'] = $posts['title'].' - <span class="grey-text">'.ucfirst($posts['category']).'</span>'; 
	    	$sub_title = $posts['sub_title'];
	    }
	    $PTMPL['intro_content'] = postIntro($posts['title'], $posts['quote'], $sub_title);
	    $PTMPL['post_image'] = getImage($posts['image'], 1);
	    $PTMPL['post_details'] = $posts['details'];
	 
		$theme = new themer('posts/content');
		$PTMPL['page_content'] = $theme->make();

	} else {
		$PTMPL['page_content'] = '<div class="m-5">'.notAvailable('', '', 404).'</div>';
	}

	// Set the active landing page_title 
	$theme = new themer('posts/container');
	return $theme->make();
}
?>
