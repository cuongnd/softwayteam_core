<?php
/**
 * softwaycore! Content Management System
 *
 * @copyright  Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace SoftWay\CMS\Log\Logger;

defined('_SOFT_WAY_EXEC') or die;

use SoftWay\CMS\Log\LogEntry;
use SoftWay\CMS\Log\Logger;

/**
 * softwaycore Echo logger class.
 *
 * @since  1.7.0
 */
class EchoLogger extends Logger
{
	/**
	 * Value to use at the end of an echoed log entry to separate lines.
	 *
	 * @var    string
	 * @since  1.7.0
	 */
	protected $line_separator = "\n";

	/**
	 * Constructor.
	 *
	 * @param   array  &$options  Log object options.
	 *
	 * @since   3.0.0
	 */
	public function __construct(array &$options)
	{
		parent::__construct($options);

		if (!empty($this->options['line_separator']))
		{
			$this->line_separator = $this->options['line_separator'];
		}
	}

	/**
	 * Method to add an entry to the log.
	 *
	 * @param   LogEntry  $entry  The log entry object to add to the log.
	 *
	 * @return  void
	 *
	 * @since   1.7.0
	 */
	public function addEntry(LogEntry $entry)
	{
		echo $this->priorities[$entry->priority] . ': '
			. $entry->message . (empty($entry->category) ? '' : ' [' . $entry->category . ']')
			. $this->line_separator;
	}
}
