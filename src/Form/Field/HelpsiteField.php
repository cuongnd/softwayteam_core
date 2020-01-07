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
use SoftWay\CMS\Help\Help;

FormHelper::loadFieldClass('list');

/**
 * Form Field class for the softwaycore Platform.
 * Provides a select list of help sites.
 *
 * @since       1.6
 * @deprecated  4.0 To be removed
 */
class HelpsiteField extends FormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  1.6
	 */
	public $type = 'Helpsite';

	/**
	 * Method to get the help site field options.
	 *
	 * @return  array  The field option objects.
	 *
	 * @since   1.6
	 */
	protected function getOptions()
	{
		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), Help::createSiteList(JPATH_ADMINISTRATOR . '/help/helpsites.xml', $this->value));

		return $options;
	}

	/**
	 * Override to add refresh button
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   3.2
	 */
	protected function getInput()
	{
		\Html::_('script', 'system/helpsite.js', array('version' => 'auto', 'relative' => true));

		$showDefault = (string) $this->getAttribute('showDefault') === 'false' ? 'false' : 'true';

		$html = parent::getInput();
		$button = '<button
						type="button"
						class="btn btn-small"
						id="helpsite-refresh"
						rel="' . $this->id . '"
						showDefault="' . $showDefault . '"
					>
					<span>' . SoftWayText::_('JGLOBAL_HELPREFRESH_BUTTON') . '</span>
					</button>';

		return $html . $button;
	}
}
