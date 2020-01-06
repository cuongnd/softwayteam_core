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
 * Text handling class.
 *
 * @since  11.1
 */
class WoobookingText
{
	/**
	 * JavaScript strings
	 *
	 * @var    array
	 * @since  11.1
	 */
	protected static $strings = array();
	/**
	 * Translates a string into the current language.
	 *
	 * Examples:
	 * `<script>alert(softwaycore.WoobookingText._('<?php echo WoobookingText::_("JDEFAULT", array("script"=>true)); ?>'));</script>`
	 * will generate an alert message containing 'Default'
	 * `<?php echo WoobookingText::_("JDEFAULT"); ?>` will generate a 'Default' string
	 *
	 * @param   string   $string                The string to translate.
	 * @param   mixed    $jsSafe                Boolean: Make the result javascript safe.
	 * @param   boolean  $interpretBackSlashes  To interpret backslashes (\\=\, \n=carriage return, \t=tabulation)
	 * @param   boolean  $script                To indicate that the string will be push in the javascript language store
	 *
	 * @return  string  The translated string or the key is $script is true
	 *
	 * @since   11.1
	 */
	public static function _($string, $jsSafe = false, $interpretBackSlashes = true, $script = false)
	{
		if (is_array($jsSafe))
		{
			if (array_key_exists('interpretBackSlashes', $jsSafe))
			{
				$interpretBackSlashes = (boolean) $jsSafe['interpretBackSlashes'];
			}
			if (array_key_exists('script', $jsSafe))
			{
				$script = (boolean) $jsSafe['script'];
			}
			$jsSafe = array_key_exists('jsSafe', $jsSafe) ? (boolean) $jsSafe['jsSafe'] : false;
		}
		if (self::passSprintf($string, $jsSafe, $interpretBackSlashes, $script))
		{
			return $string;
		}
		$lang = Factory::getLanguage();



        if ($script)
		{
			self::$strings[$string] = $lang->_($string, $jsSafe, $interpretBackSlashes);
			return $string;
		}
		return $lang->_($string, $jsSafe, $interpretBackSlashes);
	}
	public static function __($string,$add_com=false)
	{
		$lang = Factory::getLanguage();
		$has_com=substr($string,0,4);
		if(strtolower($has_com)==="com_"){
		    $space=strpos($string,"_",4);
		    $com=substr($string,0,$space);
            $com=strtolower($com);
            $lang->load($com);
        }
        return WoobookingText::_($string);
	}
	/**
	 * Checks the string if it should be interpreted as sprintf and runs sprintf over it.
	 *
	 * @param   string   &$string               The string to translate.
	 * @param   mixed    $jsSafe                Boolean: Make the result javascript safe.
	 * @param   boolean  $interpretBackSlashes  To interpret backslashes (\\=\, \n=carriage return, \t=tabulation)
	 * @param   boolean  $script                To indicate that the string will be push in the javascript language store
	 *
	 * @return  boolean  Whether the string be interpreted as sprintf
	 *
	 * @since   3.4.4
	 */
	private static function passSprintf(&$string, $jsSafe = false, $interpretBackSlashes = true, $script = false)
	{
		// Check if string contains a comma
		if (strpos($string, ',') === false)
		{
			return false;
		}
		$lang = Factory::getLanguage();
		$string_parts = explode(',', $string);
		// Pass all parts through the WoobookingText translator
		foreach ($string_parts as $i => $str)
		{
			$string_parts[$i] = $lang->_($str, $jsSafe, $interpretBackSlashes);
		}
		$first_part = array_shift($string_parts);
		// Replace custom named placeholders with sprinftf style placeholders
		$first_part = preg_replace('/\[\[%([0-9]+):[^\]]*\]\]/', '%\1$s', $first_part);
		// Check if string contains sprintf placeholders
		if (!preg_match('/%([0-9]+\$)?s/', $first_part))
		{
			return false;
		}
		$final_string = vsprintf($first_part, $string_parts);
		// Return false if string hasn't changed
		if ($first_part === $final_string)
		{
			return false;
		}
		$string = $final_string;
		if ($script)
		{
			foreach ($string_parts as $i => $str)
			{
				self::$strings[$str] = $string_parts[$i];
			}
		}
		return true;
	}
	/**
	 * Translates a string into the current language.
	 *
	 * Examples:
	 * `<?php echo WoobookingText::alt('JALL', 'language'); ?>` will generate a 'All' string in English but a "Toutes" string in French
	 * `<?php echo WoobookingText::alt('JALL', 'module'); ?>` will generate a 'All' string in English but a "Tous" string in French
	 *
	 * @param   string   $string                The string to translate.
	 * @param   string   $alt                   The alternate option for global string
	 * @param   mixed    $jsSafe                Boolean: Make the result javascript safe.
	 * @param   boolean  $interpretBackSlashes  To interpret backslashes (\\=\, \n=carriage return, \t=tabulation)
	 * @param   boolean  $script                To indicate that the string will be pushed in the javascript language store
	 *
	 * @return  string  The translated string or the key if $script is true
	 *
	 * @since   11.1
	 */
	public static function alt($string, $alt, $jsSafe = false, $interpretBackSlashes = true, $script = false)
	{
		$lang = Factory::getLanguage();
		if ($lang->hasKey($string . '_' . $alt))
		{
			$string .= '_' . $alt;
		}
		return self::_($string, $jsSafe, $interpretBackSlashes, $script);
	}
	/**
	 * Like WoobookingText::sprintf but tries to pluralise the string.
	 *
	 * Note that this method can take a mixed number of arguments as for the sprintf function.
	 *
	 * The last argument can take an array of options:
	 *
	 * array('jsSafe'=>boolean, 'interpretBackSlashes'=>boolean, 'script'=>boolean)
	 *
	 * where:
	 *
	 * jsSafe is a boolean to generate a javascript safe strings.
	 * interpretBackSlashes is a boolean to interpret backslashes \\->\, \n->new line, \t->tabulation.
	 * script is a boolean to indicate that the string will be push in the javascript language store.
	 *
	 * Examples:
	 * `<script>alert(softwaycore.WoobookingText._('<?php echo WoobookingText::plural("COM_PLUGINS_N_ITEMS_UNPUBLISHED", 1, array("script"=>true)); ?>'));</script>`
	 * will generate an alert message containing '1 plugin successfully disabled'
	 * `<?php echo WoobookingText::plural('COM_PLUGINS_N_ITEMS_UNPUBLISHED', 1); ?>` will generate a '1 plugin successfully disabled' string
	 *
	 * @param   string   $string  The format string.
	 * @param   integer  $n       The number of items
	 *
	 * @return  string  The translated strings or the key if 'script' is true in the array of options
	 *
	 * @since   11.1
	 */
	public static function plural($string, $n)
	{
		$lang = Factory::getLanguage();
		$args = func_get_args();
		$count = count($args);
		if ($count < 1)
		{
			return '';
		}
		if ($count == 1)
		{
			// Default to the normal sprintf handling.
			$args[0] = $lang->_($string);
			return call_user_func_array('sprintf', $args);
		}
		// Try the key from the language plural potential suffixes
		$found = false;
		$suffixes = $lang->getPluralSuffixes((int) $n);
		array_unshift($suffixes, (int) $n);
		foreach ($suffixes as $suffix)
		{
			$key = $string . '_' . $suffix;
			if ($lang->hasKey($key))
			{
				$found = true;
				break;
			}
		}
		if (!$found)
		{
			// Not found so revert to the original.
			$key = $string;
		}
		if (is_array($args[$count - 1]))
		{
			$args[0] = $lang->_(
				$key, array_key_exists('jsSafe', $args[$count - 1]) ? $args[$count - 1]['jsSafe'] : false,
				array_key_exists('interpretBackSlashes', $args[$count - 1]) ? $args[$count - 1]['interpretBackSlashes'] : true
			);
			if (array_key_exists('script', $args[$count - 1]) && $args[$count - 1]['script'])
			{
				self::$strings[$key] = call_user_func_array('sprintf', $args);
				return $key;
			}
		}
		else
		{
			$args[0] = $lang->_($key);
		}
		return call_user_func_array('sprintf', $args);
	}
	/**
	 * Passes a string thru a sprintf.
	 *
	 * Note that this method can take a mixed number of arguments as for the sprintf function.
	 *
	 * The last argument can take an array of options:
	 *
	 * array('jsSafe'=>boolean, 'interpretBackSlashes'=>boolean, 'script'=>boolean)
	 *
	 * where:
	 *
	 * jsSafe is a boolean to generate a javascript safe strings.
	 * interpretBackSlashes is a boolean to interpret backslashes \\->\, \n->new line, \t->tabulation.
	 * script is a boolean to indicate that the string will be push in the javascript language store.
	 *
	 * @param   string  $string  The format string.
	 *
	 * @return  string  The translated strings or the key if 'script' is true in the array of options.
	 *
	 * @since   11.1
	 */
	public static function _sprintf($string)
	{
        $lang = Factory::getLanguage();
        $has_com=substr($string,0,4);
        if(strtolower($has_com)==="com_"){
            $space=strpos($string,"_",4);
            $com=substr($string,0,$space);
            $com=strtolower($com);
            $lang->load($com);
        }
        $lang->load("com_users");
        $lang->load("com_content");
        $lang->load("com_categories");
        $lang->load("com_config");
        $lang->load("com_menus");
        $lang->load("com_modules");
        $lang->load("com_plugins");
		$lang = Factory::getLanguage();
		$args = func_get_args();
		$count = count($args);
		if ($count < 1)
		{
			return '';
		}
		if (is_array($args[$count - 1]))
		{
			$args[0] = $lang->_(
				$string, array_key_exists('jsSafe', $args[$count - 1]) ? $args[$count - 1]['jsSafe'] : false,
				array_key_exists('interpretBackSlashes', $args[$count - 1]) ? $args[$count - 1]['interpretBackSlashes'] : true
			);
			if (array_key_exists('script', $args[$count - 1]) && $args[$count - 1]['script'])
			{
				self::$strings[$string] = call_user_func_array('sprintf', $args);
				return $string;
			}
		}
		else
		{
			$args[0] = $lang->_($string);
		}
		// Replace custom named placeholders with sprintf style placeholders
		$args[0] = preg_replace('/\[\[%([0-9]+):[^\]]*\]\]/', '%\1$s', $args[0]);
		return call_user_func_array('sprintf', $args);
	}
	public static function sprintf($string)
	{
		$lang = Factory::getLanguage();
		$args = func_get_args();
		$count = count($args);
		if ($count < 1)
		{
			return '';
		}
		if (is_array($args[$count - 1]))
		{
			$args[0] = $lang->_(
				$string, array_key_exists('jsSafe', $args[$count - 1]) ? $args[$count - 1]['jsSafe'] : false,
				array_key_exists('interpretBackSlashes', $args[$count - 1]) ? $args[$count - 1]['interpretBackSlashes'] : true
			);
			if (array_key_exists('script', $args[$count - 1]) && $args[$count - 1]['script'])
			{
				self::$strings[$string] = call_user_func_array('sprintf', $args);
				return $string;
			}
		}
		else
		{
			$args[0] = $lang->_($string);
		}
		// Replace custom named placeholders with sprintf style placeholders
		$args[0] = preg_replace('/\[\[%([0-9]+):[^\]]*\]\]/', '%\1$s', $args[0]);
		return call_user_func_array('sprintf', $args);
	}
	/**
	 * Passes a string thru an printf.
	 *
	 * Note that this method can take a mixed number of arguments as for the sprintf function.
	 *
	 * @param   format  $string  The format string.
	 *
	 * @return  mixed
	 *
	 * @since   11.1
	 */
	public static function printf($string)
	{
		$lang = Factory::getLanguage();
		$args = func_get_args();
		$count = count($args);
		if ($count < 1)
		{
			return '';
		}
		if (is_array($args[$count - 1]))
		{
			$args[0] = $lang->_(
				$string, array_key_exists('jsSafe', $args[$count - 1]) ? $args[$count - 1]['jsSafe'] : false,
				array_key_exists('interpretBackSlashes', $args[$count - 1]) ? $args[$count - 1]['interpretBackSlashes'] : true
			);
		}
		else
		{
			$args[0] = $lang->_($string);
		}
		return call_user_func_array('printf', $args);
	}
	/**
	 * Translate a string into the current language and stores it in the JavaScript language store.
	 *
	 * @param   string   $string                The WoobookingText key.
	 * @param   boolean  $jsSafe                Ensure the output is JavaScript safe.
	 * @param   boolean  $interpretBackSlashes  Interpret \t and \n.
	 *
	 * @return  string
	 *
	 * @since   11.1
	 */
	public static function script($string = null, $jsSafe = false, $interpretBackSlashes = true)
	{
		if (is_array($jsSafe))
		{
			if (array_key_exists('interpretBackSlashes', $jsSafe))
			{
				$interpretBackSlashes = (boolean) $jsSafe['interpretBackSlashes'];
			}
			if (array_key_exists('jsSafe', $jsSafe))
			{
				$jsSafe = (boolean) $jsSafe['jsSafe'];
			}
			else
			{
				$jsSafe = false;
			}
		}
		// Add the string to the array if not null.
		if ($string !== null)
		{
			// Normalize the key and translate the string.
			self::$strings[strtoupper($string)] = Factory::getLanguage()->_($string, $jsSafe, $interpretBackSlashes);
			// Load core.js dependency
			Html::_('behavior.core');
		}
		return self::$strings;
	}
}