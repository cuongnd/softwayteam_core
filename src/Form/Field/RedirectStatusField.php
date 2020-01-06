<?php
/**
 * softwaycore! Content Management System
 *
 * @copyright  Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace SoftWay\CMS\Form\Field;

defined('_SOFT_WAY_EXEC') or die;

use SoftWay\CMS\Form\FormHelper;

FormHelper::loadFieldClass('predefinedlist');

/**
 * Redirect Status field.
 *
 * @since  3.8.0
 */
class RedirectStatusField extends FormFieldPredefinedList
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  3.8.0
	 */
	public $type = 'Redirect_Status';

	/**
	 * Available statuses
	 *
	 * @var  array
	 * @since  3.8.0
	 */
	protected $predefinedOptions = array(
		'-2' => 'JTRASHED',
		'0'  => 'JDISABLED',
		'1'  => 'JENABLED',
		'2'  => 'JARCHIVED',
		'*'  => 'JALL',
	);
}
