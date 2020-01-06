<?php
/**
 * @package     softwaycore.Platform
 * @subpackage  Database
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
namespace WooBooking\CMS\Database\exporter;
defined('_WOO_BOOKING_EXEC') or die;
/**
 * MySQL export driver.
 *
 * @since       11.1
 * @deprecated  Will be removed when the minimum supported PHP version no longer includes the deprecated PHP `mysql` extension
 */
class DatabaseExporterMysql extends DatabaseExporterMysqli
{
	/**
	 * Checks if all data and options are in order prior to exporting.
	 *
	 * @return  DatabaseExporterMysql  Method supports chaining.
	 *
	 * @since   11.1
	 * @throws  Exception if an error is encountered.
	 */
	public function check()
	{
		// Check if the db connector has been set.
		if (!($this->db instanceof DatabaseDriverMysql))
		{
			throw new Exception('JPLATFORM_ERROR_DATABASE_CONNECTOR_WRONG_TYPE');
		}
		// Check if the tables have been specified.
		if (empty($this->from))
		{
			throw new Exception('JPLATFORM_ERROR_NO_TABLES_SPECIFIED');
		}
		return $this;
	}
}
