<?php
/**
 * @package     softwaycore.Platform
 * @subpackage  Database
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
namespace SoftWay\CMS\Database;
defined('_SOFT_WAY_EXEC') or die;
/**
 * Database connector class.
 *
 * @since       11.1
 * @deprecated  13.3 (Platform) & 4.0 (CMS)
 */
abstract class DatabaseMongodb
{
	/**
	 * Execute the SQL statement.
	 *
	 * @return  mixed  A database cursor resource on success, boolean false on failure.
	 *
	 * @since   11.1
	 * @throws  RuntimeException
	 * @deprecated  13.1 (Platform) & 4.0 (CMS)
	 */
	public function query()
	{
		Log::add('Database::query() is deprecated, use DatabaseDriver::execute() instead.', Log::WARNING, 'deprecated');
		return $this->execute();
	}
	/**
	 * Get a list of available database connectors.  The list will only be populated with connectors that both
	 * the class exists and the static test method returns true.  This gives us the ability to have a multitude
	 * of connector classes that are self-aware as to whether or not they are able to be used on a given system.
	 *
	 * @return  array  An array of available database connectors.
	 *
	 * @since   11.1
	 * @deprecated  13.1 (Platform) & 4.0 (CMS)
	 */
	public static function getConnectors()
	{
		Log::add('Database::getConnectors() is deprecated, use DatabaseDriver::getConnectors() instead.', Log::WARNING, 'deprecated');
		return DatabaseMongoDbDriver::getConnectors();
	}
	/**
	 * Gets the error message from the database connection.
	 *
	 * @param   boolean  $escaped  True to escape the message string for use in JavaScript.
	 *
	 * @return  string  The error message for the most recent query.
	 *
	 * @deprecated  13.3 (Platform) & 4.0 (CMS)
	 * @since   11.1
	 */
	public function getErrorMsg($escaped = false)
	{
		Log::add('Database::getErrorMsg() is deprecated, use exception handling instead.', Log::WARNING, 'deprecated');
		if ($escaped)
		{
			return addslashes($this->errorMsg);
		}
		else
		{
			return $this->errorMsg;
		}
	}
	/**
	 * Gets the error number from the database connection.
	 *
	 * @return      integer  The error number for the most recent query.
	 *
	 * @since       11.1
	 * @deprecated  13.3 (Platform) & 4.0 (CMS)
	 */
	public function getErrorNum()
	{
		Log::add('Database::getErrorNum() is deprecated, use exception handling instead.', Log::WARNING, 'deprecated');
		return $this->errorNum;
	}
	/**
	 * Method to return a DatabaseDriver instance based on the given options.  There are three global options and then
	 * the rest are specific to the database driver.  The 'driver' option defines which DatabaseDriver class is
	 * used for the connection -- the default is 'mysqli'.  The 'database' option determines which database is to
	 * be used for the connection.  The 'select' option determines whether the connector should automatically select
	 * the chosen database.
	 *
	 * Instances are unique to the given options and new objects are only created when a unique options array is
	 * passed into the method.  This ensures that we don't end up with unnecessary database connection resources.
	 *
	 * @param   array  $options  Parameters to be passed to the database driver.
	 *
	 * @return  DatabaseDriver  A database object.
	 *
	 * @since       11.1
	 * @deprecated  13.1 (Platform) & 4.0 (CMS)
	 */
	public static function getInstance($options = array())
	{
		Log::add('Database::getInstance() is deprecated, use DatabaseDriver::getInstance() instead.', Log::WARNING, 'deprecated');
		return DatabaseDriver::getInstance($options);
	}
	/**
	 * Splits a string of multiple queries into an array of individual queries.
	 *
	 * @param   string  $query  Input SQL string with which to split into individual queries.
	 *
	 * @return  array  The queries from the input string separated into an array.
	 *
	 * @since   11.1
	 * @deprecated  13.1 (Platform) & 4.0 (CMS)
	 */
	public static function splitSql($query)
	{
		Log::add('Database::splitSql() is deprecated, use DatabaseDriver::splitSql() instead.', Log::WARNING, 'deprecated');
		return DatabaseDriver::splitSql($query);
	}
	/**
	 * Return the most recent error message for the database connector.
	 *
	 * @param   boolean  $showSQL  True to display the SQL statement sent to the database as well as the error.
	 *
	 * @return  string  The error message for the most recent query.
	 *
	 * @since   11.1
	 * @deprecated  13.3 (Platform) & 4.0 (CMS)
	 */
	public function stderr($showSQL = false)
	{
		Log::add('Database::stderr() is deprecated.', Log::WARNING, 'deprecated');
		if ($this->errorNum != 0)
		{
			return SoftWayText::sprintf('JLIB_DATABASE_ERROR_FUNCTION_FAILED', $this->errorNum, $this->errorMsg)
			. ($showSQL ? "<br />SQL = <pre>$this->sql</pre>" : '');
		}
		else
		{
			return SoftWayText::_('JLIB_DATABASE_FUNCTION_NOERROR');
		}
	}
	/**
	 * Test to see if the connector is available.
	 *
	 * @return  boolean  True on success, false otherwise.
	 *
	 * @since   11.1
	 * @deprecated  12.3 (Platform) & 4.0 (CMS) - Use DatabaseDriver::isSupported() instead.
	 */
	public static function test()
	{
		Log::add('Database::test() is deprecated. Use DatabaseDriver::isSupported() instead.', Log::WARNING, 'deprecated');
		return static::isSupported();
	}
}
