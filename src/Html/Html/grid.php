<?php
/**
 * @package     SoftWay.Libraries
 * @subpackage  HTML
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_SOFT_WAY_EXEC') or die;

/**
 * Utility class for creating HTML Grids
 *
 * @since  1.5
 */
abstract class HtmlGrid
{
	/**
	 * Display a boolean setting widget.
	 *
	 * @param   integer  $i        The row index.
	 * @param   integer  $value    The value of the boolean field.
	 * @param   string   $taskOn   Task to turn the boolean setting on.
	 * @param   string   $taskOff  Task to turn the boolean setting off.
	 *
	 * @return  string   The boolean setting widget.
	 *
	 * @since   1.6
	 *
	 * @deprecated  4.0 This is only used in hathor and will be removed without replacement
	 */
	public static function boolean($i, $value, $taskOn = null, $taskOff = null)
	{
		// Load the behavior.
		static::behavior();
		Html::_('bootstrap.tooltip');

		// Build the title.
		$title = $value ? SoftWayText::_('JYES') : SoftWayText::_('JNO');
		$title = Html::_('tooltipText', $title, SoftWayText::_('JGLOBAL_CLICK_TO_TOGGLE_STATE'), 0);

		// Build the <a> tag.
		$bool = $value ? 'true' : 'false';
		$task = $value ? $taskOff : $taskOn;
		$toggle = (!$task) ? false : true;

		if ($toggle)
		{
			return '<a class="grid_' . $bool . ' hasTooltip" title="' . $title . '" rel="{id:\'cb' . $i . '\', task:\'' . $task
				. '\'}" href="#toggle"></a>';
		}
		else
		{
			return '<a class="grid_' . $bool . '"></a>';
		}
	}

	/**
	 * Method to sort a column in a grid
	 *
	 * @param   string  $title          The link title
	 * @param   string  $order          The order field for the column
	 * @param   string  $direction      The current direction
	 * @param   string  $selected       The selected ordering
	 * @param   string  $task           An optional task override
	 * @param   string  $new_direction  An optional direction for the new column
	 * @param   string  $tip            An optional text shown as tooltip title instead of $title
	 * @param   string  $form           An optional form selector
	 *
	 * @return  string
	 *
	 * @since   1.5
	 */
	public static function sort($title, $order, $direction = 'asc', $selected = '', $task = null, $new_direction = 'asc', $tip = '', $form = null)
	{
		Html::_('behavior.core');
		Html::_('bootstrap.popover');

		$direction = strtolower($direction);
		$icon = array('arrow-up-3', 'arrow-down-3');
		$index = (int) ($direction === 'desc');

		if ($order != $selected)
		{
			$direction = $new_direction;
		}
		else
		{
			$direction = $direction === 'desc' ? 'asc' : 'desc';
		}

		if ($form)
		{
			$form = ', document.getElementById(\'' . $form . '\')';
		}

		$html = '<a href="#" onclick="SoftWay.tableOrdering(\'' . $order . '\',\'' . $direction . '\',\'' . $task . '\'' . $form . ');return false;"'
			. ' class="hasPopover" title="' . htmlspecialchars(SoftWayText::_($tip ?: $title)) . '"'
			. ' data-content="' . htmlspecialchars(SoftWayText::_('JGLOBAL_CLICK_TO_SORT_THIS_COLUMN')) . '" data-placement="top">';

		if (isset($title['0']) && $title['0'] === '<')
		{
			$html .= $title;
		}
		else
		{
			$html .= SoftWayText::_($title);
		}

		if ($order == $selected)
		{
			$html .= '<span class="icon-' . $icon[$index] . '"></span>';
		}

		$html .= '</a>';

		return $html;
	}

	/**
	 * Method to check all checkboxes in a grid
	 *
	 * @param   string  $name    The name of the form element
	 * @param   string  $tip     The text shown as tooltip title instead of $tip
	 * @param   string  $action  The action to perform on clicking the checkbox
	 *
	 * @return  string
	 *
	 * @since   3.1.2
	 */
	public static function checkall($name = 'checkall-toggle', $tip = 'JGLOBAL_CHECK_ALL', $action = 'SoftWay.checkAll(this)')
	{
		Html::_('behavior.core');
		Html::_('bootstrap.tooltip');

		return '<input type="checkbox" name="' . $name . '" value="" class="hasTooltip" title="' . Html::_('tooltipText', $tip)
			. '" onclick="' . $action . '" />';
	}

	/**
	 * Method to create a checkbox for a grid row.
	 *
	 * @param   integer  $rowNum      The row index
	 * @param   integer  $recId       The record id
	 * @param   boolean  $checkedOut  True if item is checked out
	 * @param   string   $name        The name of the form element
	 * @param   string   $stub        The name of stub identifier
	 *
	 * @return  mixed    String of html with a checkbox if item is not checked out, null if checked out.
	 *
	 * @since   1.5
	 */
	public static function id($rowNum, $recId, $checkedOut = false, $name = 'cid', $stub = 'cb')
	{
		return $checkedOut ? '' : '<input type="checkbox" id="' . $stub . $rowNum . '" name="' . $name . '[]" value="' . $recId
			. '" onclick="SoftWay.isChecked(this.checked);" />';
	}

	/**
	 * Displays a checked out icon.
	 *
	 * @param   object   &$row        A data object (must contain checkedout as a property).
	 * @param   integer  $i           The index of the row.
	 * @param   string   $identifier  The property name of the primary key or index of the row.
	 *
	 * @return  string
	 *
	 * @since   1.5
	 */
	public static function checkedOut(&$row, $i, $identifier = 'id')
	{
		$user = Factory::getUser();
		$userid = $user->get('id');

		if ($row instanceof JTable)
		{
			$result = $row->isCheckedOut($userid);
		}
		else
		{
			$result = false;
		}

		if ($result)
		{
			return static::_checkedOut($row);
		}
		else
		{
			if ($identifier === 'id')
			{
				return Html::_('grid.id', $i, $row->$identifier);
			}
			else
			{
				return Html::_('grid.id', $i, $row->$identifier, $result, $identifier);
			}
		}
	}

	/**
	 * Method to create a clickable icon to change the state of an item
	 *
	 * @param   mixed    $value   Either the scalar value or an object (for backward compatibility, deprecated)
	 * @param   integer  $i       The index
	 * @param   string   $img1    Image for a positive or on value
	 * @param   string   $img0    Image for the empty or off value
	 * @param   string   $prefix  An optional prefix for the task
	 *
	 * @return  string
	 *
	 * @since   1.5
	 */
	public static function published($value, $i, $img1 = 'tick.png', $img0 = 'publish_x.png', $prefix = '')
	{
		if (is_object($value))
		{
			$value = $value->published;
		}

		$img = $value ? $img1 : $img0;
		$task = $value ? 'unpublish' : 'publish';
		$alt = $value ? SoftWayText::_('JPUBLISHED') : SoftWayText::_('JUNPUBLISHED');
		$action = $value ? SoftWayText::_('JLIB_HTML_UNPUBLISH_ITEM') : SoftWayText::_('JLIB_HTML_PUBLISH_ITEM');

		return '<a href="#" onclick="return listItemTask(\'cb' . $i . '\',\'' . $prefix . $task . '\')" title="' . $action . '">'
			. Html::_('image', 'admin/' . $img, $alt, null, true) . '</a>';
	}

	/**
	 * Method to create a select list of states for filtering
	 * By default the filter shows only published and unpublished items
	 *
	 * @param   string  $filter_state  The initial filter state
	 * @param   string  $published     The SoftWayText string for published
	 * @param   string  $unpublished   The SoftWayText string for Unpublished
	 * @param   string  $archived      The SoftWayText string for Archived
	 * @param   string  $trashed       The SoftWayText string for Trashed
	 *
	 * @return  string
	 *
	 * @since   1.5
	 */
	public static function state($filter_state = '*', $published = 'JPUBLISHED', $unpublished = 'JUNPUBLISHED', $archived = null, $trashed = null)
	{
		$state = array('' => '- ' . SoftWayText::_('JLIB_HTML_SELECT_STATE') . ' -', 'P' => SoftWayText::_($published), 'U' => SoftWayText::_($unpublished));

		if ($archived)
		{
			$state['A'] = SoftWayText::_($archived);
		}

		if ($trashed)
		{
			$state['T'] = SoftWayText::_($trashed);
		}

		return Html::_(
			'select.genericlist',
			$state,
			'filter_state',
			array(
				'list.attr' => 'class="inputbox" size="1" onchange="SoftWay.submitform();"',
				'list.select' => $filter_state,
				'option.key' => null,
			)
		);
	}

	/**
	 * Method to create an icon for saving a new ordering in a grid
	 *
	 * @param   array   $rows   The array of rows of rows
	 * @param   string  $image  The image [UNUSED]
	 * @param   string  $task   The task to use, defaults to save order
	 *
	 * @return  string
	 *
	 * @since   1.5
	 */
	public static function order($rows, $image = 'filesave.png', $task = 'saveorder')
	{
		return '<a href="javascript:saveorder('
			. (count($rows) - 1) . ', \'' . $task . '\')" rel="tooltip" class="saveorder btn btn-micro pull-right" title="'
			. SoftWayText::_('JLIB_HTML_SAVE_ORDER') . '"><span class="icon-menu-2"></span></a>';
	}

	/**
	 * Method to create a checked out icon with optional overlib in a grid.
	 *
	 * @param   object   &$row     The row object
	 * @param   boolean  $overlib  True if an overlib with checkout information should be created.
	 *
	 * @return  string   HTMl for the icon and overlib
	 *
	 * @since   1.5
	 */
	protected static function _checkedOut(&$row, $overlib = true)
	{
		$hover = '';

		if ($overlib)
		{
			Html::_('bootstrap.tooltip');

			$date = Html::_('date', $row->checked_out_time, SoftWayText::_('DATE_FORMAT_LC1'));
			$time = Html::_('date', $row->checked_out_time, 'H:i');

			$hover = '<span class="editlinktip hasTooltip" title="' . Html::_('tooltipText', 'JLIB_HTML_CHECKED_OUT', $row->editor)
				. '<br />' . $date . '<br />' . $time . '">';
		}

		return $hover . Html::_('image', 'admin/checked_out.png', null, null, true) . '</span>';
	}

	/**
	 * Method to build the behavior script and add it to the document head.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 *
	 * @deprecated  4.0 This is only used in hathor and will be removed without replacement
	 */
	public static function behavior()
	{
		static $loaded;

		if (!$loaded)
		{
			// Include jQuery
			Html::_('jquery.framework');

			// Build the behavior script.
			$js = '
		jQuery(function($){
			$actions = $(\'a.move_up, a.move_down, a.grid_true, a.grid_false, a.grid_trash\');
			$actions.each(function(){
				$(this).on(\'click\', function(){
					args = JSON.decode(this.rel);
					listItemTask(args.id, args.task);
				});
			});
			$(\'input.check-all-toggle\').each(function(){
				$(this).on(\'click\', function(){
					if (this.checked) {
						$(this).closest(\'form\').find(\'input[type="checkbox"]\').each(function(){
							this.checked = true;
						})
					}
					else {
						$(this).closest(\'form\').find(\'input[type="checkbox"]\').each(function(){
							this.checked = false;
						})
					}
				});
			});
		});';

			// Add the behavior to the document head.
			$document = Factory::getDocument();
			$document->addScriptDeclaration($js);

			$loaded = true;
		}
	}
}
