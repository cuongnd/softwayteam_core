<?php
/**
 * @package     WooBooking.Libraries
 * @subpackage  HTML
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_WOO_BOOKING_EXEC') or die;
use WooBooking\CMS\Log\Log;
Log::add('JHtmlRules is deprecated.', Log::WARNING, 'deprecated');

/**
 * Extended Utility class for all HTML drawing classes.
 *
 * @since       1.6
 * @deprecated  4.0
 */
abstract class HtmlRules
{
	/**
	 * Creates the HTML for the permissions widget
	 *
	 * @param   array    $actions   Array of action objects
	 * @param   integer  $assetId   Id of a specific asset to  create a widget for.
	 * @param   integer  $parent    Id of the parent of the asset
	 * @param   string   $control   The form control
	 * @param   string   $idPrefix  Prefix for the ids assigned to specific action-group pairs
	 *
	 * @return  string   HTML for the permissions widget
	 *
	 * @see     JAccess
	 * @see     JFormFieldRules
	 * @since   1.6
	 * @deprecated  4.0
	 */
	public static function assetFormWidget($actions, $assetId = null, $parent = null, $control = 'jform[rules]', $idPrefix = 'jform_rules')
	{
		$images = static::_getImagesArray();

		// Get the user groups.
		$groups = static::_getUserGroups();

		// Get the incoming inherited rules as well as the asset specific rules.
		$inheriting = JAccess::getAssetRules($parent ?: static::_getParentAssetId($assetId), true);
		$inherited = JAccess::getAssetRules($assetId, true);
		$rules = JAccess::getAssetRules($assetId);

		$html = array();

		$html[] = '<div class="acl-options">';
		$html[] = Html::_('tabs.start', 'acl-rules-' . $assetId, array('useCookie' => 1));
		$html[] = Html::_('tabs.panel', WoobookingText::_('JLIB_HTML_ACCESS_SUMMARY'), 'summary');
		$html[] = '			<p>' . WoobookingText::_('JLIB_HTML_ACCESS_SUMMARY_DESC') . '</p>';
		$html[] = '			<table class="aclsummary-table" summary="' . WoobookingText::_('JLIB_HTML_ACCESS_SUMMARY_DESC') . '">';
		$html[] = '			<caption>' . WoobookingText::_('JLIB_HTML_ACCESS_SUMMARY_DESC_CAPTION') . '</caption>';
		$html[] = '			<tr>';
		$html[] = '				<th class="col1 hidelabeltxt">' . WoobookingText::_('JLIB_RULES_GROUPS') . '</th>';

		foreach ($actions as $i => $action)
		{
			$html[] = '				<th class="col' . ($i + 2) . '">' . WoobookingText::_($action->title) . '</th>';
		}

		$html[] = '			</tr>';

		foreach ($groups as $i => $group)
		{
			$html[] = '			<tr class="row' . ($i % 2) . '">';
			$html[] = '				<td class="col1">' . $group->text . '</td>';

			foreach ($actions as $j => $action)
			{
				$html[] = '				<td class="col' . ($j + 2) . '">'
					. ($assetId ? ($inherited->allow($action->name, $group->identities) ? $images['allow'] : $images['deny'])
					: ($inheriting->allow($action->name, $group->identities) ? $images['allow'] : $images['deny'])) . '</td>';
			}

			$html[] = '			</tr>';
		}

		$html[] = ' 		</table>';

		foreach ($actions as $action)
		{
			$actionTitle = WoobookingText::_($action->title);
			$actionDesc = WoobookingText::_($action->description);
			$html[] = Html::_('tabs.panel', $actionTitle, $action->name);
			$html[] = '			<p>' . $actionDesc . '</p>';
			$html[] = '			<table class="aclmodify-table" summary="' . strip_tags($actionDesc) . '">';
			$html[] = '			<caption>' . WoobookingText::_('JLIB_HTML_ACCESS_MODIFY_DESC_CAPTION_ACL') . ' ' . $actionTitle . ' '
				. WoobookingText::_('JLIB_HTML_ACCESS_MODIFY_DESC_CAPTION_TABLE') . '</caption>';
			$html[] = '			<tr>';
			$html[] = '				<th class="col1 hidelabeltxt">' . WoobookingText::_('JLIB_RULES_GROUP') . '</th>';
			$html[] = '				<th class="col2">' . WoobookingText::_('JLIB_RULES_INHERIT') . '</th>';
			$html[] = '				<th class="col3 hidelabeltxt">' . WoobookingText::_('JMODIFY') . '</th>';
			$html[] = '				<th class="col4">' . WoobookingText::_('JCURRENT') . '</th>';
			$html[] = '			</tr>';

			foreach ($groups as $i => $group)
			{
				$selected = $rules->allow($action->name, $group->value);

				$html[] = '			<tr class="row' . ($i % 2) . '">';
				$html[] = '				<td class="col1">' . $group->text . '</td>';
				$html[] = '				<td class="col2">'
					. ($inheriting->allow($action->name, $group->identities) ? $images['allow-i'] : $images['deny-i']) . '</td>';
				$html[] = '				<td class="col3">';
				$html[] = '					<select id="' . $idPrefix . '_' . $action->name . '_' . $group->value
					. '" class="inputbox" size="1" name="' . $control . '[' . $action->name . '][' . $group->value . ']" title="'
					. WoobookingText::sprintf('JLIB_RULES_SELECT_ALLOW_DENY_GROUP', $actionTitle, $group->text) . '">';
				$html[] = '						<option value=""' . ($selected === null ? ' selected="selected"' : '') . '>'
					. WoobookingText::_('JLIB_RULES_INHERIT') . '</option>';
				$html[] = '						<option value="1"' . ($selected === true ? ' selected="selected"' : '') . '>'
					. WoobookingText::_('JLIB_RULES_ALLOWED') . '</option>';
				$html[] = '						<option value="0"' . ($selected === false ? ' selected="selected"' : '') . '>'
					. WoobookingText::_('JLIB_RULES_DENIED') . '</option>';
				$html[] = '					</select>';
				$html[] = '				</td>';
				$html[] = '				<td class="col4">'
					. ($assetId ? ($inherited->allow($action->name, $group->identities) ? $images['allow'] : $images['deny'])
					: ($inheriting->allow($action->name, $group->identities) ? $images['allow'] : $images['deny'])) . '</td>';
				$html[] = '			</tr>';
			}

			$html[] = '			</table>';
		}

		$html[] = Html::_('tabs.end');

		// Build the footer with legend and special purpose buttons.
		$html[] = '	<div class="clr"></div>';
		$html[] = '	<ul class="acllegend fltlft">';
		$html[] = '		<li class="acl-allowed">' . WoobookingText::_('JLIB_RULES_ALLOWED') . '</li>';
		$html[] = '		<li class="acl-denied">' . WoobookingText::_('JLIB_RULES_DENIED') . '</li>';
		$html[] = '	</ul>';
		$html[] = '</div>';

		return implode("\n", $html);
	}

	/**
	 * Get the id of the parent asset
	 *
	 * @param   integer  $assetId  The asset for which the parentid will be returned
	 *
	 * @return  integer  The id of the parent asset
	 *
	 * @since   1.6
	 * @deprecated  4.0
	 */
	protected static function _getParentAssetId($assetId)
	{
		// Get a database object.
		$db = Factory::getDbo();
		$query = $db->getQuery(true);

		// Get the user groups from the database.
		$query->select($db->quoteName('parent_id'))
			->from($db->quoteName('#__assets'))
			->where($db->quoteName('id') . ' = ' . (int) $assetId);
		$db->setQuery($query);

		return (int) $db->loadResult();
	}

	/**
	 * Get the user groups
	 *
	 * @return  array  Array of user groups
	 *
	 * @since   1.6
	 * @deprecated  4.0
	 */
	protected static function _getUserGroups()
	{
		// Get a database object.
		$db = Factory::getDbo();

		// Get the user groups from the database.
		$db->setQuery(
			'SELECT a.id AS value, a.title AS text, b.id as parent'
			. ' FROM #__usergroups AS a LEFT JOIN #__usergroups AS b ON a.lft >= b.lft AND a.rgt <= b.rgt'
			. ' ORDER BY a.lft ASC, b.lft ASC'
		);
		$result = $db->loadObjectList();
		$options = array();

		// Pre-compute additional values.
		foreach ($result as $option)
		{
			$end = end($options);

			if ($end === false || $end->value != $option->value)
			{
				$end = $option;
				$end->level = 0;
				$options[] = $end;
			}
			else
			{
				$end->level++;
			}

			$end->identities[] = $option->parent;
		}

		return $options;
	}

	/**
	 * Get the array of images associate with specific permissions
	 *
	 * @return  array  An associative  array of permissions and images
	 *
	 * @since   1.6
	 * @deprecated  4.0
	 */
	protected static function _getImagesArray()
	{
		$images['allow-l'] = '<label class="icon-16-allow" title="' . WoobookingText::_('JLIB_RULES_ALLOWED') . '">' . WoobookingText::_('JLIB_RULES_ALLOWED')
			. '</label>';
		$images['deny-l'] = '<label class="icon-16-deny" title="' . WoobookingText::_('JLIB_RULES_DENIED') . '">' . WoobookingText::_('JLIB_RULES_DENIED') . '</label>';
		$images['allow'] = '<a class="icon-16-allow" title="' . WoobookingText::_('JLIB_RULES_ALLOWED') . '"> </a>';
		$images['deny'] = '<a class="icon-16-deny" title="' . WoobookingText::_('JLIB_RULES_DENIED') . '"> </a>';
		$images['allow-i'] = '<a class="icon-16-allowinactive" title="' . WoobookingText::_('JRULE_ALLOWED_INHERITED') . '"> </a>';
		$images['deny-i'] = '<a class="icon-16-denyinactive" title="' . WoobookingText::_('JRULE_DENIED_INHERITED') . '"> </a>';

		return $images;
	}
}
