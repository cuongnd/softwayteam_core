<?php
/**
 * @package     softwaycore.Platform
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_SOFT_WAY_EXEC') or die;

/**
 * Form Field class for the softwaycore Platform.
 * Supports a one line text field.
 *
 * @link   http://www.w3.org/TR/html-markup/input.text.html#input.text
 * @since  1.7.0
 */
class FormFieldNote extends FormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  1.7.0
	 */
	protected $type = 'Note';

	/**
	 * Method to get the field label markup.
	 *
	 * @return  string  The field label markup.
	 *
	 * @since   1.7.0
	 */
	protected function getLabel()
	{
		if (empty($this->element['label']) && empty($this->element['description']))
		{
			return '';
		}

		$title = $this->element['label'] ? (string) $this->element['label'] : ($this->element['title'] ? (string) $this->element['title'] : '');
		$heading = $this->element['heading'] ? (string) $this->element['heading'] : 'h4';
		$description = (string) $this->element['description'];
		$class = !empty($this->class) ? ' class="' . $this->class . '"' : '';
		$close = (string) $this->element['close'];

		$html = array();

		if ($close)
		{
			$close = $close == 'true' ? 'alert' : $close;
			$html[] = '<button type="button" class="close" data-dismiss="' . $close . '">&times;</button>';
		}

		$html[] = !empty($title) ? '<' . $heading . '>' . WoobookingText::_($title) . '</' . $heading . '>' : '';
		$html[] = !empty($description) ? WoobookingText::_($description) : '';

		return '</div><div ' . $class . '>' . implode('', $html);
	}

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   1.7.0
	 */
	protected function getInput()
	{
		return '';
	}
}
