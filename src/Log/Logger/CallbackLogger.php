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
 * softwaycore! Callback Log class
 *
 * This class allows logging to be handled by a callback function.
 * This allows unprecedented flexibility in the way logging can be handled.
 *
 * @since  3.0.1
 */
class CallbackLogger extends Logger
{
	/**
	 * The function to call when an entry is added
	 *
	 * @var    callable
	 * @since  3.0.1
	 */
	protected $callback;

	/**
	 * Constructor.
	 *
	 * @param   array  &$options  Log object options.
	 *
	 * @since   3.0.1
	 * @throws  \RuntimeException
	 */
	public function __construct(array &$options)
	{
		// Call the parent constructor.
		parent::__construct($options);

		// Throw an exception if there is not a valid callback
		if (!isset($this->options['callback']) || !is_callable($this->options['callback']))
		{
			throw new \RuntimeException(sprintf('%s created without valid callback function.', get_class($this)));
		}

		$this->callback = $this->options['callback'];
	}

	/**
	 * Method to add an entry to the log.
	 *
	 * @param   LogEntry  $entry  The log entry object to add to the log.
	 *
	 * @return  void
	 *
	 * @since   3.0.1
	 * @throws  \RuntimeException
	 */
	public function addEntry(LogEntry $entry)
	{
		// Pass the log entry to the callback function
		call_user_func($this->callback, $entry);
	}
}
