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
 * Wrapper class for LanguageTransliterate
 *
 * @package     softwaycore.Platform
 * @subpackage  Language
 * @since       3.4
 */
class LanguageWrapperTransliterate
{
	/**
	 * Helper wrapper method for utf8_latin_to_ascii
	 *
	 * @param   string   $string  String to transliterate.
	 * @param   integer  $case    Optionally specify upper or lower case. Default to null.
	 *
	 * @return  string  Transliterated string.
	 *
	 * @see     LanguageTransliterate::utf8_latin_to_ascii()
	 * @since   3.4
	 */
	public function utf8_latin_to_ascii($string, $case = 0)
	{
		return LanguageTransliterate::utf8_latin_to_ascii($string, $case);
	}
}
