<?php
/**
 * @package     softwaycore.Platform
 * @subpackage  Language
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_SOFT_WAY_EXEC') or die;
/**
 * Wrapper class for SoftWayText
 *
 * @package     softwaycore.Platform
 * @subpackage  Language
 * @since       3.4
 */
class LanguageWrapperText
{
	/**
	 * Helper wrapper method for _
	 *
	 * @param   string   $string                The string to translate.
	 * @param   mixed    $jsSafe                Boolean: Make the result javascript safe.
	 * @param   boolean  $interpretBackSlashes  To interpret backslashes (\\=\, \n=carriage return, \t=tabulation).
	 * @param   boolean  $script                To indicate that the string will be push in the javascript language store.
	 *
	 * @return  string  The translated string or the key is $script is true.
	 *
	 * @see     SoftWayText::_
	 * @since   3.4
	 */
	public function _($string, $jsSafe = false, $interpretBackSlashes = true, $script = false)
	{
		return SoftWayText::_($string, $jsSafe, $interpretBackSlashes, $script);
	}
	/**
	 * Helper wrapper method for alt
	 *
	 * @param   string   $string                The string to translate.
	 * @param   string   $alt                   The alternate option for global string.
	 * @param   mixed    $jsSafe                Boolean: Make the result javascript safe.
	 * @param   boolean  $interpretBackSlashes  To interpret backslashes (\\=\, \n=carriage return, \t=tabulation).
	 * @param   boolean  $script                To indicate that the string will be pushed in the javascript language store.
	 *
	 * @return  string  The translated string or the key if $script is true.
	 *
	 * @see     SoftWayText::alt
	 * @since   3.4
	 */
	public function alt($string, $alt, $jsSafe = false, $interpretBackSlashes = true, $script = false)
	{
		return SoftWayText::alt($string, $alt, $jsSafe, $interpretBackSlashes, $script);
	}
	/**
	 * Helper wrapper method for plural
	 *
	 * @param   string   $string  The format string.
	 * @param   integer  $n       The number of items.
	 *
	 * @return  string  The translated strings or the key if 'script' is true in the array of options.
	 *
	 * @see     SoftWayText::plural
	 * @since   3.4
	 */
	public function plural($string, $n)
	{
		return SoftWayText::plural($string, $n);
	}
	/**
	 * Helper wrapper method for sprintf
	 *
	 * @param   string  $string  The format string.
	 *
	 * @return  string  The translated strings or the key if 'script' is true in the array of options.
	 *
	 * @see     SoftWayText::sprintf
	 * @since   3.4
	 */
	public function sprintf($string)
	{
		return SoftWayText::sprintf($string);
	}
	/**
	 * Helper wrapper method for printf
	 *
	 * @param   format  $string  The format string.
	 *
	 * @return  mixed
	 *
	 * @see     SoftWayText::printf
	 * @since   3.4
	 */
	public function printf($string)
	{
		return SoftWayText::printf($string);
	}
	/**
	 * Helper wrapper method for script
	 *
	 * @param   string   $string                The SoftWayText key.
	 * @param   boolean  $jsSafe                Ensure the output is JavaScript safe.
	 * @param   boolean  $interpretBackSlashes  Interpret \t and \n.
	 *
	 * @return  string
	 *
	 * @see     SoftWayText::script
	 * @since   3.4
	 */
	public function script($string = null, $jsSafe = false, $interpretBackSlashes = true)
	{
		return SoftWayText::script($string, $jsSafe, $interpretBackSlashes);
	}
}
