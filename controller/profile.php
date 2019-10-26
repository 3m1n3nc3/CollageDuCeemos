<?php

function mainContent() {
	global $PTMPL, $LANG, $SETT, $configuration, $admin, $user, $user_role, $framework, $collage, $marxTime; 

   	if ($user) {
		$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : $user['uid'];
		$post_id = $post_ids = isset($_GET['post_id']) && $_GET['post_id'] !== '' ? $_GET['post_id'] : null;
		$get_post = $collage->fetchPost(1, $post_id)[0];
		$profile_data = $framework->userData($user_id, 1);

   	 	$PTMPL['upload_script'] = $SETT['url'].'/connection/uploader.php?action=ckeditor';

   		$PTMPL['page_title'] = 'Update Profile';
		$PTMPL['username'] = isset($_POST['username']) ? $_POST['username'] : $user['username'];
		$PTMPL['email'] = isset($_POST['email']) ? $_POST['email'] : $user['email'];
		$PTMPL['fname'] = isset($_POST['fname']) ? $_POST['fname'] : $user['fname'];
		$PTMPL['lname'] = isset($_POST['lname']) ? $_POST['lname'] : $user['lname'];
		$PTMPL['introduction'] = isset($_POST['intro']) ? $_POST['intro'] : $user['intro'];
		$PTMPL['qualification'] = isset($_POST['qualification']) ? $_POST['qualification'] : $user['qualification'];
		$PTMPL['facebook'] = isset($_POST['facebook']) ? $_POST['facebook'] : $user['facebook'];
		$PTMPL['twitter'] = isset($_POST['twitter']) ? $_POST['twitter'] : $user['twitter'];
		$PTMPL['instagram'] = isset($_POST['instagram']) ? $_POST['instagram'] : $user['instagram'];

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

		        $create_post_link = cleanUrls($SETT['url'].'/index.php?page=profile&view=create_post');
		        $PTMPL['create_post_btn'] = '<a href="'.$create_post_link.'" class="btn btn-primary font-weight-bolder mb-2">Create new blog post</a>';

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
			if (isset($_GET['update'])) {
				$theme = new themer('profile/update'); 
			} else {
				$theme = new themer('profile/profile'); 
			}

			$PTMPL['profile_photo'] = getImage($profile_data['photo'], 1);
			$PTMPL['profile_name'] = $framework->realName($profile_data['username'], $profile_data['fname'], $profile_data['lname']);
			$PTMPL['profile_qualification'] = $profile_data['qualification'];
			$PTMPL['profile_introduction'] = $profile_data['intro'];
			$PTMPL['profile_social'] = fetchSocialInfo($profile_data, 2);

			$PTMPL['page_title'] = $PTMPL['profile_name'];	
			$PTMPL['seo_meta'] = seo_plugin($PTMPL['profile_photo'], $PTMPL['profile_introduction'], $PTMPL['profile_name']);

			if (isset($_POST['update'])) {
		        $framework->fname = $_POST['fname'];
		        $framework->lname = $_POST['lname'];
		        $framework->email = $_POST['email']; 
		        $framework->password = $_POST['password']; 
		        $framework->username = $_POST['username']; 
		        $framework->facebook = $_POST['facebook'];
		        $framework->twitter = $_POST['twitter'];
		        $framework->instagram = $_POST['instagram'];  
		        $framework->intro = $_POST['introduction'];
		        $framework->qualification = $_POST['qualification'];
		        $framework->image = $_FILES['image'];
		        $PTMPL['notification'] = $framework->updateProfile();
			}
		}

		$PTMPL['side_bar'] = profile_sidebar($user_id);

		// Set the active landing page_title 
		$PTMPL['content'] = $theme->make();
		
		$theme = new themer('moderate/container');
	} else {
		$theme = new themer('homepage/fullpage');
  		$PTMPL['content'] = notAvailable('', '', 403);
	}
	return $theme->make();
}
?>
