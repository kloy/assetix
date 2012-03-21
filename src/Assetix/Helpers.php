<?php

namespace Assetix;

class Helpers
{
	/**
	 * Ensure that the string ends with the specified character
	 *
	 * @param string $string String to validate
	 * @return string
	 */
	public static function include_trailing_character($string, $character)
	{
	    if (strlen($string) > 0)
	    {
	        if (substr($string, -1) !== $character)
	        {
	            return $string . $character;
	        }
	        else
	        {
	            return $string;
	        }
	    }
	    else
	    {
	        return $character;
	    }
	}

	public static function remove_prepending_character($string, $character)
	{
		if (strlen($string) > 0)
	    {

	        if (substr($string, 0, 1) === $character)
	        {
	            return substr($string, 1);
	        }
	        else
	        {
	            return $string;
	        }
	    }
	    else
	    {
	        return $character;
	    }
	}

	/**
	 * Ensure that the string ends with forward slash
	 *
	 * @param string $string String to validate
	 * @return string
	 */
	public static function include_trailing_forward_slash($string)
	{
	    return static::include_trailing_character($string, '/');
	}

	public static function remove_prepending_forward_slash($string)
	{
	    return static::remove_prepending_character($string, '/');
	}
}