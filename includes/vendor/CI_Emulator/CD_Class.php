<?php

namespace CI_Emulator\CI_Emulator;

class CD_Input extends CD_Extender {

	// --------------------------------------------------------------------

	/**
	 * Class constructor
	 *
	 * Determines whether to globally enable the XSS processing
	 * and whether to allow the $_GET array.
	 *
	 * @return	void
	 */
	public $cd_security;

	public function __construct()
	{ 
		$this->_enable_xss		= TRUE; 
		$this->security			= new CI_Security;
	}

	/**
	 * Fetch an item from the POST array
	 *
	 * @param	mixed	$index		Index for item to be fetched from $_POST
	 * @param	bool	$xss_clean	Whether to apply XSS filtering
	 * @return	mixed
	 */
	public function post($index = NULL, $xss_clean = NULL)
	{
		return $this->_fetch_from_array($_POST, $index, $xss_clean);
	}

	/**
	 * Fetch an item from the GET array
	 *
	 * @param	mixed	$index		Index for item to be fetched from $_POST
	 * @param	bool	$xss_clean	Whether to apply XSS filtering
	 * @return	mixed
	 */
	public function get($index = NULL, $xss_clean = NULL)
	{
		return $this->_fetch_from_array($_GET, $index, $xss_clean);
	}

	// --------------------------------------------------------------------

	/**
	 * Fetch an item from POST data with fallback to GET
	 *
	 * @param	string	$index		Index for item to be fetched from $_POST or $_GET
	 * @param	bool	$xss_clean	Whether to apply XSS filtering
	 * @return	mixed
	 */
	public function post_get($index, $xss_clean = NULL)
	{
		return isset($_POST[$index])
			? $this->post($index, $xss_clean)
			: $this->get($index, $xss_clean);
	}

	// --------------------------------------------------------------------

	/**
	 * Fetch an item from GET data with fallback to POST
	 *
	 * @param	string	$index		Index for item to be fetched from $_GET or $_POST
	 * @param	bool	$xss_clean	Whether to apply XSS filtering
	 * @return	mixed
	 */
	public function get_post($index, $xss_clean = NULL)
	{
		return isset($_GET[$index])
			? $this->get($index, $xss_clean)
			: $this->post($index, $xss_clean);
	}

	// --------------------------------------------------------------------

	/**
	 * Fetch an item from the COOKIE array
	 *
	 * @param	mixed	$index		Index for item to be fetched from $_COOKIE
	 * @param	bool	$xss_clean	Whether to apply XSS filtering
	 * @return	mixed
	 */
	public function cookie($index = NULL, $xss_clean = NULL)
	{
		return $this->_fetch_from_array($_COOKIE, $index, $xss_clean);
	}

	// --------------------------------------------------------------------

	/**
	 * Fetch an item from the SERVER array
	 *
	 * @param	mixed	$index		Index for item to be fetched from $_SERVER
	 * @param	bool	$xss_clean	Whether to apply XSS filtering
	 * @return	mixed
	 */
	public function server($index, $xss_clean = NULL)
	{
		return $this->_fetch_from_array($_SERVER, $index, $xss_clean);
	}

	// --------------------------------------------------------------------

	/**
	 * Fetch from array
	 *
	 * Internal method used to retrieve values from global arrays.
	 *
	 * @param	array	&$array		$_GET, $_POST, $_COOKIE, $_SERVER, etc.
	 * @param	mixed	$index		Index for item to be fetched from $array
	 * @param	bool	$xss_clean	Whether to apply XSS filtering
	 * @return	mixed
	 */
	protected function _fetch_from_array(&$array, $index = NULL, $xss_clean = NULL)
	{
		is_bool($xss_clean) OR $xss_clean = $this->_enable_xss;

		// If $index is NULL, it means that the whole $array is requested
		isset($index) OR $index = array_keys($array);

		// allow fetching multiple keys at once
		if (is_array($index))
		{
			$output = array();
			foreach ($index as $key)
			{
				$output[$key] = $this->_fetch_from_array($array, $key, $xss_clean);
			}

			return $output;
		}

		if (isset($array[$index]))
		{
			$value = $array[$index];
		}
		elseif (($count = preg_match_all('/(?:^[^\[]+)|\[[^]]*\]/', $index, $matches)) > 1) // Does the index contain array notation
		{
			$value = $array;
			for ($i = 0; $i < $count; $i++)
			{
				$key = trim($matches[0][$i], '[]');
				if ($key === '') // Empty notation will return the value as array
				{
					break;
				}

				if (isset($value[$key]))
				{
					$value = $value[$key];
				}
				else
				{
					return NULL;
				}
			}
		}
		else
		{
			return NULL;
		}

		return ($xss_clean === TRUE)
			? $this->security->xss_clean($value)
			: $value;
	}
}


class CD_Extender
{
 
	function write_flashdata($key = '', $value = '') { 
	    $_SESSION[$key] = $value; 
	}

	function read_flashdata($key = '') {  

	    if (isset($_SESSION[$key]) && $_SESSION[$key]) {
	        $flash = $_SESSION[$key];
	        unset($_SESSION[$key]);
	        return $flash;
	    } 

	}

}
