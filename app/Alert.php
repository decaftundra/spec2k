<?php
	
namespace App;

use Illuminate\Http\Request;

class Alert {
    
	/**
	 * Flash success message.
	 *
	 * @params (string) $message
	 * @returns session
	 */
	public static function success($message, $confirm = false)
	{
		return self::create($message, 'success', $confirm);
	}
	
	/**
	 * Flash warning message.
	 *
	 * @params (string) $message
	 * @returns session
	 */
	public static function warning($message, $confirm = false)
	{
		return self::create($message, 'warning', $confirm);
	}
	
	/**
	 * Flash danger message.
	 *
	 * @params (string) $message
	 * @returns session
	 */
	public static function error($message, $confirm = false)
	{
		return self::create($message, 'error', $confirm);
	}
	
	/**
	 * Flash info message.
	 *
	 * @params (string) $message
	 * @returns session
	 */
	public static function info($message, $confirm = false)
	{
		return self::create($message, 'info', $confirm);
	}
	
	/**
	 * Create the flash message.
	 *
	 * @param (type) $name
	 * @return
	 */
	private static function create($message, $status, $confirm = false)
	{
    	session()->flash('alert.message', $message);
		session()->flash('alert.status', $status);
		
		if ($confirm) {
    		session()->flash('alert.confirm', $confirm);
		}
	}

}