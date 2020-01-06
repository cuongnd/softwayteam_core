<?php
/**
 * @package     softwaycore.Platform
 * @subpackage  Session
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
namespace SoftWay\CMS\Session\handler;

use Factory;

defined('_WOO_BOOKING_EXEC') or die;
require_once __DIR__."/native.php";
/**
 * Interface for managing HTTP sessions
 *
 * @since       3.5
 * @deprecated  4.0  The CMS' Session classes will be replaced with the `softwaycore/session` package
 */
class SessionHandlerWoobooking extends SessionHandlerNative
{
	/**
	 * The input object
	 *
	 * @var    JInput
	 * @since  3.5
	 */
	public $input = null;

	/**
	 * Force cookies to be SSL only
	 *
	 * @var    boolean
	 * @since  3.5
	 */
	protected $force_ssl = false;

	/**
	 * Public constructor
	 *
	 * @param   array  $options  An array of configuration options
	 *
	 * @since   3.5
	 */
	public function __construct($options = array())
	{


        register_shutdown_function(array($this, 'save'));
		// Set options
		$this->setOptions($options);
		$this->setCookieParams();
	}
    
	/**
	 * Starts the session
	 *
	 * @return  boolean  True if started
	 *
	 * @since   3.5
	 * @throws  RuntimeException If something goes wrong starting the session.
	 */
	public function start()
	{
		$session_name = $this->getName();
       if(!$this->input){
           $this->input=Factory::getInput();
       }

        // Get the JInputCookie object
		$cookie = $this->input->cookie;

        if (is_null($cookie->get($session_name)))
		{
			$session_clean = $this->input->get($session_name, false, 'string');

			if ($session_clean)
			{
				$this->setId($session_clean);
				$cookie->set($session_name, '', 1);
			}
		}

		return parent::start();
	}

	/**
	 * Clear all session data in memory.
	 *
	 * @return  void
	 *
	 * @since   3.5
	 */
	public function clear()
	{
		$sessionName = $this->getName();

		/*
		 * In order to kill the session altogether, such as to log the user out, the session id
		 * must also be unset. If a cookie is used to propagate the session id (default behavior),
		 * then the session cookie must be deleted.
		 * We need to use setcookie here or we will get a warning in some session handlers (ex: files).
		 */
		if (isset($_COOKIE[$sessionName]))
		{
			$cookie = session_get_cookie_params();

			setcookie($sessionName, '', 1, $cookie['path'], $cookie['domain'], $cookie['secure'], true);
		}

		parent::clear();
	}

	/**
	 * Set session cookie parameters
	 *
	 * @return  void
	 *
	 * @since   3.5
	 */
	protected function setCookieParams()
	{


	}

	/**
	 * Set additional session options
	 *
	 * @param   array  $options  List of parameter
	 *
	 * @return  boolean  True on success
	 *
	 * @since   3.5
	 */
	protected function setOptions(array $options)
	{
		if (isset($options['force_ssl']))
		{
			$this->force_ssl = (bool) $options['force_ssl'];
		}

		return true;
	}
}
