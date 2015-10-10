<?php
class StringValidator{
	
	/**
	 * Method to validate the input is locale (Ex.: EN-US)
	 */
	public static function isLocale($input){
		$pattern = "/^[A-Z]{2}\-[A-Z]{2}$/";
		return preg_match($pattern,$input);
	}
	
	/**
	 * Method to validate if the input is integer
	 */
	public static function isInteger($input){
		$pattern = "/^[0-9]+$/";
		return preg_match($pattern,$input);
	}
	
	public static function isIccid($input){
		$pattern = "/^[0-9]{19}$/";
		return preg_match($pattern,$input);
	}
	
	/**
	 * Method to validate if the input is a date (MM-DD-YYYY)
	 */
	public static function isDate($input){
		$pattern = "/^[0-9]{2}\-[0-9]{2}\-[0-9]{4}$/";
		return preg_match($pattern,$input);
	}
	
	/**
	 * Method to verify if the input is a alphanumeric
	 */
	public static function isAlphanumeric($input){
		$pattern = "/^[A-Za-z0-9\_\-\ ]+$/";
		return preg_match($pattern,$input);
	}
	
	/**
	 * Method to validate if the input is a country iso 2. EX.: US (United States)
	 */
	public static function isCountryIso2($input){
		$pattern = "/^[A-Za-z]{2}$/";
		return preg_match($pattern,$input);
	}
	
	/**
	 * Method to validate the input is email
	 */
	public static function isEmail($input = null){
		return filter_var($input, FILTER_VALIDATE_EMAIL);;
	}	
	
	/**
	 * Method to validate the input is a valid password
	 */
	public static function isPassword($input){
		$pattern = "/^.{6,}$/";
		return preg_match($pattern,$input);
	}
	
	
	public static function isDomain($input){
		return (preg_match("/^([a-z\d](-*[a-z\d])*)(\.([a-z\d](-*[a-z\d])*))*$/i", $input) //valid chars check
            && preg_match("/^.{1,253}$/", $input) //overall length check
            && preg_match("/^[^\.]{1,63}(\.[^\.]{1,63})*$/", $input)   ); //length of each label
	}
}

?>
