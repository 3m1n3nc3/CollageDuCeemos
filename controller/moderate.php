<?php

function mainContent() {
	global $PTMPL, $LANG, $SETT, $configuration, $admin, $user, $user_role, $framework, $collage, $marxTime; 

   	if ($admin || $user['founder'] || $user_role >= 4) {
   	 	$PTMPL['upload_script'] = $SETT['url'].'/connection/uploader.php?action=ckeditor';
			
		$PTMPL['page_title'] = $LANG['homepage'];
		$PTMPL['site_url'] = $SETT['url'];

		$post_id = $post_ids = isset($_GET['post_id']) && $_GET['post_id'] !== '' ? $_GET['post_id'] : null;
		$get_post = $collage->fetchPost(1, $post_id)[0];
		$get_statics = $collage->fetchStatic($post_id)[0];

		$option = $option_var = $opt_var = $class = $PTMPL['notification'] = '';
		$excess_['ap'] = $excess_['cp'] = 0;

		// Set category select options for new posts
		$category = $collage->dbProcessor("SELECT id, title, value FROM categories", 1);
		foreach ($category as $row) { 
			$sel = (isset($_POST['category']) && $_POST['category'] == $row['value']) || ($get_post['category'] == $row['value']) ? ' selected="selected"' : ''; 
			$option .= '<option value="'.$row['value'].'"'.$sel.'>'.$row['title'].'</option>';
		}
		$PTMPL['categories'] = $option;
		
		$PTMPL['return_btn'] = cleanUrls($SETT['url'].'/index.php?page=moderate');
		$delete_btn = '<button type="submit" name="delete" class="btn btn-danger my-4 btn-block"><i class="fa fa-trash"></i> Delete</a>';

		// Set parents select options for static content
		$parents = array('about' => 'About Page Section', 'contact' => 'Contact Page Section', 'static' => 'New static Page');
		foreach ($parents as $key => $row) { 
			$sel = (isset($_POST['parent']) && $_POST['parent'] == $key) || ($get_statics['parent'] == $key) ? ' selected="selected"' : ''; 
			$option_var .= '<option value="'.$key.'"'.$sel.'>'.$row.'</option>';
		}
		$PTMPL['static_parents'] = $option_var; 

		// Set priority select options for static content
		$parents = array(0, 1, 2, 3);
		foreach ($parents as $key => $row) { 
			$sel = (isset($_POST['priority']) && $_POST['priority'] == $row) || ($get_statics['priority'] == $row) ? ' selected="selected"' : ''; 
			$opt_var .= '<option value="'.$row.'"'.$sel.'>'.$row.'</option>';
		}
		$PTMPL['priority'] = $opt_var; 

		// Set icons for static content
		$set_icon = isset($_POST['icon']) ? $_POST['icon'] : $get_statics['icon'] ? $get_statics['icon'] : ''; 
		$PTMPL['icons'] = icon(1, $set_icon);

		if (isset($_GET['view'])) {
			if ($_GET['view'] == 'config') {
				$PTMPL['page_title'] = 'Site Configuration'; 

				// Set config option to update
				$sett = $alld = '';
				$allowed = $collage->dbProcessor("SELECT * FROM allowed_config", 1); 
				foreach ($configuration as $key => $config) { 
					if (isset($_POST['setting'])) {
						$sel = (isset($_POST['setting']) && $_POST['setting'] == $key) ? ' selected="selected"' : ''; 
					} else {
						$sel = (isset($_POST['allowed_setting']) && $_POST['allowed_setting'] == $key) ? ' selected="selected"' : ''; 
					}
					$marxTime->explode = '_';
					$title = ucwords($marxTime->reconstructString($key));

					if ($admin['level'] == 1) {
						$sett .= '<option value="'.$key.'"'.$sel.'>'.$title.'</option>';
						if (in_array($key, $marxTime->dekeyArray($allowed))) {
							$alld .= '<option value="'.$key.'" class="text-success"'.$sel.'>'.$title.'</option>';
						} else {
							$alld .= '<option value="'.$key.'"'.$sel.'"">'.$title.'</option>';
						}
					} else {
						if (in_array($key, $marxTime->dekeyArray($allowed))) {
							$sett .= '<option value="'.$key.'"'.$sel.'>'.$title.'</option>';
						} else {
							$sett .= '<option disabled>'.$title.'</option>';
						}
					}
				}

				$PTMPL['settings'] = $sett;
 
				// Set variables to show that this update is an image
				$so_update = $tr_update = $ads_update = $logo_update = $banner_update = $image_update = 0; 
				if (isset($_POST['setting'])) {
					str_ireplace('logo', '', $_POST['setting'], $logo_update); 
					str_ireplace('banner', '', $_POST['setting'], $banner_update); 
					str_ireplace('image', '', $_POST['setting'], $image_update); 
					str_ireplace('site_office', '', $_POST['setting'], $so_update);
					str_ireplace('tracking', '', $_POST['setting'], $tr_update);
					str_ireplace('ads_', '', $_POST['setting'], $ads_update);
				}

				$clear_image = '';
				if ($logo_update>0 || $banner_update>0 || $image_update>0) {
					$this_is_an_image = 1;
					$clear_image = 
					'<button class="btn btn-danger my-4 btn-block flex-grow-1" type="submit" name="clear_image">Clear Image</button>';
				} elseif ($so_update > 0 || $tr_update > 0 || $ads_update > 0) {
					$this_is_a_text_field = 1;
				}

				// Buttons to show or update the configuration
				$PTMPL['conf_btn'] = isset($_POST['view']) ? 
				$clear_image.
				'<button class="btn btn-dark-green my-4 btn-block flex-grow-1" type="submit" name="update">Update</button>' : 
				'<button class="btn btn-success my-4 btn-block flex-grow-1" type="submit" name="view">View Setting</button>';

				if ($admin['level'] == 1) { 

					// Set the configuration allowed for lower admin
					if (isset($_POST['show_btn'])) {
						$allow_btn = '
						<div class="col">
	                      	<button class="btn btn-success btn-md" type="submit" name="allow">Allow</button>
	                    </div>
	                    <div class="col">
	                      	<button class="btn btn-danger btn-md" type="submit" name="remove">Remove</button>
	                    </div>';
					} else {
						$allow_btn = '
						<div class="col">
	                      	<button class="btn btn-success btn-md" type="submit" name="show_btn">Show Actions</button>
	                    </div>';						
					}
					$allowed = '';
					$PTMPL['allowed_conf'] = '
					<form style="color: #757575;" method="post" action="">
						<label for="select">Set Allowed Configuration</label>
	                  	<div class="form-row mb-4 text-center">
	                    	<div class="col-md-5">
								<select class="browser-default custom-select mt-1" id="select" name="allowed_setting">
									<option disabled>Choose a setting</option>
									'.$alld.'
								</select>
							</div>
							'.$allow_btn.'
						</div>
					</form>';

					if (isset($_POST['allow']) || isset($_POST['remove'])) { 
						$value = $_POST['allowed_setting'];
						if ($value != '') {
							if (isset($_POST['allow'])) {
								$msg = ucwords($marxTime->reconstructString($value)).' has been allowed';
								$allowed = $collage->dbProcessor("INSERT INTO allowed_config (`name`) VALUES ('$value')", 0, $msg);
							} elseif (isset($_POST['remove'])) {
								$msg = ucwords($marxTime->reconstructString($value)).' has been removed';
								$allowed = $collage->dbProcessor("DELETE FROM allowed_config WHERE `name` = '$value'", 0, $msg);
							}
							$PTMPL['notification'] = (isset($allowed) ? messageNotice($allowed) : '');
						}
					}
				} else { 
					$PTMPL['notification'] = messageNotice('Some settings have been disabled, due to their sensitivity and risk of breaking the site!', 2, 7);
				}

				$PTMPL['conf_value'] = '  ';
				if (isset($_POST['view'])) {
					$PTMPL['conf_value'] = $configuration[$_POST['setting']]; 
				} elseif (isset($_POST['update']) || isset($_POST['clear_image'])) {
					// Save the new image
					if (isset($_POST['clear_image'])) { 
						$set_image = null;
					} elseif (isset($_FILES['image'])) {
						$image = $framework->imageUploader($_FILES['image'], 1);
						if (is_array($image)) {  
							deleteFile($configuration[$_POST['setting']], 2); 
							$set_image = $image[0];
						} else {
							if (isset($this_is_an_image) && isset($image)) {
								$errors = messageNotice($image);
							}
							if (isset($configuration[$_POST['setting']])) {
								$set_image = $configuration[$_POST['setting']];
							} else {
								$set_image = null;
							}
						}
					}

					if (isset($errors)) {
						$PTMPL['notification'] = $errors;
					} else {
						$PTMPL['conf_value'] = $value = isset($_POST['value']) ? $_POST['value'] : $set_image;
						if (isset($_POST['setting']) && $value != '' || !isset($set_image)) {
							$sql = sprintf("UPDATE configuration SET `%s` = '%s'", $_POST['setting'], $value);
							$set = $collage->dbProcessor($sql, 0, 1);
							$PTMPL['notification'] = $set == 1 ? messageNotice('Configuration Updated', 1) : messageNotice($set);
						}
					}
				}

				// Determine to show text field or upload form
				if (isset($this_is_an_image)) {
					$post_value = ucwords($marxTime->reconstructString($_POST['setting']));
					$PTMPL['input_field'] = '
					<label for="upload-col">Upload '.$post_value.' Image</label>
					<div class="input-group mb-4" id="upload-col">
						<div class="input-group-prepend">
							<span class="input-group-text">Choose '.$post_value.' File</span>
						</div>
						<div class="custom-file">
							<input type="file" class="custom-file-input" id="fileInput" aria-describedby="fileInput" name="image">
							<label class="custom-file-label" for="fileInput">File Name</label>
						</div>
					</div>';
				} elseif (isset($this_is_a_text_field)) {
					$PTMPL['input_field'] = '
					<div class="mb-4 mx-0">
						<label for="content_title">New Value</label>
						<textarea id="value" class="form-control" placeholder="New Value" name="value" row="3" required>'.$PTMPL['conf_value'].'</textarea>
						<div class="mt-0 invalid-feedback">
							Please provide a valid value.
						</div>
					</div>';
				} else {
					$PTMPL['input_field'] = '
					<div class="mb-4 mx-0">
						<label for="content_title">New Value</label>
						<input type="text" id="value" class="form-control" placeholder="New Value" name="value" value="'.$PTMPL['conf_value'].'" required>
						<div class="mt-0 invalid-feedback">
							Please provide a valid value.
						</div>
					</div>';
				} 

				$theme = new themer('moderate/config');

			} 
			elseif ($_GET['view'] == 'posts') {
				// Show the list of created posts

				$PTMPL['page_title'] = 'Manage posts';

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

			    $framework->all_rows = $collage->fetchPost(2);
			    $PTMPL['pagination'] = $framework->pagination(1);
				$list_posts = $collage->fetchPost(2);
				if ($list_posts) {
					$table_row = ''; $i=0;
					foreach ($list_posts as $post) {
						$i++;
	    				$page = $SETT['url'].$_SERVER['REQUEST_URI'];
						if (isset($_GET['delete'])) {
							$delete_link = cleanUrls(str_replace('&delete='.$_GET['delete'], '', $page).'&delete='.$post['id']);
						} else {
							$delete_link = cleanUrls($page.'&delete='.$post['id']);
						} 
						$edit_link = cleanUrls($SETT['url'].'/index.php?page=moderate&view=create_post&post_id='.$post['id']);
						$view_link = cleanUrls($SETT['url'].'/index.php?page=post&post_id='.$post['id']);
						$set_status = $post['public'] == 1 ? 'Public' : 'Private';
						$set_featured = $post['featured'] == 1 ? 'Yes' : 'Not Featured';
						$set_promo = $post['featured'] == 1 ? 'Yes' : 'No';
						$views = $collage->fetchStatistics(1, $post['id'])[0];
						$table_row .= '
						<tr>
							<th scope="row">'.$i.'</th>
							<td><a href="'.$view_link.'" title="View Content">'.$post['title'].'</a></td>
							<td>'.ucfirst($post['category']).'</td>
							<td>'.$set_status.'</td>
							<td>'.$set_featured.'</td>
							<td>'.$set_promo.'</td>
							<td>'.$views['total'].'</td>
							<td class="d-flex justify-content-around">
								<a href="'.$edit_link.'" title="Edit Content"><i class="fa fa-edit text-info hoverable"></i></a>
								<a href="'.$delete_link.'" title="Delete Content"><i class="fa fa-trash text-danger hoverable"></i></a> 
							</td>
						</tr>';
					}
					$PTMPL['posts_list'] = $table_row;
				}

				$theme = new themer('moderate/posts_content');
			} 
			elseif ($_GET['view'] == 'static') {
				// Show the list of static pages
				// 
				$collage->parent = 'about'; $collage->priority = '3';
				$ex_about =  $collage->fetchStatic(1);
				if ($ex_about && count($ex_about) > 1) {
					$excess_['ap'] = 1;
					$PTMPL['notification'] = messageNotice($LANG['excess_about_priority'], 3, 6);
				}
				$collage->parent = 'contact'; $collage->priority = '3';
				$ex_cont =  $collage->fetchStatic(1);
				if ($ex_cont && count($ex_cont) > 1) {
					$excess_['cp'] = 1;
					$PTMPL['notification'] .= messageNotice($LANG['excess_contact_priority'], 3, 6);
				}
				$collage->parent = $collage->priority = null;

				if (isset($_GET['delete'])) {
					$did = $collage->db_prepare_input($_GET['delete']);
					$delete = $collage->deleteContent($did, 1);
					if ($delete === 1) {
						$PTMPL['notification'] = messageNotice('Content has been deleted successfully', 1, 6);
					} elseif ($delete === 0) {
						$PTMPL['notification'] = messageNotice('Content does not exist, or may have already been deleted', 2, 6);
					} else {
						$PTMPL['notification'] = messageNotice($delete, 3, 7);
					}
				}

				$PTMPL['page_title'] = 'Manage static content';
			    $framework->all_rows = $collage->fetchStatic(null, 1);
			    $PTMPL['pagination'] = $framework->pagination(1); 
				$list_statics = $collage->fetchStatic(null, 1);

				if ($list_statics) {
					$table_row_static = ''; $i=0;
					foreach ($list_statics as $sta) {
						$i++;
	    				$page = $SETT['url'].$_SERVER['REQUEST_URI'];
						if (isset($_GET['delete'])) {
							$delete_link = cleanUrls(str_replace('&delete='.$_GET['delete'], '', $page).'&delete='.$sta['id']);
						} else {
							$delete_link = cleanUrls($page.'&delete='.$sta['id']);
						}
						$edit_link = cleanUrls($SETT['url'].'/index.php?page=moderate&view=create_static&post_id='.$sta['id']);
						$set_view = $sta['parent'] == 'about' || $sta['parent'] == 'contact' ? $sta['parent'] : $sta['safelink'];
						$view_link = cleanUrls($SETT['url'].'/index.php?page=static&view='.$set_view);

						// Highlight items exceeding normal usage
						foreach (array('ap' => 'about', 'cp' => 'contact') as $k => $r) {
							if ($excess_[$k] && ($sta['parent'] == $r && $sta['priority'] == 3)) {
								$class = ' class="text-danger font-weight-bold"';
							}
						}

						// Generate the table
						$table_row_static .= '
						<tr>
							<th scope="row">'.$i.'</th>
							<td><a href="'.$view_link.'" title="View Content"'.$class.'>'.$sta['title'].'</a></td>
							<td>'.$sta['parent'].'</td>
							<td class="'.$framework->mdbColors($sta['priority']).'">'.$sta['priority'].'</td>
							<td class="d-flex justify-content-around">
								<a href="'.$edit_link.'" title="Edit Content"><i class="fa fa-edit text-info hoverable"></i></a>
								<a href="'.$delete_link.'" title="Delete Content"><i class="fa fa-trash text-danger hoverable"></i></a> 
							</td>
						</tr>';
					}
					$PTMPL['static_list'] = $table_row_static;
				}

				$theme = new themer('moderate/static_content');
			} elseif ($_GET['view'] == 'create_static') {
				$PTMPL['up_btn'] = $get_statics ? 'Update Content' : 'Create Content';
				$PTMPL['page_title'] = $get_statics ? 'Update '.$get_statics['title'] : 'Create new Static Content';
				$PTMPL['content_title'] = isset($_POST['title']) ? $_POST['title'] : $get_statics['title']; 
				$PTMPL['main_content'] = isset($_POST['main_content']) ? $_POST['main_content'] : $get_statics['content']; 
				$PTMPL['footer_check'] = isset($_POST['footer']) || $get_statics['footer'] == 1 ? ' checked' : '';
				$PTMPL['header_check'] = isset($_POST['header']) || $get_statics['header'] == 1 ? ' checked' : ''; 

				if (isset($_POST['create_content'])) {  
					$collage->parent = $_POST['parent'];
					$collage->priority = $_POST['priority'];
					$collage->icon = $_POST['icon'];
					$collage->title = $_POST['title'];
					$collage->main_content = str_replace('\'', '', $_POST['main_content']);
					$collage->image = $_FILES['image'];
					$collage->footer = isset($_POST['footer']) ? 1 : 0;
					$collage->header = isset($_POST['header']) ? 1 : 0;

					$create = $collage->createStaticContent();
					$PTMPL['notification'] = $create;
				}

				$theme = new themer('moderate/create_static');
			} elseif ($_GET['view'] == 'categories') {
	    		$page = $SETT['url'].$_SERVER['REQUEST_URI'];
	    		$set_msg = isset($_GET['msg']) ? $_GET['msg'] : '';
	    		if (isset($_POST['select_category'])) {
		    		$page = str_replace('&set='.$_GET['set'], '', $page);
		    		$page = str_replace('&msg='.$set_msg, '', $page);
	    			$framework->redirect(cleanUrls($page).'&set='.$_POST['category'], 1);
	    		}

	    		$ctid = isset($_GET['set']) ? $_GET['set'] : null;
	    		$category = $collage->dbProcessor("SELECT id, title, value, info FROM categories WHERE `value` = '$ctid'", 1)[0];

				$PTMPL['page_title'] = $category ? 'Update '.$category['title'] : 'Create new Category';
				
				$PTMPL['up_btn'] = $category ? 'Update Category' : 'Create Category';
				$PTMPL['new_btn'] = cleanUrls($SETT['url'].'/index.php?page=moderate&view=categories');
	    		$PTMPL['delete_btn'] = $category ? $delete_btn : '';

				$PTMPL['ct_title'] = isset($_POST['title']) ? $_POST['title'] : $category['title']; 
				$PTMPL['ct_description'] = isset($_POST['info']) ? $_POST['info'] : $category['info'];  

				if (isset($_POST['delete'])) { 
					$PTMPL['notification'] = messageNotice($collage->dbProcessor("DELETE FROM categories WHERE `value` = '$ctid'", 0, 'Category Deleted'));
				}

				if (isset($_POST['create_'])) {  
					$info = $framework->db_prepare_input($_POST['info']); 
					$title = $framework->db_prepare_input($_POST['title']); 
					str_ireplace('event', '', $title, $cev_rep);
					str_ireplace('exhibition', '', $title, $cex_rep);
					if ($cev_rep > 0 || $cex_rep > 0) {
						$value = 'event';
					} else {
						$value = $framework->safeLinks($title);
					}
	 				
	 				if ($category) {
	 					$sql = "UPDATE categories SET `title` = '$title', `info` = '$info' WHERE `value` = '$ctid'";
	 					$msg = $category['title'].' has been updated';
	 				} else {
	 					$sql = "INSERT INTO categories (`title`, `value`, `info`) VALUES ('$title', '$value', '$info')";
	 					$msg = 'New category created';
	 				}

					$notification =  $collage->dbProcessor($sql, 0, 1);
					if ($notification == 1) {
						if ($category) {
							$up = 2;
						} else {
							$up = 1;
						}
					} else {
						$up = 0;
					}

		    		if ($category) {
		    			$page = str_replace('&set='.$_GET['set'], '', $page);
		    			$page = str_replace('&msg='.$set_msg, '', $page);
		    			$framework->redirect(cleanUrls($page).'&set='.$value.'&msg='.$up, 1);
		    		}
				}

				if (isset($notification)) {
					$PTMPL['notification'] = messageNotice($msg);
				} elseif (isset($_GET['msg'])) {
					if ($_GET['msg'] == 1) {
						$msg = messageNotice('New category created', 1);
					} elseif ($_GET['msg'] == 2) {
						$msg = messageNotice('Selected category has been updated', 1);
					} else {
						$msg = messageNotice('You have not made any changes');
					}
					$PTMPL['notification'] = $msg;
				}

				$theme = new themer('moderate/categories');
			} elseif ($_GET['view'] == 'admin') {
				$this_admin = isset($admin) ? ' ('.$admin['username'].')' : '';
				$PTMPL['page_title'] = 'Update Admin'.$this_admin; 
				
				$PTMPL['username'] = isset($_POST['username']) ? $_POST['username'] : $admin['username'];
				$PTMPL['password'] = isset($_POST['password']) ? $_POST['password'] : '';
				if ($admin['level'] == 1) {
					$checked = isset($_POST['new']) ? ' checked' : '';
					$PTMPL['action'] = '
					<div class="custom-control custom-checkbox mb-2 pr-5">
						<input type="checkbox" class="custom-control-input" id="checkbox_new" name="new"'.$checked.'>
						<label class="custom-control-label" for="checkbox_new">Create new admin user</label>
					</div>
					'; 
				}

				if (isset($_POST['update'])) { 
					$username = $_POST['username'];
					$password = hash('md5', $_POST['password']);
					$auth = $framework->generateToken(null, 1);
					$admin_id = $admin['id'];

	 				if ($admin && !isset($_POST['new'])) {
	 					$sql = "UPDATE admin SET `username` = '$username', `password` = '$password' WHERE `id` = '$admin_id'";
	 					$msg = messageNotice($username.' has been updated', 1);
	 				} else {
	 					$sql = "INSERT INTO admin (`username`, `password`, `auth_token`) VALUES ('$username', '$password', '$auth')";
	 					$msg = messageNotice('New admin user created', 1);
	 				}
 					if (isset($_POST['new']) && $framework->administrator(2, $username)) {
 						$msg = messageNotice('This Admin already exists!');
 					} else {
 						$msg = $msg;
 						$do = $framework->dbProcessor($sql, 0, 1);
 						if ($do == 1) {
 							$msg = $msg;
 						} else {
 							$msg = messageNotice($do);
 						}
 					}
					$PTMPL['notification'] = $msg;
				}

				// Set the active landing page_title 
				$theme = new themer('moderate/admin');
			} else {
				$PTMPL['up_btn'] = $get_post ? 'Update Post' : 'Create Post';
				$PTMPL['page_title'] = $get_post ? 'Update '.$get_post['title'] : 'Create new post';
				$PTMPL['post_title'] = isset($_POST['title']) ? $_POST['title'] : $get_post['title'];
				$PTMPL['post_sub_title'] = isset($_POST['sub_title']) ? $_POST['sub_title'] : $get_post['sub_title'];
				$PTMPL['post_details'] = isset($_POST['post_details']) ? $_POST['post_details'] : $get_post['details']; 
				$PTMPL['post_quote'] = isset($_POST['quote']) ? $_POST['quote'] : $get_post['quote'];
				$PTMPL['post_date'] = isset($_POST['date']) ? $_POST['date'] : $get_post['event_date'] ? date('Y-m-d', strtotime($get_post['event_date'])) : '';
				$PTMPL['post_time'] = isset($_POST['time']) ? $_POST['time'] : $get_post['event_date'] ? date('h:i', strtotime($get_post['event_date'])) : '';
				$PTMPL['public'] = isset($_POST['public']) || $get_post['public'] == 1 ? ' checked' : '';
				$PTMPL['featured'] = isset($_POST['featured']) || $get_post['featured'] == 1 ? ' checked' : '';
				$PTMPL['promote'] = isset($_POST['promote']) || $get_post['promoted'] == 1 ? ' checked' : '';

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
			$PTMPL['content'] = $theme->make();
		} else {
            $category =  array(
            	'create_post' 	=> 	'Create New blog post',
            	'posts' 		=> 	'View and manage post',
            	'create_static'	=>	'New static content',
            	'static'		=>	'Manage Static content',
            	'categories'	=>	'Manage categories',
            	'config'		=> 	'Site Configuration',
            	'admin'			=>	'Update Admin Details'
            ); 
            $categories = '';$i = 280;$ii = 3;
            foreach ($category as $key => $row) {
                $i++;$ii++; 
                $link = cleanUrls($SETT['url'].'/index.php?page=moderate&view='.$key);  
                $categories .= '
                <div class="col-md-4 mb-4">
                    <div class="col-1 col-md-2 float-left">
                        <i class="fa '.icon(3, $i).' fa-2x '.$framework->mdbcolors($ii).'"></i> 
                    </div> 
                    <div class="col-10 col-md-9 col-lg-10 px-3 float-right"> 
                        <a href="'.$link.'" class="btn '.$framework->mdbcolors($ii, 1).' btn-sm ml-0 p-4 px-0">'.$row.'</a>
                    </div>
                </div>'; 
            }
            $PTMPL['content'] = '            
            <div class="row text-left"> 
              	'.$categories.'
            </div>'; 
		}
	 
		$PTMPL['side_bar'] = moderate_sidebar();

		// Set the active landing page_title 
		$theme = new themer('moderate/container');
	} else {
		if (isset($_GET['view']) && $_GET['view'] == 'access') {
			$PTMPL['page_title'] = 'Admin Login';
			$PTMPL['return_btn'] = cleanUrls($SETT['url'].'/index.php?page=homepage');

			if (isset($_POST['login'])) {
				$PTMPL['username'] = $username = $framework->db_prepare_input($_POST['username']);
				$PTMPL['password'] = $password = $framework->db_prepare_input($_POST['password']);

				if (isset($_POST['remember']) && $_POST['remember'] == 'on') {
					$PTMPL['remember'] = ' checked';
					$framework->remember = 1;
				}
				$framework->username = $username;
				$framework->password = hash('md5', $password);
				if (isset($_POST['user_login'])) {
					$PTMPL['user_login'] = ' checked';
					$login = $framework->authenticateUser();
				} else {
					$login = $framework->administrator(1);
				}
				if (isset($login['username']) && $login['username'] == $username) {
					$notice = messageNotice('Login Successful', 1);
					$framework->redirect(cleanUrls('moderate'));
				} else {
					$notice = messageNotice($login, 3);
				}
				$PTMPL['notification'] = $notice; 
			}
			$theme = new themer('moderate/admin_login');
			$PTMPL['content'] = $theme->make();
		} else {
        	$PTMPL['page_title'] = 'Error 403';
			$PTMPL['content'] = notAvailable('', '', 403);
		}
		// Set the active landing page_title 
		$theme = new themer('homepage/fullpage');
	}
		// $data = 'deserunt {$texp->davidson} in';
		// echo $t = $framework->auto_template($data, 1);
	return $theme->make();
}
?>
