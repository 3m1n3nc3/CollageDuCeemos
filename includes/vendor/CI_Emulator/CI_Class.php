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

namespace CI_Emulator\CI_Emulator;


/**
 * This is not a Code Igniter class
 */
class Not_CIClass
{
    /**
     * summary
     */
    public function __construct()
    {
        
    }
}

class CI_Session extends Not_CIClass {

	/**
	 * Session destroy
	 *
	 * Legacy CI_Session compatibility method
	 *
	 * @return	void
	 */
	public function sess_destroy()
	{
		session_destroy();
	}

	// ------------------------------------------------------------------------

	/**
	 * Get userdata reference
	 *
	 * Legacy CI_Session compatibility method
	 *
	 * @returns	array
	 */
	public function &get_userdata()
	{
		return $_SESSION;
	}

	// ------------------------------------------------------------------------

	/**
	 * Set userdata
	 *
	 * Legacy CI_Session compatibility method
	 *
	 * @param	mixed	$data	Session data key or an associative array
	 * @param	mixed	$value	Value to store
	 * @return	void
	 */
	public function set_userdata($data, $value = NULL)
	{
		if (is_array($data))
		{
			foreach ($data as $key => &$value)
			{
				$_SESSION[$key] = $value;
			}

			return;
		}

		$_SESSION[$data] = $value;
	}

	// ------------------------------------------------------------------------

	/**
	 * Unset userdata
	 *
	 * Legacy CI_Session compatibility method
	 *
	 * @param	mixed	$key	Session data key(s)
	 * @return	void
	 */
	public function unset_userdata($key)
	{
		if (is_array($key))
		{
			foreach ($key as $k)
			{
				unset($_SESSION[$k]);
			}

			return;
		}

		unset($_SESSION[$key]);
	}

	// ------------------------------------------------------------------------

	/**
	 * Userdata (fetch)
	 *
	 * Legacy CI_Session compatibility method
	 *
	 * @param	string	$key	Session data key
	 * @return	mixed	Session data value or NULL if not found
	 */
	public function userdata($key = NULL)
	{
		if (isset($key))
		{
			return isset($_SESSION[$key]) ? $_SESSION[$key] : NULL;
		}
		elseif (empty($_SESSION))
		{
			return array();
		}

		$userdata = array();
		$_exclude = array_merge(
			array('__ci_vars'),
			$this->get_flash_keys(),
			$this->get_temp_keys()
		);

		foreach (array_keys($_SESSION) as $key)
		{
			if ( ! in_array($key, $_exclude, TRUE))
			{
				$userdata[$key] = $_SESSION[$key];
			}
		}

		return $userdata;
	}

	// ------------------------------------------------------------------------

	/**
	 * Has userdata
	 *
	 * Legacy CI_Session compatibility method
	 *
	 * @param	string	$key	Session data key
	 * @return	bool
	 */
	public function has_userdata($key)
	{
		return isset($_SESSION[$key]);
	}

	// ------------------------------------------------------------------------

	/**
	 * Flashdata (fetch)
	 *
	 * Legacy CI_Session compatibility method
	 *
	 * @param	string	$key	Session data key
	 * @return	mixed	Session data value or NULL if not found
	 */
	public function flashdata($key = NULL)
	{
		if (isset($key))
		{
			return (isset($_SESSION['__ci_vars'], $_SESSION['__ci_vars'][$key], $_SESSION[$key]) && ! is_int($_SESSION['__ci_vars'][$key]))
				? $_SESSION[$key]
				: NULL;
		}

		$flashdata = array();

		if ( ! empty($_SESSION['__ci_vars']))
		{
			foreach ($_SESSION['__ci_vars'] as $key => &$value)
			{
				is_int($value) OR $flashdata[$key] = $_SESSION[$key];
			}
		}

		return $flashdata;
	}

	// ------------------------------------------------------------------------

	/**
	 * Set flashdata
	 *
	 * Legacy CI_Session compatibility method
	 *
	 * @param	mixed	$data	Session data key or an associative array
	 * @param	mixed	$value	Value to store
	 * @return	void
	 */
	public function set_flashdata($data, $value = NULL)
	{
		$this->set_userdata($data, $value);
		$this->mark_as_flash(is_array($data) ? array_keys($data) : $data);
	}

	// ------------------------------------------------------------------------

	/**
	 * Keep flashdata
	 *
	 * Legacy CI_Session compatibility method
	 *
	 * @param	mixed	$key	Session data key(s)
	 * @return	void
	 */
	public function keep_flashdata($key)
	{
		$this->mark_as_flash($key);
	}

	// ------------------------------------------------------------------------

	/**
	 * Mark as flash
	 *
	 * @param	mixed	$key	Session data key(s)
	 * @return	bool
	 */
	public function mark_as_flash($key)
	{
		if (is_array($key))
		{
			for ($i = 0, $c = count($key); $i < $c; $i++)
			{
				if ( ! isset($_SESSION[$key[$i]]))
				{
					return FALSE;
				}
			}

			$new = array_fill_keys($key, 'new');

			$_SESSION['__ci_vars'] = isset($_SESSION['__ci_vars'])
				? array_merge($_SESSION['__ci_vars'], $new)
				: $new;

			return TRUE;
		}

		if ( ! isset($_SESSION[$key]))
		{
			return FALSE;
		}

		$_SESSION['__ci_vars'][$key] = 'new';
		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Get flash keys
	 *
	 * @return	array
	 */
	public function get_flash_keys()
	{
		if ( ! isset($_SESSION['__ci_vars']))
		{
			return array();
		}

		$keys = array();
		foreach (array_keys($_SESSION['__ci_vars']) as $key)
		{
			is_int($_SESSION['__ci_vars'][$key]) OR $keys[] = $key;
		}

		return $keys;
	}

	// ------------------------------------------------------------------------

	/**
	 * Unmark flash
	 *
	 * @param	mixed	$key	Session data key(s)
	 * @return	void
	 */
	public function unmark_flash($key)
	{
		if (empty($_SESSION['__ci_vars']))
		{
			return;
		}

		is_array($key) OR $key = array($key);

		foreach ($key as $k)
		{
			if (isset($_SESSION['__ci_vars'][$k]) && ! is_int($_SESSION['__ci_vars'][$k]))
			{
				unset($_SESSION['__ci_vars'][$k]);
			}
		}

		if (empty($_SESSION['__ci_vars']))
		{
			unset($_SESSION['__ci_vars']);
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * Mark as temp
	 *
	 * @param	mixed	$key	Session data key(s)
	 * @param	int	$ttl	Time-to-live in seconds
	 * @return	bool
	 */
	public function mark_as_temp($key, $ttl = 300)
	{
		$ttl += time();

		if (is_array($key))
		{
			$temp = array();

			foreach ($key as $k => $v)
			{
				// Do we have a key => ttl pair, or just a key?
				if (is_int($k))
				{
					$k = $v;
					$v = $ttl;
				}
				else
				{
					$v += time();
				}

				if ( ! isset($_SESSION[$k]))
				{
					return FALSE;
				}

				$temp[$k] = $v;
			}

			$_SESSION['__ci_vars'] = isset($_SESSION['__ci_vars'])
				? array_merge($_SESSION['__ci_vars'], $temp)
				: $temp;

			return TRUE;
		}

		if ( ! isset($_SESSION[$key]))
		{
			return FALSE;
		}

		$_SESSION['__ci_vars'][$key] = $ttl;
		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Get temp keys
	 *
	 * @return	array
	 */
	public function get_temp_keys()
	{
		if ( ! isset($_SESSION['__ci_vars']))
		{
			return array();
		}

		$keys = array();
		foreach (array_keys($_SESSION['__ci_vars']) as $key)
		{
			is_int($_SESSION['__ci_vars'][$key]) && $keys[] = $key;
		}

		return $keys;
	}
}
