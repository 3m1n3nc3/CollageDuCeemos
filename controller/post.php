<?php

function mainContent() {
	global $PTMPL, $LANG, $SETT, $framework, $collage, $marxTime, $user, $user_role;  
	
	$PTMPL['site_url'] = $SETT['url']; 
 
	$post_id = isset($_GET['post_id']) ? $_GET['post_id'] : null;
	$posts = $collage->fetchPost(1, $post_id)[0];
	$views = $collage->fetchStatistics(1, $posts['id'])[0];
	$page = $SETT['url'].$_SERVER['REQUEST_URI'];

	if ($posts) {
		$PTMPL['page_title'] = $posts['title'];	
		$PTMPL['seo_meta'] = seo_plugin(getImage($posts['image'], 1), $posts['details'], $posts['title']);

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

	    $profile = $framework->userData($posts['user_id'], 1);
	    $PTMPL['author'] = author_link($profile, 1);

	    $PTMPL['sharing'] = sharingLinks(urlencode($page), urlencode($sub_title.'. '.$posts['quote']));
	    $PTMPL['count_views'] = $views['total'];
	 
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
