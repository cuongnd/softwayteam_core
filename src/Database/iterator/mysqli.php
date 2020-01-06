<?php
/**
 * @package     softwaycore.Platform
 * @subpackage  Database
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
namespace SoftWay\CMS\Database\iterator;
defined('_SOFT_WAY_EXEC') or die;
/**
 * MySQLi database iterator.
 *
 * @since  12.1
 */
class DatabaseIteratorMysqli extends DatabaseIterator
{
	/**
	 * Get the number of rows in the result set for the executed SQL given by the cursor.
	 *
	 * @return  integer  The number of rows in the result set.
	 *
	 * @since   12.1
	 * @see     Countable::count()
	 */
	public function count()
	{
		return mysqli_num_rows($this->cursor);
	}
	/**
	 * Method to fetch a row from the result set cursor as an object.
	 *
	 * @return  mixed   Either the next row from the result set or false if there are no more rows.
	 *
	 * @since   12.1
	 */
	protected function fetchObject()
	{
		return mysqli_fetch_object($this->cursor, $this->class);
	}
	/**
	 * Method to free up the memory used for the result set.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	protected function freeResult()
	{
		mysqli_free_result($this->cursor);
	}
}