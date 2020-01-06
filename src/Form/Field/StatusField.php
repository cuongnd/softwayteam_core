<?php
/**
 * softwaycore! Content Management System
 *
 * @copyright  Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace WooBooking\CMS\Form\Field;

defined('_WOO_BOOKING_EXEC') or die;

use WooBooking\CMS\Form\FormHelper;
use WooBooking\CMS\Form\fields\FormFieldPredefinedList;
FormHelper::loadFieldClass('predefinedlist');

/**
 * Form Field to load a list of states
 *
 * @since  3.2
 */
class StatusField extends FormFieldPredefinedList
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  3.2
	 */
	public $type = 'Status';

	/**
	 * Available statuses
	 *
	 * @var  array
	 * @since  3.2
	 */
	protected $predefinedOptions = array(
		'0'  => 'UNPUBLISHED',
		'1'  => 'PUBLISHED',
		'*'  => 'ALL',
	);
}
