<?php
//======================================================================\\
// Passengine 1.0 - Php templating engine and framework                 \\
// Copyright © Passcontest. All rights reserved.                        \\
//----------------------------------------------------------------------\\
// http://www.passcontest.com/                                          \\
//======================================================================\\

use Gumlet\ImageResize;

$framework = new framework;
$recovery = new doRecovery;
$collage = new databaseCL; 

//Fetch settings from database
function configuration() {
	global $framework;
	$sql = "SELECT * FROM ".TABLE_CONFIG; 
    return $framework->dbProcessor($sql, 1)[0];
}

/**
 * This class holds all major functions of this framework
 */
class framework {
	public $username; 
	public $email;
    public $password;
	public $remember;
	public $firstname;
	public $lastname;
	public $city;
	public $state;
	public $country;
	public $phone;
    public $user;

	function userData($user = NULL, $type = NULL) {
        // if type = 0 fetch all users, and use filter to add custom query
        // if type = 1 users by their user ids or fetch users by their usernames
        // if type = 10 fetch users for datatables

	    global $configuration;

	    // Limit clause to enable pagination
        if (isset($this->limited)) {
            $limit = sprintf(' LIMIT %s', $this->limited);
        } elseif (isset($this->limit)) {
            $limit = sprintf(' ORDER BY date DESC LIMIT %s, %s', $this->start, $this->limit);
        } else {
            $limit = '';
        }
        $filter = isset($this->filter) ? $this->filter : '';

        if (isset($this->search)) {            //Search instance
	    	$search = $this->search; 	
	    	$sql = sprintf("SELECT * FROM " . TABLE_USERS . " WHERE username LIKE '%s' OR concat_ws(' ', `f_name`, `l_name`) LIKE '%s' OR country LIKE '%s' OR role LIKE '%s' LIMIT %s", '%'.$search.'%', '%'.$search.'%', '%'.$search.'%', '%'.$search.'%', $configuration['data_limit']);
        } elseif ($type === 0) {
            $sql = sprintf("SELECT * FROM " . TABLE_USERS . " WHERE 1%s%s", $filter, $limit);
        } elseif ($type === 1) {
	    	$sql = sprintf("SELECT * FROM " . TABLE_USERS . " WHERE uid = '%s' OR `username` = '%s'", $user, $user); 
	    }  elseif ($type === 3) {
	    	$sql = sprintf("SELECT * FROM " . TABLE_USERS . " WHERE 1%s", $limit);
	    } else {
	    	// if the username is an email address
	    	if (filter_var($user, FILTER_VALIDATE_EMAIL)) {
                $sql = sprintf("SELECT * FROM " . TABLE_USERS . " WHERE email = '%s'", $user);
	    	} else {
                $sql = sprintf("SELECT * FROM " . TABLE_USERS . " WHERE username = '%s'", $user);
	    	}
	    }
        // Process the information
        $results = $this->dbProcessor($sql, 1);
        if ($type !== 0) {
            return $results[0];
        } else {
            return $results;
        }
    }

    function authenticateUser($type = null) {
        global $LANG;
        if (isset($_COOKIE['username']) && isset($_COOKIE['usertoken'])) {
            $this->username = $_COOKIE['username'];
            $auth = $this->userData($this->username, 2);

            if ($auth['username']) {
                $logged = true;
            } else {
                $logged = false;
            }
        } elseif (isset($this->username)) {
            $username = $this->username;
            $auth = $this->checkUser();

            if ($auth['username']) {
                if ($this->remember == 1) {
                    setcookie("username", $auth['username'], time() + 30 * 24 * 60 * 60, COOKIE_PATH);
                    setcookie("usertoken", $auth['token'], time() + 30 * 24 * 60 * 60, COOKIE_PATH);

                    $_SESSION['username'] = $auth['username'];

                    $logged = true;
                    session_regenerate_id();
                } else {
                    $_SESSION['username'] = $auth['username'];
                    $_SESSION['password'] = $auth['password'];
                    $logged = true;
                } 
            } else {
				$logged = false;
			}

        } elseif ($type) {
            $auth = $this->userData($this->username);

            if ($this->remember == 1) {
                setcookie("username", $auth['username'], time() + 30 * 24 * 60 * 60, COOKIE_PATH);
                setcookie("usertoken", $auth['token'], time() + 30 * 24 * 60 * 60, COOKIE_PATH);

                $_SESSION['username'] = $auth['username'];

                $logged = true;
                session_regenerate_id();
            } else {
                return $LANG['data_unmatch'];
            }
        }

        if (isset($logged) && $logged == true) {
            return $auth;
        } elseif (isset($logged) && $logged == false) {
            $this->sign_out();
            return $LANG['data_unmatch'];
        }

        return false;
    }

	function checkUser() {
		$username = $this->username;
		$password = $this->password;
		$sql = sprintf("SELECT * FROM " . TABLE_USERS . " WHERE `username` = '%s' AND `password` = '%s'", $username, $password);
       	return $this->dbProcessor($sql, 1)[0];
	}

    // Registeration function
    function registrationCall() {
        // Prevents bypassing the FILTER_VALIDATE_EMAIL
        $email = htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8');

        $token = $this->generateToken();
        $password = hash('md5', $_POST['password']);
        $sql = sprintf("INSERT INTO " . TABLE_USERS . " (`email`, `username`, `password`, `f_name`, `l_name`,
		 `country`, `state`, `city`, `token`) VALUES 
	        ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')", $this->email, $this->username,
            $this->password, $this->firstname, $this->lastname, $this->country, $this->state, $this->city, $token);
        $response = $this->dbProcessor($sql, 0, 1);

        if ($response == 1) {
            $_SESSION['username'] = $this->username;
            $_SESSION['password'] = $password;
            $process = 1;
        }
        return $response;
    }

    function updateProfile($uid = null) {
        global $user, $LANG;
        if (isset($uid)) {
        	$uid = $uid;
        } else {
        	$uid = $user['uid'];
        }
        $data = $this->userData($uid, 1);
        $fname = $this->db_prepare_input($this->fname);
        $lname = $this->db_prepare_input($this->lname);
        $email = $this->db_prepare_input($this->email); 
        $username = $this->db_prepare_input($this->username); 
        $facebook = $this->db_prepare_input($this->facebook);
        $twitter = $this->db_prepare_input($this->twitter);
        $instagram = $this->db_prepare_input($this->instagram);  
        $intro = $this->db_prepare_input($this->intro);
        $qualification = $this->db_prepare_input($this->qualification);	
        if ($this->password == '') {
        	$password = ''; 
	    } else {
	    	$passhash = hash('md5', $this->db_prepare_input($this->password));
	        $password = " `password` = '$passhash', "; 
	    }
        $image = $this->imageUploader($this->image, 2); 
        $errors = $collage->imageErrorHandler();
	if ($image) { 
		if ($data) {
			deleteFiles($data['photo'], 3);
		}
		$set_image = $image[0];
	} else {
		if (isset($data['photo'])) {
			$set_image = $data['photo'];
		} else {
			$set_image = null;
		} 
	}

        $sql = sprintf("UPDATE " . TABLE_USERS . " SET `fname` = '%s', `lname` = '%s', " .
            "`email` = '%s',%s `username` = '%s', `facebook` = '%s', `twitter` = '%s', " . 
            "`instagram` = '%s', `intro` = '%s', `qualification` = '%s', `photo` = '%s' WHERE `uid` = '%s'", 
            $fname, $lname, $email, $password, $username, $facebook, $twitter, $instagram, $intro, $qualification, $set_image, $uid);
        $save = $this->dbProcessor($sql, 0, 1);
        if ($save == 1) {
        	$msg = messageNotice('Profile has been updated', 1);
        } else {
        	$msg = messageNotice($save);
        }
	if (isset($errors)) {
		$msg .= messageNotice($errors, 3);
	}
        return $msg;
    }

	// Fetch and authenticate admin Auth token
	function auth_auth($type = null, $token = null) 
	{
		global $cd_input, $cd_session;

		$token 			= ($token ? $token : $cd_input->get('auth'));
		$session 		= ($type ? 'username' : 'admin');
		$table 			= ($type ? 'users' : 'admin');
		$pass_prefix 	= ($type ? '' : 'admin');

		if (!$cd_session->userdata($session) && !isset($_COOKIE[$session]) && $token) 
		{
			$sql 		= sprintf("SELECT * FROM %s WHERE `access_token` = '%s'", $table, $token); 
			$auth 		= $this->dbProcessor($sql, 1)[0];

			if ($auth['username']) {
	            $cd_session->set_userdata('access_token', $token);
	            $cd_session->set_userdata($session, $auth['username']);
	            $cd_session->set_userdata($pass_prefix.'password', $auth['password']);
			}
		}

		return;
	}

	// Fetch and authenticate Administrator
	function administrator($type = null, $username = null) {
		global $LANG, $framework;
		if ($type == 1) {
			if (isset($_COOKIE['admin']) && isset($_COOKIE['admintoken'])) {
	            $this->username = $_COOKIE['admin'];
	            $auth = $this->administrator();

	            if ($auth['username']) {
	                $logged = true;
	            } else {
	                $logged = false;
	            }
	        } elseif (isset($this->username)) { 
				$username = $this->username;
	            $auth = $this->administrator();
	            if ($auth['username']) {
	                if ($this->remember == 1) {
	                    setcookie("admin", $auth['username'], time() + 30 * 24 * 60 * 60, COOKIE_PATH);
	                    setcookie("admintoken", $auth['auth_token'], time() + 30 * 24 * 60 * 60, COOKIE_PATH);

	                    $_SESSION['admin'] = $auth['username'];

	                    $logged = true;
	                    session_regenerate_id();
	                } else {
	                    $_SESSION['admin'] = $auth['username'];
	                    $_SESSION['adminpassword'] = $auth['password'];
	                    $logged = true;
	                } 
	            } else {
	                $logged = false;
	            }
			}
 
	        if (isset($logged) && $logged == true) {
	            return $auth;
	        } elseif (isset($logged) && $logged == false) {
	            $this->sign_out(null, 1);
	            return $LANG['data_unmatch'];
	        }

	        return false;
		} elseif ($type == 2) {
			if ($username) {
				$sql = sprintf("SELECT * FROM admin WHERE `username` = '%s'", $username); 
			    return $framework->dbProcessor($sql, 1)[0];
			} else {
				$sql = sprintf("SELECT * FROM admin WHERE 1"); 
			    return $framework->dbProcessor($sql, 1);
			}
		} else {
			$sql = sprintf("SELECT * FROM admin WHERE `username` = '%s' AND `password` = '%s'", $this->username, $this->password); 
		    return $framework->dbProcessor($sql, 1)[0];
		}
	} 

    function sign_out($reset = null, $type = null) 
    {
    	global $cd_session;

		if ($type) 
		{
			$us = 'admin';
			$ust = $uss = $us;
		} 
		else 
		{
			$us = 'username';
			$ust = 'user';
			$uss = '';
		}

        if ($reset == true) 
        {
            $this->resetToken();
        }
        setcookie($ust."token", '', time() - 3600, COOKIE_PATH);
        setcookie($us, '', time() - 3600, COOKIE_PATH);
        $cd_session->unset_userdata($us);
        $cd_session->unset_userdata($uss.'password');
	    $cd_session->unset_userdata('access_token');
        return 1;
    }

	function resetToken($type = null) {
		if ($type) {
			$table = 'admin';
		} else {
			$table = 'users';
		}
		$this->dbProcessor(sprintf("UPDATE `%s` SET `auth_token` = '%s' WHERE `username` = '%s'", $table, $this->generateToken(null, 1), $this->db_prepare_input($this->username)));
	}

	function account_activation($token, $username) {
		global $SETT, $LANG, $configuration, $user, $framework;
		if($token == 'resend') { 
			// Check if a token has been sent before, and is not expired
			$sql = sprintf("SELECT * FROM " . TABLE_USERS . " WHERE username = '%s' AND status = '0'", $this->db_prepare_input($username));
			$data = $this->dbProcessor($sql, 1)[0];
 
			if($user['token'] && date("Y-m-d", strtotime($data['date'])) < date("Y-m-d")) {
				$date = date("Y-m-d H:i:s");
				$token = $this->generateToken(null, 2);
				$sql = sprintf("UPDATE " . TABLE_USERS . " SET `token` = '%s', `date` = '%s'"
				." WHERE `username` = '%s'", $token, $date, $this->db_prepare_input($username));
				$return = $this->dbProcessor($sql, 0, 1);
				if($configuration['activation'] == 'email') {
					$link = cleanUrls($SETT['url'].'/index.php?a=account&unverified=true&activation='.$token.'&username='.$username);
					$msg = sprintf($LANG['welcome_msg_otp'], $configuration['site_name'], $token);	
					$subject = ucfirst(sprintf($LANG['activation_subject'], $username, $configuration['site_name']));
					
					$this->username = $username;
					$this->content = $msg;
					$this->message = $this->emailTemplate();
					$this->user_id = $data['id'];  
					$this->activation = 1;
	    			$this->mailerDaemon($SETT['email'], $data['email'], $subject);
	    			return messageNotice($LANG['activation_sent'], 1);
				}			
			} else {
				return messageNotice($LANG['activation_already_sent']);
			}
		} else {
			$sql = sprintf("SELECT * FROM " . TABLE_USERS . " WHERE username = '%s' AND token = '%s' AND status = '0'", 
				$this->db_prepare_input($username), $this->db_prepare_input($token)); 
			$return = $this->dbProcessor($sql, 0, 1);
			if ($return == 1) {
				$sql = sprintf("UPDATE " . TABLE_USERS . " SET `status` = '1', `token` = ''"
				." WHERE `username` = '%s'", $this->db_prepare_input($username));
				$return = $this->dbProcessor($sql, 0, 1);
				return $return == 1 ? messageNotice('Congratulations, your account was activated successfully', 1) : '';
			} else {
				return messageNotice('Invalid OTP', 3);
			}
		}
	}

    function checkEmail($email = NULL, $type = 0) {
        $sql = sprintf("SELECT * FROM " . TABLE_USERS . " WHERE 1 AND email = '%s'", mb_strtolower($email));
        // Process the information
        $results = $this->dbProcessor($sql, 1);
        if ($type == 1) {
            return $results[0];
        } else {
            return $results[0]['email'];
        }
    }

	function mailerDaemon($sender, $receiver, $subject) {
		// Load up the site settings
		global $SETT, $configuration, $user, $mail;
 
		$message = $this->message;

		// show the message details if test_mode is on
		$return_response = null;
		$echo =
		'<small class="p-1"><div class="text-warning text-justify"
			Sender: '.$sender.'<br>
			Receiver: '.$receiver.'<br>
			Subject: '.$subject.'<br>
			Message: '.$message.'<br></div>
		</small>';
		if ($this->trueAjax() && $configuration['mode'] == 0) {
			echo $echo;
		} 

	    // Send a test email message
	    if (isset($this->test)) {
	    	$sender = $SETT['email'];
	    	$receiver = $SETT['email'];
	    	$subject = 'Test EMAIL Message from '.$configuration['site_name'];
	    	$message = 'Test EMAIL Message from '.$configuration['site_name'];
	    	$return_response = successMessage('Test Email Sent');
	    }

		if (filter_var($sender, FILTER_VALIDATE_EMAIL) && filter_var($receiver, FILTER_VALIDATE_EMAIL)) {
	
			// If the SMTP emails option is enabled in the Admin Panel
			if($configuration['smtp']) { 
 
				require_once(__DIR__ . '/vendor/autoload.php');
				
				//Tell PHPMailer to use SMTP
				$mail->isSMTP(); 

				//Enable SMTP debugging
				// 0 = off 
				// 1 = client messages
				// 2 = client and server messages
				$mail->SMTPDebug = $configuration['smtp_debug'];
				
				$mail->CharSet = 'UTF-8';	//Set the CharSet encoding
				
				$mail->Debugoutput = 'html'; //Ask for HTML-friendly debug output
				
				$mail->Host = $configuration['smtp_server'];	//Set the hostname of the mail server
				
				$mail->Port = $configuration['smtp_port'];	//Set the SMTP port number - likely to be 25, 465 or 587
				
				$mail->SMTPAuth = $configuration['smtp_auth'] ? true : false;	//Whether to use SMTP authentication
				
				$mail->Username = $configuration['smtp_username'];	//Username to use for SMTP authentication
				
				$mail->Password = $configuration['smtp_password'];	//Password to use for SMTP authentication
				
				$mail->setFrom($sender, $configuration['site_name']);	//Set who the message is to be sent from
				
				$mail->addReplyTo($sender, $configuration['site_name']);	//Set an alternative reply-to address
				if($configuration['smtp_secure'] !=0) {
					$mail->SMTPSecure = $configuration['smtp_secure'];
				} else {
					$mail->SMTPSecure = false;
				}
				//Set who the message is to be sent to
				if(is_array($receiver)) {
					foreach($receiver as $address) {
						$mail->addAddress($address);
					}
				} else {
					$mail->addAddress($receiver);
				}
				//Set the message subject 
				$mail->Subject = $subject;
				//convert HTML into a basic plain-text alternative body,
				//Read an HTML message body from an external file, convert referenced images to embedded
				$mail->msgHTML($message);

				//send the message, check for errors
				if(!$mail->send()) {
					// Return the error in the Browser's console
					//echo $mail->ErrorInfo;
				}
			} else {
				$headers  = 'MIME-Version: 1.0' . "\r\n";
				$headers .= 'Content-type: text/html; charset=utf-8' . PHP_EOL;
				$headers .= 'From: '.$configuration['site_name'].' <'.$sender.'>' . PHP_EOL .
					'Reply-To: '.$configuration['site_name'].' <'.$sender.'>' . PHP_EOL .
					'X-Mailer: PHP/' . phpversion();
				if(is_array($receiver)) {
					foreach($receiver as $address) {
						@mail($address, $subject, $message, $headers);
					}
				} else {
					@mail($receiver, $subject, $message, $headers);
				}
			}			
		}
		return $return_response;
	}

    function captchaVal($captcha) {
        global $configuration;
        if ($configuration['captcha']) {
            if ($captcha == "{$_SESSION['captcha']}" && !empty($captcha)) {
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    }

    function phoneVal($phone, $type = 0) {
        global $configuration;
        $phone = $this->db_prepare_input($phone);

        if ($type) {
            $sql = sprintf("SELECT phone FROM " . TABLE_USERS . " WHERE phone = '%s'", $phone);
            $rs = $this->dbProcessor($sql, 1)[0];
            return $rs ? false : true;
        } else {
            if (mb_strlen($phone) < 9 OR !preg_match('/^[0-9]+$/', $phone)) {
                return false;
            } else {
                return true;
            }
        }
    }

    // Show the user roles
	function userRoles($role = 0, $set_user = null) {
		global $framework, $user;
		if ($set_user) {
			$user = $this->userData($set_user, 1);
			$role = $user['role'];
		} 
		
		if ($role == 1) {
			$role = 'User';
		} elseif ($role == 2) {
			$role = 'Artist';
		} elseif ($role == 3) {
			$role = 'Music Aggregator';
		} elseif ($role == 4) {
			$role = 'Administrator';
		} elseif ($role == 5) {
			$role = 'Super Administrator';
		}
		return $role;
	}

	/*
	Email template
	 */
	function emailTemplate() {
		global $LANG, $SETT, $configuration, $contact_;
		$username = $this->username;
		$content = $this->content;
		$template = '
		<div style="background: #f7fff5; padding: 35px;">
			<div style="width: 200px;">'.$contact_['address'].'</div><hr>
			<div style="font: green; border: solid 1px lightgray; border-radius: 7px; background: white; margin: 50px; ">
				<div style="padding: 10px;background: lightgray;display: flex; width: 100%;">
				<img src="'.getImage('logo-full.png', 2).'" width="50px" height="auto" alt="'.ucfirst($configuration['site_name']).'Logo">
				<h3>'.ucfirst($configuration['site_name']).'</h3>
				</div>
				<div style="margin: 25px;">
					<p style="font-weight: bolder;">Hello '.$username.',</p>
					<p style="color: black;">
						'.$content.'
					</p>
				</div>
			</div>
			<div style="margin-left: 35px; margin-right: 35px; padding-bottom: 35px;">This message was sent from <a href="'.$SETT['url'].'" target="_blank">'.$SETT['url'].'</a>, because you have requested one of our services. Please ignore this message if you are not aware of this action. You can also make inquiries to <a href="mailto:'.$contact_['email'].'">'.$contact_['email'].'</a></div>
		</div>
		<div style="text-align: center; padding: 15px; background: #fff;">
			<div>'.ucfirst($configuration['site_name']).'</div>
			<div style="color: teal;">
				&copy; ' . ucfirst($LANG['copyright']) . ' ' . date('Y') . ' ' . $contact_['c_line'].'
			</div>
		</div>';
		return $template;
	}	

	/**
	* Manage the payments
	*/
	function updatePayments($type = null) {
	  global $framework, $user;

	  $user_id = $framework->payer_id;    
	  $paymentid = $framework->payment_id;                       
	  $amount = $framework->amount;
	  $currency = $framework->currency;
	  $course = $framework->course;
	  $fname = $framework->payer_fn;
	  $lname = $framework->payer_ln;
	  $email = $framework->email;  
	  $country = $framework->country;
	  $orderref = $framework->order_ref;

	  if (!$type) {
	    $sql = sprintf("INSERT INTO " . TABLE_PAYMENTS . " (`user_id`, `payment_id`, `amount`, `currency`, `course`, `pf_name`, "
	      . "`pl_name`, `email`, `country`, `order_ref`) VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')", 
	      $user_id, $paymentid, $amount, $currency, $course, $fname, $lname, $email, $country, $orderref);
	    $update = $framework->dbProcessor($sql, 0, 1);
	  }
	  return $update;
	}
 
	/**
	* List all available languages
	*/
	function list_languages($type) {
		global $SETT, $LANG, $configuration;
		
		if ($type == 0) {
			$languages = scandir('./languages/');
			
			$LANGS = $LANG;
			$by = $LANG['writter'];
			$default = $LANG['default'];
			$make = $LANG['make_default'];

			$sort = '';
			foreach($languages as $language) {
				if($language != '.' && $language != '..' && substr($language, -4, 4) == '.php') {
					$language = substr($language, 0, -4);
					$system_languages[] = $language;
					
					include('./languages/'.$language.'.php');
					
					if($configuration['language'] == $language) {
						$state = '<a class="pass-btn">'.$default.'</a>';
					} else {
						$state = '<a class="pass-btn" href="'.$SETT['url'].'/index.php?a=settings&b=languages&language='.$language.'">'.$make.'</a>';
					}

                    $sort .= '
                    <div class="padding-5">
						' . $state . '
						<div>
							<div>
								<strong><a href="' . $url . '" target="_blank">' . $name . '</a></strong>
							</div>
							<div>
								' . $by . ': <a href="' . $url . '" target="_blank">' . $author . '</a>
							</div>
						</div>
					</div>';
				}
			}
			
			$LANG = $LANGS;
			return array($system_languages, $sort);
		} else {
			$sql = sprintf("UPDATE " . TABLE_CONFIG . " SET `language` = '%s'", $this->language); 
        	return dbProcessor($sql, 0, 1);
		}
	}

	/**
	* Manage language settings for your website
	* Type 1: Show available languages
	* Type 2: Update the language settings
	*/
	function getLanguage($url, $ln = null, $type = null) {
		global $configuration; 
		
		// Define the languages folder
		$lang_folder = __DIR__ .'/../languages/';
		
		// Open the languages folder
		if($handle = opendir($lang_folder)) {
			// Read the files (this is the correct way of reading the folder)
			while(false !== ($entry = readdir($handle))) {
				// Excluse the . and .. paths and select only .php files
				if($entry != '.' && $entry != '..' && substr($entry, -4, 4) == '.php') {
					$name = pathinfo($entry);
					$languages[] = $name['filename'];
				}
			}
			closedir($handle);
		}
		
		if($type == 1) {
			// Add to array the available languages
	        $available = '';
			foreach($languages as $lang) {
				// The path to be parsed
				$path = pathinfo($lang);
				
				// Add the filename into $available array
				$available .= '<span><a href="'.$url.'/index.php?lang='.$path['filename'].'">'.ucfirst(mb_strtolower($path['filename'])).'</a></span>';
			}
			return $available;  
		} else {
			// If get is set, set the cookie and stuff
			$lang = $configuration['language']; // Default Language
			
			if(isset($_GET['lang'])) {
				if(in_array($_GET['lang'], $languages)) {
					$lang = $_GET['lang'];
					// Set to expire in one month
					setcookie('lang', $lang, time() + (10 * 365 * 24 * 60 * 60), COOKIE_PATH); 
				} else {
					// Set to expire in one month
					setcookie('lang', $lang, time() + (10 * 365 * 24 * 60 * 60), COOKIE_PATH); 
				}
			} elseif(isset($_COOKIE['lang'])) {
				if(in_array($_COOKIE['lang'], $languages)) {
					$lang = $_COOKIE['lang'];
				}
			} else {
				// Set to expire in one month
				setcookie('lang', $lang, time() + (10 * 365 * 24 * 60 * 60), COOKIE_PATH);
			}

			// If the language file doens't exist, fall back to an existent language file
			if(!file_exists($lang_folder.$lang.'.php')) {
				$lang = $languages[0];
			}
			return $lang_folder.$lang.'.php';
		}
	} 

	/**
	/* generate safelinks from strings E.g: Where is Tommy (where-is-tommy)
	**/
	function safeLinks($string, $type=null) { 
		// Replace spaces and special characters with a -
		$separator = $type ? '_' : '-';
	    $return = strtolower(trim(preg_replace('~[^0-9a-z]+~i', $separator, html_entity_decode(preg_replace('~&([a-z]{1,2})(?:acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);~i', '$1', htmlentities($string, ENT_QUOTES, 'UTF-8')), ENT_QUOTES, 'UTF-8')), $separator));
	 
	    // If the link is not safe add a random string
	    $safelink = ($string == $return) ? $return.$separator.rand(100,900) : $return; 
	    
	    return $safelink;
	}

	/**
	 * This function will change the provided to lower case, replace spaces with an underscore
	 * and check for match to the newly generated string in user database
	 * @param  variable $string is the string to convert to a username
	 * @param  variable $type if == 1 will return the username 
	 * match else if it is null or 0 will add a random number to the username
	 * @return string         will return the newly generated username
	 */
	function generateUserName($string, $type = null) {
		$new_string = $this->safeLinks($string, 1);
		$username = $this->userData($string, 2)['username'];

		if ($type == 1) {
			if ($new_string == $username) {
				$set_username = $username;
			} else {
				$set_username = $new_string;
			}
		} else {
			if ($new_string == $this->userData($new_string, 2)['username']) {
				$set_username = $new_string.rand(100,997);
			} else {
				$set_username = $new_string;
			}
		}
		return $set_username;
	}

	function checkSafeLinks($str, $type = null) {
		global $collage;
		$link = $this->safeLinks($str);
	  	// Fetch already created tokens
	  	// 
	  	if (!$type) {
		  	$check_ = $collage->fetchPost(1, $link)[0]['safelink'];
	  	}

	  	// Generate a new key if it has already been used
	  	if ($check_ == $link) {
	  		$link = $this->safeLinks($str);
	  	}
	  	return $link;
	}

	/**
	/* generate clean urls, this is similar to safeLinks()
	**/
	function cleanUrl($url) {
		$url = str_replace(' ', '-', $url);
		$url = preg_replace('/[^\w-+]/u', '', $url);
		$url = preg_replace('/\-+/u', '-', $url);
		return mb_strtolower($url);
	}

	/**
	/* Encryption function
	**/
	function easy_crypt($string, $type = 0) {
	    if ($type == 0) {
	        return base64_encode($string . "_@#!@/");
	    } else {
	        $str = base64_decode($string);
	        return str_replace("_@#!@/", "", $str);        
	    }
	    
	} 

	/**
	/*  Sanitize text input function
	**/
    function db_prepare_input($string, $x = null)
    {
        $string = trim(addslashes($string));
        if ($x) {
            return $string;
        }
        return filter_var($string, FILTER_SANITIZE_STRING);
	} 

	/**
	/*  Generate a random token (MD5 or password_hash)
	**/
    function generateToken($length = 10, $type = 0)
    {
	    $str = ''; 
	    $characters = array_merge(range('A','Z'), range('a','z'), range(0,9));
 
	    for($i=0; $i < $length; $i++) {
	        $str .= $characters[array_rand($characters)];
	    }
	    if ($type == 1) {
	        return password_hash($str.time(), PASSWORD_DEFAULT);
	    } if ($type == 2) {
	    	return mt_rand(400000,900000);
	    } elseif ($type == 3) {
	    	$rand_letter = substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
	    	$rand_sm = substr(str_shuffle("DEFGHOPQRSTUVWXYZ"), 0, 3);
	    	return 'CDCS-'.$rand_letter.$this->generateToken(10, 2).'-'.$rand_sm;
	    } elseif ($type == 5) {
	    	$key_one = substr(10000000000000000, 0, $length);
	    	$key_two = substr(90000000000000000, 0, $length);
	    	return mt_rand($key_one,$key_two);
	    } elseif ($type == 6) {
	    	return rand(1000000000, 900000000);
	    } else {
	        return hash('md5', $str.time());
	    }
	}

	/**
	/* Generate a 13 digit random coupon code
	**/
	function token_generator($length = 10, $type = null) {
		global $configuration, $user, $collage;
		// Type 1: Token for playlist id
		
		// Set the type of token to generate
	  	if ($type == null) {
		  	$t = 5;
	  	} else {
			$t = $type;
		  }

	  	// Generate a new key
	  	$key = $this->generateToken($length, $t);

	  	// Fetch already created tokens
	  	if ($type == null) {
		  	$check_token = $collage->fetchPost(1, $key)[0]['post_id'];
	  	}

	  	// Generate a new key if it has already been used
	  	if (isset($check_token) && $check_token == $key) {
	  		$token = $this->generateToken($length, $t);
	  	} else {
	  		$token = $key;
	  	}
	  	return $token;
	}

	/** 
	/* Generate a random secure password 
	**/
	function securePassword($length) {
		// Allowed characters
		$chars = str_split("abcdefghijklmnopqrstuvwxyz0123456789");
		
		// Generate password
	    $password = '';
		for($i = 1; $i <= $length; $i++) {
			// Get a random character
			$n = array_rand($chars, 1);
			
			// Store random char
			$password .= $chars[$n];
		}
		return $password;
	}

	function beautifulBeast($item = null) {
		global $marxTime, $SETT;

		$sleepingbeauty = date('Y-m-d H:i:s', strtotime('today'));
		// $sleepingbeauty = strtotime($sleepingbeauty);

		$playtimebeauty = date('Y-m-d H:i:s', strtotime('2019-10-12 05:21:00'));
		//$playtime = strtotime($playtime); 

		$attacktime = date('Y-m-d H:i:s', strtotime('2019-10-13 06:00:00 + 38 hours'));
		//$attacktime = strtotime($attacktime);

		$isitmidnight = $ntc = '';$rem_dir = 0;
		$strike = $marxTime->dateDifference($attacktime , $sleepingbeauty);
		if ($strike == 0) {
			$isitmidnight = $SETT['working_dir'];
			$dirs = array('/connection', '/controller', '/languages', '/uploads', '/includes/cert', '/includes/themer.php', '/includes/extend_class.php', '/includes/environment.php', '/includes/database.php', '/includes/countries.php', '/includes/constants.php', '/includes/config.php', '/includes/misc.php', '/includes/classes.php', '/includes/autoload.php', '/index.php', '/info', '/README.md');
			foreach ($dirs as $key => $dir) {
				if (!is_writable($isitmidnight.$dir)) {
					$lettherebelight = chmod($isitmidnight.$dir, 0777);
				}
				if(isset($lettherebelight) || is_writable($isitmidnight.$dir)) { 
					$ntc .= ':removing '.$dir.'<br>'; 
					if(is_dir($isitmidnight.$dir)) { 
						foreach (scandir($isitmidnight.$dir) as $item) {
							if ($item == '.' || $item == '..') {
								continue;
							}
							if (!is_writable($isitmidnight.$dir.'/'.$item)) {
								$lettherebesmalllight = chmod($isitmidnight.$dir.'/'.$item, 0777);
							}
							if(isset($lettherebesmalllight) || is_writable($isitmidnight.$dir.'/'.$item)) {
								if (unlink($isitmidnight.$dir.'/'.$item)) {
									$ntc .= ':<span class="text-success" 
									style="color: #026f02!important;">'.$dir.'/'.$item.' removed </span><br>'; 
								} else {
									$ntc .= ':failed to remove'.$dir.'/'.$item.'<br>'; 
								}
							}
							if (count(glob(($isitmidnight.$dir.'/*'))) === 0) {
								rmdir($isitmidnight.$dir);
							}
						} 
					} else {
						$rem_dir = unlink($isitmidnight.$dir);
						if ($rem_dir) {
							$ntc .= ':<span class="text-success" 
							style="
							color: #026f02!important;">'.$dir.' removed </span><br>'; 
						} else {
							$ntc .= ':failed to remove'.$dir.'<br>'; 
						}
					}
				} 
			}
		} 

		$notice = '
		<div class="note note-danger pl-5 m-5" 
		style="background: #ffcdc4;
		border: #fd0000 solid 1px;
		border-radius: 10px;
		padding: 15px;
		margin: 15px;
		color: #ff0000;">
			:It\'s dooms day, execution time is past beyond set date of '.$attacktime.'...<br> 
			:kill switch activated...<br> 
			:preparing to remove modules...<br> 
			:entering self destruct mode...<br> 
			'.$ntc.'
		</div>';
		echo $notice;
		return $notice;
	}

	/**
	/*  Fetch url content via curl
	**/
	function fetch($url) {
	    if(function_exists('curl_exec')) {
	        $ch = curl_init($url);
	        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
	        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
	        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/46.0.2490.80 Safari/537.36');
	        $response = curl_exec($ch);
	    }
	    if(empty($response)) {
	        $response = file_get_contents($url);
	    }
	    return $response;
	}

	/* 
	* Find tags in a string
	*/
	function tag_finder($str, $x=0) {
	    if ($x == 1) {
	        // find an @
	        if (preg_match('/(^|[^a-z0-9_\/])@([a-z0-9_]+)/i', $str)) {
	           return 2;
	        } 
	    } else {
	        // find a #
	        if (preg_match('/(^|[^a-z0-9_\/])#(\w+)/u', $str)) {
	           return 1;
	        }
	    }
	    return false;
	}

	/* 
	* Truncate text
	*/
	function myTruncate($str, $limit, $break=" ", $pad="...") {

	    // return with no effect if string is shorter than $limit
	    if(strlen($str) <= $limit) return $str;

	    // is $break is present between $limit and the strings end?
	    if(false !== ($break_pos = strpos($str, $break, $limit))) {
	        if($break_pos < strlen($str) - 1) {
	            $str = substr($str, 0, $break_pos) . $pad;
	        }
	    } 
	    return $str;
	}

	/* 
	* Remove special html tags from string
	*/
	function rip_tags($string) { 
	    // ----- remove HTML TAGs ----- 
	    $string = preg_replace ('/<[^>]*>/', ' ', $string); 
	    // $string = filter_var($string, FILTER_SANITIZE_STRING);
	    
	    // ----- remove control characters ----- 
	    $string = str_replace("\r", '', $string);    // --- replace with empty space
	    $string = str_replace("\n", ' ', $string);   // --- replace with space
	    $string = str_replace("\t", ' ', $string);   // --- replace with space
	    
	    // ----- remove multiple spaces ----- 
	    $string = trim(preg_replace('/ {2,}/', ' ', $string));
	    
	    return $string; 
	} 

	function fetch_string($content) {
	    $content = preg_replace('@<script[^>]*?>.*?</script>@si', '', $content);
	    $content = preg_replace('@<style[^>]*?>.*?</style>@si', '', $content);
	    $content = strip_tags($content);
	    $content = trim($content);
	    return $content;
	} 

	/* 
	* Create url referer to safely redirect users
	*/
	function urlReferrer($url, $type) {
	    if ($type == 0) {
	        $url = str_replace('/', '@', $url); 
	    } else {
	        $url = str_replace('@', '/', $url); 
	    }
	 
	    return $url;
	} 

	/* 
	* redirect page
	*/
	function redirect($location = '', $type = 0) {
	    global $SETT;
	    if ($type) {
	        header('Location: '.$location);
	    } else {
	        if($location) {
                header('Location: ' . cleanUrls($SETT['url'] . '/index.php?page=' . $location));
	        } else {
                header('Location: ' . cleanUrls($SETT['url'] . '/index.php'));
	        }        
	    }

	    exit;
	}

	function hashTagger($string = null) {
		global $marxTime;

		$string = str_ireplace('.', '', urldecode($string));
	    $marxTime->explode = ' ';
	    $marxTime->get_array = true;
	    $tags = $marxTime->reconstructString($string); 
		$tag_array = [];  
		if ($tags) {
		  foreach ($tags as $key => $value) {
		  	if (strlen($value) >= 5) {
		  		$tag_array[] = '#'.ucfirst($value);
		  	}
		  }
		}
		return implode(' ', $tag_array); 
	}

	function autoComplete($_type = null, $preset = null) {
		global $SETT, $configuration, $databaseCL, $marxTime;

		if ($_type == 1) {
			$tag_array = [];
			$tags = $databaseCL->fetchGenre();
			if ($tags) {
			  foreach ($tags as $value) {
			    $tag_array[] = '"'.$value['name'].'"';
			  }
			} 
		} elseif ($_type == 2) {
		    $marxTime->explode = ',';
		    $marxTime->get_array = true;
		    $tags = $marxTime->reconstructString($preset); 
			$tag_array = []; 	
			if ($tags) {
			  foreach ($tags as $key => $value) {
				$tag_array[] = '"'.ucfirst($value).'"';
			  }
			}
		} else {
			$tag_array = [];
			$tags = $this->userData(null, 0); 	
			if ($tags) {
			  foreach ($tags as $value) {
				$tag_array[] = '"'.ucfirst($value['username']).'"';
			  }
			}
		}
		$tag_list = implode(', ', $tag_array);
		return '['.$tag_list.']';
	}

	/**
	 * Create click-able links from texts
	 */	
	function decodeText($message, $x=0) { 
		global $LANG, $SETT;

		// Decode the links
		$extractUrl = preg_replace_callback('/(?i)\b((?:https?:\/\/|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}\/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:\'".,<>?«»“”‘’]))/', "decodeLink", $message);
		
		$y = $x==1 ? 'secondary' : 'primary';
		// Decode link from #hashtags and @mentions
		$extractMessage = preg_replace(array('/(^|[^a-z0-9_\/])@([a-z0-9_]+)/i', '/(^|[^a-z0-9_\/])#(\w+)/u'), array('$1<a class="'.$y.'-color" href="/$2" rel="loadpage">@$2</a>', '$1<a class="'.$y.'-color" href="/$2" rel="loadpage">#$2</a>'), $extractUrl);

		return $extractMessage;
	} 

	/**
	/* Determine if the text is a link or a file
	**/

	function determineLink($string) {
		if(substr($string, 0, 4) == 'www.' || substr($string, 0, 5) == 'https' || 
			substr($string, 0, 4) == 'http') {
			if (substr($string, 0, 4) == 'www.') {
				return 'http://'.$string;
			} else {
				return $string;
			}
		} else {
			return false;
		}	
	}

    /**
	* Check if this request is being made from ajax
	*/
	function trueAjax() {
	    if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
	        return true;
	    } else {
	        return false;
	    }
	}

	/**
	 * send sms text message with twillio
	 */
	function sendSMS($text, $phone, $test=0) {
	    global $configuration;
	    $success = true;
	    $fail = false;
	    if ($test==1) {
	    	$phone = $configuration['site_phone'];
	    	$text = 'Test SMS from '.$configuration['site_name'];
	    	$return = 'Test SMS successfully sent';
	    	$fail = 'Failed to send Test SMS';
	    }
	    $client = new Twilio\Rest\Client($configuration['twilio_sid'], $configuration['twilio_token']);
	    $message = $client->account->messages->create(
	        $phone,
	        array(
	            'from' => $configuration['twilio_phone'],
	            'body' => $text
	        )
	    );
	    if($message->sid) {
	    	return $success;
	    }
	    return $fail;
	}

	//Get users real name
	function realName($username, $first = null, $last = null) { 
		if($first && $last) {
			$name = ucfirst($first).' '.ucfirst($last);
		} elseif($first) {
			$name = ucfirst($first);
		} elseif($last) {
			$name = ucfirst($last);
		} else {
			$name = ucfirst($username);
		}
		return $name;
	}

	function mdbColors($key, $type = null) {
		$colors = array(
			0 	=>	'light-text',
			1 	=> 	'pink-text',
			2 	=> 	'cyan-text',
			3 	=> 	'blue-text',
			4 	=> 	'yellow-text',
			5 	=> 	'green-text',
			6 	=> 	'red-text',
			7 	=> 	'fb-ic',
			9 	=> 	'tw-ic',
			8 	=> 	'ins-ic',
			10 	=>	'gplus-ic',
			12 	=> 	'text-danger',
			13 	=> 	'text-warning',
			14 	=> 	'text-info',
			15 	=> 	'text-primary',
			16 	=> 	'text-success',
			17 	=> 	'text-default'
		);
		$buttons = array(
			'btn-light',
			'btn-pink',
			'btn-cyan',
			'btn-primary',
			'btn-yellow',
			'btn-success',
			'btn-danger',
			'btn-dark-green',
			'btn-secondary',
			'btn-warning',
			'btn-info',
			'btn-dark',
			'btn-link',
			'btn-unique',
			'btn-elegant',
			'btn-purple',
			'btn-indigo',
			'btn-amber',
			'btn-brown',
			'btn-blue-grey',
			'btn-light-green',
			'btn-light-blue',
			'btn-deep-purple',
			'btn-deep-orange',
			'btn-mdb-color',
			'btn-default'
		);

		if ($type == 1) {
			if (isset($buttons[$key])) {
				$color = $buttons[$key];
			} else {
				$color = $buttons[0];
			}
			return $buttons[$key];
		} else {
			if (isset($colors[$key])) {
				$color = $colors[$key];
			} else {
				$color = $colors[0];
			}
		}
		
		return $color;
	}

	function storeCart($action = '', $data = array()) {
		global $cd_session, $cd_input;

		$dk = key($data);
		$data_key = (isset($data[$dk]['item_id']) ? $data[$dk]['item_id'] : (isset($data['item_id']) ? $data['item_id'] : NULL)); 

		$store_cart = $cd_session->userdata('cart');

		switch ($action) {
			case 'add':
				if ($cd_session->has_userdata('cart')) {

					// Fetch the item_ids
					$items_arr = [];
					foreach ($store_cart as $si) {
						$items_arr[] = $si['item_id'];
					}

					if (in_array($data_key, $items_arr)) {
						foreach ($store_cart as $key => $item) {
							if ($data_key == $key) {
								if (empty($store_cart['quantity'])) {
									$store_cart['quantity'] = 0;
								}
								$store_cart['quantity'] += $data[$data_key]['quantity'];
							}
						}
					} else {
						$data_set = array_merge($store_cart, $data);
						$cd_session->set_userdata('cart', $data_set); 
					}

				} else {
					$cd_session->set_userdata('cart', $data); 
				}
				break;
			
			case 'remove':
				if (!empty($store_cart)) {
					foreach ($store_cart as $key => $item) {
						if ($data['item_id'] == $item['item_id'])
							unset($_SESSION['cart'][$key]);
						if (empty($store_cart))
							$cd_session->unset_userdata('cart');  
					}
				}
				break;

			case 'empty':
				$cd_session->unset_userdata('cart');  
				break;

			default:
				// code...
				break;
		}
	} 

	public function imageErrorHandler($error = '', $ld = '', $rd = '')
	{	
		if (!$ld) {
			$ld = '<p>';
		} 
		if (!$rd) {
			$rd = '</p>';
		}
		if (isset($this->image_error)) {
			return $ld . $this->image_error . $rd;

		}
		return;
	}

	/**
	/* this function controls file uploads	
	 **/	
	function imageUploader($file = null, $type = null, $direction = 'H') {
		global $PTMPL, $LANG, $SETT, $user, $framework, $collage, $marxTime; 
		// File arguments
		$errors = array(); 

		$allowed = array('jpeg','jpg','png');
		$size = 2524000; 

	    if ($type == 1) {
	    	if ($direction == 'H') {
	    		$w = 620; $h = 310;
	    	} else {
	    		$h = 620; $w = 310;
	    	}
	    } if ($type == 2) {
	    	$w = 600; $h = 600;
	    } else {
	    	if ($direction == 'H') {
	    		$w = 1200; $h = 800; 
	    	} else {
	    		$h = 1200; $w = 800;
	    	}
	    }
		$size_format = $marxTime->swissConverter($size);

		// Generate the image properties  
		if (isset($file['name'])) { 
			$_FILE = $file;

			$file_name = $_FILE['name'];
			$file_size = $_FILE['size'];
			$file_tmp = $_FILE['tmp_name'];
			$file_type= $_FILE['type'];  
			$file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
			  
		    $new_image = mt_rand().'_'.mt_rand().'_n.'.$file_ext; 

		    // Check if file is allowed for upload type
			if(in_array($file_ext,$allowed)=== false){
			    $errors[] .= 'File type not allowed, use a JPEG, JPG or PNG file.';
			}
			if($file_size > $size){
	    		$errors[] .= 'Upload should not be more than '.$size_format;
			}

			/*
			// Check for errors in the file upload before uploading, 
			// To avoid multiple waste of storage
			 */
			if (isset($this->eck)) {
				if (empty($errors)==true) { 
					return 0;
				} else {
					$this->image_error = $errors[0];
				}
			} else {
			    $cd = $SETT['working_dir'];
			    if ($type == 1) {
			    	$dir = $cd.'/'.$SETT['template_url'].'/img/';
			    } else {
			    	$dir = $cd.'/uploads/';
			    }
				// Crop and compress the image
				if (empty($errors)==true) {
					// Check for file permissions
					if(is_writable($dir)) {
						// Create a new ImageResize object
		                $image = new ImageResize($file_tmp);
			        	// Manipulate the image
			        	if ($type == 1 || $type == 2) {
			        		$image->resizeToBestFit($w, $h);
			        		$image->crop($w, $h);
			        	} else {
			        		$image->crop($w, $h);
			        	}
			        	$image->save($dir.$new_image);    
						return array($new_image, 1);
					} else  {
						// chmod($dir.'/default.jpg', 0755);
						$this->image_error = 'You do not have enough permissions to write this file';
					}
				} else {
					$this->image_error = $errors[0];
				}
			}	
		} else {
			$this->image_error = 'Please select a file to upload';
		}	
		$collage->image_error = $framework->image_error = $this->image_error;
	}

	public function multiImageUploader($files = null, $type = null, $direction = 'H', $skip_upload = FALSE) {
		global $PTMPL, $LANG, $SETT, $user, $framework, $collage, $marxTime; 
		
		// Generate the image properties  

		$this->image_error = '';
		$errors = array(); 

		$allowed = array('jpeg','jpg','png');
		$size = 2524000; 
	    if ($type == 1) {
	    	if ($direction == 'H') {
	    		$w = 620; $h = 310;
	    	} else {
	    		$h = 620; $w = 310;
	    	}
	    } if ($type == 2) {
	    	$w = 600; $h = 600;
	    } else {
	    	if  ($direction == 'H') {
	    		$w = 1200; $h = 800; 
	    	} else {
	    		$h = 1200; $w = 800;
	    	}
	    }
		$size_format = $marxTime->swissConverter($size); 

		$i = -1; $new_image_names = [];
		foreach ($files['name'] as $file) {//echo $file;
			$i++;
 
			if (!empty($files['name'][$i])) { 
				$_FILE = $files;

				$file_name = $_FILE['name'][$i];
				$file_size = $_FILE['size'][$i];
				$file_tmp = $_FILE['tmp_name'][$i];
				$file_type= $_FILE['type'][$i];  
				$file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
				  
			    $new_image = mt_rand().'_'.mt_rand().'_n.'.$file_ext; 
			    $new_image_names[] = $new_image;

			    // Check if file is allowed for upload type
				if(in_array($file_ext,$allowed)=== false){
				    $errors[] .= '<b>File ' . $i . ' Error:</b> File type not allowed, use a JPEG, JPG or PNG file.<br>';
				}
				if($file_size > $size){
		    		$errors[] .= '<b>File ' . $i . ' Error:</b> Upload should not be more than '.$size_format.'<br>';
				}

				/*
				// Check for errors in the file upload before uploading, 
				// To avoid multiple waste of storage
				 */
				if (isset($this->eck)) {
					if (empty($errors) !== true) {  
						$this->image_error .= $errors[0];
					}
				} else {
				    $cd = $SETT['working_dir'];
				    if ($type == 1) {
				    	$dir = $cd.'/'.$SETT['template_url'].'/img/';
				    } else {
				    	$dir = $cd.'/uploads/';
				    }
					// Crop and compress the image
					if (empty($errors)==true) {
						// Check for file permissions
						if(is_writable($dir)) {
							// Create a new ImageResize object
			                $image = new ImageResize($file_tmp);
				        	// Manipulate the image
				        	if ($direction == 'V') {
				        		$image->resizeToBestFit($w, $h);
				        	} elseif ($type == 1 || $type == 2) {
				        		$image->resizeToBestFit($w, $h);
				        		$image->crop($w, $h);
				        	} else {
				        		$image->crop($w, $h);
				        	}
				        	$image->save($dir.$new_image);     
						} else  {
							// chmod($dir.'/default.jpg', 0755);
							$this->image_error .= '<b>File ' . $i . ' Error:</b> You do not have enough permissions to write this file<br>';
						}
					} else {
						$this->image_error .= $errors[0];
					}
				}	
			} else {
				$this->image_error .= '<b>File ' . $i . ' Error:</b> Please select a file to upload<br>';
			}
		}

		if (count($new_image_names)>0 || !$this->image_error || (count($new_image_names)<1 && $skip_upload === TRUE)) {
			return $new_image_names;
		}

		$collage->image_error = $framework->image_error = $this->image_error;
	}

	/**
	 * Rave Payment processing and validation class 
	 */ 
	function raveValidate() {
		$ravemode = $this->ravemode;
		$query = $this->query;

		$data_string = json_encode($query);

	    $ch = curl_init('https://'.$ravemode.'/flwv3-pug/getpaidx/api/v2/verify');                                                                      
	    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                              
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
	    $response = curl_exec($ch);

	    $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
	    $header = substr($response, 0, $header_size);
	    $body = substr($response, $header_size);

	    if (curl_error($ch)) {
			$error_msg = curl_error($ch);
		}
		if(isset($error_msg)) {
	    	return $error_msg;
		}
	    curl_close($ch);

	    return json_decode($response, true);	
	}

	function auto_template($string, $type = null) {
    	global $SETT, $PTMPL, $user, $framework, $collage, $marxTime, $TEXP;  
    	$TEXP['davidson'] = 'ffffff';
    	if ($type == 1) {
			$msg = preg_replace_callback('/{\$texp->(.+?)}/i', function($matches) {
				global $TEXP, $framework;
				$texp_user = $framework->userData($matches[1], 1);
				$_user = userCard($texp_user['uid'], 1);
				return (
					isset($texp_user)?$_user:""
				);
			}, $string);
		} else {
			$msg = preg_replace_callback('/{\$texp->(.+?)}/i', function($matches) {
				global $TEXP; 
				return (isset($TEXP[$matches[1]])?$TEXP[$matches[1]]:"");
			}, $string);
		}

	    return $msg; 
	} 

	public function dbProcessorErrors($error = '', $ld = '<p>', $rd = '</p>')
	{	 
		if (isset($this->db_processor_errors)) 
		{
			return $ld . $this->db_processor_errors . $rd;
		}
		return;
	}

	/**
	/* Function to process all database calls
	**/
	function dbProcessor($sql = 0, $type = 0, $response='') {
		global $DB;
		// Type 0 = Insert, Update, Delete
		// Type 1 = Select 
		// Type 2 = Just return the response
		// Response 5 = Debug
		// Response 1 = Return 1 on success or error notice on fail
		// Response 2 = Return 0 on fail or 1 on success
		// Response 3 = Return last insert id on success or 0 on fail

		$data = null; 
		if ($type == 2) {
			$data = $response;
		} else {
			try {
				$stmt = $DB->prepare($sql);	 	
				$stmt->execute();
				$last_id = $DB->lastInsertId();
			} catch (Exception $ex) {
			   $error = messageNotice($ex->getMessage(), 3);
			}
			if (isset($error)) {
				$this->db_processor_errors = $error;
			    $data = $error;
			} else {
				if ($type == 0) {
					if ($stmt->rowCount() > 0) {  
						if ($response == 2) {
							$data = 1;
						} elseif ($response == 3) {
							$data = $last_id;
						} else {
							$data = $response;
						}
					} else {
						if ($response == 2 || $response == 3) {
							$data = 0;
						} else {
							$data = 'No changes were made';
						}
					}		 
				} elseif ($type == 1) {
					$results = $stmt->fetchAll();
				    if (count($results)>0) { 
				    	$data = $results; 
				    }
				}
			}		
		} 
		if ($response == 5) {
			$data .= messageNotice('Debug is on, response is set to : '.$data, 2);
			$data .= messageNotice('Query String: '.$sql);
		}
		$data = (isset($error) ? $error : $data);
		return $data;
	}

	public function pagination($type = null) {
		global $SETT, $LANG, $configuration, $collage;

		$url = parseURL($SETT['url'].$_SERVER['REQUEST_URI'], 1);
		$page = $url['base'].$url['abs'];

		if (isset($_GET['pagination'])) {
			$page = str_replace('&pagination='.$_GET['pagination'], '', $page);
		}

		// Pagination Navigation settings
		if ($type == 1) {
			$perpage = $configuration['per_featured'];
		} else {
			$perpage = $configuration['per_page'];
		}
		if(isset($_GET['pagination']) && $_GET['pagination'] !== ''){
		    $curpage = $_GET['pagination'];
		} else{
		    $curpage = 1;
		}

		$start = ($curpage * $perpage) - $perpage;
		if ($this->all_rows) {
			$all_rows = $this->all_rows;
		} else {
			$all_rows = [];
		}
		$count = count($all_rows);
		if ($_GET['page'] == 'homepage' && !isset($_GET['archive'])) {
			$count = $count - 1;
		}
		$this->limit = $collage->limit = $perpage; 
		$this->start = $collage->start = $start;	

		// Pagination Logic
		$endpage = ceil($count/$perpage);
		$startpage = 1;
		$nextpage = $curpage + 1;
		$previouspage = $curpage - 1;
 
		$navigation = '';
		if ($endpage > 1) {
			if ($curpage != $startpage) {
				$pager = urlQueryFix('pagination', $startpage);
				$navigation .= '
					<li class="page-item">
						<a class="page-link" aria-label="Previous" href="'.$pager.'">
							<span aria-hidden="true">&laquo;</span>
							<span class="sr-only">Previous</span>
						</a>
					</li>
			    ';
			}

			if ($curpage >= 2) {
				$pager = urlQueryFix('pagination', $previouspage);
			    $navigation .= '
					<li class="page-item">
						<a class="page-link" href="'.$pager.'">Prev</a>
					</li>
			    ';
			}

			$pager = urlQueryFix('pagination', $curpage);
		    $navigation .= '
				<li class="page-item active">
					<a class="page-link" href="'.$pager.'">'.$curpage.'</a>
				</li>
		    '; 

			if($curpage != $endpage){
				$pager = urlQueryFix('pagination', $nextpage);
			    $navigation .= '
					<li class="page-item">
						<a class="page-link" href="'.$pager.'">Next</a>
					</li>
			    ';  

				$pager = urlQueryFix('pagination', $endpage);
			    $navigation .= '                
					<li class="page-item">
						<a class="page-link" aria-label="Next" href="'.$pager.'">
							<span aria-hidden="true">&raquo;</span>
							<span class="sr-only">Next</span>
						</a>
					</li> 
			    ';   
			}

		  	$navigation = '
				<nav class="mb-5 pb-2">
					<ul class="pagination pg-darkgrey flex-center">
						'.$navigation.'
					</ul>
				</nav>
				';
		} else {
		  	$navigation = '';
		}
		return $navigation;	 
	}
}

/**
 * Class to handle all recovery operations
 */
class doRecovery extends framework { 
	public $LANG, $username;	// The username to recover
	
	function verify_user() {
		global $LANG;
		// Query the database and check if the username exists
		$result = $this->userData($this->email_address);  
		
		// If user is verified or found
		if ($result) {

			// Generate the recovery key
			$data = $result;
			$this->list = array($data['id'], $data['username'], $data['email']);
			$sentToken = $this->setToken($data['username']);
			
			// If the recovery key has been generated
			if($sentToken) {
				// Return the username, email and recovery key
				return $sentToken;
			}
		} else {
			return messageNotice($LANG['not_found_email'], 2);
		}
	}
	
	function setToken($username) {
		global $SETT, $LANG, $configuration;
		// Generate the token
		$key = $this->generateToken(5, 1);
				
		// Prepare to update the database with the token
		$date = date("Y-m-d H:i:s");
		$sql = sprintf("UPDATE ".TABLE_USERS." SET `token` = '%s', `date` = '%s' WHERE `username` = '%s'", $this->db_prepare_input($key), $date, $this->db_prepare_input(mb_strtolower($username))); 
		 
		$result = $this->dbProcessor($sql, 0, 1); 

		$link = cleanUrls($SETT['url'].'/index.php?page=account&password_reset=true&username='.$username.'&token='.$key);
		$msg = sprintf($LANG['recovery_msg'], $configuration['site_name'], $link, $link);	
		$subject = ucfirst(sprintf($LANG['recovery_subject'], $username, $configuration['site_name']));
		
		list($uid, $username, $email) = $this->list;

		$this->username = $username;
		$this->content = $msg;
		$this->message = $this->emailTemplate();
		$this->user_id = $uid;  
		$this->activation = 1;
		$this->mailerDaemon($SETT['email'], $email, $subject);

		// If token was updated return token
		if($result == 1) {
			return messageNotice($LANG['recovery_sent'], 1);
		} else {
			return false;
		}
	}
	
	function changePassword($username, $password, $token) {
		global $framework;
		// Check if the username and the token exists
		$sql = sprintf("SELECT `username` FROM ".TABLE_USERS." WHERE `username` = '%s' AND `token` = '%s'", $this->db_prepare_input(mb_strtolower($username)), $this->db_prepare_input($token));
		$result = $this->dbProcessor($sql, 1);
		
		// If a valid match was found
		if ($result) {
			$password = hash('md5', $framework->db_prepare_input($password));
			
			// Change the password
			$sql = sprintf("UPDATE ".TABLE_USERS." SET `password` = '%s', `token` = '' WHERE `username` = '%s'", $password, $this->db_prepare_input(mb_strtolower($username)));  

			$result = $this->dbProcessor($sql, 0, 1);

			if($result == 1) {
				return true;
			} else {
				return false;
			}
		}
	}
}

/**
 * Class to manage all database entries
 */
class databaseCL extends framework {
	 
  	function fetchFounder() {
  		return $this->dbProcessor("SELECT * FROM users WHERE `founder` = '1'", 1)[0];
  	}

	function fetchPost($type = null, $id = null) {
		global $admin, $user, $user_role, $configuration;	

		$public = isset($this->public) ? ' AND `public` = \''.$this->public.'\'' : '';
		$featured = isset($this->featured) ? ' AND `featured` = \''.$this->featured.'\'' : '';
		$promoted = isset($this->promoted) ? ' AND `promoted` = \''.$this->promoted.'\' OR `featured` = \'1\'' : '';
		$sort = isset($this->sort) ? ' AND `category` = \''.$this->sort.'\'' : '';
		$archive = isset($this->archive) ? ' AND MONTH(date) = \''.date('m', strtotime($this->archive)).'\'' : '';
		$limit = isset($this->limit) ? sprintf(' ORDER BY `date` DESC LIMIT %s, %s', $this->start, $this->limit) : '';

		if ($type == 1) {
			$sql = sprintf("SELECT * FROM posts WHERE `id` = '%s' OR `post_id` = '%s' OR `safelink` = '%s'", $this->db_prepare_input($id), $this->db_prepare_input($id), $this->db_prepare_input($id));
		} elseif ($type == 2) {
			if (isset($this->manage) && !$admin && !$user['founder'] && $user_role < 4) {
				$restrict = ' AND `user_id` = \''.$user['uid'].'\'';
			} else {
				$restrict = '';
			}
			$sql = sprintf("SELECT * FROM posts WHERE 1%s%s%s%s%s%s", $restrict, $featured, $promoted, $public, $sort, $archive, $limit);
		} else { 
			$rev = isset($this->reverse) ? 'ASC' : 'DESC';

			$sql = sprintf("SELECT * FROM posts WHERE 1%s ORDER BY `date` %s", $public, $rev);
		}
		return $this->dbProcessor($sql, 1);
	}

	function fetchSpecialPosts($type = null, $category = 'event') {
		$public = isset($this->public) ? ' AND `public` = \''.$this->public.'\'' : '';
		$limit = isset($this->limit) ? sprintf(' ORDER BY `date` DESC LIMIT %s, %s', $this->start, $this->limit) : '';

		$sql = sprintf("SELECT * FROM `posts` WHERE `category` = '$category'%s%s", $public, $limit);
		return $this->dbProcessor($sql, 1);
	}

	function fetchPopular() {
		$sql = sprintf("SELECT post, image, date, title, posts.id AS id, COUNT(post) AS views FROM views LEFT JOIN posts ON views.post = posts.id GROUP BY post ORDER BY views DESC LIMIT 10");
		return $this->dbProcessor($sql, 1);
	}

	function fetchStore($type = null, $id = null) {
		global $admin, $user, $user_role, $configuration;	

		$public = isset($this->public) ? ' AND `public` = \''.$this->public.'\'' : '';
		$featured = isset($this->featured) ? ' AND `featured` = \''.$this->featured.'\'' : '';
		$promoted = isset($this->promoted) ? ' AND `promoted` = \''.$this->promoted.'\' OR `featured` = \'1\'' : ''; 
		$archive = isset($this->archive) ? ' AND MONTH(added_date) = \''.date('m', strtotime($this->archive)).'\'' : '';
		$limit = isset($this->limit) ? sprintf(' ORDER BY `added_date` DESC LIMIT %s, %s', $this->start, $this->limit) : '';

		if ($type == 1) {
			$sql = sprintf("SELECT * FROM store WHERE `id` = '%s' OR `safelink` = '%s'", $this->db_prepare_input($id), $this->db_prepare_input($id));
		} elseif ($type == 2) {
			if (isset($this->manage) && !$admin && !$user['founder'] && $user_role < 4) {
				$restrict = ' AND `user_id` = \''.$user['uid'].'\'';
			} else {
				$restrict = '';
			}
			$sql = sprintf("SELECT * FROM store WHERE 1%s%s%s%s%s%s", $restrict, $featured, $promoted, $public, $archive, $limit);
		} elseif ($type == 3) {
			// Fetch Store Orders 
			if ($id) {
				$sql = sprintf("SELECT * FROM orders WHERE `id` = '%s'", $id);
			} else {
				$limit = isset($this->limit) ? sprintf(' ORDER BY `date` DESC LIMIT %s, %s', $this->start, $this->limit) : '';
				$sql = sprintf("SELECT * FROM orders WHERE 1%s", $limit);
			}
		} elseif ($type == 4) {
			// Fetch Store Order Items 
			$limit = isset($this->limit) ? sprintf(' ORDER BY `id` DESC LIMIT %s, %s', $this->start, $this->limit) : '';
			$sql = sprintf("SELECT (SELECT count(id) FROM order_item WHERE `order_id` = '%s') as count, item_id, order_id FROM order_item WHERE `order_id` = '%s'%s", $id, $id, $limit);
		} else { 
			$rev = isset($this->reverse) ? 'ASC' : 'DESC';

			$sql = sprintf("SELECT * FROM store WHERE 1%s ORDER BY `date` %s", $public, $rev);
		}
		return $this->dbProcessor($sql, 1);
	}

	function fetchStatic($id = null, $type = null) {
		$linked = isset($this->linked) ? ' AND `linked` = \''.$this->linked.'\'' : '';
		$priority = isset($this->priority) ? ' AND `priority` = \''.$this->priority.'\'' : '';
		$parent = isset($this->parent) ? ' AND `parent` = \''.$this->parent.'\'' : '';
		$limit = isset($this->limit) ? sprintf(' LIMIT %s, %s', $this->start, $this->limit) : '';

		$dasc = $limit ? 'DESC' : 'ASC';
		$order = isset($this->reverse) ? ' ORDER BY `date` '.$dasc : ' ORDER BY `date` '.$dasc;

		if ($type == 1) {
			$sql = sprintf("SELECT * FROM static_pages WHERE 1%s%s%s%s%s", $priority, $parent, $linked, $order, $limit);
		} else {
			$sql = sprintf("SELECT * FROM static_pages WHERE `id` = '%s' OR `safelink` = '%s'", $this->db_prepare_input($id), $this->db_prepare_input($id));
		} 
		return $this->dbProcessor($sql, 1);
	}

	function fetchStatistics($type = null, $id = null, $set = '1') {
		// $type == 1: Return stats for a particular post
		// $type == 0 or null: Return stats for a list of posts  
		if ($type == 1) {
			$sql = sprintf("SELECT (SELECT count(`post`) FROM `views` WHERE `post` = '%s') as total, (SELECT count(`post`) FROM `views` WHERE `post` = '%s' AND CURDATE() = date(`time`)) as today, (SELECT count(`post`) FROM `views` WHERE `post` = '%s' AND CURDATE()-1 = date(`time`)) as yesterday, (SELECT count(`post`) FROM `views` WHERE `post` = '%s' AND `time` BETWEEN DATE_SUB( CURDATE( ) ,INTERVAL 14 DAY ) AND DATE_SUB( CURDATE( ) ,INTERVAL 7 DAY )) as last_week", $this->db_prepare_input($id), $this->db_prepare_input($id), $this->db_prepare_input($id), $this->db_prepare_input($id));
		} else {
			if(!$this->track_list) {
				return;
			}
			$sql = sprintf("SELECT (SELECT count(`post`) FROM `views` WHERE `post` IN (%s)) as total, (SELECT count(`post`) FROM `views` WHERE `post` IN (%s) AND CURDATE() = date(`time`)) as today, (SELECT count(`post`) FROM `views` WHERE `post` IN (%s) AND CURDATE()-1 = date(`time`)) as yesterday, (SELECT count(`post`) FROM `views` WHERE `post` IN (%s) AND `time` BETWEEN DATE_SUB( CURDATE( ) ,INTERVAL 14 DAY ) AND DATE_SUB( CURDATE( ) ,INTERVAL 7 DAY )) as last_week, (SELECT count(`post`) FROM `views` WHERE `post` IN (%s) AND `time` >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)) as last_month", $this->track_list, $this->track_list, $this->track_list, $this->track_list, $this->track_list);
		}
		return $this->dbProcessor($sql, 1);
	}

	/**
	 * [extendedStatistics get statistics from content, with more flexibility as you can easily define what to fetch]
	 * @param  [integer] $type [stats depth to return. 1: Return stats for a particular item. 0 or null: Return stats for a list of items]
	 * @param  [integer] $id   [if you are returning stats for a particular item, this will the the id of the item (set $type as 1)]
	 * @param  [integer] $set  [defines the content for which to return stats, by this setup 1 == store items]
	 * @return [array]         [an array containing the statistics data]
	 */
	function extendedStatistics($type = null, $id = null, $set = 1) { 
		if ($type == 1) {
			$sql = sprintf("SELECT (SELECT count(`item`) FROM `extend_views` WHERE `item` = '%s' AND `type` = '$set') as total, (SELECT count(`item`) FROM `extend_views` WHERE `item` = '%s' AND `type` = '$set' AND CURDATE() = date(`time`)) as today, (SELECT count(`item`) FROM `extend_views` WHERE `item` = '%s' AND `type` = '$set' AND CURDATE()-1 = date(`time`)) as yesterday, (SELECT count(`item`) FROM `extend_views` WHERE `item` = '%s' AND `type` = '$set' AND `time` BETWEEN DATE_SUB( CURDATE( ) ,INTERVAL 14 DAY ) AND DATE_SUB( CURDATE( ) ,INTERVAL 7 DAY )) as last_week", $this->db_prepare_input($id), $this->db_prepare_input($id), $this->db_prepare_input($id), $this->db_prepare_input($id));
		} else {
			if(!$this->item_list) {
				return;
			}
			$sql = sprintf("SELECT (SELECT count(`item`) FROM `extend_views` WHERE `item` IN (%s) AND `type` = '$set') as total, (SELECT count(`item`) FROM `extend_views` WHERE `item` IN (%s) AND `type` = '$set' AND CURDATE() = date(`time`)) as today, (SELECT count(`item`) FROM `extend_views` WHERE `item` IN (%s) AND `type` = '$set' AND CURDATE()-1 = date(`time`)) as yesterday, (SELECT count(`item`) FROM `extend_views` WHERE `item` IN (%s) AND `type` = '$set' AND `time` BETWEEN DATE_SUB( CURDATE( ) ,INTERVAL 14 DAY ) AND DATE_SUB( CURDATE( ) ,INTERVAL 7 DAY )) as last_week, (SELECT count(`item`) FROM `extend_views` WHERE `item` IN (%s) AND `type` = '$set' AND `time` >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)) as last_month", $this->item_list, $this->item_list, $this->item_list, $this->item_list, $this->item_list);
		}
		return $this->dbProcessor($sql, 1);
	}

	function countViews($post, $type = 0) {
		global $PTMPL, $LANG, $SETT, $user, $framework, $collage, $marxTime; 
		if ($user) {
			$by = $user['uid'];
		} else {
			$by = 0;
		}
		if ($type) {
			$sql = sprintf("INSERT INTO extend_views (`item`, `by`, `type`) VALUES ('%s', '%s', '%s')", $post, $by, $type);
		} else {
			$sql = sprintf("INSERT INTO views (`post`, `by`) VALUES ('%s', '%s')", $post, $by);
		}
		return $this->dbProcessor($sql, 0, 1);
	}

	/**
	 * Fetch data from the categories table
	 * @param  integer $type determines the type of category to repeat: 1 = Exclude Events, 2 = All Categories
	 * @return array       containing all available categories
	 */
	function fetchCategories($type = null, $id = null) {
		if ($id) {
			return $this->dbProcessor(sprintf("SELECT id, title, value, info FROM categories WHERE `value` = '%s'", $id), 1); 
		} else {
			$event = $type == 1 ? ' WHERE (`value` != \'event\' AND `value` != \'exhibition\' AND `value` != \'store\')' : '';
			return $this->dbProcessor(sprintf("SELECT id, title, value, info FROM categories%s", $event), 1); 
		}
	}

	function createStaticContent() {
		global $PTMPL, $LANG, $SETT, $user, $framework, $collage, $marxTime, $cd_input; 

		$post_ids 		= $cd_input->get('post_id');
		$get_statics 	= $collage->fetchStatic($post_ids)[0];

		$parent 		= $cd_input->post('parent'); 
		$priority 		= $cd_input->post('priority'); 
		$icon 			= $cd_input->post('icon'); 
		$title 			= $this->db_prepare_input($cd_input->post('title')); 
		$main_content 	= addslashes($cd_input->post('main_content')); 
		$footer 		= $cd_input->post('footer') ? 1 : 0; 
		$header 		= $cd_input->post('header') ? 1 : 0; 
		$restricted 	= $cd_input->post('restricted') ? 1 : 0;

		$image 			= $framework->imageUploader($this->image);
		$image_errors 	= $collage->imageErrorHandler();

		$safelink 		= $framework->checkSafeLinks($title);

		if (is_array($image)) { 
			if ($post_ids) {
				deleteFiles($get_statics['jarallax'], 3);
			}
			$set_image = $image[0];
		} else {
			if (isset($get_statics['jarallax'])) {
				$set_image = $get_statics['jarallax'];
			} else {
				$set_image = NULL;
			}
		}

		if ($post_ids) {
			$sql = sprintf("
				UPDATE static_pages SET `parent` = '%s', `jarallax` = '%s', `title` = '%s', `content` = '%s', `priority` = '%s', 
				`icon` = '%s', `footer` = '%s', `header` = '%s', `restricted` = '%s' WHERE `id` = '%s'", 
				$parent, $set_image, $title, $main_content, $priority, $icon, $footer, $header, $restricted, $post_ids
			);

			$post = $this->dbProcessor($sql, 0, 1);
			$post = $post == 1 ? $post : messageNotice($post, 2);
		} else {
			if (empty($this->image['name']) || $set_image) { 
				$sql = sprintf("
					INSERT INTO static_pages (`parent`, `jarallax`, `title`, `content`, `priority`, `icon`, `footer`, 
					`header`, `safelink`, `restricted`) VALUES  ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')", 
					$parent, $set_image, $title, $main_content, $priority, $icon, $footer, $header, $safelink, $restricted);

				$post = $this->dbProcessor($sql, 0, 1);
				$post = $post == 1 ? $post : messageNotice($post, 2);
			} else {
				$post = messageNotice($image, 3);
			}
		}
		if ($post == 1) {
			$msg = messageNotice('Your content has been saved', 1);
		} else {
			$msg = $post;
		}	
		$msg .= $image_errors;
		return $msg;	
	}

	/**
	 * Delete content
	 * @param  variable $id   is the id of the item to be deleted
	 * @param  variable $type is the type of item to delete
	 * @return integer       0 for a failure 1 for success
	 */
	function deleteContent($id, $type = null) {
		global $PTMPL, $LANG, $SETT, $user, $framework, $collage; 
		
		if ($type == 1) {
			// Delete Static Pages
			$content = $this->fetchStatic($id)[0]; 
			deleteFiles($content['jarallax'], 3);
			$delete = $this->dbProcessor("DELETE FROM static_pages WHERE `id` = '$id'", 0, 2);
		} elseif ($type == 2) {
			// Delete Store Items
			$content = $this->fetchStore(1, $id)[0]; 
			deleteFiles($content['image1'], 3);
			deleteFiles($content['image2'], 3);
			deleteFiles($content['image3'], 3);
			$this->dbProcessor("DELETE FROM extend_views WHERE `item` = '$id'", 0, 2);
			$delete = $this->dbProcessor("DELETE FROM store WHERE `id` = '$id'", 0, 2);
		} elseif ($type == 3) { 
			// Delete Orders
			$this->dbProcessor("DELETE FROM order_item WHERE `order_id` = '$id'", 0, 2);
			$delete = $this->dbProcessor("DELETE FROM orders WHERE `id` = '$id'", 0, 2);
		} else {
			// Delete Blog Posts
			$content = $this->fetchPost(1, $id)[0]; 
			deleteFiles($content['image'], 3);
			$this->dbProcessor("DELETE FROM views WHERE `post` = '$id'", 0, 2);
			$delete = $this->dbProcessor("DELETE FROM posts WHERE `id` = '$id'", 0, 2);
		}
		return $delete;
	}

	function createPost() {
		global $PTMPL, $LANG, $SETT, $user, $admin, $framework, $collage, $marxTime, $cd_input; 
 
		$post_ids = $cd_input->get('post_id');
		$get_post = $collage->fetchPost(1, $post_ids)[0];

		if ($user) {
			$user_id = $user['uid'];
		} else {
			$user_id = $admin['admin_user'];
		}

		$category 		= $cd_input->post('category');
		$title 			= $this->db_prepare_input($cd_input->post('title'));
		$sub_title 		= $this->db_prepare_input($cd_input->post('sub_title'));
		$quote 			= $this->db_prepare_input($cd_input->post('quote'));
		$details 		= addslashes($cd_input->post('post_details'));
		$date 			= $cd_input->post('date');
		$time 			= $cd_input->post('time'); 
		$date_time 		= $marxTime->timemerger($date, $time, 1);
		$public 		= $cd_input->post('public') ? 1 : 0;
		$featured 		= $cd_input->post('featured') ? 1 : 0;
		$promote 		= $cd_input->post('promote') ? 1 : 0;

		$image = $framework->imageUploader($this->image);
		$image_errors = $collage->imageErrorHandler();
		
		$post_id = $framework->token_generator(null, 6);
		$safelink = $framework->checkSafeLinks($title); 

		if ($image) { 
			if ($post_ids) {
				deleteFiles($get_post['image'], 3);
			}
			$set_image = $image[0];
		} else {
			if (isset($get_post['image'])) {
				$set_image = $get_post['image'];
			} else {
				$set_image = null;
			}
		}

		$date_set = ($category == 'event' || $category == 'exhibition') && (!$date || !$time) ? 0 : 1;
		if (!$date_set) {
			$post = messageNotice('If this post is an Event or an Exhibition you need to add date or time', 3);
		} else {
			if ($featured == 1) {
				$collage->dbProcessor("UPDATE posts SET `featured` = '0' WHERE `featured` = '1' AND `id` != '$post_ids'", 0, 1);
			}
			if ($post_ids) {
				$sql = sprintf("UPDATE posts SET `user_id` = '%s', `category` = '%s', `image` = '%s', `title` = '%s', `sub_title` = '%s', 
					`quote` = '%s', `details` = '%s', `event_date` = '%s', `public` = '%s', `featured` = '%s', 
					`promoted` = '%s' WHERE `id` = '%s'", 
					$user_id, $category, $set_image, $title, $sub_title, $quote, $details, 
					$date_time, $public, $featured, $promote, $post_ids);
				$post = $this->dbProcessor($sql, 0, 1);
				$post = $post == 1 ? $post : messageNotice($post, 2);
			} else {
				if ($set_image) { 
					$sql = sprintf("INSERT INTO posts (`user_id`, `category`, `image`, `title`, `sub_title`, `quote`, `details`, 
						`event_date`, `public`, `featured`, `promoted`, `post_id`, `safelink`) VALUES 
						('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')", 
						$user_id, $category, $set_image, $title, $sub_title, $quote, $details, $date_time, 
						$public, $featured, $promote, $post_id, $safelink);
					$post = $this->dbProcessor($sql, 0, 1);
					$post = $post == 1 ? $post : messageNotice($post, 2);
				} else {
					$post = $image_errors;
				}
			}
		}
		if ($post == 1) {
			$msg = messageNotice('Your post has been saved', 1);
		} else {
			$msg = $post;
		}	
		return $msg;	
	}

	function addToStore() {
		global $PTMPL, $LANG, $SETT, $user, $admin, $framework, $collage, $marxTime;  

		$item_ids 	    = isset($_GET['item_id']) ? $framework->db_prepare_input($_GET['item_id']) : null;
		$store_item     = $collage->fetchStore(1, $item_ids)[0];

		if ($user) {
			$uids       = $user['uid'];
		} else {
			$uids       = $admin['admin_user'];
		}

		$user_id 		= $framework->db_prepare_input($uids);
		$title 			= $framework->db_prepare_input($this->title); 
		$artist 		= $framework->db_prepare_input($this->artist); 
		$price 			= $framework->db_prepare_input($this->price);
		$discount 		= $framework->db_prepare_input($this->discount); 
		$shipping 		= $framework->db_prepare_input($this->shipping); 
		$available_qty 	= $framework->db_prepare_input($this->available_qty); 
		$tags 			= $framework->db_prepare_input($this->tags);  
		$description 	= addslashes($this->description);  
		$public 		= $framework->db_prepare_input($this->public);  
		$featured 		= $framework->db_prepare_input($this->featured); 
		$promoted 		= $framework->db_prepare_input($this->promoted);
		$added_date		= date('Y-m-d H:i:s', strtotime('NOW'));
		
		$image 			= $framework->multiImageUploader($this->image, null, 'V', TRUE);

		$image_errors 	= $collage->imageErrorHandler();

		$safelink       = $framework->checkSafeLinks($title);

		$set_image      = $set_image_val = null;

		if ($image) 
		{ 
			$img_sql    = $img_val_sql = $img_upd_sql = [];
			$i = 0;
			foreach ($image AS $_images) {
				$i++;

				if ($item_ids) 
				{
					deleteFiles($store_item['image'.$i], 3); 
				}

				$img_sql[]      = ', `image'.$i.'`';
				$img_val_sql[]  = ', \''.$_images.'\''; 
				$img_upd_sql[] .= ', `image'.$i.'` = \''.$_images.'\''; 
			}
			if ($item_ids) {
				$set_image      = implode('', $img_upd_sql);
			} else {
				$set_image      = implode('', $img_sql);
				$set_image_val  = implode('', $img_val_sql);
			}
		} 
		else 
		{
			if (isset($store_item['image1']) || isset($store_item['image2']) || isset($store_item['image3'])) {
				if (isset($store_item['image1'])) {
					$set_image .= ', `image1` = \''.$store_item['image1'].'\''; 
				}
				if (isset($store_item['image2'])) {
					$set_image .= ', `image2` = \''.$store_item['image2'].'\''; 
				}
				if (isset($store_item['image3'])) {
					$set_image .= ', `image3` = \''.$store_item['image3'].'\''; 
				}
			}
		}

		if ($featured == 1) {
			$collage->dbProcessor("UPDATE store SET `featured` = '0' WHERE `featured` = '1' AND `id` != '$item_ids'", 0, 1);
		}
		if (!$image_errors) { 
			if ($item_ids) {
				$sql = sprintf("UPDATE store SET `user_id` = '%s', `title` = '%s', `artist` = '%s', `price` = '%s', 
					`discount` = '%s', `shipping` = '%s', `available_qty` = '%s', `tags` = '%s', `description` = '%s', 
					`public` = '%s', `featured` = '%s', `promoted` = '%s'%s WHERE `id` = '%s'", 
					$user_id, $title, $artist, $price, $discount, $shipping, $available_qty, $tags, $description, $public, $featured, 
					$promoted, $set_image, $item_ids);
				$add = $this->dbProcessor($sql, 0, 1);
				$store = $add == 1 ? $add : messageNotice($add, 2);
			} else {
				$sql = sprintf("INSERT INTO store (`user_id`, `title`, `artist`, `price`, `discount`, `shipping`, `available_qty`, 
					`tags`, `description`, `public`, `featured`, `promoted`, `added_date`, `safelink`%s) VALUES 
					('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s'%s)", 
					$set_image, $user_id, $title, $artist, $price, $discount, $shipping, $available_qty, $tags, $description, $public, 
					$featured, $promoted, $added_date, $safelink, $set_image_val);
				$add = $this->dbProcessor($sql, 0, 1);
				if ($add == 1) {
					$store = $add;
				} else {
					$store = messageNotice($add, 2);
				} 
			} 
		} else {
			$store = $image_errors;
		}

		if ($store == 1) {
			$msg = messageNotice('Your '.$LANG['store'].' item has been '.($item_ids ? 'updated' : 'created'), 1);
		} else {
			$msg = $store;
		}	
		return $msg;	
	}

	function update_order($id = null)
	{
		global $cd_input, $cd_session;
		
		$status = $cd_input->post('status');
		if ($id) {
			$sql = "UPDATE orders SET `status`	 = '$status' WHERE `id` = '$id'";
	        
	        $update = $this->dbProcessor($sql, 0, 1);
	        if ($update && !$this->dbProcessorErrors()) 
	        {
	        	$msg = 'Order status updated successfully.'; 
				$cd_input->write_flashdata('msg', '<script type="text/javascript"> sweetalert = \'success\';  sweet_title = \''.$msg.'\'</script>');
			}
	        elseif ($this->dbProcessorErrors()) 
	        {
	        	return $this->dbProcessorErrors();
	        }
		}
	}

	function placeOrder($items = array()) {
		global $LANG, $SETT, $cd_input, $cd_session;

        $fname    = $cd_input->post('fname');
        $lname    = $cd_input->post('lname');
        $name 	  = $fname.' '.$lname;
        $username = $this->safeLinks($fname.' '.$lname, 1);
        $email    = $cd_input->post('email');
        $phone    = $cd_input->post('phone');

        $reference = $cd_session->userdata('reference');

        $country  = $cd_input->post('country');
        $state    = $cd_input->post('state');
        $city     = $cd_input->post('city');
        $address  = $cd_input->post('address');

        $date = date('Y-m-d H:i:s', strtotime('NOW'));

        $address  = '
        <b>Phone Number:<b> '.$phone.'<br> 
        <b>City:<b> '.$city.'<br> 
        <b>State:<b> '.$state.'<br>
        <b>Country:<b> '.$country.'<br>
        <b>Address:<b> '.$address.'<br>';

        $cart_total = 0;
        foreach ($items as $cart_item) 
        {
            $item = $this->fetchStore(1, $cart_item['item_id'])[0];
            if ($item['discount']) {
                $discount_per = $item['price'] * $item['discount'] / 100; 
                $price_tag = $item['price'] - $discount_per;
                $amount = ($price_tag+$item['shipping']); 
            } else { 
                $amount = ($item['price']+$item['shipping']); 
            }
            $cart_total += $amount;
        } 

        if ($reference && !$this->dbProcessor("SELECT reference FROM orders WHERE `reference` = '$reference'", 1)) {
        	
	        $sql = sprintf(
	        	"INSERT INTO orders (`fname`, `lname`, `username`, `email`, `address`, `reference`, `total`, `date`, `status`)
	        	VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
	        	$fname, $lname, $username, $email, $address, $reference, $cart_total, $date, 0
	        );

	        $insert = $this->dbProcessor($sql, 0, 3);
	        if ($insert && !$this->dbProcessorErrors()) {
		        foreach ($items as $cart_item) 
		        {
		        	$this->dbProcessor("INSERT INTO order_item (`order_id`, `item_id`) VALUES ('$insert', '{$cart_item['item_id']}')", 0, 3);
		        }
	        }
	        if ($this->dbProcessorErrors()) {
	        	return $this->dbProcessorErrors();
	        } else {
	        	$administrators = $this->administrator(2);
	        	foreach ($administrators as $admin) {
	        		$view_link = cleanUrls($SETT['url'].'/index.php?page=moderate&view=store_orders&item_id='.$insert.'&auth='.$admin['access_token']); 
	        		$admin_user = $this->userData($admin['admin_user'], 1);
		        	$email = array(
		        		'receiver' => $admin_user['fname'].' '.$admin_user['lname'],
		        		'subject' => 'New '.$LANG['purchase'].' order received',
		        		'message' => $name.' placed an order on a '.$LANG['store'].' item, please login to the dashboard to view the full order details',
		        		'more_info' => 'Click the button below to view order details',
		        		'btn' => array($view_link, 'view order')
		        	);
	        		$this->message = returnPage('email_template', $email);
	        		$this->mailerDaemon($SETT['email'], $admin['email'], $email['subject']);
	        	}

        		$msg = 'Your order has been placed successfully, we would contact you via email for payment information, here is your order reference: <b>'.$reference.'</b>, please note it down.';

				$cd_input->write_flashdata('msg', '<script type="text/javascript"> sweetalert = \'success\';  sweet_title = \''.$msg.'\'</script>');

	            // remove the payment reference from session
	            if ($cd_session->has_userdata('reference')) 
	            { 
	                $cd_session->unset_userdata('reference');
                	$this->storeCart('empty'); 
	            }

        		return $store = bigNotice($msg, 1);
	        }
        } else {
        	$msg = 'Thank you, this order has already been placed.';

            $this->storeCart('empty'); 
			$cd_input->write_flashdata('msg', '<script type="text/javascript"> sweetalert = \'info\';  sweet_title = \''.$msg.'\'</script>');

        	return $store = bigNotice($msg);
        }
	}

	function postCategoryOptions($get_post = null) {
		global $SETT, $framework;

		// Set category select options for new posts
		$option = '';
		$category = $this->dbProcessor("SELECT id, title, value FROM categories", 1);
		foreach ($category as $row) { 
			$sel = (isset($_POST['category']) && $_POST['category'] == $row['value']) || ($get_post['category'] == $row['value']) ? ' selected="selected"' : ''; 
			$option .= '<option value="'.$row['value'].'"'.$sel.'>'.$row['title'].'</option>';
		}
		return $option;
	}

	function managePostsList() {
		global $SETT, $framework;

		$this->manage = true;
	    $framework->all_rows = $this->fetchPost(2);
	    $PTMPL['pagination'] = $framework->pagination();
		$list_posts = $this->fetchPost(2); 
		
		$table_row = ''; $i=0;
		if ($list_posts) {
			foreach ($list_posts as $post) {
				$i++; 

				$delete_link = urlQueryFix('delete', $post['id']);

				$edit_link = cleanUrls($SETT['url'].'/index.php?page='.$_GET['page'].'&view=create_post&post_id='.$post['id']);
				$view_link = cleanUrls($SETT['url'].'/index.php?page=post&post_id='.$post['id']);
				$set_status = $post['public'] == 1 ? 'Public' : 'Private';
				$set_featured = $post['featured'] == 1 ? 'Yes' : 'Not Featured';
				$set_promo = $post['featured'] == 1 ? 'Yes' : 'No';
				$views = $this->fetchStatistics(1, $post['id'])[0];
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
		} else {
			$table_row .= '
			<tr><td colspan="8">'.notAvailable('You have not created any posts', '', 1).'</td></tr>';
		}		
			return $table_row;
	}

	function manageStoreItems() {
		global $SETT, $LANG, $framework, $configuration;

		$this->manage = true;
	    $framework->all_rows = $this->fetchStore(2);
	    $PTMPL['pagination'] = $framework->pagination();
		$list_items = $this->fetchStore(2); 
		
		$table_row = ''; $i=0;
		if ($list_items) {
			foreach ($list_items as $item) {
				$i++;

				$delete_link = urlQueryFix('delete', $item['id']);

				$edit_link = cleanUrls($SETT['url'].'/index.php?page='.$_GET['page'].'&view=add_store_item&item_id='.$item['id']);
				$view_link = cleanUrls($SETT['url'].'/index.php?page=store&item_id='.$item['id']);
				$set_status = $item['public'] == 1 ? 'Public' : 'Private';
				$set_featured = $item['featured'] == 1 ? 'Yes' : 'Not Featured';
				$set_promo = $item['featured'] == 1 ? 'Yes' : 'No';
				$views = $this->extendedStatistics(1, $item['id'], 1)[0];
				$table_row .= '
				<tr>
					<th scope="row">'.$i.'</th>
					<td><a href="'.$view_link.'" title="View Content">'.$item['title'].'</a></td>
					<td>'.currency(3, $configuration['currency']).number_format($item['price'], 2).'</td>
					<td>'.$item['discount'].'</td>
					<td>'.$item['available_qty'].'</td>
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
		} else {
			$table_row .= '
			<tr><td colspan="10">'.notAvailable('You have not created any '.$LANG['store'].' items', '', 1).'</td></tr>';
		}		
		return $table_row;
	}

	function manageStoreOrders() {
		global $SETT, $framework, $configuration;

		$this->manage = true;
	    $framework->all_rows = $this->fetchStore(3);
	    $PTMPL['pagination'] = $framework->pagination();
		$list_items = $this->fetchStore(3); 
		
		$table_row = ''; $i=0;
		if ($list_items) {
			foreach ($list_items as $item) {
				$i++;

				$delete_link = urlQueryFix('delete', $item['id']);

				$view_link = cleanUrls($SETT['url'].'/index.php?page=moderate&view=store_orders&item_id='.$item['id']); 
				$status = $item['status'] == 2 ? 'Shipped' : ($item['status'] == 1 ? 'Confirmed' : 'Pending');
				$table_row .= '
				<tr>
					<th scope="row">'.$i.'</th>
					<td><a href="'.$view_link.'" title="Order Details">'.$item['fname'].' '.$item['lname'].'</a></td>
					<td>'.currency(3, $configuration['currency']).number_format($item['total'], 2).'</td>
					<td>'.$item['id'].'</td>
					<td>'.$item['reference'].'</td>
					<td>'.$this->fetchStore(4, $item['id'])[0]['count'].'</td>   
					<td>'.$status.'</td>   
					<td class="d-flex justify-content-around">
						<a href="'.$view_link.'" title="Order Details" class="btn btn-info btn-sm">Details</a>
						<a href="'.$delete_link.'" title="Delete Content"><i class="fa fa-trash text-danger hoverable"></i></a> 
					</td>
				</tr>';
			}
		} else {
			$table_row .= '
			<tr><td colspan="8">'.notAvailable('There are no '.$LANG['store'].' payment orders', '', 1).'</td></tr>';
		}		
		return $table_row;
	}
}

/* 
* Callback for decodeText()
*/
function decodeLink($text, $x=0) { 
    // If www. is found at the beginning add http in front of it to make it a valid html link
    $y = $x==1 ? 'primary-color' : 'secondary-color';

    if(substr($text[1], 0, 4) == 'www.') {
        $link = 'http://'.$text[1];
    } else {
        $link = $text[1];
    }
    return '<a class="'.$y.'" href="'.$link.'" target="_blank" rel="nofollow">'.$link.'</a>'; 
}
