<?php
/**
 * softwaycore! Content Management System
 *
 * @copyright  Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace SoftWay\CMS\Form\Field;

defined('_WOO_BOOKING_EXEC') or die;

use SoftWay\CMS\Form\FormHelper;

FormHelper::loadFieldClass('predefinedlist');

/**
 * Plugin Status field.
 *
 * @since  3.5
 */
class PluginstatusField extends FormFieldPredefinedList
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  3.5
	 */
	public $type = 'Plugin_Status';

	/**
	 * Available statuses
	 *
	 * @var  array
	 * @since  3.5
	 */
	protected $predefinedOptions = array(
		'0'  => 'JDISABLED',
		'1'  => 'JENABLED',
	);
}
