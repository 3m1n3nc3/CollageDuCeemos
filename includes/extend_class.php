<?php 

use wapmorgan\Mp3Info\Mp3Info;

function messageNotice($str, $type = null, $size = '3', $iS = '2') {
    switch ($type) {
        case 1:
            $alert = 'success';
            $i = 'check-circle';
            break;

        case 2:
            $alert = 'warning';
            $i = 'question-circle';
            break;

        case 3:
            $alert = 'danger';
            $i = 'times-circle';
            break;

        default:
            $alert = 'info';
            $i = 'exclamation-circle';
            break;
    }
    $string = '
    <div class="p-2 mx-1 alert alert-' . $alert . '"> 
        <div class="d-flex">
            <i class="pr-4 fa fa-'.$iS.'x fa-'.$i.'"></i>
            <div class="flex-grow-1"><h'.$size.' class="text-center font-weight-bolder" style="margin-bottom: 0px;">' . $str . '</h'.$size.'></div>
        </div>
    </div>';
    return $string;
}

function bigNotice($str, $type = null, $alt = null) {
    switch ($type) {
        case 1:
            $alert = 'success';
            $i = 'check-circle';
            break;

        case 2:
            $alert = 'warning';
            $i = 'question-circle';
            break;

        case 3:
            $alert = 'danger';
            $i = 'times-circle';
            break;

        default:
            $alert = 'info';
            $i = 'exclamation-circle';
            break;
    }
    if ($alt) {
        $extra = $alt;
        
    } else {
        $extra = 'bg-light';
    }
    $string = '
    <div class="h1 d-flex text-'.$alert.' p-4 m-4 '.$extra.' rounded border border-'.$alert.'"> 
        <i class="pr-4 fa fa-'.$i.'"></i> 
        <div class="flex-grow-1"><div class="text-center">' . $str . '</div></div>
    </div>';
    return $string;
}

function seo_plugin($image, $twitter, $facebook, $desc, $title) {
    global $SETT, $PTMPL, $configuration, $site_image;

    $twitter = ($twitter) ? $twitter : $configuration['site_name'];
    $facebook = ($facebook) ? $facebook : $configuration['site_name'];
    $title = ($title) ? $title . ' ' : '';
    $titles = $title . 'On ' . $configuration['site_name'];
    $image = ($image) ? $image : $site_image;
    $alt = ($title) ? $title : $titles;
    $desc = rip_tags(strip_tags(stripslashes($desc)));
    $desc = strip_tags(myTruncate($desc, 350));
    $url = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

    $plugin = '
    <meta name="description" content="' . $desc . '"/>
    <link rel="canonical" href="' . $url . '" />
    <meta property="og:locale" content="en_US" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="' . $titles . '" />
    <meta property="og:url" content="' . $url . '"/>
    <meta property="og:description" content="' . $desc . '" />
    <meta property="og:site_name" content="' . $configuration['site_name'] . '" />
    <meta property="article:publisher" content="https://www.facebook.com/' . $configuration['site_name'] . '" />
    <meta property="article:author" content="https://www.facebook.com/' . $facebook . '" />
    <meta property="og:image" content="' . $image . '" />
    <meta property="og:image:secure_url" content="' . $image . '" />
    <meta property="og:image:width" content="1200" />
    <meta property="og:image:height" content="628" />
    <meta property="og:image:alt" content="' . $alt . '" />
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:description" content="' . $desc . '" />
    <meta name="twitter:title" content="' . $titles . '" />
    <meta name="twitter:site" content="@' . $configuration['site_name'] . '" />
    <meta name="twitter:image" content="' . $image . '" />
    <meta name="twitter:creator" content="@' . $twitter . '" />';
    return $plugin;
}

function getLocale($type = null, $id = null) {
    // $framework->
    global $framework;
    if ($type == 1) {
        $sql = sprintf("SELECT * FROM " . TABLE_CITIES . " WHERE state_id = '%s'", $id);
    } elseif ($type == 2) {
        $sql = sprintf("SELECT * FROM " . TABLE_STATES . " WHERE country_id = '%s'", $id);
    } else {
        $sql = sprintf("SELECT * FROM " . TABLE_COUNTRIES);
    }
    if ($type == 3) {
        $list = getLocale();
        $listed = '';
        foreach ($list as $name) {
            if ($id == $name['name']) {
                $selected = ' selected="selected"';
            } else {
                $selected = '';
            }
            $listed .= '<option id="' . $name['id'] . '" value="' . $name['name'] . '"' . $selected . '>' . $name['name'] . '</option>';
        }
        return $listed;
    } else {
        return $framework->dbProcessor($sql, 1);
    }
}

function fileInfo($file, $type = null) {
    $getID3 = new getID3;
    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    $source = $file;
    if ($ext == 'mp3' || $ext == 'wav') {
        $newFile = getAudio($source, 1);
    } else {
        $newFile = getFiles($source, 1);
    }
    if (file_exists($newFile) && is_file($newFile)) {
        if ($type) {
            // Use Mp3Info to get audio tags
            $audio_tags = new Mp3Info($newFile);
            return $duration = floor($audio_tags->duration / 60).':'.floor($audio_tags->duration % 60);
        } else {
            // Use getID3 to get file info
            $FileInfo = $getID3->analyze($newFile);
            return $FileInfo;
        }
    }
    return;
}

function getImage($image, $type = null) {
    // $a = 1: Get direct link to image
    global $SETT, $framework;
    if (!$image) {
      $dir = $SETT['url'] . '/uploads/img/';
      $image = 'default.png';
    }
    $default = $SETT['url'] . '/uploads/default.jpg';

    $c = null;
    if ($type == 1 || $type == 3) {
      // Uploaded images
      $dir_url = $SETT['url'] . '/uploads/';
      $_dir = $SETT['working_dir'].'/uploads/';
      $c = 1;
    } else {
      // Site specific images
      $dir_url = $SETT['url'] . '/' . $SETT['template_url'] . '/img/';
      $_dir = $SETT['template_url'] . '/img/';
    } 

    // Show the image
    if ($framework->trueAjax()) {
        if (file_exists($_dir.$image) && is_file($_dir.$image)) {
          $image = $dir_url.$image;
        } else {
          $image = $default;
        }
    } elseif ($type == 3)  {
        $image = $dir_url.$image;
        if (@exif_imagetype($image)) {
          $image = $image;
        } else {
            $image = $default;
        }
    } else {
        if (file_exists($_dir.$image) && is_file($_dir.$image)) {
          $image = $dir_url.$image;
        } else {
          $image = $default;
        }
    } 
    return $image;
}

function getVideo($source) {
    global $SETT, $framework;
    $link = $framework->determineLink($source);

    if (!$source) {
        $source = 'defaultvid.png';
        return $source = $SETT['url'] . '/uploads/videos/' . $source;
    }
    if ($link) {
        $source = $link;
    } else {
        $source = $SETT['url'] . '/uploads/videos/' . $source;
    }
    return $source;
} 

function getAudio($source, $t=null) {
    global $SETT, $framework;   

    $_source = $SETT['working_dir'].'/uploads/audio/' . $source;
    if (file_exists($_source) && is_file($_source)) {
        if ($t) {
            return $_source;    
        }
        return $source = $SETT['url'] . '/uploads/audio/' . $source;    
    }
    return;
} 

function getFiles($source, $t=null) {
    global $SETT, $framework;   

    $_source = $SETT['working_dir'].'/uploads/files/' . $source;
    if (file_exists($_source) && is_file($_source)) {
        if ($t) {
            return $_source;    
        }
        return $source = $SETT['url'] . '/uploads/files/' . $source;    
    }
    return;
} 

function fetchSocialInfo($profile, $type = null) {
    global $configuration, $framework, $user;
        
        // Array: database column name => url model

        $links = '';
        if ($type) {
            $social = array( 
                'facebook'      => array('https://facebook.com/%s', 'fb-ic'), 
                'twitter'       => array('https://twitter.com/%s', 'tw-ic'),
                'instagram'     => array('https://instagram.com/%s', 'ins-ic'),
                'gplus'         => array('https://plus.google.com/%s', 'gplus-ic')
            );  
            foreach($social as $value => $url) { 
                $class = $url[1];
                if ($type == 1) { 
                    $links .= ((!empty($profile[$value])) ? '
                    <li class="nav-item">
                        <a class="nav-link waves-effect waves-light" href="'.sprintf($url[0], $profile[$value]).'" rel="nofllow" title="Follow us on '.ucfirst($value).'">
                            <i class="fa '.icon(3, $value).'"></i>
                        </a>
                    </li>' : ''); 
                } elseif ($type == 2) {
                    $links .= ((!empty($profile[$value])) ? '
                    <a href="'.sprintf($url[0], $profile[$value]).'"  rel="nofllow" title="Find me on '.ucfirst($value).'" class="p-2 m-2 fa-lg '.$class.'"><i class="fa '.icon(3, $value).'"> </i></a>' : '');                        
                } else {   
                    $links .= ((!empty($profile[$value])) ? ' 
                    <li>
                        <a href="'.sprintf($url[0], $profile[$value]).'"  rel="nofllow" title="Find me on '.ucfirst($value).'" class="'.$class.'"> <i class="fa '.icon(3, $value).'"> </i> </a>
                    </li>' : '');             
                }
            }
        } else {
            $social = array(
                'site_office'   => array('%s', 'orange-text'),
                'facebook'      => array('https://facebook.com/%s', 'fb-ic'),
                'twitter'       => array('https://twitter.com/%s', 'tw-ic'),
                'instagram'     => array('https://instagram.com/%s', 'ins-ic'),
                'whatsapp'      => array('whatsapp:%s', 'green-text'),
                'email'         => array('mailto:%s', 'red-text')
            ); 
            foreach($social as $value => $url) {
                $_url = $url[0];
                if ($profile[$value] == $profile['site_office']) {
                    $icon = 'fa-map-marker'; 
                    $links = '
                    <li>
                        <i class="fa '.icon(3, $icon).' fa-2x '.$url[1].'"></i>
                        <p>
                            '.ucfirst($profile['site_office']).'
                        </p>
                    </li>'; 
                } else {
                    $icon = $profile[$value] !== $profile['email'] ? $value : 'envelope';
                    $links .= ((!empty($profile[$value])) ? '
                    <li>
                        <i class="fa '.icon(3, $icon).' fa-2x '.$url[1].'"></i>
                        <p>
                            <a href="'.sprintf($url[0], $profile[$value]).'" rel="nofllow" title="Follow us on '.ucfirst($value).'">'.$profile[$value].'</a>
                        </p>
                    </li>' : ''); 
                }
            } 
            $links = 
            '<ul class="contact-icons text-center list-unstyled">
                '.$links.'
            </ul>
            ';
        }
        return $links;
}

function userAction() {
    global $SETT, $configuration, $admin, $user, $user_role;

    if ($admin || $user) {
        $logout_url = cleanUrls($SETT['url'] . '/index.php?page=introduction&logout=admin');
        $logout_user = cleanUrls($SETT['url'] . '/index.php?page=introduction&logout=user');

        $user_link = cleanUrls($SETT['url'] . '/index.php?page=profile&update='.$user['uid']);
        $admin_link = cleanUrls($SETT['url'] . '/index.php?page=moderate&view=admin');

        $admin_out = $admin ? '<a href="'.$logout_url.'" class="dropdown-item" href="#">Admin Logout</a>' : '';
        $user_out = $user ? '<a class="dropdown-item" href="'.$logout_user.'">Account Logout</a>' : '';        

        $admin_account = $admin ? '<a class="dropdown-item" href="'.$admin_link.'">Admin Details</a>' : '';
        $user_account = $user ? '<a class="dropdown-item" href="'.$user_link.'">Update Profile</a>' : '';

        $dropdown = '
        <li class="nav-item dropdown">
            <a class="nav-link waves-effect waves-light dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" href="" rel="nofllow" title="Account Options">
                <i class="fa fa-user text-light"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                '.$user_account.'
                '.$admin_account.'
                <div class="dropdown-divider"></div> 
                '.$user_out.'
                '.$admin_out.'
            </div>
        </li>';
        return $dropdown;  
    }
}

/**
 * This function will convert your urls into cleaner urls
 **/
function cleanUrls($url) {
    global $configuration; //$configuration['cleanurl'] = 1;
    if ($configuration['cleanurl']) {
        $pager['homepage'] = 'index.php?page=homepage';
        $pager['introduction'] = 'index.php?page=introduction';
        $pager['static'] = 'index.php?page=static';
        $pager['post'] = 'index.php?page=post';
        $pager['events'] = 'index.php?page=events';
        $pager['listing'] = 'index.php?page=listing';

        if (strpos($url, $pager['homepage'])) {
            $url = str_replace(array($pager['homepage'], '&archive=', '&data='), array('homepage', '/archive/', '/data/'), $url);
        } elseif (strpos($url, $pager['introduction'])) {
            $url = str_replace(array($pager['introduction']), array('introduction'), $url);
        } elseif (strpos($url, $pager['post'])) {
            $url = str_replace(array($pager['post'], '&post_id=', '&id'), array('post', '/', '/'), $url);
        } elseif (strpos($url, $pager['static'])) {
            $url = str_replace(array($pager['static'], '&view=', '&id'), array('static', '/', '/'), $url);
        }  elseif (strpos($url, $pager['events'])) {
            $url = str_replace(array($pager['events'], '&view=', '&id'), array('events', '/', '/'), $url);
        } elseif (strpos($url, $pager['listing'])) {
            $url = str_replace(array($pager['listing'], '&sorting=', '&id'), array('listing', '/sort/', '/'), $url);
        }
    }
    return $url;
}
 
function accountAccess($type = null) {
    global $LANG, $PTMPL, $SETT, $settings;
    if ($type == 0) {
        $theme = new themer('homepage/signup');
        $footer = '';
    } else {
        $theme = new themer('homepage/login');
        $footer = '';
    }

    $OLD_THEME = $PTMPL;
    $PTMPL = array();

    $PTMPL['register_link'] = cleanUrls($SETT['url'] . '/index.php?page=account&register=true');


    $footer = $theme->make();
    $PTMPL = $OLD_THEME;
    unset($OLD_THEME);
    return $footer;
}

function manageButtons($type = null, $cid = null, $mid = null) {
    global $user_role, $user, $framework, $SETT;
    $link = '';

    if ($type == 0) {
        // Edit Module
        $link = cleanUrls($SETT['url'] . '/index.php?page=training&module=edit&moduleid=' . $mid);
    } elseif ($type == 1) {
        // Edit Course
        $link = cleanUrls($SETT['url'] . '/index.php?page=training&course=edit&courseid=' . $cid);
    } elseif ($type == 2) {
        $link = cleanUrls($SETT['url'] . '/index.php?page=training&module=add');
    } elseif ($type == 3) {
        $link = cleanUrls($SETT['url'] . '/index.php?page=training&course=add');
    }
    return $link;
}

function secureButtons($class, $title, $type, $cid, $mid, $x = null) {
    global $user, $user_role;
    $link = manageButtons($type, $cid, $mid);
    $gcrs = getCourses(1, $cid)[0];
    $gmd = getModules(2, $mid)[0];

    $class = $class ? ' ' . $class : '';
    $btnClass = $x ? '' : 'btn';
    $_btn = '';
    $btn = '<a href="' . $link . '" class="' . $btnClass . $class . '">' . $title . '</a>';
    $allow = 0;

    if ($type == 0) {
        // Edit Module
        if ($gmd['creator_id'] == $user['id']) {
            $allow = 1;
        }
    } elseif ($type == 1) {
        // Edit Course
        if ($gcrs['creator_id'] == $user['id']) {
            $allow = 1;
        }
    }
    if ($allow == 1 && ($type == 0 || $type == 1)) {
        $btn = $btn;
    } elseif ($user_role >= 2 && ($type == 0 || $type == 1)) {
        $btn = $btn;
    } elseif ($user_role >= 1 && ($type == 2 || $type == 3)) {
        $btn = $btn;
    } else {
        $btn = $_btn;
    }

    return $btn;
}

function simpleButtons($class, $title, $link, $x = null) {
    global $user, $user_role;

    $class = $class ? ' ' . $class : '';
    $btnClass = $x ? '' : 'btn';
    $btn = '<a href="' . $link . '" class="' . $btnClass . $class . '">' . $title . '</a>';

    return $btn;
}

/**
 * [deleteFile description]
 * @param  variable $name is the full qualified name including extension of the file to be deleted
 * @param  variable $type describes what is to be deleted; 0 or null for audio, 1 for photo, 2 for other files
 * @param  [variable $fb is used as fallback when an ajax xhr request type is not possible for a ajax request
 * @return integer       1 if successful of 0 if failed
 */
function deleteFile($name, $type = null, $fb = null) {
    global $SETT, $framework;
 
    if ($type == 1) {
        $path = 'photos/';
    } elseif ($type == 2) { 
        $dir = $SETT['working_dir'].'/'.$SETT['template_url'].'/img/'; 
    }  elseif ($type == 3) {
        $path = '';
    } else {
        $path = 'audio/';
    } 

    if ($type == 2) {
        $file = $dir.$name;
        $fallback = $file;
    } else {
        $fallback = $SETT['working_dir'] . '/uploads/' . $path . $name; 

        if ($framework->trueAjax() || $fb) {
            $file =  '../uploads/' . $path . $name;
        } else {
            $file =  getcwd() . '/uploads/' . $path . $name;
        }
    }

    if ($name !== 'default.jpg') {
        if (file_exists($file) && is_file($file)) {  
            clearstatcache();
            return unlink($file);
        } elseif (file_exists($fallback) && is_file($fallback)) { 
            clearstatcache();
            return unlink($fallback);  
        } 
    }
    return 0;
}

function notAvailable($string, $pad='', $type = null) {
    if (strlen($type) >= 3) {
        $title = '- '.$type.' -';
        $pad = 'text-danger';
        if ($type == 403) {
            $string = 'You do not have sufficient privileges to access the resource you requested!';
        } elseif ($type == 404) {
            $string = 'The resource you requested was not found on this server!';
        }
        $type = 2;
    } else {
        $title = 'No content to see here';
    }
    if ($type == 1) {
        $return = 
        '<div class="p-5 text-center shadow-sm border border-info '.$pad.'">
            <div class="'.$pad.'pad-section">  
                <i class="'.$pad.' fa fa-question-circle"></i>
                <p class="small">' . $string . '</p> 
            </div>
        </div>';
    } elseif ($type == 2) {
        $return = 
        '<div class="row mb-4"> 
            <div class="my-5"> 
                <div class="card"> 
                    <div class="card-body p-5"> 
                        <h1 class="card-title d-flex justify-content-center">
                            <strong class="text-default">'.$title.'</strong>
                        </h1>
                        <hr>
                        <h1 class="text-center"><i class="'.$pad.' fa fa-question-circle"></i></h1>
                        <div class="row my-3 mx-3 d-flex justify-content-center">
                            <h2 class="'.$pad.'">' . $string . '</h2> 
                        </div>
                    </div>
                </div>
            </div>
        </div>';
    } else {
        if ($pad == '') {
            $pad = 'display-1';
        }
        $return = 
        '<div class="text-center">
            <div class="p-4 m-4 border rounded border-info bg-light text-info">
                <i class="'.$pad.' ion-ios-help-circle-outline"></i>
                <p class="'.$pad.'">'.$string.'</p>
            </div>
        </div>';
    }
    return $return;
} 

function restrictedContent($content, $tab = null) {
    global $LANG, $SETT, $PTMPL, $contact_, $configuration, $framework, $user, $user_role;
    if (isset($tab)) {
        if ($content == 1) {
            $theme = new themer('project/restricted_tabs'); $section = '';
        }
    } else {
        if ($content == 1) {
            $theme = new themer('project/restricted_tabs_content'); $section = '';
        } elseif ($content == 2) {
            $theme = new themer('project/display_project_stems'); $section = '';
        } elseif ($content == 3) {
            $theme = new themer('project/display_manage_buttons'); $section = '';
        }
    }

    $section = $theme->make();
    return $section;
}

function globalTemplate($type = null, $jar = null) {
    global $LANG, $SETT, $PTMPL, $contact_, $configuration, $framework, $collage, $user, $admin, $user_role;

    $PTMPL['home_url'] = cleanUrls($SETT['url'] . '/index.php?page=homepage');
    $PTMPL['introduction_url'] = cleanUrls($SETT['url'] . '/index.php?page=introduction');
    $PTMPL['artist_page_url'] = cleanUrls($SETT['url'] . '/index.php?page=listing&sorting=catalog&type=artist');
    $PTMPL['event_page_url'] = cleanUrls($SETT['url'] . '/index.php?page=events');
    $PTMPL['about_page_url'] = cleanUrls($SETT['url'] . '/index.php?page=static&view=about');
    $PTMPL['contact_page_url'] = cleanUrls($SETT['url'] . '/index.php?page=static&view=contact'); 

    $PTMPL['social_links'] = fetchSocialInfo($configuration, 1);  
    $PTMPL['site_name'] = $configuration['site_name'];  

    if ($admin || $user['founder'] || $user_role >= 4) {
        $moderate = cleanUrls($SETT['url'] . '/index.php?page=moderate');
        $PTMPL['admin_url'] = '<a href="'.$moderate.'" class="ml-3"><i class="fa fa-cog"></i> Site Admin </a>'; 
        $PTMPL['social_links'] .= userAction();         
    }
 
    // Set footer navigation links
    $nav_list = $foot_list = $foot_list_var = '';
    $collage->limit = 10;
    $collage->start = 0;
    $collage->parent = 'static'; 
    $collage->priority = null;
    $navis = $collage->fetchStatic( null, 1 );
    if ($navis) {
        $i = 1;
        foreach ($navis as $link) {
            $i++;
            $view_link = cleanUrls($SETT['url'].'/index.php?page=static&view='.$link['safelink']);
            if ($link['header'] == '1') {
                $hs = 1;
                $nav_list .= '<a class="dropdown-item waves-effect waves-light font-weight-bold" href="'.$view_link.'">'.$link['title'].'</a>';
            } elseif ($link['footer'] == '1') {
                if ($i > 6) {
                    $foot_list_var .= '<li><a href="'.$view_link.'">'.$link['title'].'</a></li>';
                } else {
                    $foot_list .= '<li><a href="'.$view_link.'">'.$link['title'].'</a></li>';
                }
            }
        }

        $PTMPL['content_menu_link'] = isset($hs) ? '
        <li class="nav-item dropdown ml-4 mb-0">
            <a class="nav-link dropdown-toggle waves-effect waves-light font-weight-bold"
            id="contentMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> CONTENT </a>
            <div class="dropdown-menu dropdown-primary dropdown-menu-right" aria-labelledby="contentMenuLink">
                 '.$nav_list.'
            </div>
        </li>' : '';
        $PTMPL['footer_list'] = $foot_list;  
        $PTMPL['footer_list_var'] = $foot_list_var;    
    } 

    $categ = $collage->fetchCategories(1); 
    if ($categ) {
        $nav_li = ''; 
        foreach ($categ as $row) {  
            $link = cleanUrls($SETT['url'].'/index.php?page=listing&sorting='.$row['value']);  
            $nav_li .= '<a class="dropdown-item waves-effect waves-light font-weight-bold" href="'.$link.'">'.$row['title'].'</a>'; 
        }    

        $PTMPL['artist_drop'] = '
        <li class="nav-item dropdown ml-4 mb-0">
            <a class="nav-link dropdown-toggle waves-effect waves-light font-weight-bold"
            id="artistMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> ARTISTS </a>
            <div class="dropdown-menu dropdown-primary dropdown-menu-right" aria-labelledby="contentMenuLink">
                 '.$nav_li.'
            </div>
        </li>';           
    }

    $collage->reverse = $collage->limit = $collage->start = $collage->parent = $collage->priority = null;

    $theme = new themer('navigation/nav_bar');
    $PTMPL['navigation'] = $theme->make();

    if ($type) {
        $theme = new themer('navigation/header'); $section = '';
    } else {
        $theme = new themer('navigation/footer'); $section = '';
    }
    $section = $theme->make();
    return $section;
}  

function getPage($page = null) {
    if ($page == 'artist') {
        $page = 'profile';
    } elseif ($page == 'listen') {
        if ($_GET['to'] == 'albums') {
            $page = 'albums';
        } elseif ($_GET['to'] == 'tracks') {
            $page = 'tracks';
        }
    } elseif ($page == 'playlist') { 
        $page = 'playlist';
    } elseif ($page == 'view_artists') { 
        $page = 'artists';
    } elseif ($page == 'follow') { 
        if ($_GET['get'] == 'followers') {
            $page = 'followers';
        } elseif ($_GET['get'] == 'following') {
            $page = 'following';
        } 
    } elseif ($page == 'project') { 
        $page = 'projects'; 
    } elseif ($page == 'homepage') { 
        $page = 'homepage'; 
    } else {
        $page = $page;
    }
    return $page;
} 

/**
 * Create the links for the navigation navbar nav of the distribution portal
**/
function superNavigation($user_id) {
    global $SETT, $user, $framework, $databaseCL;  
    $new_release = cleanUrls($SETT['url'] . '/index.php?page=distribution&action=new_release');
    $all_releases = cleanUrls($SETT['url'] . '/index.php?page=distribution&action=releases');
    $artist_services = cleanUrls($SETT['url'] . '/index.php?page=distribution&action=artist-services');
    $reports = cleanUrls($SETT['url'] . '/index.php?page=distribution&action=reports');
    $support = cleanUrls($SETT['url'] . '/index.php?page=distribution&action=support');

    $linkers = array(
        'new_release' => array('New Release',  $new_release), 
        'releases' => array('Discography',  $all_releases),
        'artist-services' => array('Artist Services',  $artist_services),
        'support' => array('Community and Support',  $support),
    );
    
    $rows = '';
    foreach ($linkers as $key => $value) {
        if ($key == $pager) {
            $active = ' class="active"';
        } else {
            $active = '';
        }
        $rows .= '<li'.$active.'><a href="'.$value.'">'.strtoupper($key).'</a></li>';
    }
}

/**
 * Create the links for the navigation navbar nav of the secondary user navigation
**/
function secondaryNavigation($user_id) {
    global $SETT, $user, $framework, $databaseCL; 
    $artist = $framework->userData($user_id, 1);
    $followers = cleanUrls($SETT['url'] . '/index.php?page=follow&get=followers&artist='.$artist['uid']);
    $tracks = cleanUrls($SETT['url'] . '/index.php?page=listen&to=tracks&artist='.$artist['uid']);
    $albums = cleanUrls($SETT['url'] . '/index.php?page=listen&to=albums&artist='.$artist['uid']);
    $artists = cleanUrls($SETT['url'] . '/index.php?page=view_artists&artist='.$artist['uid']);
    $home = cleanUrls($SETT['url'] . '/index.php?page=artist&artist='.$artist['username']);
    $playlists = cleanUrls($SETT['url'] . '/index.php?page=playlist&playlist=list&creator='.$artist['uid']);
    $projects = cleanUrls($SETT['url'] . '/index.php?page=project&creator='.$artist['uid']);

    $linkers = array('profile' => $home, 'projects' => $projects, 'albums' => $albums, 'tracks' => $tracks, 'playlist' => $playlists, 'artists' => $artists, 'followers' => $followers);
    $pager = getPage($_GET['page']); 
    $rows = '';
    foreach ($linkers as $key => $value) {
        if ($key == $pager) {
            $active = ' class="active"';
        } else {
            $active = '';
        }
        $rows .= '<li'.$active.'><a href="'.$value.'">'.strtoupper($key).'</a></li>';
    }

    $card = '
    <div class="custom-navigation">
        <nav class="navbar navbar-default">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#passNavigation">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button> 
                </div>
                <div class="collapse navbar-collapse" id="passNavigation">
                    <ul class="nav navbar-nav navbar-right">
                        '.$rows.'
                    </ul>
                </div>
            </div>
        </nav>
    </div>
    ';
    return $user ? $card : '';
}  

function archiveLinks() {
    global $SETT, $PTMPL, $user, $framework, $collage, $marxTime; 
    $archive = $marxTime->yearMonthlyArray();
    $month = '';
    $i = 0;$t_month = date('m');
    foreach ($archive as $key => $val) {
        $i++; 
        if ($i == $t_month+1) break;
        $date = date('Y-m-d',strtotime($val[1].'-'.$val[0].'-'.$val[2]));
        $link = cleanUrls($SETT['url'].'/index.php?page=homepage&archive='.$val[0].'&data='.$date);
        $month .= '
        <li>
            <p class="mb-1 mt-2">
                <a href="'.$link.'" class="dark-grey-text">'.ucfirst($val[0]).' '.$val[2].'</a>
            </p>
        </li>
        ';
    }

    $card = 
    '<div class="row mb-4"> 
        <div class="col-md-12 text-center"> 
            <ul class="list-unstyled">
                '.$month.'
            </ul> 
        </div> 
    </div>
    ';
    return $card;
}

function userCard($id = null, $type = null) {
    global $SETT, $PTMPL, $user, $framework, $collage; 
    $profile = $framework->userData($id, 1);
    $name = $framework->realName($profile['username'],$profile['fname'], $profile['lname']);
    $intro = $framework->myTruncate($profile['intro'], 200);
    $photo = getImage($profile['photo'], 1);
    
    if ($type == 1) {
        $card ='
        <div class="row mb-lg-4 text-center text-md-left team-activator"> 
            <div class="col-lg-12 col-md-12 mb-4"> 
                <div class="col-md-3 float-left">
                    <div class="avatar mx-auto mb-md-0 mb-3">
                        <img src="'.$photo.'" class="z-depth-1" alt="'.$name.'">
                    </div>
                </div> 
                <div class="col-md-8 float-right">
                    <h4><strong>'.$name.'</strong></h4>
                    <h6 class="font-weight-bold grey-text mb-4">'.$profile['qualification'].'</h6>
                    <p class="grey-text">'.$profile['intro'].'</p> 
                    '.fetchSocialInfo($profile, 2).'
                </div> 
            </div>  
        </div>
        ';       
    } else {
        $card = '
        <div class="card">
            <div class="view overlay">
                <img src="'.$photo.'" class="card-img-top" alt="'.$name.'">
                <a>
                    <div class="mask rgba-white-slight"></div>
                </a>
            </div>
            <div class="card-body">
                <h5 class="card-title dark-grey-text text-center grey lighten-4 py-2">
                <strong>'.$name.'</strong>
                </h5>
                <p class="mt-3 dark-grey-text font-small text-center">
                    <em>'.$profile['intro'].'</em>
                </p>
                <ul class="list-unstyled list-inline-item circle-icons list-unstyled flex-center">
                    '.fetchSocialInfo($profile, 3).'
                </ul>
            </div>
        </div>
        ';
    }
    return $card;
}

function eventsCard($image = '', $title = '', $details = '', $date = '', $link = '') {
    global $SETT, $PTMPL, $user, $framework, $collage, $marxTime; 
    $details = $framework->rip_tags($details);
    $details = $framework->myTruncate($details, 150);
    $card = '
    <div class="col-lg-4 col-md-12 mb-4"> 
        <div class="view overlay z-depth-1 mb-2" style="max-height: 100px;">
            <img src="'.$image .'" class="img-fluid"
            alt="First sample image">
            <a>
                <div class="mask rgba-white-slight"></div>
            </a>
        </div>
        <h4 class="mb-2 mt-4 font-weight-bold">'.$title .'</h4> 
        <div class="row"> 
            <div class="col-12 ">
                <p class="grey-text">
                <i class="far fa-clock-o" aria-hidden="true"></i> '.$date.' </p>
            </div> 

        </div> 
        <p class="dark-grey-text">'.$details.'</p>
        <a href="'.$link.'" class="text-uppercase font-small font-weight-bold spacing">Read more</a>
        <hr class="mt-1" style="max-width: 90px">
    </div>';
            // <div class="col-lg-6 col-md-6 text-lg-left">
            //     <p class="grey-text">
            //     <i class="far fa-comment-dots" aria-hidden="true"></i> 6 Comments</p>
            // </div>   
            // Place this next after the date col and change date col to correspond with comments col
    return $card;
}

function popularCard() {
    global $SETT, $PTMPL, $user, $framework, $collage, $marxTime; 
    $popular = $collage->fetchPopular(); 
    $card = '';
    foreach ($popular as $key => $post) { 
        $image = getImage($post['image'], 1);
        $date = $marxTime->dateFormat($post['date'], 2);
        $link = cleanUrls($SETT['url'].'/index.php?page=post&post_id='.$post['id']);
        $card .= 
            '<div class="single-post">
                <div class="row mb-4">
                    <div class="col-5">
                        <div class="view overlay" style="max-height: 50px;">
                            <img src="'.$image.'"
                            class="img-fluid z-depth-1 rounded-0" alt="sample image">
                            <a>
                                <div class="mask rgba-white-slight"></div>
                            </a>
                        </div>
                    </div>
                    
                    <div class="col-7">
                        <h6 class="mt-0 font-small">
                        <a href="'.$link.'">
                            <strong>'.$post['title'].'</strong>
                        </a>
                        </h6>
                        <div class="post-data">
                            <p class="font-small grey-text mb-0">
                            <i class="far fa-clock-o"></i> '.$date.'</p>
                        </div>
                    </div>
                </div>
            </div>
            ';
    }
    return $card;
}

/**
 * This function fills different cards with the supplied details
 * @param  variable $type specifies the card to return
 * @return returns          a card layout
 */
function cardLayout($type = 0, $image = '', $title = '', $details = '', $author = '', $date = '', $link = '', $category = '') {
    global $SETT, $framework, $collage;
     $hide = '';
    if (is_array($type)) {
        if ($type[1] == 'hide') {
            $hide = ' d-none d-lg-block';
        }
        $type = $type[0];
    }
    $details = $framework->rip_tags($details);
    $trunc = $type == 0 ? 500 : 200;
    $details = $framework->myTruncate($details, $trunc);

    $category = $collage->fetchCategories(null, $category)[0]; 
    $ct_link = cleanUrls($SETT['url'].'/index.php?page=listing&sorting='.$category['value']);  

    if ($type == 1) {
        $card = ' 
            <div class="col-md-6 mb-4'.$hide.'"> 
                <div class="card text-left"> 
                    <div class="view overlay" style="max-height: 235px;">
                        <img src="'.$image.'" class="card-img-top" alt="'.$title.'">
                        <a>
                            <div class="mask rgba-white-slight"></div>
                        </a>
                    </div> 
                    <div class="card-body mx-4">
                        <a href="" class="teal-text text-center text-uppercase font-small">
                            <h6 class="mb-3 mt-3">
                            <strong>'.$category.'</strong>
                            <a class="dark-grey-text font-small"> - '.$date.'</a>
                            </h6>
                        </a> 
                        <h4 class="card-title">
                        <strong>'.$title.'</strong>
                        </h4>
                        <hr> 
                        <p class="dark-grey-text mb-4">'.$details.'</p>
                        <p class="text-right mb-0 text-uppercase font-small spacing font-weight-bold">
                            <a href="'.$link.'">read more
                                <i class="fa fa-chevron-right" aria-hidden="true"></i>
                            </a>
                        </p>
                    </div> 
                </div> 
            </div>
        '; 
    } elseif ($type == 2 || $type == 3) {  
        $card = '
            <div class="card mb-4'.$hide.'">
                <div class="view overlay">
                    <img src="'.$image.'" class="card-img-top" alt="'.$title.'">
                    <a>
                        <div class="mask"></div>
                    </a>
                </div>
                <div class="card-body">
                    <a class="activator mr-3"><i class="fa fa-share-alt"></i></a> 
                    <h4 class="card-title">'.$title.'</h4>
                    <hr>
                    <p class="card-text">'.$details.'</p>
                    <a class="link-text" href="'.$link.'">
                        <h5>Read more <i class="fa fa-chevron-right"></i></h5>
                    </a>
                </div>
            </div>
        ';
    } else {
        $card = ' 
            <div class="col-md-12'.$hide.' mb-5"> 
                <div class="card"> 
                    <div class="view overlay" style="max-height: 350px;">
                        <img src="'.$image.'" class="card-img-top" alt="'.$title.'">
                        <a>
                            <div class="mask rgba-white-slight"></div>
                        </a>
                    </div> 
                    <div class="card-body mx-4"> 
                        <h4 class="card-title">
                        <strong>'.$title.'</strong>
                        </h4>
                        <hr> 
                        <p class="dark-grey-text mb-3">'.$details.'</p>
                        <p class="font-small font-weight-bold blue-grey-text mb-1">
                            <i class="fa fa-clock-o"></i> '.$date.' - 
                            <a href="'.$ct_link.'" class="blue-grey-text">'.ucfirst($category['title']).'</a>
                        </p>
                        <p class="font-small dark-grey-text mb-0 font-weight-bold">'.$author.'</p>
                        <p class="text-right mb-0 text-uppercase dark-grey-text font-weight-bold">
                            <a href="'.$link.'">read more
                                <i class="fa fa-chevron-right" aria-hidden="true"></i>
                            </a>
                        </p>
                    </div> 
                </div> 
            </div>  
        '; 
    }
    return $card;
}

/**
 * This function fills different cards with the supplied details like the cardLayout() function just an 
 * alternate version to reduce ambiguity and congestion
 * @param  variable $type specifies the card to return
 * @return returns          a card layout
 */
function listCardLayout($type = 0, $image = '', $title = '', $details = '', $date = '', $link = '', $category = '') {
    global $SETT, $framework;
     $hide = '';
    if (is_array($type)) {
        if ($type[1] == 'hide') {
            $hide = ' d-none d-lg-block';
        }
        $type = $type[0];
    }
    $details = $framework->rip_tags($details);
    $details = $framework->myTruncate($details, 200);

    $page = $SETT['url'].$_SERVER['REQUEST_URI'];
    if (isset($_GET['sorting'])) {
        $page = str_replace('&sorting='.$_GET['sorting'], '', $page);
    }

    if ($type == 1) {
        $card = '
            <div class="row">
                <div class="col-lg-5 mb-4'.$hide.'">
                    <div class="view overlay z-depth-1">
                        <img src="'.$image.'" class="img-fluid" alt="'.$title.'">
                        <a>
                            <div class="mask rgba-white-slight"></div>
                        </a>
                    </div>
                </div>
                <div class="col-lg-6 ml-xl-4 mb-4">
                    <div class="row">
                        <div class="col-xl-2 col-md-6 text-sm-center text-md-right text-lg-left">
                            <p class="orange-text font-small font-weight-bold mb-1 spacing">
                                <a href="'.$page.'&sorting='.$category.'" class="orange-text">
                                    <strong>'.ucfirst($category).'</strong>
                                </a>
                            </p>
                        </div>
                        <div class="col-xl-5 col-md-6 text-sm-center text-md-left">
                            <p class="font-small grey-text">
                                <em>'.$date.'</em>
                            </p>
                        </div>
                    </div>
                    <h4 class="mb-3 dark-grey-text mt-0">
                        <strong>
                            <a href="'.$link.'" class="dark-grey-text">'.$title.'</a>
                        </strong>
                    </h4>
                    <p class="dark-grey-text">'.$details.'</p>
                    <a href="'.$link.'" class="btn btn-deep-orange btn-rounded btn-sm">Read more</a>
                </div>
            </div>
            <hr class="mb-5">
        '; 
    } 
    return $card;
}

/**
 * generates the card layout for the post supplied with details
 * @param  variable  $post_id is the id of the post to fetch
 * @param  integer $type    is the type of card to return
 * @return variable           containing the formated card details
 */
function big_postCard($post_id = null, $type = 0) {
    global $SETT, $PTMPL, $user, $framework, $collage, $marxTime; 

    $post = $collage->fetchPost(1, $post_id)[0]; 
    $profile = $framework->userData($post['user_id'], 1);

    $image = getImage($post['image'], 1);
    $date = $marxTime->dateFormat($post['date'], 2);
    $link = cleanUrls($SETT['url'].'/index.php?page=post&post_id='.$post['safelink']);

    $author = $framework->realName($profile['username'],$profile['fname'], $profile['lname']);

    $card = cardLayout($type, $image, $post['title'], $post['details'], $author, $date, $link, $post['category']); 
    return $card;
}

function postIntro($title, $quote, $sub_title = null) {
    global $SETT, $PTMPL, $user, $framework;
    $template = new themer('posts/intro'); $section = '';

    $retitle = explode(' ', $title);
    $retitle = str_replace($retitle[0], '<strong>'.$retitle[0].'</strong>', $title);

    $PTMPL['main_title'] = $retitle;

    if ($sub_title) {
        $PTMPL['sub_title'] = '<p class="grey-text text-center mb-4 text-uppercase spacing">'.$sub_title.'</p>';
    } 
    $PTMPL['post_quote'] = $quote;

    $section = $template->make(); 
    return $section; 
}

function postFooter($reverse = null) {
    global $SETT, $PTMPL, $configuration, $user, $framework, $collage, $marxTime; 
    $template = new themer('posts/pre_footer'); $section = '';

    // Fetch newer posts
    $collage->public = 1;
    $posts = $collage->fetchPost();      

    // Fetch Older posts
    $collage->reverse = 1;
    $old_posts = $collage->fetchPost(); 

    $cards =  
        '
        <div class="row%s">
            <div class="col-4">
                
                <div class="view overlay z-depth-1 mb-3" style="max-height: 70px;">
                    <img src="%s" class="img-fluid" alt="Post">
                    <a>
                        <div class="mask rgba-white-slight"></div>
                    </a>
                </div>
            </div>
            
            <div class="col-8 mb-1">
                
                <div class="">
                    <p class="mb-1">
                        <a href="%s" class="text-hover font-weight-bold">%s</a>
                    </p>
                    <p class="font-small grey-text">
                        <em>%s</em>
                    </p>
                </div>
            </div>
        </div>
        ';

    if ($posts) {
        $list_posts = ''; $top = ' mt-4';
        $i = 0;
        foreach ($posts as $pst => $post) {
            $i++;
            if ($i == 5) break;
            if ($i == 2) $top = ''; 

            $image = getImage($post['image'], 1);
            $date = $marxTime->dateFormat($post['date'], 3);
            $link = cleanUrls($SETT['url'].'/index.php?page=post&post_id='.$post['id']);
            $list_posts .= sprintf($cards, $top, $image, $link, $post['title'], $date);

        }
        $PTMPL['list_latest_post'] = $list_posts; 
    } else {
        $PTMPL['list_latest_post'] = notAvailable('No post have been added', 'text-info', 1);
    }

    if ($old_posts) {
        $list_posts = ''; $top = ' mt-4';
        $i = 0;
        foreach ($old_posts as $pst => $post) {
            $i++;
            if ($i == 5) break;
            if ($i == 2) $top = ''; 

            $image = getImage($post['image'], 1);
            $date = $marxTime->dateFormat($post['date'], 3);
            $link = cleanUrls($SETT['url'].'/index.php?page=post&post_id='.$post['id']);
            $list_posts .= sprintf($cards, $top, $image, $link, $post['title'], $date);

        } 
        $PTMPL['list_older_post'] = $list_posts;
    } else {
        $PTMPL['list_older_post'] = notAvailable('No post have been added', 'text-info', 1);
    }
    $PTMPL['promo_post'] = featuredPost();

    $section = $template->make(); 
    return $section; 
}

function site_sidebar() {
    global $SETT, $PTMPL, $configuration, $user, $admin, $user_role, $framework, $collage; 
    $template = new themer('posts/sidebar'); $section = '';

    $collage->public = 1;
    $post_id = isset($_GET['post_id']) ? $_GET['post_id'] : null;
    $post = $collage->fetchPost(1, $post_id)[0];
    $update_id = '';
    if ($post) {
        // $PTMPL['user_card'] = userCard($post['user_id']);
        $update_id = '&post_id='.$post['id'];
    }

    if ($admin || $user['founder'] || $user_role >= 4) {
        $create_post_link = cleanUrls($SETT['url'].'/index.php?page=moderate&view=create_post'.$update_id);
        $PTMPL['create_post_btn'] = '<a href="'.$create_post_link.'" class="btn btn-primary font-weight-bolder btn-block mb-2">Create new blog post</a>';
    }
    $PTMPL['advert_unit_one'] = $configuration['ads_off'] == 0 && $configuration['ads_1'] ? '
    <section class="my-5">
        <div class="card card-body py-0 px-0">
            <div class="single-post">
                <p class="font-weight-bold dark-grey-text text-center spacing grey lighten-4 py-2 my-1">
                    <strong>ADVERT</strong>
                </p>
                <div class="pb-0">'.$configuration['ads_1'].'</div>
            </div>
        </div>
    </section>' : '';

    $PTMPL['popular_posts'] = popularCard();
    $PTMPL['archives'] = archiveLinks();

    $section = $template->make(); 
    return $section; 
}

function moderate_sidebar() {
    global $SETT, $PTMPL, $user, $framework, $collage; 
    $template = new themer('moderate/side_bar'); $section = '';

    $PTMPL['create_post_link'] = cleanUrls($SETT['url'].'/index.php?page=moderate&view=create_post');
    $PTMPL['static_content_link'] = cleanUrls($SETT['url'].'/index.php?page=moderate&view=static');
    $PTMPL['posts_content_link'] = cleanUrls($SETT['url'].'/index.php?page=moderate&view=posts');
    $PTMPL['admin_link'] = cleanUrls($SETT['url'].'/index.php?page=moderate&view=admin');
    $PTMPL['cofiguration_link'] = cleanUrls($SETT['url'].'/index.php?page=moderate&view=config'); 
    $PTMPL['admin_url'] = cleanUrls($SETT['url'] . '/index.php?page=moderate'); 

    if (isset($_GET['view'])) {
        if ($_GET['view'] == 'posts') {
            $PTMPL['p_active'] = ' active';
        } elseif ($_GET['view'] == 'config') {
            $PTMPL['c_active'] = ' active';
        } elseif ($_GET['view'] == 'admin') {
            $PTMPL['a_active'] = ' active';
        } elseif ($_GET['view'] == 'config') {
            $PTMPL['c_active'] = ' active';
        } elseif ($_GET['view'] == 'static') {
            $PTMPL['s_active'] = ' active';
        }
    } else {
        $PTMPL['ss_active'] = ' active';
    }
    $collage->featured = 1;
    $featured_posts = $collage->fetchPost(2);
    if ($featured_posts) {
        $post_card = '<label for="list-group" class="font-weight-bold">Featured Article</label>'; $top = ' mt-4';
        $i = 0;
        foreach ($featured_posts as $pst => $post) {
            $i++;
            $post_card .= big_postCard($post['id'], [2, 'hide']);
        } 
        $PTMPL['feature_post'] = $post_card; 
    }
    $collage->featured = null;

    $section = $template->make(); 
    return $section; 
}

function profile_sidebar($id = null) {
    global $SETT, $PTMPL, $user, $framework, $collage; 
    $template = new themer('profile/sidebar'); $section = '';

    $PTMPL['view_profile_link'] = cleanUrls($SETT['url'].'/index.php?page=profile&id='.$id); 
    $PTMPL['edit_profile_link'] = cleanUrls($SETT['url'].'/index.php?page=profile&update='.$id); 

    if (isset($_GET['view'])) {
        if ($_GET['view'] == 'posts') {
            $PTMPL['p_active'] = ' active';
        } elseif ($_GET['view'] == 'config') {
            $PTMPL['c_active'] = ' active';
        } elseif ($_GET['view'] == 'admin') {
            $PTMPL['a_active'] = ' active';
        } elseif ($_GET['view'] == 'config') {
            $PTMPL['c_active'] = ' active';
        } elseif ($_GET['view'] == 'static') {
            $PTMPL['s_active'] = ' active';
        }
    } else {
        $PTMPL['ss_active'] = ' active';
    }
    $collage->featured = 1;
    $featured_posts = $collage->fetchPost(2);
    if ($featured_posts) {
        $post_card = '<label for="list-group" class="font-weight-bold">Featured Article</label>'; $top = ' mt-4';
        $i = 0;
        foreach ($featured_posts as $pst => $post) {
            $i++;
            $post_card .= big_postCard($post['id'], [2, 'hide']);
        } 
        $PTMPL['feature_post'] = $post_card; 
    }
    $collage->featured = null;

    $section = $template->make(); 
    return $section; 
}

function featuredPost($type = null) {
    global $SETT, $PTMPL, $configuration, $user, $framework, $collage; 

    $today = $framework->dbProcessor("SELECT * FROM posts WHERE date(`promo_date`) = CURDATE()", 1);
    if ($today) {
        $key = array_rand($today);
        $today = $today[$key];

        $image = getImage($today['image'], 1);
        $link = cleanUrls($SETT['url'].'/index.php?page=post&post_id='.$today['safelink']);
        $details = $framework->rip_tags($today['details']); 
        $details = $framework->myTruncate($details, 150); 
        $post = '
        <div class="col-lg-4 col-md-12">
            <h6 class="font-weight-bold mt-5 mb-3">FEATURED TODAY</h6>
            <hr class="mb-5">
            <img src="'.$image.'" alt="sample image" class="img-fluid z-depth-1" style="max-height: 200px;">
            <p class="mt-4 mb-5">'.$details.'
                <a href="'.$link.'">Read more <i class="fa fa-chevron-right" aria-hidden="true"></i> </a>
            </p>
        </div>';        
    } else {
        // Update todays featured post
        $collage->promoted = 1;
        $rand = $collage->fetchPost(2);
        $k = array_rand($rand);
        $new_id = $rand[$k]['id'];
        $framework->dbProcessor("UPDATE posts SET `promo_date` = CURDATE() WHERE `id` = '$new_id'", 0, 1);
        $collage->promoted = null;
        $post = '';
    }
    return $post;
}

function jarallax($image = '', $type = null, $title = '', $sub = '', $buttons = null) {
    global $SETT, $PTMPL, $configuration, $user, $framework, $collage; 
    $template = new themer('navigation/jarallax'); $section = '';

    if ($type == 1) {
        $theme = new themer('coder/big-jarallax-styles');
    } elseif ($type == null || $type == 2) {
        $theme = new themer('coder/jarallax-styles');
    }
    if ($title) {
        $PTMPL['title'] = $title;
    } else {
        $PTMPL['title'] = $configuration['site_name'];
    }
    if ($sub) {
        $PTMPL['sub_title'] = $sub;
    } else {
        $PTMPL['sub_title'] = $configuration['slug'];
    }
    
    // '<a class="btn btn-outline-white wow fadeInDown" data-wow-delay="0.4s">Portfolio</a>';
    if ($buttons) {
        $PTMPL['buttons'] = $buttons;
    } 

    $PTMPL['header_scripts'] = $theme->make();
    if ($type == 2) {
        $PTMPL['jarallax_image'] = getImage($image);
    } else {
        $PTMPL['jarallax_image'] = getImage($image, 1);
    }

    $section = $template->make();
    return $section; 
}
