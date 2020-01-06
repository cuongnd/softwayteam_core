<?php
/**
 * softwaycore! Content Management System
 *
 * @copyright  Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace SoftWay\CMS\Form\Rule;

defined('_WOO_BOOKING_EXEC') or die;

use SoftWay\CMS\Form\FormRule;

/**
 * Form Rule class for the softwaycore Platform.
 *
 * @since  1.7.0
 */
class BooleanRule extends FormRule
{
	/**
	 * The regular expression to use in testing a form field value.
	 *
	 * @var    string
	 * @since  1.7.0
	 */
	protected $regex = '^(?:[01]|true|false)$';

	/**
	 * The regular expression modifiers to use when testing a form field value.
	 *
	 * @var    string
	 * @since  1.7.0
	 */
	protected $modifiers = 'i';
}
