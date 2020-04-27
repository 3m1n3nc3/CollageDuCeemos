<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2014 - 2017, British Columbia Institute of Technology
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package	CodeIgniter
 * @author	EllisLab Dev Team
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc. (https://ellislab.com/)
 * @copyright	Copyright (c) 2014 - 2017, British Columbia Institute of Technology (http://bcit.ca/)
 * @license	http://opensource.org/licenses/MIT	MIT License
 * @link	https://codeigniter.com
 * @since	Version 2.0.0
 * @filesource
 */



// --------------------------------------------------------------------

if ( ! function_exists('parseURL'))
{
  /*
    |--------------------------------------------------------------------------
    | Check domain
    |--------------------------------------------------------------------------
    |
    | Parse and check the URL Sets the following array parameters
    | scheme, host, port, user, pass, path, query, fragment, dirname, basename, filename, extension, domain, 
    | domainX, absolute address
    |
    | @param string $url of the site
    | @param string $retdata if true then return the parsed URL data otherwise set the $urldata class variable
    | @return array|mixed|boolean 
   */ 
  function parseURL($url, $retdata=true){
      $url = substr($url,0,4)=='http'? $url: 'http://'.$url; //assume http if not supplied
      if ($urldata = parse_url(str_replace('&amp;','&',$url))){
          $path_parts = pathinfo($urldata['host']);
          $tmp = explode('.',$urldata['host']); $n = count($tmp);
          if ($n>=2){
              if ($n==4 || ($n==3 && strlen($tmp[($n-2)])<=3)){
                  $urldata['domain'] = $tmp[($n-3)].".".$tmp[($n-2)].".".$tmp[($n-1)];
                  $urldata['tld'] = $tmp[($n-2)].".".$tmp[($n-1)]; //top-level domain
                  $urldata['root'] = $tmp[($n-3)]; //second-level domain
                  $urldata['subdomain'] = $n==4? $tmp[0]: ($n==3 && strlen($tmp[($n-2)])<=3)? $tmp[0]: '';
              } else {
                  $urldata['domain'] = $tmp[($n-2)].".".$tmp[($n-1)];
                  $urldata['tld'] = $tmp[($n-1)];
                  $urldata['root'] = $tmp[($n-2)];
                  $urldata['subdomain'] = $n==3? $tmp[0]: '';
              }
          }
          //$urldata['dirname'] = $path_parts['dirname'];
          $urldata['basename'] = $path_parts['basename'];
          $urldata['filename'] = $path_parts['filename'];
          $urldata['extension'] = $path_parts['extension'];
          $urldata['base'] = $urldata['scheme']."://".$urldata['host'];
          $urldata['abs'] = (isset($urldata['path']) && strlen($urldata['path']))? $urldata['path']: '/';
          $urldata['abs'] .= (isset($urldata['query']) && strlen($urldata['query']))? '?'.$urldata['query']: '';
          //Set data
          if ($retdata){
              return $urldata;
          } else {
              $this->urldata = $urldata;
              return true;
          }
      } else {
          //invalid URL
          return false;
      }
  }
}

// --------------------------------------------------------------------

if ( ! function_exists('remove_invisible_characters'))
{
	/**
	 * Remove Invisible Characters
	 *
	 * This prevents sandwiching null characters
	 * between ascii characters, like Java\0script.
	 *
	 * @param	string
	 * @param	bool
	 * @return	string
	 */
	function remove_invisible_characters($str, $url_encoded = TRUE)
	{
		$non_displayables = array();

		// every control character except newline (dec 10),
		// carriage return (dec 13) and horizontal tab (dec 09)
		if ($url_encoded)
		{
			$non_displayables[] = '/%0[0-8bcef]/i';	// url encoded 00-08, 11, 12, 14, 15
			$non_displayables[] = '/%1[0-9a-f]/i';	// url encoded 16-31
			$non_displayables[] = '/%7f/i';	// url encoded 127
		}

		$non_displayables[] = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S';	// 00-08, 11, 12, 14-31, 127

		do
		{
			$str = preg_replace($non_displayables, '', $str, -1, $count);
		}
		while ($count);

		return $str;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('is_php'))
{
  /**
   * Determines if the current version of PHP is equal to or greater than the supplied value
   *
   * @param string
   * @return  bool  TRUE if the current version is $version or higher
   */
  function is_php($version)
  {
    static $_is_php;
    $version = (string) $version;

    if ( ! isset($_is_php[$version]))
    {
      $_is_php[$version] = version_compare(PHP_VERSION, $version, '>=');
    }

    return $_is_php[$version];
  }
}

// ------------------------------------------------------------------------

if ( ! function_exists('redirect_to'))
{
  /**
   * Header Redirect
   *
   * Header redirect in two flavors
   * For very fine grained control over headers, you could use the Output
   * Library's set_header() function.
   *
   * @param string  $uri  URL
   * @param string  $method Redirect method
   *      'auto', 'location' or 'refresh'
   * @param int $code HTTP Response status code
   * @return  void
   */
  function redirect($uri = '', $method = 'auto', $code = NULL)
  {
    if ( ! preg_match('#^(\w+:)?//#i', $uri))
    {
      $uri = site_url($uri);
    }

    // IIS environment likely? Use 'refresh' for better compatibility
    if ($method === 'auto' && isset($_SERVER['SERVER_SOFTWARE']) && strpos($_SERVER['SERVER_SOFTWARE'], 'Microsoft-IIS') !== FALSE)
    {
      $method = 'refresh';
    }
    elseif ($method !== 'refresh' && (empty($code) OR ! is_numeric($code)))
    {
      if (isset($_SERVER['SERVER_PROTOCOL'], $_SERVER['REQUEST_METHOD']) && $_SERVER['SERVER_PROTOCOL'] === 'HTTP/1.1')
      {
        $code = ($_SERVER['REQUEST_METHOD'] !== 'GET')
          ? 303 // reference: http://en.wikipedia.org/wiki/Post/Redirect/Get
          : 307;
      }
      else
      {
        $code = 302;
      }
    }

    switch ($method)
    {
      case 'refresh':
        header('Refresh:0;url='.$uri);
        break;
      default:
        header('Location: '.$uri, TRUE, $code);
        break;
    }
    exit;
  }
}
