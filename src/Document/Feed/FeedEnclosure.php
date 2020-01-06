<?php
/**
 * SoftWay! Content Management System
 *
 * @copyright  Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace SoftWay\CMS\Document\Feed;

defined('_SOFT_WAY_EXEC') or die;

/**
 * Data object representing a feed enclosure
 *
 * @since  1.7.0
 */
class FeedEnclosure
{
	/**
	 * URL enclosure element
	 *
	 * required
	 *
	 * @var    string
	 * @since  1.7.0
	 */
	public $url = '';

	/**
	 * Length enclosure element
	 *
	 * required
	 *
	 * @var    string
	 * @since  1.7.0
	 */
	public $length = '';

	/**
	 * Type enclosure element
	 *
	 * required
	 *
	 * @var    string
	 * @since  1.7.0
	 */
	public $type = '';
}
