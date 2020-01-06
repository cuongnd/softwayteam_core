<?php
/**
 * SoftWay! Content Management System
 *
 * @copyright  Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace SoftWay\CMS\Document\Opensearch;

defined('_SOFT_WAY_EXEC') or die;

/**
 * Data object representing an OpenSearch URL
 *
 * @since  1.7.0
 */
class OpensearchUrl
{
	/**
	 * Type item element
	 *
	 * required
	 *
	 * @var    string
	 * @since  1.7.0
	 */
	public $type = 'text/html';

	/**
	 * Rel item element
	 *
	 * required
	 *
	 * @var    string
	 * @since  1.7.0
	 */
	public $rel = 'results';

	/**
	 * Template item element. Has to contain the {searchTerms} parameter to work.
	 *
	 * required
	 *
	 * @var    string
	 * @since  1.7.0
	 */
	public $template;
}
