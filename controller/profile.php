<?php

function mainContent() 
{
	global $PTMPL, $LANG, $SETT, $configuration, $admin, $user, $user_role, $framework, $collage, $marxTime, $cd_input, $cd_session; 

   	if ($user || (!$cd_input->get('view') && !$cd_input->get('update'))) 
   	{
		$user_id = $cd_input->get('user_id') ?? $user['uid'];
		$post_id = $post_ids = $cd_input->get('post_id') ?? null;
		$get_post = $collage->fetchPost(1, $post_id)[0];
		$profile_data = $framework->userData($user_id, 1);

   	 	$PTMPL['upload_script'] = $SETT['url'].'/connection/uploader.php?action=ckeditor';

   	 	$PTMPL['misc'] = '';
		if ($user && !$user['verified']) 
		{
        	$resend_link = ' <a href="'.cleanUrls($SETT['url'].'/index.php?page=profile&auth=resend').'">Resend Activation Message</a>'; 
	   		$PTMPL['misc'] = '<div class="alert alert-danger font-weight-bold text-center">'.$LANG['activation_notice'].$resend_link.'</div>';

	   		// Show the activation link
	   		if ($cd_input->get('auth')) 
	   		{  
		   		// Resend the activation email
		   		if ($cd_input->get('auth') === 'resend') 
		   		{
		   			$PTMPL['misc'] .= $framework->account_activation($cd_input->get('auth'), $user['username']);
		   		}
		   		else
		   		{
		   			if ($cd_input->get('update') !== 'verify')  
			   		{
			   			$activation_link = cleanUrls($SETT['url'].'/index.php?page=profile&update=verify&auth='.$cd_input->get('auth')); 
			   			$framework->redirect($activation_link, 1);
			   		}
			   	}
	   			$PTMPL['auth_btn'] = '<button class="btn btn-success my-4 btn-block" type="submit" name="activate">Activate Account</button>';
	   		}
		}
		else
		{
			// Show link to create new posts
	        $create_post_link = cleanUrls($SETT['url'].'/index.php?page=profile&view=create_post');
	        $PTMPL['create_post_btn'] = '<a href="'.$create_post_link.'" class="btn btn-primary font-weight-bolder mb-2">Create new blog post</a>';
	        $PTMPL['create_post_btn_block'] = '<a href="'.$create_post_link.'" class="btn btn-primary btn-block font-weight-bolder mb-2">Create new blog post</a>';
    	}

   		$PTMPL['page_title'] = 'Update Profile';
		$PTMPL['username']   = $_POST['username'] ?? $user['username'];
		$PTMPL['email'] = $_POST['email'] ?? $user['email'];
		$PTMPL['fname'] = $_POST['fname'] ?? $user['fname'];
		$PTMPL['lname'] = $_POST['lname'] ?? $user['lname'];
		$PTMPL['introduction']  = $_POST['intro'] ?? $user['intro'];
		$PTMPL['qualification'] = $_POST['qualification'] ?? $user['qualification'];
		$PTMPL['facebook']  = $_POST['facebook'] ?? $user['facebook'];
		$PTMPL['twitter']   = $_POST['twitter'] ?? $user['twitter'];
		$PTMPL['instagram'] = $_POST['instagram'] ?? $user['instagram'];

		if (isset($_GET['view'])) {
			if ($_GET['view'] == 'posts') {
				// Show the list of created posts

				$PTMPL['page_title'] = 'Manage your posts';

				if (isset($_GET['delete'])) {
					$did = $collage->db_prepare_input($_GET['delete']);
					$delete = $collage->deleteContent($did);
					if ($delete === 1) {
						$PTMPL['notification'] = messageNotice('Content has been deleted successfully', 1, 6);
					} elseif ($delete === 0) {
						$PTMPL['notification'] = messageNotice('Content does not exist, or may have already been deleted', 2, 6);
					} else {
						$PTMPL['notification'] = messageNotice($delete, 3, 7);
					}
				}	 

				$PTMPL['posts_list'] = $collage->managePostsList();
			
				$theme = new themer('moderate/posts_content'); 
			} elseif ($_GET['view'] == 'create_post') { 

				$PTMPL['categories'] = $collage->postCategoryOptions($get_post);
			
				$PTMPL['up_btn'] = $get_post ? 'Update Post' : 'Create Post';
				$PTMPL['page_title'] = $get_post ? 'Update '.$get_post['title'] : 'Create new post';
				$PTMPL['post_title'] = isset($_POST['title']) ? $_POST['title'] : $get_post['title'];
				$PTMPL['post_sub_title'] = isset($_POST['sub_title']) ? $_POST['sub_title'] : $get_post['sub_title'];
				$PTMPL['post_details'] = isset($_POST['post_details']) ? $_POST['post_details'] : $get_post['details']; 
				$PTMPL['post_quote'] = isset($_POST['quote']) ? $_POST['quote'] : $get_post['quote'];
				$PTMPL['post_date'] = isset($_POST['date']) ? $_POST['date'] : ($get_post['event_date'] ? date('Y-m-d', strtotime($get_post['event_date'])) : '');
				$PTMPL['post_time'] = isset($_POST['time']) ? $_POST['time'] : ($get_post['event_date'] ? date('h:i A', strtotime($get_post['event_date'])) : '');
				$PTMPL['public'] = isset($_POST['public']) || $get_post['public'] == 1 ? ' checked' : '';
				$PTMPL['featured'] = ' disabled';
				$PTMPL['promote'] = ' disabled';

				if (isset($_POST['create_post'])) {  
					$collage->category = $_POST['category'];
					$collage->title = $_POST['title'];
					$collage->sub_title = $_POST['sub_title'];
					$collage->quote = $_POST['quote'];
					$collage->post_details = str_replace('\'', '', $_POST['post_details']);
					$collage->post_date = $_POST['date'];
					$collage->post_time = $_POST['time'];
					$collage->public = isset($_POST['public']) ? 1 : 0;
					$collage->featured = isset($_POST['featured']) ? 1 : 0;
					$collage->promote = isset($_POST['promote']) ? 1 : 0;
					$collage->image = $_FILES['image'];

					$create = $collage->createPost();
					$PTMPL['notification'] = $create;
				}
				$theme = new themer('moderate/create_post'); 
			}	
		} else {
			if ($cd_input->get('update')) {
				$theme = new themer('profile/update'); 
			} else {
				$theme = new themer('profile/profile'); 
			}

			$ident = ($profile_data['uid'] == $user['uid'] ? 'Please update your profile.' : 'Has not updated their profile information.');

			$PTMPL['profile_photo'] = getImage($profile_data['photo'], 1);
			$PTMPL['profile_name'] = $framework->realName($profile_data['username'], $profile_data['fname'], $profile_data['lname']);
			$PTMPL['profile_qualification'] = ($profile_data['qualification'] ? $profile_data['qualification'] : $ident);
			$PTMPL['profile_introduction'] = ($profile_data['intro'] ? $profile_data['intro'] : $ident);
			$PTMPL['profile_social'] = fetchSocialInfo($profile_data, 2);

			$PTMPL['page_title'] = $PTMPL['profile_name'];	
			$PTMPL['seo_meta'] = seo_plugin($PTMPL['profile_photo'], $PTMPL['profile_introduction'], $PTMPL['profile_name']);

			if ($cd_input->post('update') !== NULL || $cd_input->post('activate') !== NULL) {

		   		// Activate the user
		   		if ($cd_input->get('auth')) 
		   		{
		   			$PTMPL['misc'] .= $framework->account_activation($cd_input->get('auth'), $user['username']);
		   		}

		   		// Update the profile
		        $framework->fname = $cd_input->post('fname');
		        $framework->lname = $cd_input->post('lname');
		        $framework->email = $cd_input->post('email'); 
		        $framework->password = $cd_input->post('password');
		        $framework->username = $cd_input->post('username'); 
		        $framework->facebook = $cd_input->post('facebook');
		        $framework->twitter  = $cd_input->post('twitter');
		        $framework->instagram = $cd_input->post('instagram');
		        $framework->intro = $cd_input->post('introduction');
		        $framework->qualification = $cd_input->post('qualification');
		        $framework->image = $_FILES['image'];
		        $update = $framework->updateProfile();
		        $PTMPL['notification'] = $cd_input->post('update') !== NULL ? $update : '';
			}
		}

		$PTMPL['side_bar'] = profile_sidebar($user['uid']);

		// Set the active landing page_title 
		if ($profile_data) 
		{
			$PTMPL['content'] = $theme->make();
		} 
		else
		{
			$PTMPL['content'] = notAvailable('', '', 404);
		}
		
		$theme = new themer('moderate/container');
	} else {
		$theme = new themer('homepage/fullpage');
  		$PTMPL['content'] = notAvailable('', '', 403);
		
		$page = $SETT['url'].$_SERVER['REQUEST_URI'];
		$cd_session->set_userdata('redirect_to', $page);
		$framework->redirect(cleanUrls($SETT['url'].'/index.php?page=moderate&view=access&login=user'), 1);
	}
	return $theme->make();
}
?>
