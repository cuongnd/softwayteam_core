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
 * softwaycore Platform Database Factory class
 *
 * @since  12.1
 */
class DatabaseFactory
{
	/**
	 * Contains the current DatabaseFactory instance
	 *
	 * @var    DatabaseFactory
	 * @since  12.1
	 */
	private static $_instance = null;
	/**
	 * Method to return a DatabaseDriver instance based on the given options. There are three global options and then
	 * the rest are specific to the database driver. The 'database' option determines which database is to
	 * be used for the connection. The 'select' option determines whether the connector should automatically select
	 * the chosen database.
	 *
	 * Instances are unique to the given options and new objects are only created when a unique options array is
	 * passed into the method.  This ensures that we don't end up with unnecessary database connection resources.
	 *
	 * @param   string  $name     Name of the database driver you'd like to instantiate
	 * @param   array   $options  Parameters to be passed to the database driver.
	 *
	 * @return  DatabaseDriver  A database driver object.
	 *
	 * @since   12.1
	 * @throws  RuntimeException
	 */
	public function getDriver($name = 'mysqli', $options = array())
	{
		// Sanitize the database connector options.
		$options['driver']   = preg_replace('/[^A-Z0-9_\.-]/i', '', $name);
		$options['database'] = (isset($options['database'])) ? $options['database'] : null;
		$options['select']   = (isset($options['select'])) ? $options['select'] : true;
		// Derive the class name from the driver.
		$class = 'DatabaseDriver' . ucfirst(strtolower($options['driver']));
		// If the class still doesn't exist we have nothing left to do but throw an exception.  We did our best.
		if (!class_exists($class))
		{
			throw new DatabaseExceptionUnsupported(sprintf('Unable to load Database Driver: %s', $options['driver']));
		}
		// Create our new DatabaseDriver connector based on the options given.
		try
		{
			$instance = new $class($options);
		}
		catch (RuntimeException $e)
		{
			throw new DatabaseExceptionConnecting(sprintf('Unable to connect to the Database: %s', $e->getMessage()), $e->getCode(), $e);
		}
		return $instance;
	}
	/**
	 * Gets an exporter class object.
	 *
	 * @param   string           $name  Name of the driver you want an exporter for.
	 * @param   DatabaseDriver  $db    Optional DatabaseDriver instance
	 *
	 * @return  DatabaseExporter  An exporter object.
	 *
	 * @since   12.1
	 * @throws  RuntimeException
	 */
	public function getExporter($name, DatabaseDriver $db = null)
	{
		// Derive the class name from the driver.
		$class = 'DatabaseExporter' . ucfirst(strtolower($name));
		// Make sure we have an exporter class for this driver.
		if (!class_exists($class))
		{
			// If it doesn't exist we are at an impasse so throw an exception.
			throw new DatabaseExceptionUnsupported('Database Exporter not found.');
		}
		$o = new $class;
		if ($db instanceof DatabaseDriver)
		{
			$o->setDbo($db);
		}
		return $o;
	}
	/**
	 * Gets an importer class object.
	 *
	 * @param   string           $name  Name of the driver you want an importer for.
	 * @param   DatabaseDriver  $db    Optional DatabaseDriver instance
	 *
	 * @return  DatabaseImporter  An importer object.
	 *
	 * @since   12.1
	 * @throws  RuntimeException
	 */
	public function getImporter($name, DatabaseDriver $db = null)
	{
		// Derive the class name from the driver.
		$class = 'DatabaseImporter' . ucfirst(strtolower($name));
		// Make sure we have an importer class for this driver.
		if (!class_exists($class))
		{
			// If it doesn't exist we are at an impasse so throw an exception.
			throw new DatabaseExceptionUnsupported('Database importer not found.');
		}
		$o = new $class;
		if ($db instanceof DatabaseDriver)
		{
			$o->setDbo($db);
		}
		return $o;
	}
	/**
	 * Gets an instance of the factory object.
	 *
	 * @return  DatabaseFactory
	 *
	 * @since   12.1
	 */
	public static function getInstance()
	{
		return self::$_instance ? self::$_instance : new DatabaseFactory;
	}
	/**
	 * Get the current query object or a new DatabaseQuery object.
	 *
	 * @param   string           $name  Name of the driver you want an query object for.
	 * @param   DatabaseDriver  $db    Optional DatabaseDriver instance
	 *
	 * @return  DatabaseQuery  The current query object or a new object extending the DatabaseQuery class.
	 *
	 * @since   12.1
	 * @throws  RuntimeException
	 */
	public function getQuery($name, DatabaseDriver $db = null)
	{
		// Derive the class name from the driver.
		$class = 'DatabaseQuery' . ucfirst(strtolower($name));
		// Make sure we have a query class for this driver.
		if (!class_exists($class))
		{
			// If it doesn't exist we are at an impasse so throw an exception.
			throw new DatabaseExceptionUnsupported('Database Query class not found');
		}
		return new $class($db);
	}
	/**
	 * Gets an instance of a factory object to return on subsequent calls of getInstance.
	 *
	 * @param   DatabaseFactory  $instance  A DatabaseFactory object.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public static function setInstance(DatabaseFactory $instance = null)
	{
		self::$_instance = $instance;
	}
}
