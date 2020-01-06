<?php
/**
 * softwaycore! Content Management System
 *
 * @copyright  Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace SoftWay\CMS\Form\Rule;

defined('_SOFT_WAY_EXEC') or die;

use SoftWay\CMS\Form\Form;
use SoftWay\CMS\Form\FormRule;
use softwaycore\Registry\Registry;

/**
 * Form Rule class for the softwaycore Platform.
 *
 * @since  3.5
 */
class NumberRule extends FormRule
{
	/**
	 * Method to test the range for a number value using min and max attributes.
	 *
	 * @param   \SimpleXMLElement  $element  The SimpleXMLElement object representing the `<field>` tag for the form field object.
	 * @param   mixed              $value    The form field value to validate.
	 * @param   string             $group    The field name group control value. This acts as an array container for the field.
	 *                                       For example if the field has name="foo" and the group value is set to "bar" then the
	 *                                       full field name would end up being "bar[foo]".
	 * @param   Registry           $input    An optional Registry object with the entire data set to validate against the entire form.
	 * @param   Form               $form     The form object for which the field is being tested.
	 *
	 * @return  boolean  True if the value is valid, false otherwise.
	 *
	 * @since   3.5
	 */
	public function test(\SimpleXMLElement $element, $value, $group = null, Registry $input = null, Form $form = null)
	{
		// Check if the field is required.
		$required = ((string) $element['required'] == 'true' || (string) $element['required'] == 'required');

		// If the value is empty and the field is not required return True.
		if (($value === '' || $value === null) && ! $required)
		{
			return true;
		}

		$float_value = (float) $value;

		if (isset($element['min']))
		{
			$min = (float) $element['min'];

			if ($min > $float_value)
			{
				return false;
			}
		}

		if (isset($element['max']))
		{
			$max = (float) $element['max'];

			if ($max < $float_value)
			{
				return false;
			}
		}

		return true;
	}
}
