<?php

function mainContent() {
	global $PTMPL, $LANG, $SETT, $configuration, $framework, $marxTime, $collage, $cd_input, $cd_session; 

    // Set the skin for the home page
    $PTMPL['skin'] = ' class="'.$configuration['skin'].' homepage-v4"'; 

    // Add an item to the shopping cart
    if ($cd_input->post('cart_item') || ($cd_input->get('add') && ($cd_input->get('add') === 'cart_item' || $cd_input->get('add') === 'cart_item_inline'))) {

        $i_id     = $cd_input->post('cart_item') ? $cd_input->post('cart_item') : $cd_input->get('item_id');
        $quantity = $cd_input->post('quantity') ? $cd_input->post('quantity') : $cd_input->get('quantity') ? $cd_input->get('quantity') : 1;
        $item     = $collage->fetchStore(1, $i_id)[0];

        $cart = array(
            array(
                'item_title'    => $item['title'],
                'item_id'       => $item['id'],
                'quantity'      => $quantity,
                'item_type'     => 'store'
            )
        );
        $collage->storeCart('add', $cart); 

        // Set the reference for this session
        if (!$cd_session->has_userdata('reference')) 
        {
            $reference = $collage->generateToken(6, 3);
            $cd_session->set_userdata('reference', $reference);
        }

        $cd_input->write_flashdata('msg', '<script type="text/javascript"> sweetalert = \'success\';  sweet_title = \'Item successfully added to your shopping cart\'</script>');

        $redr = $cd_input->get('add') === 'cart_item_inline' ? '' : ($cd_input->get('view') ? '&view='.$cd_input->get('view') : '').($cd_input->get('item_id') ? '&item_id='.$cd_input->get('item_id') : '');
        $collage->redirect('store'.$redr);
    }
 
    $PTMPL['notification'] = $cd_input->read_flashdata('msg'); 

    if ($cd_input->get('view')) 
    {
        if ($cd_input->get('view') === 'cart') 
        {
            
            $PTMPL['page_title']        = 'Shopping Cart';  

            $cart_total = 0;
            if ($cd_session->has_userdata('cart')) 
            {
                $store_cart = $cd_session->userdata('cart');

                $shopping_cart = '';
                foreach ($store_cart as $cart_item) {

                    $details = $collage->fetchStore(1, $cart_item['item_id'])[0];
                    $shopping_cart .= storeCartCard($details); 

                    if ($details['discount']) {
                        $discount_per = $details['price'] * $details['discount'] / 100; 
                        $price_tag = $details['price'] - $discount_per;
                        $amount = ($price_tag+$details['shipping']); 
                    } else { 
                        $amount = ($details['price']+$details['shipping']); 
                    }
                    $cart_total += $amount;

                }
                $shopping_cart_total   = currency(3, $configuration['currency']).number_format($cart_total, 2);

            } 
            else 
            {
                $shopping_cart = '    
                <tr> 
                    <td colspan="7">'.notavailable('Your shopping cart is empty', '', 2).'</td> 
                </tr>';
                $shopping_cart_total   = currency(3, $configuration['currency']).'0.00'; 
            }
            $PTMPL['shopping_cart']         = $shopping_cart;
            $PTMPL['shopping_cart_total']   = $shopping_cart_total;
            $PTMPL['disabled']              = $cart_total<1 ? ' disabled' : '';
            $PTMPL['checkout_url']          = cleanUrls($SETT['url'] . '/index.php?page=store&view=checkout');
            $PTMPL['empty_url']          = cleanUrls($SETT['url'] . '/index.php?page=store&view=cart&empty=cart');

            if ($cd_input->get('remove') && $cd_input->get('remove') === 'cart_item') 
            {
                $collage->storeCart('remove', ['item_id' => $cd_input->get('item_id')]); 
                $cd_input->write_flashdata('msg', '<script type="text/javascript"> sweetalert = \'success\'; sweet_title = \'Item removed from cart, keep shopping to discover amazing artworks\';</script>');
                $collage->redirect('store&view=cart');
            }

            if ($cd_input->get('empty') && $cd_input->get('empty') === 'cart') 
            {
                $collage->storeCart('empty'); 

                // remove the payment reference from session
                if ($cd_session->has_userdata('reference')) 
                { 
                    $cd_session->unset_userdata('reference');
                }

                $cd_input->write_flashdata('msg', '<script type="text/javascript"> sweetalert = \'success\';  sweet_title = \'All items have been removed from the cart, keep shopping to discover amazing artworks\'</script>');
                $collage->redirect('store&view=cart');
            } 

            $theme = new themer('store/cart');
        } 
        elseif ($cd_input->get('view') === 'checkout') 
        {
            $PTMPL['page_title']        = 'Complete Order';  

            if ($cd_session->has_userdata('cart')) 
            {
                $store_cart = $cd_session->userdata('cart');

                if (count($store_cart) >= 1) {

                    $PTMPL['country_options']   = countries(1, $cd_input->post('country'));
                    $PTMPL['fname']             = $cd_input->post('fname');
                    $PTMPL['lname']             = $cd_input->post('lname');
                    $PTMPL['phone']             = $cd_input->post('phone');
                    $PTMPL['email']             = $cd_input->post('email');
                    $PTMPL['state']             = $cd_input->post('state');
                    $PTMPL['city']              = $cd_input->post('city');
                    $PTMPL['address']           = $cd_input->post('address');
                    $PTMPL['return_btn']        = cleanUrls($SETT['url'] . '/index.php?page=store&view=cart');

                    if ($cd_input->post('order') !== NULL) {
                        $message = $collage->placeOrder($store_cart);
                        $PTMPL['message'] = $message;
                    }

                } 
                else 
                {
                    $collage->redirect('store');
                }
            } 
            else 
            {
                $collage->redirect('store');
            }
            
            $theme = new themer('store/checkout');

        }
    }
    elseif ($cd_input->get('item_id') && $cd_input->get('add') !== 'cart_item_inline') 
    {
        // Fetch the store item
        $item = $collage->fetchStore(1, $_GET['item_id'])[0];

        $PTMPL['page_title']        = $item['title'];  

        $PTMPL['item_id']           = $item['id'];  
        $PTMPL['item_title']        = $item['title'];  
        $PTMPL['item_description']  = $item['description'];
        $PTMPL['item_tags']         = showTags($item['tags'], 'product mb-4 ml-2');  

        $images = [];
        if ($item['image1']) {
            $images[] = $item['image1'];
        } 
        if ($item['image2']) {
            $images[] = $item['image2'];
        } 
        if ($item['image3']) {
            $images[] = $item['image3'];
        }

        $item_carousel = $item_figure = '';
        $i = 0;
        if ($images) {       
            foreach ($images as $image) {
                $i++; 

                $active = ($i == 1 ? ' active' : '');

                $item_carousel .=        
                '<div class="carousel-item'.$active.'">
                    <img src="'.getImage($image, 1).'" alt="Slide '.$i.'" class="img-fluid">
                </div>';

                $item_figure .= '
                <figure class="col-md-4"> 
                    <a href="'.getImage($image, 1).'" data-size="1600x1067"> 
                        <img src="'.getImage($image, 1).'" class="img-fluid">
                    </a>
                </figure>';
            }
        } else {
            $item_carousel .=        
            '<div class="carousel-item active">
                <img src="'.getImage($item['image1'], 1).'" alt="Slide '.$i.'" class="img-fluid">
            </div>';
        }

        $PTMPL['item_figure'] = $item_figure;
        $PTMPL['item_carousel']  = $item_carousel;

        $discount = '';
        if ($item['discount']) {

           $discount_per = $item['price'] * $item['discount'] / 100;
           $PTMPL['real_price'] = '
           <span class="grey-text"> <small> <s>'.currency(3, $configuration['currency']).number_format($item['price']).'</s></small></span> 
           <span class="red-text"><small><sup>-'.$item['discount'].'%</sup></small></span>';

           $price_tag = $item['price'] - $discount_per;

           $PTMPL['sale_price'] = '
           <span class="red-text font-weight-bold"> <strong>'.currency(3, $configuration['currency']).number_format($price_tag, 2).'</strong></span>';

        } else {  

           $PTMPL['sale_price'] = '
           <span class="red-text font-weight-bold"> <strong>'.currency(3, $configuration['currency']).number_format($item['price'], 2).'</strong> </span>';

        }

        $theme = new themer('store/content');
    }
    else
    {
        
        $collage->parent = 'store'; 
        $collage->priority = '3';
        $intro =  $collage->fetchStatic(null, 1)[0];  
        
        $PTMPL['site_url'] = $SETT['url'];

        $PTMPL['page_title'] = $intro['title'];  
        $PTMPL['seo_meta'] = seo_plugin(getImage($intro['jarallax'], 1), $intro['content'], $intro['title']);

        $PTMPL['main_title'] = $intro['title'];
        $PTMPL['main_content'] = $framework->rip_tags($intro['content']);
        $PTMPL['jarallax'] = $intro['jarallax'] ? jarallax($intro['jarallax']) : '';  

        // This is a holder for the big col-12 cards
        // text-centers
        $store_holder = 
        '<div class="row mb-2"> 
            %s
        </div>'; 

        // Fetch the pagination for the posts
        $framework->all_rows = $collage->fetchStore(2);
        $PTMPL['pagination'] = $framework->pagination(1);

        // Fetch the store
        $store = $collage->fetchStore(2);

        if ($store) {
            $store_card = ''; $top = ' mt-4';
            $i = 0;
            foreach ($store as $ev => $item) { 
                $image = getImage($item['image1'], 1);
                $store_card .= storeCard($image, $item); 
            }  
            $PTMPL['list_events'] = sprintf($store_holder, $store_card);  
        } else {
            $PTMPL['list_events'] = notavailable('Sorry, the store is still empty, check back tomorrow', '', 2);  
        }

        $theme = new themer('events/content');
    }
    
    $PTMPL['page_content'] = $theme->make();

	// Set the active landing page_title 
	$theme = new themer('events/container');
	return $theme->make();
}
?>
