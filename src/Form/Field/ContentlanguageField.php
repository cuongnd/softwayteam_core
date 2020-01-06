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
use  SoftWay\CMS\Form\fields\FormFieldList;
FormHelper::loadFieldClass('list');

/**
 * Provides a list of content languages
 *
 * @see    JFormFieldLanguage for a select list of application languages.
 * @since  1.6
 */
class ContentlanguageField extends FormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  1.6
	 */
	public $type = 'ContentLanguage';

	/**
	 * Method to get the field options for content languages.
	 *
	 * @return  array  The options the field is going to show.
	 *
	 * @since   1.6
	 */
	protected function getOptions()
	{
		return array_merge(parent::getOptions(), \Html::_('contentlanguage.existing'));
	}
}
