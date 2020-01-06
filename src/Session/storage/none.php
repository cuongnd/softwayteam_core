<?php
/**
 * @package     softwaycore.Platform
 * @subpackage  Session
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_WOO_BOOKING_EXEC') or die;

/**
 * File session handler for PHP
 *
 * @link        https://www.php.net/manual/en/function.session-set-save-handler.php
 * @since       1.7.0
 * @deprecated  4.0  The CMS' Session classes will be replaced with the `softwaycore/session` package
 */
class SessionStorageNone extends SessionStorage
{
	/**
	 * Register the functions of this class with PHP's session handler
	 *
	 * @return  void
	 *
	 * @since   1.7.0
	 */
	public function register()
	{
		// Default session handler is `files`
	}
}
