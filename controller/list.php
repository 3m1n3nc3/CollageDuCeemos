<?php

function mainContent() {
	global $PTMPL, $LANG, $SETT, $framework, $marxTime, $collage; 
	
	$PTMPL['site_url'] = $SETT['url']; 
    $PTMPL['page_title'] = 'Blog Posts';  

	// This is a holder for the big col-12 cards
    $item_holder = 
    '<section class="pb-3 text-center text-lg-left"> 
        <hr class="mb-5">
        %s
    </section>'; 

    // Sort the result by query
    if (isset($_GET['sorting']) && $_GET['sorting'] !== '') {
        $collage->sort = $_GET['sorting'];
        $PTMPL['page_title'] = ucfirst($_GET['sorting']);    
    }

    // Set the skin for the home page
	$PTMPL['skin'] = ' class="black-skin homepage-v2';

    if (isset($_GET['sorting']) && $_GET['sorting'] == 'catalog') {
	   $theme = new themer('listings/list_index');
       if (isset($_GET['type']) && $_GET['type'] == 'artist') {
            $PTMPL['page_title'] = 'Artists';  
            $PTMPL['page_description'] = 'See portfolios and creative works from artists and creative people from all around the world!';
            // Set category  
            $category = $collage->fetchCategories(1); 
            if ($category) {
                $categories = '';$i = 35;$ii = 0;
                foreach ($category as $row) { 
                    $i++;$ii++; 
                    $link = cleanUrls($SETT['url'].'/index.php?page=listing&sorting='.$row['value']);  
                    $categories .= '
                    <div class="col-md-4 mb-4">
                        <div class="col-1 col-md-2 float-left">
                            <i class="fa '.icon(3, $i).' fa-2x '.$framework->mdbcolors($ii).'"></i> 
                        </div> 
                        <div class="col-10 col-md-9 col-lg-10 px-3 float-right">
                            <h4 class="font-weight-bold mb-4">'.$row['title'].'</h4>
                            <p class="grey-text">'.$row['info'].'</p>
                            <a href="'.$link.'" class="btn '.$framework->mdbcolors($ii, 1).' btn-sm ml-0 p-4 px-0">Explore '.$row['title'].'</a>
                        </div>
                    </div>'; 
                }
                $PTMPL['categories'] = $categories;                
            }
       }
    } else {
        $theme = new themer('listings/content');

        // Fetch the pagination for the posts
        $framework->all_rows = $collage->fetchPost(2);
        $PTMPL['pagination'] = $framework->pagination(1);

        // Fetch the event
        $all_listings = $collage->fetchPost(2);

        if ($all_listings) {
            $listing_card = ''; $top = ' mt-4';
            $i = 0;
            foreach ($all_listings as $ev => $item) {
                $image = getImage($item['image'], 1);
                $link = cleanUrls($SETT['url'].'/index.php?page=post&post_id='.$item['safelink']); 
                $ed = $item['category'] == 'event' ? 'event_' : ''; 
                $date = $marxTime->dateFormat($item[$ed.'date'], 2);
                $listing_card .= listCardLayout(1, $image, $item['title'], $item['details'], $date, $link, $item['category']); 
            }  
            $PTMPL['listings'] = sprintf($item_holder, $listing_card);  
        } else {
            $PTMPL['listings'] = notAvailable('No '.$PTMPL['page_title'].' to display', 'text-info', 2);
        }
    }

	$PTMPL['page_content'] = $theme->make();

	// Set the active landing page_title 
	$theme = new themer('listings/container1');
	return $theme->make();
}
?>
