<?php
/**
 * @package     softwaycore.Platform
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_SOFT_WAY_EXEC') or die;

FormHelper::loadFieldClass('list');

/**
 * Form Field class for the softwaycore Framework.
 *
 * @since  2.5.0
 */
class FormFieldAliastag extends JFormFieldList
{
	/**
	 * The field type.
	 *
	 * @var    string
	 * @since  3.6
	 */
	protected $type = 'Aliastag';

	/**
	 * Method to get a list of options for a list input.
	 *
	 * @return	array  An array of JHtml options.
	 *
	 * @since   3.6
	 */
	protected function getOptions()
	{
			// Get list of tag type alias
			$db    = Factory::getDbo();
			$query = $db->getQuery(true)
				->select('Distinct type_alias AS value, type_alias AS text')
				->from('#__contentitem_tag_map');
			$db->setQuery($query);

			$options = $db->loadObjectList();

			$lang = Factory::getLanguage();

			foreach ($options as $i => $item)
			{
				$parts     = explode('.', $item->value);
				$extension = $parts[0];
				$lang->load($extension . '.sys', JPATH_ADMINISTRATOR, null, false, true)
				|| $lang->load($extension, JPath::clean(JPATH_ADMINISTRATOR . '/components/' . $extension), null, false, true);
				$options[$i]->text = SoftWayText::_(strtoupper($extension) . '_TAGS_' . strtoupper($parts[1]));
			}

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		// Sort by language value
		usort(
			$options,
			function($a, $b)
			{
				return $a->text > $b->text;
			}
		);

		return $options;
	}
}
