<?php

namespace App\Modules\Api\Common\Library;

use Phalcon\Di as Di;


class UtilityBase
{
	private static $fastHashAlgorithm = 'SHA256';
	
	public static function isInteger($value = '')
	{
		if(is_scalar($value) && preg_match('/^[+-]?[0-9]+$/', (string)$value) && $value <= PHP_INT_MAX && $value >= PHP_INT_MIN)
		{
			return true;
		}
		
		return false;
	}
	
	public static function isDecimal($value = '')
	{
		if(is_scalar($value) && preg_match('/^[+-]?[0-9]*\.?[0-9]+$/', (string)$value))
		{
			return true;
		}
		
		return false;
	}
	
	public static function getClientUniqueSecretToken($uniqueText = null)
	{
		return empty($uniqueText)?base64_encode(hash(self::$fastHashAlgorithm, Di::getDefault()->getRandom()->bytes(32) . time()%10000 . Di::getDefault()->getRequest()->getClientAddress())):base64_encode(hash(self::$fastHashAlgorithm, $uniqueText));
	}
	
	public static function getPasswordHash($password)
	{
		return Di::getDefault()->getSecurity()->hash($password);
	}
	
	public static function checkPasswordHash($password, $passwordHash)
	{
		if(Di::getDefault()->getSecurity()->checkHash($password, $passwordHash))
		{
			return true;
		}
		else
		{
			self::getPasswordHash(Di::getDefault()->getRandom()->bytes(8));
		}
		
		return false;
	}
	
	public static function getNewAuthToken($tokenId, $tokenPayload = array(), $createdTimestamp = null, $validTillTimestamp = null)
	{
		return null;
	}
	
	public static function getAuthPayload($authToken)
	{
		return null;
	}
	
	public static function sendNotificationMessage($channel, $subject, $message, $toAddress, $fromAddress = null)
	{
		return;
	}
}
