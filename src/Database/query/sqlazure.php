<?php
/**
 * @package     softwaycore.Platform
 * @subpackage  Database
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
namespace SoftWay\CMS\Database\query;
defined('_SOFT_WAY_EXEC') or die;
/**
 * Query Building Class.
 *
 * @since  11.1
 */
class DatabaseQuerySqlazure extends DatabaseQuerySqlsrv
{
	/**
	 * The character(s) used to quote SQL statement names such as table names or field names,
	 * etc.  The child classes should define this as necessary.  If a single character string the
	 * same character is used for both sides of the quoted name, else the first character will be
	 * used for the opening quote and the second for the closing quote.
	 *
	 * @var    string
	 *
	 * @since  11.1
	 */
	protected $name_quotes = '';
}
