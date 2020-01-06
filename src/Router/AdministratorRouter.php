<?php
/**
 * Joomla! Content Management System
 *
 * @copyright  Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace SoftWay\CMS\Router;

defined('_SOFT_WAY_EXEC') or die;

use SoftWay\CMS\Uri\Uri;

/**
 * Class to create and parse routes
 *
 * @since  1.5
 */
class AdministratorRouter extends Router
{
	/**
	 * Function to convert a route to an internal URI.
	 *
	 * @param   Uri  &$uri  The uri.
	 *
	 * @return  array
	 *
	 * @since   1.5
	 */
	public function parse(&$uri)
	{
		return array();
	}

	/**
	 * Function to convert an internal URI to a route
	 *
	 * @param   string  $url  The internal URL
	 *
	 * @return  Uri  The absolute search engine friendly URL
	 *
	 * @since   1.5
	 */
	public function build($url)
	{
		// Create the URI object
		$uri = parent::build($url);

		// Get the path data
		$route = $uri->getPath();

		// Add basepath to the uri
		$uri->setPath(Uri::root(true) . '/' . basename(JPATH_ADMINISTRATOR) . '/' . $route);

		return $uri;
	}
}
