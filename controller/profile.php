<?php

function mainContent() {
	global $PTMPL, $LANG, $SETT, $configuration, $admin, $user, $user_role, $framework, $collage, $marxTime; 

   	if ($user) {
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

		$PTMPL['side_bar'] = profile_sidebar();

		// Set the active landing page_title 
		$theme = new themer('profile/update'); 
		$PTMPL['content'] = $theme->make();
		
		$theme = new themer('moderate/container');
	} else {
		$theme = new themer('homepage/fullpage');
  		$PTMPL['content'] = notAvailable('', '', 403);
	}
	return $theme->make();
}
?>
