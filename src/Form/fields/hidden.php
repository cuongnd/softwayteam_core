<?php
/**
 * @package     softwaycore.Platform
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

namespace WooBooking\CMS\Form\fields;
defined('_WOO_BOOKING_EXEC') or die;
use WooBooking\CMS\Form\FormField;
use WoobookingText;
use WooBooking\CMS\Html\Html;
use SimpleXMLElement;

/**
 * Form Field class for the softwaycore Platform.
 * Provides a hidden field
 *
 * @link   http://www.w3.org/TR/html-markup/input.hidden.html#input.hidden
 * @since  1.7.0
 */
class FormFieldHidden extends FormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  1.7.0
	 */
	protected $type = 'Hidden';

	/**
	 * Name of the layout being used to render the field
	 *
	 * @var    string
	 * @since  3.7
	 */
	protected $layout = 'WooBooking.form.field.hidden';

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   1.7.0
	 */
	protected function getInput()
	{
		// Trim the trailing line in the layout file
		return rtrim($this->getRenderer($this->layout)->render($this->getLayoutData()), PHP_EOL);
	}

	/**
	 * Method to get the data to be passed to the layout for rendering.
	 *
	 * @return  array
	 *
	 * @since 3.7
	 */
	protected function getLayoutData()
	{
		return parent::getLayoutData();
	}
}
