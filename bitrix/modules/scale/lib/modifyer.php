<?
namespace Bitrix\Scale;

/**
* Class Modifyers
* @package Bitrix\Scale
*/
class Modifyer
{
	public static function paramsMaker(&$param, $key, $hostname)
	{
		$param = str_replace('##SERVER_PARAMS:hostname##', $hostname, $param);
	}

	public static function startInnerAction($actionId, $hostname, $actionParams)
	{
		if(!is_array($actionParams))
			throw new \Bitrix\Main\ArgumentTypeException("actionParams", "array");

		if(!isset($actionParams["START_FUNC"]) || !is_callable($actionParams["START_FUNC"]))
			throw new \Bitrix\Main\ArgumentTypeException("actionParams[\"START_FUNC\"]", "callable");

		if(strlen($hostname) <= 0)
			throw new \Bitrix\Main\ArgumentNullException("hostname");

		if(isset($actionParams["FUNC_PARAMS"]))
			$params =  $actionParams["FUNC_PARAMS"];
		else
			$params = array();

		array_walk($params, "\\Bitrix\\Scale\\Modifyer::paramsMaker", $hostname);

		sleep(10);
		return call_user_func_array($actionParams["START_FUNC"], $params);
	}

	public static function actionAddRole($hostname, $roleId)
	{
		$roles = \Bitrix\Main\Config\Option::get("scale", "modifyedRoles", "");
		$roles = unserialize($roles);

		if(!isset($roles[$hostname]))
			$roles[$hostname] = array();

		$roleParams = RolesData::getRole($roleId);

		if(isset($roleParams["ONLY_ONE"]) && $roleParams["ONLY_ONE"] == "Y")
		{
			foreach($roles as $host => $hostRoles)
				if($host != $hostname)
					$roles[$host][$roleId] = "N";
		}

		$roles[$hostname][$roleId] = "Y";

		\Bitrix\Main\Config\Option::set("scale", "modifyedRoles", serialize($roles));

		return true;
	}

	public static function actionDelRole($hostname, $roleId)
	{
		$roles = \Bitrix\Main\Config\Option::get("scale", "modifyedRoles", "");
		$roles = unserialize($roles);

		if(!isset($roles[$hostname]))
			$roles[$hostname] = array();

		$roles[$hostname][$roleId] = "N";
		\Bitrix\Main\Config\Option::set("scale", "modifyedRoles", serialize($roles));

		return true;
	}

	public static function getModifyedRoles($hostname)
	{
		$roles = \Bitrix\Main\Config\Option::get("scale", "modifyedRoles", "");
		$roles = unserialize($roles);

		return isset($roles[$hostname]) ? $roles[$hostname] : array();
	}

	public static function setRoleOnlyOne($hostname)
	{
		$roles = \Bitrix\Main\Config\Option::get("scale", "modifyedRoles", "");
		$roles = unserialize($roles);

		return isset($roles[$hostname]) ? $roles[$hostname] : array();
	}
}
