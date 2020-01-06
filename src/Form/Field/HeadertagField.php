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

FormHelper::loadFieldClass('list');

/**
 * Form Field class for the softwaycore! CMS.
 *
 * @since  3.0
 */
class HeadertagField extends FormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  3.0
	 */
	protected $type = 'HeaderTag';

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 *
	 * @since   3.0
	 */
	protected function getOptions()
	{
		$options = array();
		$tags = array('h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'div');

		// Create one new option object for each tag
		foreach ($tags as $tag)
		{
			$tmp = \Html::_('select.option', $tag, $tag);
			$options[] = $tmp;
		}

		reset($options);

		return $options;
	}
}
